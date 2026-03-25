<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelExtensionModuleEbayConnector extends Model {

	/**
	 * [createTable to create the module tables]
	 * @return [type] [description]
	 */
	 public function createTables() {
 		/**
 		 * create table : "wk_ebay_accounts"
 		 */

  		// Table Structure for Product Mapping Data
  		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_product_data` (
 				`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 				`product_id` INT(11) NOT NULL,
 				`product_template_id` INT(11) NOT NULL
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");


 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_accounts (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`ebay_connector_store_name` varchar(500) NOT NULL,
				`ebay_connector_ebay_sites` int(10) NOT NULL,
				`ebay_connector_ebay_user_id` varchar(500) NOT NULL,
				`ebay_connector_ebay_auth_token` varchar(1000) NOT NULL,
				`ebay_connector_ebay_application_id` varchar(1000) NOT NULL,
				`ebay_connector_ebay_developer_id` varchar(1000) NOT NULL,
				`ebay_connector_ebay_certification_id` varchar(1000) NOT NULL,
				`ebay_connector_ebay_currency` varchar(15) NOT NULL,
				`ebay_connector_ebay_shop_postal_code` varchar(10) NOT NULL,
				`allowed_ebay_event` text NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 			/**
 			 * create table : "wk_ebay_product_schedule"
 			 */
 			$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_product_schedule (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`oc_product_id` int(100) NOT NULL,
				`scheduling_type` varchar(100) NOT NULL,
				`scheduling_date` date NOT NULL,
				`scheduling_time` varchar(100) NOT NULL,
 				PRIMARY KEY (`id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebay_categories"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_categories (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`ebay_category_id` varchar(250) NOT NULL,
				`ebay_category_parent_id` varchar(250) NOT NULL,
				`ebay_category_level` int(10) NOT NULL,
				`ebay_category_name` varchar(250) NOT NULL,
				`ebay_site_id` int(50) NOT NULL,
				`added_date` datetime NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebay_category_data"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_category_data (
 				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 				`data` longtext NOT NULL,
 				`count` varchar(250) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebaysync_categories"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebaysync_categories (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`opencart_category_id` int(100) NOT NULL,
				`ebay_category_id` varchar(500) NOT NULL,
				`ebay_category_name` varchar(250) NOT NULL,
				`pro_condition_attr` varchar(250) NOT NULL,
				`variations_enabled` int(5) NOT NULL,
				`added_date` datetime NOT NULL,
				`account_id` int(50) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 			/**
 			 * create table : "wk_ebay_prod_condition"
 			 */
 			 /**
 	 		 * create table : "ebay_price_rules"
 	 		 */
 		 $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ebay_price_rules (
			 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			 `price_from` int(50) NOT NULL,
			 `price_to` int(50) NOT NULL,
			 `price_value` int(50) NOT NULL,
			 `price_type` int(10) NOT NULL,
			 `price_opration` varchar(20) NOT NULL,
			 `price_status` int(10) NOT NULL,
 			 PRIMARY KEY (`id`)
 		 ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 	  $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "ebay_price_rules_map_product (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`rule_product_id` int(50) NOT NULL,
				`change_price` int(50) NOT NULL,
				`change_type` int(50) NOT NULL,
				`source` varchar(50) NOT NULL,
				`rule_id` int(50) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

  	  /**
  	   * create table : "ebay_price_rules"
  	   */


 		/**
 		 * create table : "wk_ebay_prod_condition"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_prod_condition (
				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` varchar(500) NOT NULL,
				`ebay_category_id` varchar(250) NOT NULL,
				`oc_category_id` int(50) NOT NULL,
				`condition_attr_code` varchar(500) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebay_prod_condition_value"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_prod_condition_value (
 				`id_no` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 				`condition_value_id` int(50),
 				`condition_id` int(50) NOT NULL,
 				`value` varchar(500) NOT NULL,
 				PRIMARY KEY (`id_no`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebay_product_to_condition"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_product_to_condition (
 				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 				`oc_product_id` int(50) NOT NULL,
 				`oc_category_id` int(50) NOT NULL,
 				`condition_value_id` int(50) NOT NULL,
 				`condition_id` int(50) NOT NULL,
 				`value` varchar(500) NOT NULL,
 				`name` varchar(500) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_specification_map"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_specification_map (
 				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 				`attr_group_id` int(50) NOT NULL,
 				`valuetype` varchar(500) NOT NULL,
 				`required` boolean NOT NULL,
 				`oc_category_id` int(50) NOT NULL,
 				`ebay_category_id` varchar(500) NOT NULL,
 				`ebay_category_name` varchar(500) NOT NULL,
 				`ebay_specification_code` varchar(500) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebay_variation"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_variation (
 				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 				`card_id` int(50) NOT NULL,
 				`value_id` int(50) NOT NULL,
 				`value_name` varchar(500) NOT NULL,
 				`label` varchar(500) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");


 		/**
 		 * create table : "wk_ebay_oc_product_map"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_oc_product_map (
 				`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 				`oc_product_id` int(50) NOT NULL,
 				`ebay_product_id` varchar(500) NOT NULL,
 				`oc_category_id` int(50) NOT NULL,
 				`oc_image` varchar(500) NOT NULL,
 				`ebay_image` varchar(500) NOT NULL,
 				`product_images` varchar(1000) NOT NULL,
 				`account_id` int(50) NOT NULL,
 				`sync_source` varchar(200) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		/**
 		 * create table : "wk_ebay_order_map"
 		 */
 		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "wk_ebay_order_map (
         `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
         `oc_order_id` int(50) NOT NULL,
         `ebay_order_id` varchar(500) NOT NULL,
         `ebay_order_status` varchar(250) NOT NULL,
         `sync_date` datetime NOT NULL,
         `account_id` int(50) NOT NULL,
         PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		//Table structure for table `wk_ebay_template`
 		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."wk_ebay_template` (
 					`id` int(11) NOT NULL AUTO_INCREMENT,
 					`title` varchar(250) NOT NULL,
 					`ebay_site_id` int(5) NOT NULL,
 					`ebay_category_id` varchar(500) NOT NULL,
 					`template_condition` varchar(250) NOT NULL,
 					`template_basicDetails` varchar(1000) NOT NULL,
 					`template_images` varchar(1000) NOT NULL,
 					`description_type` varchar(250) NOT NULL,
 					`description_content` text NOT NULL,
 					`shipping_condition` text NOT NULL,
 					`return_policy` text NOT NULL,
 					`create_date` datetime NOT NULL,
 					`modify_date` datetime NOT NULL,
 					`status` boolean NOT NULL,
 					PRIMARY KEY (`id`)
 				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		//Table structure for table `wk_ebay_template_placeholder`
 		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."wk_ebay_template_placeholder` (
 				`id` int(11) NOT NULL AUTO_INCREMENT,
 				`template_id` int(11) NOT NULL,
 				`attribute_group_id` int(15) NOT NULL,
 				`placeholder` varchar(500) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		//Table structure for table `wk_ebay_template_to_product`
 		$this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX ."wk_ebay_template_to_product` (
 				`id` int(11) NOT NULL AUTO_INCREMENT,
 				`product_id` int(11) NOT NULL,
 				`template_id` int(11) NOT NULL,
 				`ebay_site_id` int(50) NOT NULL,
 				`account_id` int(50) NOT NULL,
 				PRIMARY KEY (`id`)
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");
 		/**
 		*	Ebay Updating Tables
 		*/
 		// Table Structure for Price-Quantity Rule
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_price_qty_rule` (
 				`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 				`rule_for` ENUM('price','qty') NOT NULL,
 				`portation` ENUM('import', 'export') NOT NULL,
 				`min`	DECIMAL(15, 4) NOT NULL,
 				`max`	DECIMAL(15, 4) NOT NULL,
 				`operation_type` ENUM ('+', '-') NOT NULL,
 				`operation` ENUM ('fixed', 'percentage') NOT NULL,
 				`value` FLOAT(7, 2) NOT NULL,
 				`sort_order` TINYINT(2) NOT NULL,
 				`status` TINYINT(1) NOT NULL
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		// Table Structure for Product Mapping Data
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_product_data` (
 				`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 				`product_id` INT(11) NOT NULL,
 				`product_template_id` INT(11) NOT NULL
 			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");
 		// Table Structure for Shipping Details
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_shipping_details` (
 			`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 			`account_id` INT(11) NOT NULL,
 			`shipping_priority` TINYINT(2) NOT NULL,
 			`shipping_service` VARCHAR(50) NOT NULL,
 			`shipping_cost` DECIMAL(15,4) NOT NULL,
 			`shipping_additional_cost` DECIMAL(15,4) NOT NULL,
 			`shipping_min_time` TINYINT(2) NOT NULL,
 			`shipping_max_time` TINYINT(3) NOT NULL,
 			`free_shipping_status` TINYINT(1) NOT NULL
 		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");
 		// Table Structure for Shipping Details
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_seller_category` (
 			`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 			`ebay_category_id` VARCHAR (250) NOT NULL,
 			`account_id` INT (11) NOT NULL
 		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");
 		// Table Structure for table seller category data
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_seller_category_data` (
 			`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 			`data` LONGTEXT NOT NULL,
 			`count` INT (11) NOT NULL,
 			`account_id` INT (11) NOT NULL
 		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");
 		// Table Structure for table seller category data
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_auction_product` (
 			`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 			`product_id` INT (11) NOT NULL,
 			`ebay_product_id` VARCHAR (250) NOT NULL,
 			`auction_status` TINYINT (1) NOT NULL,
 			`buy_it_now_price` FLOAT(11) NOT NULL,
 			`price_rule_status` ENUM('enabled', 'disabled') NOT NULL
 		) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");


 		// For ebay sync products
 		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "wk_ebay_sync_products` (
 			`id` INT(11) AUTO_INCREMENT PRIMARY KEY,
 			`source_product` INT(11),
 			`destination_product` INT(11)
 		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1");

 		$this->__addVariationOption();
 	}

	public function __addVariationOption(){
		$this->removeVariationOption();

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		$this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = 'select', sort_order = '0'");

		$option_id = $this->db->getLastId();

		foreach ($languages as $key => $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . (int)$option_id . "', language_id = '" . (int)$language['language_id'] . "', name = 'Variations'");
		}
	}

	public function removeVariationOption(){
		$getOptionEntry = $this->db->query("SELECT * FROM ".DB_PREFIX."option o LEFT JOIN ".DB_PREFIX."option_description od ON(o.option_id = od.option_id) WHERE od.name = 'Variations' ")->row;
		if(!empty($getOptionEntry)){
			$this->db->query("DELETE FROM ".DB_PREFIX."option WHERE option_id = '".(int)$getOptionEntry['option_id']."' ");
			$this->db->query("DELETE FROM ".DB_PREFIX."option_description WHERE option_id = '".(int)$getOptionEntry['option_id']."' ");
			$this->db->query("DELETE FROM ".DB_PREFIX."product_option WHERE option_id = '".(int)$getOptionEntry['option_id']."' ");
			$this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE option_id = '".(int)$getOptionEntry['option_id']."' ");
		}
	}

	public function removeTables() {
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_accounts`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_categories`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_category_data`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebaysync_categories`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_prod_condition`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_prod_condition_value`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_product_to_condition`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_specification_map`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_variation`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_oc_product_map`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_order_map`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_template`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_template_placeholder`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_template_to_product`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_price_qty_rule`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_product_data`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_shipping_details`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_seller_category`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_sync_products`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX ."wk_ebay_product_schedule`");
	}
}
