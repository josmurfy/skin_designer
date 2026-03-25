<?php
//==============================================================================
// Smart Search v303.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

class ModelExtensionModuleSmartsearch extends Model {
	private $type = 'module';
	private $name = 'smartsearch';
	private $results;
	private $settings;
	
	public function smartsearch($data = array()) {
		// Uncomment the following line if you get errors about exceeding MAX_JOIN_SIZE rows
		//$this->db->query("SET SQL_BIG_SELECTS=1");
		
		$this->settings = $this->getSettings();
		$this->results = array();
		
		if (!empty($this->settings['testing_mode']) && empty($data['ajax'])) {
			$this->session->data[$this->name . '_time'] = microtime(true);
			$this->session->data[$this->name . '_message'] = '';
		}
		
		// Set up data
		$customer_group_id = (isset($this->customer) && $this->customer->isLogged()) ? (int)$this->customer->getGroupId() : $customer_group_id = (int)$this->config->get('config_customer_group_id');
		$language_id = (int)$this->config->get('config_language_id');
		$list = (isset($data['list'])) ? $data['list'] : '';
		$store_id = (isset($this->session->data['store_id'])) ? (int)$this->session->data['store_id'] : (int)$this->config->get('config_store_id');
		
		// Determine whether to record search
		$table_query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $this->name . "'");
		$this->settings['record_search'] = '';
		
		if ($table_query->num_rows && empty($data['ajax']) && !empty($data['filter_name']) && empty($data['start'])) {
			$this->settings['record_search'] = $data['filter_name'] . '<br>';
			if (!empty($data['filter_category_id'])) {
				$this->settings['record_search'] .= '[filtered by category_id ' . $data['filter_category_id'] . ']<br>';
			}
			if (!empty($this->request->get['manufacturer_id'])) {
				$this->settings['record_search'] .= '[filtered by manufacturer_id ' . $this->request->get['manufacturer_id'] . ']<br>';
			}
			if (!empty($this->request->get['attribute'])) {
				foreach ($this->request->get['attribute'] as $attribute_id => $values) {
					$this->settings['record_search'] .= '[filtered by attribute_id ' . $attribute_id . ' with values ' . $values . ']<br>';
				}
			}
		}
		
		// Make case-insensitive
		if (!empty($data['filter_name'])) $data['filter_name'] = strtolower($data['filter_name']);
		if (!empty($data['filter_tag'])) $data['filter_tag'] = strtolower($data['filter_tag']);
		
		// Log Testing Mode data
		$this->logMessage("\n" . '------------------------------ Starting Test ' . date('Y-m-d G:i:s') . ' ------------------------------');
		$this->logMessage('SEARCH PHRASE: ' . $data['filter_name']);
		
		// Perform pre-search replacements
		if (!empty($this->settings['replacement'][1]['replace'])) {
			$replace = array();
			$with = array();
			foreach ($this->settings['replacement'] as $replacement) {
				$replace[] = strtolower($replacement['replace']);
				$with[] = strtolower($replacement['with']);
			}
			if (!empty($data['filter_name'])) $data['filter_name'] = str_replace($replace, $with, ' ' . $data['filter_name'] . ' ');
			if (!empty($data['filter_tag'])) $data['filter_tag'] = str_replace($replace, $with, ' ' . $data['filter_tag'] . ' ');
			$this->logMessage('AFTER REPLACEMENTS: ' . $data['filter_name']);
		}
		
		// Trim whitespace
		if (!empty($data['filter_name'])) $data['filter_name'] = trim($data['filter_name']);
		if (!empty($data['filter_tag'])) $data['filter_tag'] = trim($data['filter_tag']);
		
		// Check if search is cached
		$data = array_merge($this->request->get, $data);
		unset($data['ajax']);
		unset($data['order']);
		unset($data['start']);
		unset($data['limit']);
		$this->settings['search_hash'] = md5(http_build_query($data));
		
