<?php
class ModelExtensionModuleSocialAutoPilot extends Model {
	public function createTables() {
		// Table sap_channel
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_channel`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_channel` ( `channel_id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `code` varchar(32) NOT NULL, `link` varchar(255) NOT NULL, `status` int(11) NOT NULL, PRIMARY KEY (`channel_id`)) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sap_channel` ( `channel_id`, `name`, `code`, `link`, `status`) VALUES (1, 'Facebook', 'facebook', 'https://www.facebook.com/', 1), (2, 'Twitter', 'twitter', 'https://twitter.com/', 1), (3, 'Google Plus', 'googleplus', 'https://plus.google.com/', 1), (4, 'Linkedin', 'linkedin', 'https://www.linkedin.com/', 1), (5, 'Pinterest', 'pinterest', 'https://www.pinterest.com/', 1);");

		// Table sap_channel_permission
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_channel_permission`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_channel_permission` ( `permission_id` int(11) NOT NULL AUTO_INCREMENT, `channel_id` int(11) NOT NULL, `name` varchar(255) NOT NULL, `page_id` varchar(96) NOT NULL, `access_token` text NOT NULL, `access_token_secret` text NOT NULL, `date_expire` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `type` varchar(32) NOT NULL, `status` int(11) NOT NULL, `extra` text NOT NULL, `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`permission_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sap_channel_setting
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_channel_setting`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_channel_setting` ( `setting_id` int(11) NOT NULL AUTO_INCREMENT, `channel_id` int(11) NOT NULL, `key` varchar(64) NOT NULL, `value` text NOT NULL, PRIMARY KEY (`setting_id`)) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sap_channel_setting` (`setting_id`, `channel_id`, `key`, `value`) VALUES (1, 1, 'message_max_length', '63206'), (2, 1, 'message_min_length', '1'), (3, 2, 'message_max_length', '250'), (4, 2, 'message_min_length', '1'), (5, 4, 'message_min_length', '1'), (6, 4, 'message_max_length', '600'), (7, 5, 'message_min_length', '1'), (8, 5, 'message_max_length', '500'), (9, 5, 'image_required', '1');");

		// Table sap_scheduled_post
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_scheduled_post`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_scheduled_post` ( `scheduled_post_id` int(11) NOT NULL AUTO_INCREMENT, `message` text NOT NULL, `link` text NOT NULL, `image` text NOT NULL, `item_type` varchar(32) NOT NULL, `item_id` int(11) NOT NULL, `status` int(11) NOT NULL, `date_schedule` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`scheduled_post_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sap_scheduled_post_to_channel_permission
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_scheduled_post_to_channel_permission`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_scheduled_post_to_channel_permission` ( `scheduled_post_id` int(11) NOT NULL, `page_id` varchar(96) NOT NULL	) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sap_task
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_task`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_task` ( `task_id` int(11) NOT NULL AUTO_INCREMENT, `request_uid` varchar(32) NOT NULL, `channel_id` int(11) NOT NULL, `message` text NOT NULL, `link` text NOT NULL, `image` text NOT NULL, `processed` int(11) NOT NULL, `response` text NOT NULL, `success_rate` decimal(15,2) NOT NULL, `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`task_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sap_task_to_channel_permission
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_task_to_channel_permission`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_task_to_channel_permission` ( `task_id` int(11) NOT NULL, `page_id` varchar(96) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		// Table sap_template
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_template`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_template` ( `template_id` int(11) NOT NULL AUTO_INCREMENT, `template_category_id` int(11) NOT NULL, `name` varchar(64) NOT NULL, `message` text NOT NULL, `status` int(11) NOT NULL, `default` int(11) NOT NULL, `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`template_id`)) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sap_template` (`template_id`, `template_category_id`, `name`, `message`, `status`, `default`, `date_added`, `date_modified`) VALUES (2, 1, 'Product  - standard', '{product.name} - {product.price} [if.discount.yes] (old price {product.price.old})[endif.discount.yes]', 1, 1, NOW(), NOW()), (3, 2, 'Review - standard', '{review.author} - {review.rating.stars}\r\n\r\n{review.text}', 1, 1, NOW(), NOW()), (4, 3, 'Category Page - standard', '{category.name}', 1, 1, NOW(), NOW()), (5, 4, 'Information Page - standard', '{information.title}', 1, 1, NOW(), NOW());");

		// Table sap_template_category
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_template_category`");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "sap_template_category` (`template_category_id` int(11) NOT NULL AUTO_INCREMENT, `code` varchar(32) NOT NULL, `name` varchar(64) NOT NULL, `status` int(11) NOT NULL, `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', PRIMARY KEY (`template_category_id`)) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "sap_template_category` (`template_category_id`, `code`, `name`, `status`, `date_added`, `date_modified`) VALUES (1, 'product', 'Product', 1, NOW(), NOW()), (2, 'review', 'Review', 1, NOW(), NOW()), (3, 'category', 'Category', 1, NOW(), NOW()), (4, 'information', 'Information', 1, NOW(), NOW());");
	}

	public function removeTables() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_channel`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_channel_permission`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_channel_setting`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_scheduled_post`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_scheduled_post_to_channel_permission`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_task`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_task_to_channel_permission`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_template`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sap_template_category`");
	}
}
?>
