<?php
class ModelExtensionHbappsOrderShipment extends Model {

	public function install(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hb_shipping_company` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(50) NOT NULL,
		  `link` varchar(300) DEFAULT NULL,
		  `store_id` int(11) NOT NULL,
		  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`)
		)ENGINE=MyISAM DEFAULT CHARSET=utf8");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "hb_shipment_order_info` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL,
		  `order_product_id` int(11) NOT NULL,
		  `product_id` int(11) DEFAULT NULL,
		  `shipped_qty` int(11) NOT NULL,
		  `courier_id` int(11) DEFAULT NULL,
		  `code` varchar(50) DEFAULT NULL,
		  `delivery_date` date DEFAULT NULL,
		  `store_id` int(11) NOT NULL,
		  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `date_modified` datetime DEFAULT NULL,
		  `mail` TINYINT NOT NULL DEFAULT '0',
		  PRIMARY KEY (`id`),
		  KEY `order_id` (`order_id`)
		)ENGINE=MyISAM DEFAULT CHARSET=utf8");
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES ('0', 'hb_order_shipment_installer', 'hb_order_shipment_installed', '1', '0')"); 
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_shipping_company` (`name`, `link`, `store_id`, `date_added`) VALUES
					('Fedex', 'https://www.fedex.com/apps/fedextrack/index.html?tracknumbers={tracking_id}&amp;cntry_code=in', 0, '2019-12-22 15:43:53'),
					('DHL Express', 'https://www.dhl.co.in/en/express/tracking.html?AWB={tracking_id}&amp;brand=DHL', 0, '2019-12-23 12:08:29'),
					('Australia Post', 'https://auspost.com.au/mypost/track/#/details/{tracking_id}', 0, '2019-12-23 12:10:54'),
					('Royal Mail', 'https://www.royalmail.com/track-your-item#/tracking-results/{tracking_id}', 0, '2019-12-23 12:13:16'),
					('Fastway', 'https://www.fastway.com.au/tools/track/', 0, '2019-12-23 12:17:48'),
					('La Poste', 'https://www.laposte.fr/outils/track-a-parcel?code={tracking_id}', 0, '2019-12-23 12:19:50'),
					('FAN Courier', 'https://www.fancourier.ro/ro/awb-tracking/', 0, '2019-12-23 12:25:17');");
		
		if (version_compare(VERSION,'2.2.0.0','<' )) {
			$theme = $this->config->get('config_template');
		}else if (version_compare(VERSION,'3.0.0.0','>' )) {
			$theme = $this->config->get('config_theme');
		} else {
			$theme = $this->config->get('theme_default_directory');
		}
		
		if ($theme == 'journal3') {
			$template_name = 'journal3';
		} elseif ($theme == 'journal2') {
			$template_name = 'journal2';
		}else{
			$template_name = 'default';
		}
		
		if ((version_compare(VERSION,'2.0.0.0','>=' )) and (version_compare(VERSION,'2.3.0.2','<=' ))) {
			$ocmod_filename = 'ocmod_order_shipment_'.$template_name.'_2xxx.txt';
			$ocmod_name = 'Add Shipment Details to Orders ['.$template_name.'] [2xxx]';
		}else if (version_compare(VERSION,'3.0.0.0','>=' )) {
			$ocmod_filename = 'ocmod_order_shipment_'.$template_name.'_3xxx.txt';
			$ocmod_name = 'Add Shipment Details to Orders ['.$template_name.'] [3xxx]';
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_courier_options_ocmod'");
		
		$ocmod_version = EXTN_VERSION;
		$ocmod_code = 'huntbee_courier_options_ocmod';	
		$ocmod_author = 'HuntBee OpenCart Services';
		$ocmod_link = 'https://www.huntbee.com/';
		
		$file = DIR_APPLICATION . 'view/template/extension/hbapps/ocmod/'.$ocmod_filename;
		if (file_exists($file)) {
			$ocmod_xml = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
			$ocmod_xml = str_replace('{huntbee_version}',$ocmod_version,$ocmod_xml);
			$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($ocmod_code) . "', name = '" . $this->db->escape($ocmod_name) . "', author = '" . $this->db->escape($ocmod_author) . "', version = '" . $this->db->escape($ocmod_version) . "', link = '" . $this->db->escape($ocmod_link) . "', xml = '" . $this->db->escape($ocmod_xml) . "', status = '1', date_added = NOW()");
		}

	}
	
	public function upgrade() {
		$delivery_date = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hb_shipment_order_info` LIKE 'delivery_date'");
		if (!$delivery_date->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "hb_shipment_order_info` ADD `delivery_date` date DEFAULT NULL AFTER `code`");
		}
		$mail = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hb_shipment_order_info` LIKE 'mail'");
		if (!$mail->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "hb_shipment_order_info` ADD `mail` TINYINT NOT NULL DEFAULT '0' AFTER `date_modified`");
		}
		$bulk = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hb_shipment_order_info` LIKE 'bulk'");
		if ($bulk->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "hb_shipment_order_info` DROP `bulk`");
		}
		
		$order_product_id = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hb_shipment_order_info` LIKE 'order_product_id'");
		if (!$order_product_id->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "hb_shipment_order_info` ADD `order_product_id` INT NOT NULL AFTER `order_id`");
		}
		
		$shipped_qty = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "hb_shipment_order_info` LIKE 'shipped_qty'");
		if (!$shipped_qty->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "hb_shipment_order_info` ADD `shipped_qty` int(11) NOT NULL AFTER `product_id`");
		}
		
		$query = $this->db->query("SELECT a.id as id, b.order_product_id as order_product_id, b.quantity as quantity FROM `" . DB_PREFIX . "hb_shipment_order_info` a LEFT JOIN " . DB_PREFIX . "order_product b ON a.order_id = b.order_id WHERE a.product_id = b.product_id AND a.order_product_id = 0");
		if ($query->rows) {
			foreach ($query->rows as $row) {
				$this->db->query("UPDATE `" . DB_PREFIX . "hb_shipment_order_info` SET shipped_qty = '".(int)$row['quantity']."' WHERE id = '".(int)$row['id']."'");
			}
		}
		
		$query = $this->db->query("SELECT a.id as id, b.order_product_id as order_product_id FROM `" . DB_PREFIX . "hb_shipment_order_info` a LEFT JOIN " . DB_PREFIX . "order_product b ON a.order_id = b.order_id WHERE a.product_id = b.product_id AND a.order_product_id = 0");
		if ($query->rows) {
			foreach ($query->rows as $row) {
				$this->db->query("UPDATE `" . DB_PREFIX . "hb_shipment_order_info` SET order_product_id = '".(int)$row['order_product_id']."' WHERE id = '".(int)$row['id']."'");
			}
		}
		
		$query = $this->db->query("SELECT a.id as id, b.order_product_id as order_product_id, b.quantity as quantity FROM `" . DB_PREFIX . "hb_shipment_order_info` a LEFT JOIN " . DB_PREFIX . "order_product b ON a.order_id = b.order_id WHERE a.product_id = b.product_id AND a.shipped_qty = 0");
		if ($query->rows) {
			foreach ($query->rows as $row) {
				$this->db->query("UPDATE `" . DB_PREFIX . "hb_shipment_order_info` SET shipped_qty = '".(int)$row['quantity']."' WHERE id = '".(int)$row['id']."'");
			}
		}
		
		$this->db->query("UPDATE `" . DB_PREFIX . "hb_shipment_order_info` SET `mail` = 1 ");
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_shipping_company`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "hb_shipment_order_info`");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `code` = 'hb_order_shipment_installer'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` = 'huntbee_courier_options_ocmod'");
	}
	
	public function addpartner($name, $tracking_url, $store_id) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "hb_shipping_company` (`name`,`link`,`store_id`) VALUES ('" . $this->db->escape($name). "','" . $this->db->escape($tracking_url). "','".(int)$store_id."')");
	}
	
	public function editpartner($id, $name, $tracking_url) {
		$this->db->query("UPDATE `" . DB_PREFIX . "hb_shipping_company` SET `name` = '" . $this->db->escape($name). "', `link` = '" . $this->db->escape($tracking_url). "' WHERE `id` = '".(int)$id."'");
	}
	
	public function deletepartner($id){
		$this->db->query("DELETE FROM `" . DB_PREFIX . "hb_shipping_company` WHERE `id` = '" . (int)$id . "'");
	}
	
	public function getPartners($data){
		$sql = "SELECT * FROM " . DB_PREFIX . "hb_shipping_company WHERE `store_id` = '".(int)$data['store_id']."' ORDER BY date_added DESC";
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
	
	public function getTotalPartners($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_shipping_company` WHERE `store_id` = '".(int)$data['store_id']."'";
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	public function getShipmentOrders($data){
		$sql = "SELECT *, b.name as ship_name, c.name as product_name FROM `".DB_PREFIX."hb_shipment_order_info` a LEFT JOIN `".DB_PREFIX."hb_shipping_company` b ON a.courier_id = b.id LEFT JOIN `".DB_PREFIX."order_product` c ON a.order_product_id = c.order_product_id WHERE a.`store_id` = '".(int)$data['store_id']."'";
		if (!empty($data['search'])) {
			$sql .= " AND (a.`order_id` LIKE '%".$this->db->escape($data['search'])."%' OR a.`code` LIKE '%".$this->db->escape($data['search'])."%')";
		}
		$sql .= " ORDER BY a.date_added DESC";
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
	
	public function getTotalShipmentOrders($data){
		$sql = "SELECT * FROM `".DB_PREFIX."hb_shipment_order_info` WHERE `store_id` = '".(int)$data['store_id']."'";
		if (!empty($data['search'])) {
			$sql .= " AND (`order_id` LIKE '%".$this->db->escape($data['search'])."%' OR `code` LIKE '%".$this->db->escape($data['search'])."%')";
		}
		$sql .= " ORDER BY date_added DESC";
		$results = $this->db->query($sql);
		return $results->num_rows;
	}
	
	
	public function get_all_courier($store_id){
		$sql = "SELECT * FROM " . DB_PREFIX . "hb_shipping_company WHERE `store_id` = '".(int)$store_id."' ORDER BY name ASC";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function send_email($order_id, $shipment_id, $send = false, $shipped_order_status = 0){
		$this->load->model('sale/order');
		$this->load->model('setting/setting');
		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		
		$order_info = $this->model_sale_order->getOrder($order_id);
		
		$extn_info = $this->model_setting_setting->getSetting('hb_shipment', $order_info['store_id']);
		
		$data = array();
		
		if (version_compare(VERSION,'2.0.2.0','<=')) {
			$language = new Language($order_info['language_directory']);
			$language->load('default');
		}else if ((version_compare(VERSION,'2.0.3.1','>=')) and (version_compare(VERSION,'2.2.0.0','<'))){
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_directory']);
		}else{
			$language = new Language($order_info['language_code']);
			$language->load($order_info['language_code']);
		}
		
		$language->load('extension/hbapps/order_shipment');
		
		$text_strings = array(
			'text_order_id','text_date_added','text_order_status','text_payment_method','text_shipping_method','text_email','text_telephone','text_ip','text_payment_address','text_shipping_address','text_products','text_product','text_product_image','text_model','text_quantity','text_price','text_order_total','text_total',
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $language->get($text);
		}
		
		$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_info['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

		if ($order_status_query->num_rows) {
			$order_status = $order_status_query->row['name'];
		} else {
			$order_status = '';
		}
		
		$data['store_name'] 		= $order_info['store_name'];
		$data['store_url'] 			= $order_info['store_url'];
		$data['customer_id'] 		= $order_info['customer_id'];
		$data['link'] 				= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;

		$data['date_added'] 		= date($language->get('date_format_short'), strtotime($order_info['date_added']));
		$data['payment_method'] 	= $order_info['payment_method'];
		$data['shipping_method'] 	= $order_info['shipping_method'];
		$data['email'] 				= $order_info['email'];
		$data['telephone'] 			= $order_info['telephone'];
		$data['ip'] 				= $order_info['ip'];
		$data['order_status'] 		= $order_status;

		if ($order_info['payment_address_format']) {
			$format = $order_info['payment_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
		);

		$replace = array(
			'firstname' => $order_info['payment_firstname'],
			'lastname'  => $order_info['payment_lastname'],
			'company'   => $order_info['payment_company'],
			'address_1' => $order_info['payment_address_1'],
			'address_2' => $order_info['payment_address_2'],
			'city'      => $order_info['payment_city'],
			'postcode'  => $order_info['payment_postcode'],
			'zone'      => $order_info['payment_zone'],
			'zone_code' => $order_info['payment_zone_code'],
			'country'   => $order_info['payment_country']
		);

		$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		if ($order_info['shipping_address_format']) {
			$format = $order_info['shipping_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
		);

		$replace = array(
			'firstname' => $order_info['shipping_firstname'],
			'lastname'  => $order_info['shipping_lastname'],
			'company'   => $order_info['shipping_company'],
			'address_1' => $order_info['shipping_address_1'],
			'address_2' => $order_info['shipping_address_2'],
			'city'      => $order_info['shipping_city'],
			'postcode'  => $order_info['shipping_postcode'],
			'zone'      => $order_info['shipping_zone'],
			'zone_code' => $order_info['shipping_zone_code'],
			'country'   => $order_info['shipping_country']
		);

		$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		// Vouchers
		$data['vouchers'] = array();

		$order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_voucher_query->rows as $voucher) {
			$data['vouchers'][] = array(
				'description' => $voucher['description'],
				'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		// Order Totals
		$data['totals'] = array();
		
		$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

		foreach ($order_total_query->rows as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}
		
		$template = $extn_info['hb_shipment_template'];
		
		$data['shipped_products'] = array();
		
		$shipment_id = explode(',',$shipment_id);
		$total_products = count($shipment_id);
		
		if ($total_products == 1 && $shipment_id[0] <> 0){
			$data['shipping_qty'] = 'single';
			$email_subject = $extn_info['hb_shipment_subject_single'.$order_info['language_id']];
			$shipment_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "hb_shipment_order_info a , `" . DB_PREFIX . "hb_shipping_company` b WHERE a.courier_id = b.id AND a.order_id = '".(int)$order_id."' AND a.id = '".(int)$shipment_id[0]."' ORDER BY date_modified DESC LIMIT 1");
			
			$shipped_products = $this->db->query("SELECT a.*, b.image, c.* FROM " . DB_PREFIX . "hb_shipment_order_info a, " . DB_PREFIX . "product b, " . DB_PREFIX . "order_product c WHERE a.product_id = b.product_id AND a.order_product_id = c.order_product_id AND a.order_id = '" . (int)$order_id . "' AND a.id = '".(int)$shipment_id[0]."'");
		}else{
			
			$email_subject = $extn_info['hb_shipment_subject_multiple'.$order_info['language_id']];
			
			if ($shipment_id[0] > 0) {
				$data['shipping_qty'] = 'single'; //not single actually but selective products. we are hiding the order totals
				$shipment_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "hb_shipment_order_info a , `" . DB_PREFIX . "hb_shipping_company` b WHERE a.courier_id = b.id AND a.order_id = '".(int)$order_id."' AND a.id = '".(int)$shipment_id[0]."'  ORDER BY date_modified DESC LIMIT 1");
				$shipped_products = $this->db->query("SELECT a.*, b.image, c.* FROM " . DB_PREFIX . "hb_shipment_order_info a, " . DB_PREFIX . "product b, " . DB_PREFIX . "order_product c WHERE a.product_id = b.product_id AND a.order_product_id = c.order_product_id and a.order_id = '" . (int)$order_id . "' AND a.id IN (".implode(',',$shipment_id).")");
			}else{
				$data['shipping_qty'] = 'multiple';
				$shipment_info = $this->db->query("SELECT * FROM " . DB_PREFIX . "hb_shipment_order_info a , `" . DB_PREFIX . "hb_shipping_company` b WHERE a.courier_id = b.id AND a.order_id = '".(int)$order_id."' ORDER BY date_modified DESC LIMIT 1");
				$shipped_products = $this->db->query("SELECT a.*, b.image, c.* FROM " . DB_PREFIX . "hb_shipment_order_info a, " . DB_PREFIX . "product b, " . DB_PREFIX . "order_product c WHERE a.product_id = b.product_id AND a.order_product_id = c.order_product_id and a.order_id = '" . (int)$order_id . "'");
			}
			
		}
		
		if ($shipment_info->row) {
			$data['shipment_partner']	=	$shipment_info->row['name'];
			$data['tracking_id']		=	$shipment_info->row['code'];
			$data['delivery_date']		=	($shipment_info->row['delivery_date'] == NULL || $shipment_info->row['delivery_date'] == '0000-00-00')? '' : date($language->get('date_format_short'), strtotime($shipment_info->row['delivery_date']));
			$data['tracking_link']		= 	str_replace('{tracking_id}',$shipment_info->row['code'],$shipment_info->row['link']);
		}else{
			$data['shipment_partner'] = $data['tracking_id'] = $data['delivery_date'] = $data['tracking_link'] = '';
		}
		
		
		// Products
		$data['products'] = array();
		foreach ($shipped_products->rows as $product) {
			$option_data = array();

			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

					if ($upload_info) {
						$value = $upload_info['name'];
					} else {
						$value = '';
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
				);
			}
			
			$image = $this->model_tool_image->resize($product['image'], isset($extn_info['hb_shipment_img_w'])?$extn_info['hb_shipment_img_w']: '100', isset($extn_info['hb_shipment_img_h'])?$extn_info['hb_shipment_img_h']: '100');
			
			$data['products'][] = array(
				'product_id'  	=> $product['product_id'],
				'name'     		=> $product['name'],
				'model'    		=> $product['model'],
				'image'    		=> str_replace(' ','%20',$image), 
				'option'   		=> $option_data,
				'quantity' 		=> $product['shipped_qty'],
				'price'    		=> $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    		=> $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
			);
			
			$data['product_name'] = $product['name'];
			$data['product_name_limited'] =  (strlen($product['name']) > 20) ? substr($product['name'], 0, 17) . '...' : $product['name'];
		}	
		$data['total_items'] 	= count($data['products']);
		$data['remaining'] 		= $data['total_items'] - 1;	

		$data['order_id'] 			= $order_info['order_id'];
		$data['invoice_no'] 		= $order_info['invoice_no'];
		$data['invoice_prefix'] 	= $order_info['invoice_prefix'];
		$data['firstname'] 			= $order_info['firstname'];
		$data['lastname'] 			= $order_info['lastname'];
		$data['customer_comment'] 	= $order_info['comment'];
		$data['order_date'] 		= $data['date_added'];
		$data['order_total'] 		= $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);
		$data['email_subject']      = $email_subject;
		
		$body_mail = $this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_templates/'.$template.TEMPLATE_EXTN, $data);
		$data['shipped_items'] = $this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_sample_blocks/shipped_items_1'.TEMPLATE_EXTN, $data);	
		
		$block1 = isset($extn_info['hb_shipment_tblock_1'.$order_info['language_id']]) ? html_entity_decode($extn_info['hb_shipment_tblock_1'.$order_info['language_id']], ENT_QUOTES, 'UTF-8'): '';	
		$block2 = isset($extn_info['hb_shipment_tblock_2'.$order_info['language_id']]) ? html_entity_decode($extn_info['hb_shipment_tblock_2'.$order_info['language_id']], ENT_QUOTES, 'UTF-8'): '';	
		$block3 = isset($extn_info['hb_shipment_tblock_3'.$order_info['language_id']]) ? html_entity_decode($extn_info['hb_shipment_tblock_3'.$order_info['language_id']], ENT_QUOTES, 'UTF-8'): '';	
		$block4 = isset($extn_info['hb_shipment_tblock_4'.$order_info['language_id']]) ? html_entity_decode($extn_info['hb_shipment_tblock_4'.$order_info['language_id']], ENT_QUOTES, 'UTF-8'): '';	
		
		$body_mail = str_replace('{block1}',$block1,$body_mail);
		$body_mail = str_replace('{block2}',$block2,$body_mail);
		$body_mail = str_replace('{block3}',$block3,$body_mail);
		$body_mail = str_replace('{block4}',$block4,$body_mail);
		
		$sms = isset($extn_info['hb_shipment_sms'.$order_info['language_id']]) ? html_entity_decode($extn_info['hb_shipment_sms'.$order_info['language_id']], ENT_QUOTES, 'UTF-8'): '';	
		$sms_enable = isset($extn_info['hb_shipment_send_sms']) ? true: false;
		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$body_mail = str_replace('{'.$key.'}',$value,$body_mail);
				$email_subject = str_replace('{'.$key.'}',$value,$email_subject);
				$sms = str_replace('{'.$key.'}',$value,$sms);
			}
		}
		$sms = str_replace('{mail_subject}',$email_subject,$sms);
		
		if ($send === true){
			//SEND EMAIL	
			$to 	= $order_info['email'];
			$from 	= $extn_info['hb_shipment_admin_email'];
			$sender = $extn_info['hb_shipment_sender'];
					
			if (version_compare(VERSION,'2.0.1.1','<=' )) {
				$mail = new Mail($this->config->get('config_mail'));
				$mail->protocol = $this->config->get('config_mail_protocol');
			}else {
				if (version_compare(VERSION,'2.3.0.2','>' )) {
					$mail = new Mail($this->config->get('config_mail_engine'));
				}else{
					$mail = new Mail();
					$mail->protocol = $this->config->get('config_mail_protocol');
				}				
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');			
			}
	
			$mail->setTo($to);
			$mail->setFrom($from);
			$mail->setSender(html_entity_decode($sender, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($email_subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(wordwrap($body_mail,50));
			$mail->send();
			
			if ($shipment_id[0] == 0) {
				$this->db->query("UPDATE ".DB_PREFIX."hb_shipment_order_info SET mail = 1 WHERE order_id = '".(int)$order_id."'");
			}else{
				foreach ($shipment_id as $id) {				
					$this->db->query("UPDATE ".DB_PREFIX."hb_shipment_order_info SET mail = 1 WHERE order_id = '".(int)$order_id."' AND id = '".(int)$id."'");
				}
			}
			
			if ($shipped_order_status > 0) {
				$history_comment = isset($extn_info['hb_shipment_shipped_comment'.$order_info['language_id']]) ? $extn_info['hb_shipment_shipped_comment'.$order_info['language_id']]:'';
				$comment_shortcodes = array('shipment_partner','tracking_id','delivery_date','tracking_link');
				foreach ($comment_shortcodes as $cs) {
					$history_comment = str_replace('{'.$cs.'}',$data[$cs],$history_comment);
				}
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$shipped_order_status . "', notify = '1', comment = '" . $this->db->escape($history_comment) . "', date_added = NOW()");
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$shipped_order_status . "' WHERE order_id = '" . (int)$order_id . "'");	
			
			}
			
			if ($sms_enable && $this->check_opencart_sms_installation()){
				if (!empty($order_info['telephone'])) {
					$sms_module_info = $this->model_setting_setting->getSetting('hb_sms', $order_info['store_id']);
					$this->load->library('hbsms');
					$this->hbsms->call_api($order_info['telephone'],$sms,'1',$sms_module_info);
				}
			}
		}else{
			$preview_data['content'] = $body_mail;
			$preview_data['subject'] = $email_subject;
			return $preview_data;
		}
		
	}
	
	public function remaining_qty($order_id, $order_product_id, $total_qty) {
		$query = $this->db->query("SELECT sum(shipped_qty) as shipped_qty  FROM ".DB_PREFIX."hb_shipment_order_info WHERE order_id = '".(int)$order_id."' AND order_product_id = '".(int)$order_product_id."'");
		$total_shipped_qty = $query->row['shipped_qty'];
		$remaining_qty = (int)$total_qty - (int)$total_shipped_qty;
		return $remaining_qty;
	}
	
	public function qty_shipped($order_id, $order_product_id) {
		$query = $this->db->query("SELECT sum(shipped_qty) as shipped_qty  FROM ".DB_PREFIX."hb_shipment_order_info WHERE order_id = '".(int)$order_id."' AND order_product_id = '".(int)$order_product_id."'");
		$total_shipped_qty = (int)$query->row['shipped_qty'];
		return $total_shipped_qty;
	}
	
	public function is_all_shipped($order_id) {
		$query_ordered = $this->db->query("SELECT sum(quantity) as total FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
		$ordered = $query_ordered->row['total'];
		
		$query_shipped = $this->db->query("SELECT sum(shipped_qty) as total FROM " . DB_PREFIX . "hb_shipment_order_info WHERE order_id = '" . (int)$order_id . "'");
		$shipped = $query_shipped->row['total'];
		
		if ($ordered == $shipped) {
			return true;
		} else {
			return false;
		}
	}
	
	public function check_opencart_sms_installation() {
		$opencart_sms_query = $this->db->query("SELECT count(*) as total FROM `".DB_PREFIX."extension` WHERE `code` = 'opencart_sms'");
		if ($opencart_sms_query->row['total'] > 0){
			return true;
		}else{
			return false;
		}
	}
}
?>