		$cache_files = glob(DIR_CACHE . $this->name . '_hash.' . $this->settings['search_hash'] . '.' . $store_id . '.' . $language_id . '.*');
		if ($cache_files && file_exists($cache_files[0]) && !isset($data['filter_status'])) {
			if (substr(strrchr($cache_files[0], '.'), 1) < time()) {
				unlink($cache_files[0]);
			} else {
				$cache = unserialize(file_get_contents($cache_files[0]));
				if (empty($this->session->data[$this->name . '_time'])) {
					return $this->finalResults($cache);
				} else {
					if ($this->settings['testing_mode']) {
						$this->session->data[$this->name . '_message'] .= 'Smart Search found this search phrase cached in ' . round(microtime(true) - $this->session->data[$this->name . '_time'], 4) . ' seconds';
						$this->session->data[$this->name . '_message'] .= ', and would normally finish at that point<br>Assuming no cached search is found . . . ';
						$this->settings['already_cached'] = true;
					} else {
						if ($this->settings['record_search'] != '' && !in_array($this->request->server['REMOTE_ADDR'], array_map('trim', explode("\n", $this->settings['excluded_ips'])))) {
							$this->db->query("INSERT INTO " . DB_PREFIX . $this->name . " SET date_added = NOW(), search = '" . $this->db->escape($this->settings['record_search']) . "', phase = 0, results = " . (int)count($cache) . ", customer_id = " . (int)(is_object($this->customer) ? $this->customer->getId() : -1) . ", ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
						}
						return $this->finalResults($cache);
					}
				}
			}
		}
		
		// Determine search fields
		$this->load->model('catalog/product');
		$product_tag_table = (method_exists($this->model_catalog_product, 'getProductTags')) ? 'product_tag' : '';
		$search_tags = (!empty($this->settings['search_tag']) || (!empty($data['filter_tag']) && $data['filter_tag'] != $data['filter_name']));
		
		$fields = array();
		if (!empty($this->settings['search_product_id']))											$fields['p.product_id'] = 'product_id';
		if (!empty($this->settings['search_name']))													$fields['pd.name'] = 'name';
		if (!empty($this->settings['search_description']) || !empty($data['filter_description']))	$fields['pd.description'] = 'description';
		if (!empty($this->settings['search_meta_title']))											$fields['pd.meta_title'] = 'meta_title';
		if (!empty($this->settings['search_meta_description']))										$fields['pd.meta_description'] = 'meta_description';
		if (!empty($this->settings['search_meta_keyword']))											$fields['pd.meta_keyword'] = 'meta_keyword';
		if ($search_tags && $product_tag_table)														$fields['pt.tag'] = 'tag';
		if ($search_tags && !$product_tag_table)													$fields['pd.tag'] = 'tag';
		if (!empty($this->settings['search_model']) || !empty($data['filter_model']))				$fields['p.model'] = 'model';
		if (!empty($this->settings['search_sku']))													$fields['p.sku'] = 'sku';
		if (!empty($this->settings['search_upc']))													$fields['p.upc'] = 'upc';
		if (!empty($this->settings['search_ean']) && version_compare(VERSION, '1.5.3', '>'))		$fields['p.ean'] = 'ean';
		if (!empty($this->settings['search_jan']) && version_compare(VERSION, '1.5.3', '>'))		$fields['p.jan'] = 'jan';
		if (!empty($this->settings['search_isbn']) && version_compare(VERSION, '1.5.3', '>'))		$fields['p.isbn'] = 'isbn';
		if (!empty($this->settings['search_mpn']) && version_compare(VERSION, '1.5.3', '>'))		$fields['p.mpn'] = 'mpn';
		if (!empty($this->settings['search_location']))												$fields['p.location'] = 'location';
		if (!empty($this->settings['search_category']))												$fields['cd.name'] = 'category';
		if (!empty($this->settings['search_manufacturer']))											$fields['m.name'] = 'manufacturer';
		if (!empty($this->settings['search_attribute_group']))										$fields['agd.name'] = 'attribute_group';
		if (!empty($this->settings['search_attribute_name']))										$fields['ad.name'] = 'attribute_name';
		if (!empty($this->settings['search_attribute_value']))										$fields['pa.text'] = 'attribute_value';
		if (!empty($this->settings['search_option_name']))											$fields['od.name'] = 'option_name';
		if (!empty($this->settings['search_option_value']))											$fields['ovd.name'] = 'option_value';
		
		$search_attributes = in_array('attribute_group', $fields) || in_array('attribute_name', $fields) || in_array('attribute_value', $fields);
		$search_options = in_array('option_name', $fields) || in_array('option_value', $fields);
		
		// Determine relevance rankings
		$relevance_rankings = array();
		foreach ($fields as $column => $alias) {
			if ($alias == 'product_option_value') $alias = 'option_value';
			if ((int)$this->settings['search_' . $alias] >= 0) $relevance_rankings[] = (int)$this->settings['search_' . $alias];
		}
		$relevance_rankings = array_unique($relevance_rankings);
		sort($relevance_rankings);
		
		// Determine sorting
		if (isset($data['sort']) && in_array($data['sort'], array('pd.name', 'p.price', 'rating', 'p.model'))) {
			$sort = $data['sort'];
			$order = "ASC";
			$ignore_relevance_rankings = true;
		} else {
			if ($this->settings['default_sort'] == 'date_added')		$sort = 'p.date_added';
			if ($this->settings['default_sort'] == 'date_available')	$sort = 'p.date_available';
			if ($this->settings['default_sort'] == 'date_modified')		$sort = 'p.date_modified';
			if ($this->settings['default_sort'] == 'model')				$sort = 'p.model';
			if ($this->settings['default_sort'] == 'name')				$sort = 'pd.name';
			if ($this->settings['default_sort'] == 'price')				$sort = 'p.price';
			if ($this->settings['default_sort'] == 'quantity')			$sort = 'p.quantity';
			if ($this->settings['default_sort'] == 'rating')			$sort = 'rating';
			if ($this->settings['default_sort'] == 'times_purchased')	$sort = 'times_purchased';
			if ($this->settings['default_sort'] == 'times_viewed')		$sort = 'p.viewed';
			if ($this->settings['default_sort'] == 'sort_order')		$sort = 'p.sort_order';
			$order = $this->settings['default_order'];
		}
		
		// Reused SQL
		$rating_sql = "
			SELECT AVG(rating) AS total
			FROM " . DB_PREFIX . "review r
			WHERE r.product_id = p.product_id
			AND r.status = 1
			GROUP BY r.product_id
		";
		
		$special_sql = "
			SELECT price FROM " . DB_PREFIX . "product_special ps
			WHERE ps.product_id = p.product_id
			AND ps.customer_group_id = " . $customer_group_id . "
			AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
			ORDER BY ps.priority ASC, ps.price ASC LIMIT 1
		";
		
		if ($list == 'bestseller') {
			$bestseller_sql = "
				SELECT SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op
				LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)
				WHERE op.product_id = p.product_id
				AND o.order_status_id > 0
				GROUP BY op.product_id
				ORDER BY total DESC
			";
		}
		
