<?php
class ModelSocialAutoPilotCategory extends Model {
	public function getShareInfo($category_id) {
		$share_info = array();

		$category_info = $this->getCategory($category_id);

		if ($category_info) {
			$this->load->model('tool/image');

			if ($category_info['image']) {
				$image = $this->model_tool_image->resize($category_info['image'], $this->config->get('social_autopilot_image_width'), $this->config->get('social_autopilot_image_height'));
			} else {
				$image = $this->model_tool_image->resize($this->config->get('social_autopilot_image'), $this->config->get('social_autopilot_image_width'), $this->config->get('social_autopilot_image_height'));
			}

			// some stores use space in image path
			$share_info['image'] = rawurlencode(str_replace(" ", "%20", $image));

			$share_info['link'] = rawurlencode($this->getLink($category_id, $category_info['keyword']));

			$share_info['title'] = html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8');
			$share_info['short_description'] = utf8_substr(strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..';

			$share_info['message'] = $this->getMessage($category_info);
		}

		return $share_info;
	}

	private function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('social_autopilot_language_id') . "' AND c.status = '1' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'category_id'      => $query->row['category_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'image'            => $query->row['image'],
				'keyword'          => $this->getKeyword($category_id)
			);
		} else {
			return false;
		}
	}

	private function getKeyword($category_id) {
		$keyword = '';

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$category_id . "'");

		if ($query->num_rows) {
			// check case multilanguage keywords (custom seo extension)
			if ($query->num_rows > 1) {
				foreach ($query->rows as $keyword_info) {
					if (isset($keyword_info['language_id']) && $keyword_info['language_id'] == $this->config->get('social_autopilot_language_id')) {
						$keyword = $keyword_info['keyword'];

						break;
					}
				}
			} else {
				$keyword = $query->row['keyword'];
			}
		}

		return $keyword;
	}

	private function getLink($category_id, $keyword) {
		$store_base_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

		if ($this->config->get('config_seo_url') && !empty($keyword)) {
			return $store_base_url . $keyword;
		} else {
			return $store_base_url . 'index.php?route=product/category&path=' . $category_id;
		}
	}

	private function getMessage($category_info, $template_id = 0) {
		$message = '';

		$this->load->model('social_autopilot/template');

		if ($template_id) {
			$template_info = $this->model_social_autopilot_template->getTemplate($template_id);
		} else {
			$template_info = $this->model_social_autopilot_template->getDefaultTemplateByCategoryCode('category');
		}

		if ($template_info) {
			$find = array(
				'{category.name}',
				'{category.url}'
			);

			$replace = array(
				'category.name'             => html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8'),
				'category.url'              => $this->getLink($category_info['category_id'], $category_info['keyword'])
			);

			$message = str_replace($find, $replace, html_entity_decode($template_info['message'], ENT_QUOTES, 'UTF-8'));
		}

		return $message;
	}

	public function getLastId() {
		$query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category ORDER BY category_id DESC LIMIT 0,1");

		return $query->row['category_id'];
	}

	public function getAutocomplete($data = array()) {
		$sql = "SELECT c.category_id AS id, cd.name as name FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = '1'";

		if (!empty($data['filter_search'])) {
			$sql .= " AND cd.name LIKE '" . $this->db->escape($data['filter_search']) . "%'";
		}

		$sql .= " GROUP BY c.category_id";

		$sort_data = array(
			'cd.name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}
