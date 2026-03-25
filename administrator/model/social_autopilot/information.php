<?php
class ModelSocialAutoPilotInformation extends Model {
	public function getShareInfo($information_id) {
		$share_info = array();

		$information_info = $this->getInformation($information_id);

		if ($information_info) {
			$this->load->model('tool/image');

			$image = $this->model_tool_image->resize($this->config->get('social_autopilot_image'), $this->config->get('social_autopilot_image_width'), $this->config->get('social_autopilot_image_height'));

			// some stores use space in image path
			$share_info['image'] = rawurlencode(str_replace(" ", "%20", $image));

			$share_info['link'] = rawurlencode($this->getLink($information_id, $information_info['keyword']));

			$share_info['title'] = html_entity_decode($information_info['title'], ENT_QUOTES, 'UTF-8');
			$share_info['short_description'] = utf8_substr(strip_tags(html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..';

			$share_info['message'] = $this->getMessage($information_info);
		}

		return $share_info;
	}

	private function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('social_autopilot_language_id') . "' AND i.status = '1' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'information_id'   => $query->row['information_id'],
				'title'            => $query->row['title'],
				'description'      => $query->row['description'],
				'keyword'          => $this->getKeyword($information_id)
			);
		} else {
			return false;
		}
	}

	private function getKeyword($information_id) {
		$keyword = '';

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'");

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

	private function getLink($information_id, $keyword) {
		$store_base_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

		if ($this->config->get('config_seo_url') && !empty($keyword)) {
			return $store_base_url . $keyword;
		} else {
			return $store_base_url . 'index.php?route=information/information&path=' . $information_id;
		}
	}

	private function getMessage($information_info, $template_id = 0) {
		$message = '';

		$this->load->model('social_autopilot/template');

		if ($template_id) {
			$template_info = $this->model_social_autopilot_template->getTemplate($template_id);
		} else {
			$template_info = $this->model_social_autopilot_template->getDefaultTemplateByCategoryCode('information');
		}

		if ($template_info) {
			$find = array(
				'{information.title}',
				'{information.url}'
			);

			$replace = array(
				'information.title' => html_entity_decode($information_info['title'], ENT_QUOTES, 'UTF-8'),
				'information.url' => $this->getLink($information_info['information_id'], $information_info['keyword'])
			);

			$message = str_replace($find, $replace, html_entity_decode($template_info['message'], ENT_QUOTES, 'UTF-8'));
		}

		return $message;
	}

	public function getLastId() {
		$query = $this->db->query("SELECT information_id FROM " . DB_PREFIX . "information ORDER BY information_id DESC LIMIT 0,1");

		return $query->row['information_id'];
	}

	public function getAutocomplete($data = array()) {
		$sql = "SELECT i.information_id AS id, id.title as name FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i.status = '1'";

		if (!empty($data['filter_search'])) {
			$sql .= " AND id.title LIKE '" . $this->db->escape($data['filter_search']) . "%'";
		}

		$sql .= " GROUP BY i.information_id";

		$sort_data = array(
			'id.name'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id.title";
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