		// Select SQL
		$select_sql = "SELECT p.product_id, ";
		
		if ($sort == 'p.price') {
			$select_sql .= "p.price, (" . $special_sql . ") AS special";
		} elseif ($sort == 'rating') {
			$select_sql .= "(" . $rating_sql . ") AS rating";
		} elseif ($sort == 'times_purchased') {
			$select_sql .= "SUM(op.quantity) AS times_purchased";
		} else {
			$select_sql .= $sort;
		}
		
		// Join SQL
		$join_sql = " FROM " . DB_PREFIX . "product p";
		$join_sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = " . $language_id . ")";
		$join_sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";
		
		if (in_array('category', $fields)) {
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)";
		}
		if (in_array('manufacturer', $fields)) {
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)";
		}
		if ($search_attributes) {
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (p.product_id = pa.product_id)";
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)";
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (a.attribute_group_id = agd.attribute_group_id)";
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id)";
		}
		if ($search_options) {
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id)";
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "option_description od ON (pov.option_id = od.option_id)";
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id)";
		}
		if ($search_tags && $product_tag_table) {
			$join_sql .= " LEFT JOIN " . DB_PREFIX . $product_tag_table . " pt ON (p.product_id = pt.product_id AND pt.language_id = " . $language_id . ")";
		}
		if ($sort == 'times_purchased') {
			$join_sql .= " LEFT JOIN " . DB_PREFIX . "order_product op ON (op.product_id = p.product_id)";
		}
		
		$join_sql .= " WHERE p.date_available <= NOW() AND p2s.store_id = " . $store_id;
		
		if (!empty($this->settings['hide_out_of_stock'])) {
			$join_sql .= " AND p.quantity > 0";
		}
		
		if (!empty($this->settings['hidden_products'])) {
			$hidden_products = array_map('intval', explode(',', preg_replace('/\s+/', '', $this->settings['hidden_products'])));
			$join_sql .= " AND p.product_id NOT IN (" . implode(",", $hidden_products) . ")";
		}
		
		$select_sql .= $join_sql;
		
		// extension-specific
		$select_sql .= " AND p.status = 1";
		
		// Tag SQL
		if (!empty($data['filter_tag']) && $data['filter_tag'] != $data['filter_name']) {
			$select_sql .= " AND " . $this->likeRegexp($product_tag_table ? 'pt.tag' : 'pd.tag', $data['filter_tag'], $this->settings['partials']);
		}
		
		// Attribute SQL
		if (isset($this->request->get['attribute'])) {
			foreach ($this->request->get['attribute'] as $attribute_id => $values) {
				$values = explode(';', html_entity_decode($values, ENT_QUOTES, 'UTF-8'));
				foreach ($values as &$value) {
					$value = $this->db->escape(htmlentities($value));
				}
				$select_sql .= " AND p.product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = " . (int)$attribute_id . " AND (`text` LIKE '%" . implode("%' OR `text` LIKE '%", $values) . "%'))";
			}
		}
		
		// Category SQL
		if (!empty($data['filter_category_id'])) {
			$select_sql .= " AND p.product_id IN (SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c";
			if (!empty($this->settings['subcategories']) || !empty($data['filter_sub_category'])) {
				$category_ids = array();
				$categories = $this->getCategoriesByParentId($data['filter_category_id']);
				foreach ($categories as $category_id) {
					$category_ids[] = (int)$category_id;
				}
				// LEFT JOIN doesn't work here
				$select_sql .= " JOIN " . DB_PREFIX . "category_path cp ON (cp.category_id = p2c.category_id) WHERE p2c.category_id IN (" . implode(",", $category_ids) . ") OR cp.path_id IN (" . implode(",", $category_ids) . "))";
			} else {
				$select_sql .= " WHERE p2c.category_id = " . (int)$data['filter_category_id'] . ")";
			}
		}
		
		// Filter SQL
		if (isset($this->request->get['filter'])) {
			foreach ($this->request->get['filter'] as $filter_group_id => $values) {
				$values = explode(';', $values);
				foreach ($values as &$value) {
					$value = (int)$value;
				}
				$select_sql .= " AND p.product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_filter WHERE filter_id IN (" . implode(",", $values) . "))";
			}
		}
		
		// Manufacturer SQL
		if (isset($this->request->get['manufacturer_id'])) {
			$select_sql .= " AND p.manufacturer_id = " . (int)$this->request->get['manufacturer_id'];
		}
		
		// Option SQL
		if (isset($this->request->get['option'])) {
			foreach ($this->request->get['option'] as $option_id => $values) {
				$values = explode(';', $values);
				foreach ($values as &$value) {
					$value = (int)$value;
				}
				$select_sql .= " AND p.product_id IN (SELECT product_id FROM " . DB_PREFIX . "product_option_value WHERE option_value_id IN (" . implode(",", $values) . "))";
			}
		}
		
		// Price SQL
		if (isset($this->request->get['price']) || !empty($data['filter_price'])) {
			if (isset($this->request->get['price'])) {
				$prices = $this->request->get['price'];
			} else {
				$prices = (strpos($data['filter_price'], '-')) ? $data['filter_price'] : $data['filter_price'] . '-' . $data['filter_price'];
			}
			
			$tax_multiplier = "";
			
			if ($this->config->get('config_tax') && empty($data['filter_price'])) {
				$country_id = (isset($this->session->data['country_id'])) ? $this->session->data['country_id'] : $this->config->get('config_country_id');
				$zone_id = (isset($this->session->data['zone_id'])) ? $this->session->data['zone_id'] : $this->config->get('config_zone_id');
				
				$tax_multiplier = "
					IFNULL(((
					SELECT IFNULL(SUM(tr.rate), 0) FROM " . DB_PREFIX . "tax_rate tr
					LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz ON (tr.geo_zone_id = z2gz.geo_zone_id)
					LEFT JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg ON (tr.tax_rate_id = tr2cg.tax_rate_id)
					LEFT JOIN " . DB_PREFIX . "tax_rule tru ON (tr.tax_rate_id = tru.tax_rate_id)
					WHERE (z2gz.country_id = 0 OR z2gz.country_id = " . (int)$country_id . ")
					AND (z2gz.zone_id = 0 OR z2gz.zone_id = " . (int)$zone_id . ")
					AND tr2cg.customer_group_id = " . (int)$customer_group_id . "
					AND p.tax_class_id = tru.tax_class_id
					AND tr.type = 'P'
					ORDER BY tru.priority ASC
					LIMIT 1) / 100 + 1), 1) *
				";
			}
			
			$ifnull_special_sql = $tax_multiplier . "IFNULL((" . $special_sql . "), p.price)";
			$price_sqls = array();
			
			foreach (explode(';', $prices) as $range) {
				$price = explode('-', $range);
				$price_sql = $ifnull_special_sql . " >= " . $price[0];
				if (!empty($price[1])) {
					$price_sql .= " AND " . $ifnull_special_sql . " <= " . $price[1];
				}
				$price_sqls[] = $price_sql;
			}
			
			$select_sql .= " AND ((" . implode(") OR (", $price_sqls) . "))";
		}
		
		// Rating SQL
		if (isset($this->request->get['rating'])) {
			$ratings = explode(';', $this->request->get['rating']);
			$rating = array_pop($ratings);
			$select_sql .= " AND (" . $rating_sql . ") >= " . (int)$rating;
		}
		
		// Stock Status SQL
		if (isset($this->request->get['stock_status'])) {
			$stock_sqls = array();
			foreach (explode(';', $this->request->get['stock_status']) as $status) {
				if ($status == 0) {
					$stock_sqls[] = "p.quantity > 0";
				} else {
					$stock_sqls[] = "p.quantity <= 0 AND p.stock_status_id = " . (int)$status;
				}
			}
			$select_sql .= " AND ((" . implode(") OR (", $stock_sqls) . "))";
		}
		
		// Extra Product Pages SQL
		if (isset($this->request->get['list'])) {
			if ($this->request->get['list'] == 'coming') {
				
				$select_sql .= " AND p.date_available > NOW()";
				
			} elseif ($this->request->get['list'] == 'featured') {
				
				$product_ids = array();
				
				$featured_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `code` = 'featured'");
				foreach ($featured_query->rows as $row) {
					$module_settings = (version_compare(VERSION, '2.1', '<')) ? unserialize($row['setting']) : json_decode($row['setting'], true);
					$product_ids = array_unique(array_merge($product_ids, $module_settings['product']));
				}
				
				if ($product_ids) {
					$select_sql .= " AND p.product_id IN (" . implode(",", $product_ids) . ")";
				}
				
			} elseif ($this->request->get['list'] == 'special') {
				
				$select_sql .= " AND EXISTS (" . $special_sql . ")";
				
			}
		}
		
		// Group By and Order By SQL
		$order_sql = " GROUP BY p.product_id ORDER BY ";
		
		if ($sort == 'pd.name' || $sort == 'p.model') {
			$order_sql .= "LCASE(" . $sort . ")";
		} elseif ($sort == 'p.price') {
			$order_sql .= "IFNULL(special, p.price)";
		} elseif ($sort == 'times_purchased') {
			$order_sql .= "times_purchased";
		} else {
			$order_sql .= $sort;
		}
		
		$order_sql .= " " . $order . ", pd.name";
		
		// Load inflector
		if (version_compare(VERSION, '2.1', '<')) {
			$this->load->library('inflect');
		}
		$inflect = new Inflect();
		
		// Phase 1: keywords as exact phrase
		if ($this->settings['phase_behavior'] != 'skip') {
			if (!empty($data['filter_name'])) {
				$keyword = $data['filter_name'];
				$singular = $inflect->singularize($keyword);
				$plural = $inflect->pluralize($keyword);
				foreach ($relevance_rankings as $ranking) {
					$sql_array = array();
					foreach ($fields as $column => $alias) {
						$relevance = ($alias == 'product_option_value') ? $this->settings['search_option_value'] : $this->settings['search_' . $alias];
						if ($relevance == $ranking || isset($ignore_relevance_rankings)) {
							$sql_array[] = $this->likeRegexp($column, $keyword, $this->settings['partials']);
							if (!empty($this->settings['plurals']) && $singular != $keyword) $sql_array[] = $this->likeRegexp($column, $singular, $this->settings['partials']);
							if (!empty($this->settings['plurals']) && $plural != $keyword) $sql_array[] = $this->likeRegexp($column, $plural, $this->settings['partials']);
						}
					}
					$this->runQuery(1, $select_sql . " AND (" . implode(" OR ", $sql_array) . ")" . $order_sql);
				}
				if ($this->settings['phase_behavior'] != 'proceed' && $this->isFinished(1)) return $this->finalResults($this->results);
			} elseif ($search_tags) {
				$this->runQuery(1, $select_sql . $order_sql);
				if ($this->settings['phase_behavior'] != 'proceed' && $this->isFinished(1)) return $this->finalResults($this->results);
			}
		}
		
		// Phase 2: all keywords, properly spelled
		$keywords = (!empty($data['filter_name'])) ? explode(' ', $data['filter_name']) : array();
		if (count($keywords) > 1 || $this->settings['phase_behavior'] == 'skip') {
			foreach ($relevance_rankings as $ranking) {
				$phase_sql = "";
				foreach ($keywords as $keyword) {
					$singular = $inflect->singularize($keyword);
					$plural = $inflect->pluralize($keyword);
					$sql_array = array();
					foreach ($fields as $column => $alias) {
						$relevance = ($alias == 'product_option_value') ? $this->settings['search_option_value'] : $this->settings['search_' . $alias];
						if ($relevance == $ranking || isset($ignore_relevance_rankings)) {
							$sql_array[] = $this->likeRegexp($column, $keyword, $this->settings['partials']);
							if (!empty($this->settings['plurals']) && $singular != $keyword) $sql_array[] = $this->likeRegexp($column, $singular, $this->settings['partials']);
							if (!empty($this->settings['plurals']) && $plural != $keyword) $sql_array[] = $this->likeRegexp($column, $plural, $this->settings['partials']);
						}
					}
					$phase_sql .= " AND (" . implode(" OR ", $sql_array) . ")";
				}
				$this->runQuery(2, $select_sql . $phase_sql . $order_sql);
			}
			if ($this->isFinished(2)) return $this->finalResults($this->results);
		}
		
		// Determine whether caching is enabled
		if (empty($this->settings['cache_misspelling'])) {
			
			// Caching is disabled
			$phase_sql = array();
			
			if (!empty($data['filter_name'])) {
				foreach (explode(' ', $data['filter_name']) as $kw) {
					$underscored = $this->generateVariations($this->settings['tolerance'], $kw, 'underscore');
					$removed = $this->generateVariations($this->settings['tolerance'], $kw, 'remove');
					$transposed = $this->generateVariations($this->settings['tolerance'], $kw, 'transpose');
					$keywords = array_merge($underscored, $removed, $transposed);
					
					if (empty($keywords)) continue;
					
					$variation_sql = array();
					foreach ($keywords as $keyword) {
						foreach ($fields as $column => $alias) {
							$variation_sql[] = $this->likeRegexp($column, $keyword, $this->settings['partials']);
						}
					}
					
					$phase_sql[] = implode(" OR ", $variation_sql);
				}
			}
			
			if (empty($phase_sql)) {
				$phase_sql = array("FALSE");
			}
			
			// Phase 3: all keywords, misspelled
			$this->runQuery(3, $select_sql . " AND ((" . implode(") AND (", $phase_sql) . "))" . $order_sql);
			if ($this->isFinished(3)) return $this->finalResults($this->results);
			
			// Phase 4: any keywords, misspelled
			$this->runQuery(4, $select_sql . " AND ((" . implode(") OR (", $phase_sql) . "))" . $order_sql);
			if ($this->isFinished(4)) return $this->finalResults($this->results);
			
		} else {
			
			// Caching is enabled, check for cache files
			$cache_files = glob(DIR_CACHE . $this->name . '.*.' . $store_id . '.' . $language_id . '.*');
			if ($cache_files) {
				foreach ($cache_files as $cache_file) {
					if (substr(strrchr($cache_file, '.'), 1) < time() && file_exists($cache_file)) {
						unlink($cache_file);
					}
				}
			}
			if (!$cache_files || !file_exists($cache_files[0])) {
				
				// Cache files don't exist
				$loop_interval = max(5000, (int)ini_get('memory_limit') * 400);
				$time = time() + (int)$this->settings['cache_misspelling'];
				
				// Cache SQL
				$cache_sql = "SELECT ";
				foreach ($fields as $column => $alias) {
					$cache_sql .= " " . $column . " AS " . $alias . ",";
				}
				$cache_sql .= " p.product_id";
				
				for ($loop = 0; true; $loop += $loop_interval) {
					$cache = array();
					
					$product_query = $this->db->query($cache_sql . $join_sql . " GROUP BY p.product_id LIMIT " . $loop . "," . $loop_interval);
					if (!$product_query->num_rows) break;
					
					foreach ($product_query->rows as $result) {
						$words = array();
						if (!empty($this->settings['search_description_misspelled'])) {
							$sanitized_description = preg_replace('/[\x00-\x1F]*/u', '', strip_tags(html_entity_decode(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8')));
							$words = array_merge($words, explode(' ', strtolower($sanitized_description)));
						}
						foreach ($fields as $column => $alias) {
							if ($alias == 'description') continue;
							if ($alias == 'tag') {
								if ($product_tag_table) {
									$tags = array();
									$tag_query = $this->db->query("SELECT tag FROM " . DB_PREFIX . $product_tag_table . " WHERE product_id = " . (int)$result['product_id'] . " AND language_id = " . (int)$language_id);
									foreach ($tag_query->rows as $tag) {
										$tags[] = trim(strtolower(html_entity_decode($tag['tag'], ENT_QUOTES, 'UTF-8')));
									}
									$words = array_merge($words, $tags);
								} else {
									$words = array_merge($words, array_map('trim', explode(',', strtolower(html_entity_decode($result[$alias], ENT_QUOTES, 'UTF-8')))));
								}
							} else {
								$words = array_merge($words, explode(' ', strtolower(html_entity_decode($result[$alias], ENT_QUOTES, 'UTF-8'))));
							}
						}
						foreach ($words as $word) {
							if (strlen($word) >= (int)$this->settings['word_length'] && (!isset($cache[$word]) || !in_array($result['product_id'], $cache[$word]))) {
								$cache[$word][] = $result['product_id'];
							}
						}
					}
					
					file_put_contents(DIR_CACHE . $this->name . '.' . ($loop/$loop_interval) . '.' . $store_id . '.' . $language_id . '.' . $time, serialize($cache));
				}
			}
			
			// Phases 3 and 4
			$matches = array();
			$cache_files = glob(DIR_CACHE . $this->name . '.*.' . $store_id . '.' . $language_id . '.*');
			if (empty($cache_files)) $cache_files = array();
			
			foreach ($cache_files as $cache_file) {
				if (!file_exists($cache_file)) continue;
				$cache = unserialize(file_get_contents($cache_file));
				//foreach (array_merge(array($data['filter_name']), explode(' ', $data['filter_name'])) as $keyword) {   // Phase 2.5
				foreach (explode(' ', $data['filter_name']) as $keyword) {
					if (!isset($matches[$keyword])) $matches[$keyword] = array();
					foreach ($cache as $word => $product_ids) {
						similar_text($word, $keyword, $percentage);
						if ($percentage >= $this->settings['tolerance']) {
							$matches[$keyword] = array_merge($matches[$keyword], $product_ids);
						}
					}
				}
			}
			
			$matches_sql = array();
			foreach ($matches as $match_list) {
				$matches_sql[] = (empty($match_list)) ? "FALSE" : "p.product_id IN (" . implode(",", $match_list) . ")";
			}
			if (empty($matches_sql)) {
				$matches_sql = array("FALSE");
			}
			
			// Phase 3: all keywords, misspelled
			$this->runQuery(3, $select_sql . " AND (" . implode(" AND ", $matches_sql) . ")" . $order_sql);
			if ($this->isFinished(3)) return $this->finalResults($this->results);
			
			// Phase 4: any keywords, misspelled
			$this->runQuery(4, $select_sql . " AND (" . implode(" OR ", $matches_sql) . ")" . $order_sql);
			if ($this->isFinished(4)) return $this->finalResults($this->results);
		}
	}
	
	//==============================================================================
	// runQuery()
	//==============================================================================
	private function runQuery($phase, $sql) {
		$this->logMessage("\n" . 'SMART SEARCH QUERY - PHASE ' . $phase . ': ' . $sql);
		$query = $this->db->query($sql);
		foreach ($query->rows as $product) {
			$this->results[] = $product['product_id'];
		}
		$this->results = array_unique($this->results);
	}
	
	//==============================================================================
	// getCategoriesByParentId()
	//==============================================================================
	private function getCategoriesByParentId($category_id) {
		$category_data = array($category_id);
		$category_query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id = " . (int)$category_id);
		foreach ($category_query->rows as $category) {
			$children = $this->getCategoriesByParentId($category['category_id']);
			if ($children) $category_data = array_merge($children, $category_data);
		}
		return $category_data;
	}
	
	//==============================================================================
	// isFinished()
	//==============================================================================
	private function isFinished($phase) {
		if (count($this->results) >= (int)$this->settings['min_results'] || $phase == 4 || $phase === '1-fulltext' || ((int)$this->settings['tolerance'] == 100 && $phase == 2)) {
			if ($this->settings['record_search'] != '' && !in_array($this->request->server['REMOTE_ADDR'], array_map('trim', explode("\n", $this->settings['excluded_ips'])))) {
				$this->db->query("INSERT INTO " . DB_PREFIX . $this->name . " SET date_added = NOW(), search = '" . $this->db->escape($this->settings['record_search']) . "', phase = " . (int)$phase . ", results = " . (int)count($this->results) . ", customer_id = " . (int)(is_object($this->customer) ? $this->customer->getId() : -1) . ", ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
			}
			if (!empty($this->settings['cache_keywords']) && empty($this->settings['already_cached'])) {
				file_put_contents(DIR_CACHE . $this->name . '_hash.' . $this->settings['search_hash'] . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$this->config->get('config_language_id') . '.' . (time() + (int)$this->settings['cache_keywords']), serialize($this->results));
			}
			return true;
		} else {
			return false;
		}
	}
	
	//==============================================================================
	// finalResults()
	//==============================================================================
	private function finalResults($results) {
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : 'module_';
		
		if (!empty($results) && strpos($this->request->get['route'], 'catalog/product') !== 0 && ($this->config->get($prefix . 'filter_by_attribute_status') || $this->config->get($prefix . 'ultimate_filters_status'))) {
			$get = $this->request->get;
			unset($get['sort']);
			unset($get['order']);
			unset($get['page']);
			unset($get['limit']);
			
			if (empty($get['module_id'])) {
				if (version_compare(VERSION, '2.0', '<')) {
					if ($this->config->get($prefix . 'filter_by_attribute_status')) {
						$module_settings = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'filter_by_attribute_module'")->row['value'];
					} else {
						$module_settings = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `key` = 'ultimate_filters_module'")->row['value'];
					}
				} else {
					$module_settings = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE (`code` = 'filter_by_attribute' OR `code` = 'ultimate_filters') ORDER BY module_id ASC")->row['setting'];
				}
				$settings = (version_compare(VERSION, '2.1', '<')) ? unserialize($module_settings) : json_decode($module_settings, true);
			} elseif (version_compare(VERSION, '3.0', '<')) {
				$this->load->model('extension/module');
				$settings = $this->model_extension_module->getModule($get['module_id']);
			} else {
				$this->load->model('setting/module');
				$settings = $this->model_setting_module->getModule($get['module_id']);
			}
			
			$get['caching'] = (!empty($settings['caching'])) ? $settings['caching'] : 0;
			
			$check_relevant_values = false;
			foreach (array('attribute', 'category', 'filter', 'manufacturer', 'option') as $filter_type) {
				if (empty($settings[$filter_type . '_choices'])) continue;
				if ($settings[$filter_type . '_choices'] == 'relevant') {
					$check_relevant_values = true;
				}
				$get[$filter_type . '_choices'] = $settings[$filter_type . '_choices'];
			}
			
			if ($check_relevant_values) {
				$this->load->model('extension/module/filter');
				$this->model_extension_module_filter->getRelevantValues($get, $results);
			}
		}
		
		return $results;
	}
	
	//==============================================================================
	// getSettings()
	//==============================================================================
	public function getSettings() {
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		return $settings;
	}
	
	//==============================================================================
	// logMessage()
	//==============================================================================
	private function logMessage($message) {
		if ($this->settings['testing_mode']) {
			file_put_contents(DIR_LOGS . $this->name . '.messages', print_r($message, true) . "\n", FILE_APPEND|LOCK_EX);
		}
	}
	
	//==============================================================================
	// likeRegexp()
	//==============================================================================
	public function likeRegexp($column, $keyword, $partials) {
		if ($this->settings['use_html_encoding']) {
			$keyword = htmlentities($keyword, ENT_COMPAT, 'UTF-8', false);
		}
		$like_sql = "LCASE(" . $column . ") LIKE '%" . $this->db->escape($keyword) . "%'";
		$like_sql .= ($partials) ? "" : " AND LCASE(" . $column . ") REGEXP '[[:<:]]" . $this->db->escape(preg_replace('/\W/', '', $keyword)) . "[[:>:]]'";
		return "(" . $like_sql . ")";
	}
	
	//==============================================================================
	// generateVariations()
	//==============================================================================
	public function generateVariations($tolerance, $word, $type, $level = 1) {
		$words = array();
		$length = strlen($word);
		if (!$length) return array();
		if ((1 - $level / $length) >= ($tolerance / 100)) {
			for ($j = 0; $j < $length; $j++) {
				if ($type == 'underscore') {
					$new_word = substr_replace($word, '_', $j, 1);
				} elseif ($type == 'remove') {
					$new_word = substr_replace($word, '', $j, 1);
				} elseif ($type == 'transpose') {
					if ($j == $length - 1) continue;
					$new_word = $word;
					$new_word[$j] = $word[$j+1];
					$new_word[$j+1] = $word[$j];
				}
				$words[] = $new_word;
				$words = array_merge($words, $this->generateVariations($tolerance, $new_word, $type, $level + 1));
			}
		}
		return array_unique($words);
	}
	
	//==============================================================================
	// getProducts()
	//==============================================================================
	public function getProducts($smartsearch_results, $filter_data) {
		if (isset($filter_data['order']) && $filter_data['order'] == 'DESC') {
			$smartsearch_results = array_reverse($smartsearch_results);
		}
		
		$results = (empty($filter_data['limit'])) ? $smartsearch_results : array_slice($smartsearch_results, $filter_data['start'], $filter_data['limit']);
		
		$this->load->model('catalog/product');
		$products = array();
		foreach ($results as $result) {
			$product = $this->model_catalog_product->getProduct($result);
			if (!empty($product)) $products[$result] = $product;
		}
		
		if (!empty($this->session->data[$this->name . '_time'])) {
			$this->session->data[$this->name . '_message'] .= 'Smart Search took ' . round(microtime(true) - $this->session->data[$this->name . '_time'], 4) . ' seconds to retrieve the products';
			unset($this->session->data[$this->name . '_time']);
		}
		
		return $products;
	}
}
?>