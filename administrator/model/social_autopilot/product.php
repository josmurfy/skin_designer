<?php
class ModelSocialAutoPilotProduct extends Model {
	public function getShareInfo($product_id) {
		$share_info = array();

		$product_info = $this->getProduct($product_id);

		if ($product_info) {
			$this->load->model('tool/image');

			if ($product_info['image']) {
				$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('social_autopilot_image_width'), $this->config->get('social_autopilot_image_height'));
			} else {
				$image = $this->model_tool_image->resize($this->config->get('social_autopilot_image'), $this->config->get('social_autopilot_image_width'), $this->config->get('social_autopilot_image_height'));
			}

			// some stores use space in image path -> ecode for sending
			$share_info['image'] = rawurlencode(str_replace(" ", "%20", $image));

			$share_info['link'] = rawurlencode($this->getLink($product_id, $product_info['keyword']));

			$share_info['title'] = html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8');
			$share_info['short_description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..';

			$share_info['message'] = $this->getMessage($product_info);
		}

		return $share_info;
	}

	private function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('social_autopilot_language_id') . "' AND p.status = '1' AND p.date_available <= NOW()");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'model'            => $query->row['model'],
				'quantity'         => $query->row['quantity'],
				'stock_status_id'  => $query->row['stock_status_id'],
				'image'            => $query->row['image'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'keyword'          => $this->getKeyword($product_id)
			);
		} else {
			return false;
		}
	}

	private function getKeyword($product_id) {
		$keyword = '';

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

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

	private function getLink($product_id, $keyword) {
		$store_base_url = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;

		if ($this->config->get('config_seo_url') && !empty($keyword)) {
			return $store_base_url . $keyword;
		} else {
			return $store_base_url . 'index.php?route=product/product&product_id=' . $product_id;
		}
	}

	private function getMessage($product_info, $template_id = 0) {
		$message = '';

		$this->load->model('social_autopilot/template');

		if ($template_id) {
			$template_info = $this->model_social_autopilot_template->getTemplate($template_id);
		} else {
			$template_info = $this->model_social_autopilot_template->getDefaultTemplateByCategoryCode('product');
		}

		if ($template_info) {
			$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));

			if ((float)$product_info['special']) {
				$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
			} else {
				$special = false;
			}

			if ((float)$product_info['special']) {
				$discount_amount = $this->currency->format($this->tax->calculate($product_info['price'] - $product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->config->get('config_currency'));
			} else {
				$discount_amount = false;
			}

			if ((float)$product_info['special']) {
				$discount_percent = round((1 - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')) / $this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))) * 100, 0);
			} else {
				$discount_percent = false;
			}

			$find = array(
				'{product.name}',
				'{product.model}',
				'{product.price}',
				'{product.price.new}',
				'{product.price.old}',
				'{product.discount.amount}',
				'{product.discount.percent}',
				'{product.quantity}',
				'{product.url}'
			);

			$replace = array(
				'product.name'             => html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'),
				'product.model'            => $product_info['model'],
				'product.price'    		   => ($special) ? $special : $price,
				'product.price.new' 			=> $special,
				'product.price.old' 		   => $price,
				'product.discount.amount'  => $discount_amount,
				'product.discount.percent' => $discount_percent,
				'product.quantity'         => $product_info['quantity'],
				'product.url'              => $this->getLink($product_info['product_id'], $product_info['keyword'])
			);

			$message = str_replace($find, $replace, html_entity_decode($template_info['message'], ENT_QUOTES, 'UTF-8'));

			// Apply Conditional Replace --------------------------------------------------------------

			// if discount (has special price)
			if ($special) {
				$message = preg_replace('/\[if.discount.no\](.+)\[endif.discount.no\]/i', '', $message);
				$message = str_replace(array('[if.discount.yes]', '[endif.discount.yes]'), '', $message);
			} else {
				$message = preg_replace('/\[if.discount.yes\](.+)\[endif.discount.yes\]/i', '', $message);
				$message = str_replace(array('[if.discount.no]', '[endif.discount.no]'), '', $message);
			}
		}

		return $message;
	}

	public function getLastId() {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product ORDER BY product_id DESC LIMIT 0,1");

		return $query->row['product_id'];
	}

	public function getAutocomplete($data = array()) {
		$sql = "SELECT p.product_id AS id, pd.name as name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1'";

		if (!empty($data['filter_search'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_search']) . "%'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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
