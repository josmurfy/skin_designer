<?php

/*
 * This file is used of Add products in the new Amazon tables
 * This file is having all the function which is used in submitting products on Amazon
 * File is added by Ashwani on 23-04-2014 
 */

class ModelJgetsyJgetsy extends Model
{
    /* BOF - code to fetch all the product option of an product by Ashwani Gupta on 25-August-2014 */

    public function install()
    {
        $attributeTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_attributes` (
                    `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
                    `etsy_property_id` int(11) NOT NULL,
                    `etsy_property_title` varchar(100) NOT NULL,
                    PRIMARY KEY (`attribute_id`)
                  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $this->db->query($attributeTableSQL);
        
        $attributeOptionTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_attribute_option_mapping` (
            `id_etsy_attribute_option_mapping` int(10) NOT NULL AUTO_INCREMENT,
            `property_id` varchar(20) NOT NULL,
            `id_etsy_profiles` int(10) NOT NULL,
            `id_attribute_group` text NOT NULL,
            `date_added` datetime NOT NULL,
            `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_etsy_attribute_option_mapping`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $this->db->query($attributeOptionTableSQL);
        
        
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "etsy_attributes`");
        if ($results->num_rows <= 0) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "etsy_attributes` (`attribute_id`, `etsy_property_id`, `etsy_property_title`) VALUES
                (1, 200, 'Color'),
                (2, 515, 'Device'),
                (3, 504, 'Diameter'),
                (4, 501, 'Dimensions'),
                (5, 502, 'Fabric'),
                (6, 500, 'Finish'),
                (7, 503, 'Flavor'),
                (8, 505, 'Height'),
                (9, 506, 'Length'),
                (10, 507, 'Material'),
                (11, 508, 'Pattern'),
                (12, 509, 'Scent'),
                (13, 510, 'Style'),
                (14, 100, 'Size'),
                (15, 511, 'Weight'),
                (16, 512, 'Width');");
        }

        $attributeMappingTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_attribute_mapping` (
                    `id_etsy_attribute_mapping` int(10) NOT NULL AUTO_INCREMENT,
                    `property_id` int(10) NOT NULL,
                    `property_title` varchar(255) NOT NULL,
                    `option_id` int(11) NOT NULL,
                    `id_etsy_profiles` int(10) NOT NULL,
                    `id_attribute_group` int(10) NOT NULL,
                    `date_added` datetime NOT NULL,
                    `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id_etsy_attribute_mapping`)
                ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $this->db->query($attributeMappingTableSQL);


        $auditLogSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_audit_log` (
                            `id_etsy_audit_log` int(11) NOT NULL auto_increment,
                            `log_entry` text NOT NULL,
                            `log_user` int(11) NOT NULL,
                            `log_class_method` varchar(255) NOT NULL,
                            `log_time` datetime NOT NULL,
                            PRIMARY KEY  (`id_etsy_audit_log`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($auditLogSQL);

        $categoryTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_categories` (
                            `id_etsy_categories` int(10) NOT NULL AUTO_INCREMENT,
                            `category_code` int(10) NOT NULL,
                            `tag` varchar(500) NOT NULL,
                            `category_name` text NOT NULL,
                            `property_set` text NOT NULL,
                            `parent_id` int(11) DEFAULT NULL,
                            PRIMARY KEY (`id_etsy_categories`),
                            UNIQUE KEY `category_code_2` (`category_code`),
                            KEY `category_code` (`category_code`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
        $this->db->query($categoryTableSQL);

        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_category_mapping` (
                            `id_profile_category` int(10) NOT NULL auto_increment,
                            `id_etsy_profiles` int(10) NOT NULL,
                            `etsy_category_code` text,
                            `prestashop_category` text,
                            `date_add` datetime NOT NULL,
                            `date_upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY  (`id_profile_category`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($createTableSQL);

        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_countries` (
                            `id_etsy_countries` int(10) NOT NULL auto_increment,
                            `country_id` int(10) NOT NULL,
                            `country_name` varchar(255) NOT NULL,
                            `iso_code` varchar(3) NOT NULL,
                            PRIMARY KEY  (`id_etsy_countries`),
                            KEY `country_id` (`country_id`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($createTableSQL);

        $imageTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_images` (
                            `image_id` int(11) NOT NULL AUTO_INCREMENT,
                            `etsy_image_id` bigint(20) DEFAULT NULL,
                            `product_id` int(11) NOT NULL,
                            `image_url` varchar(400) NOT NULL,
                            `update_flag` enum('0','1') NOT NULL DEFAULT '0',
                            `main_image` int(11) NOT NULL DEFAULT '0',
                            PRIMARY KEY (`image_id`),
                            UNIQUE KEY `PRODUCT_ID_IMAGE` (`product_id`,`image_url`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $this->db->query($imageTableSQL);

        $orderTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_orders_list` (
                            `id_etsy_orders_list` int(10) NOT NULL auto_increment,
                            `id_order` int(10) NOT NULL,
                            `id_etsy_order` bigint(25) NOT NULL,
                            `is_status_updated` enum('0','1') NOT NULL DEFAULT '0',
                            `date_added` datetime NOT NULL,
                            `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY  (`id_etsy_orders_list`),
                            KEY `is_status_updated` (`is_status_updated`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($orderTableSQL);

        $productTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_products_list` (
                            `id_etsy_products_list` int(10) NOT NULL AUTO_INCREMENT,
                            `id_etsy_profiles` int(10) NOT NULL,
                            `id_product` int(10) NOT NULL,
                            `reference` varchar(32) NOT NULL,
                            `id_product_attribute` int(10) NOT NULL,
                            `listing_status` enum('Pending','Listed','Inactive','Expired','Draft', 'Disabled') NOT NULL DEFAULT 'Pending',
                            `listing_id` bigint(25) DEFAULT NULL,
                            `listing_image_id` varchar(300) DEFAULT NULL,
                            `update_flag` enum('0','1') NOT NULL DEFAULT '0',
                            `renew_flag` enum('0','1') NOT NULL DEFAULT '0',
                            `delete_flag` enum('0','1','2') NOT NULL DEFAULT '0',
                            `date_added` datetime NOT NULL,
                            `date_listed` datetime DEFAULT NULL,
                            `date_last_renewed` datetime DEFAULT NULL,
                            `listing_error` text NOT NULL,
                            PRIMARY KEY (`id_etsy_products_list`),
                            UNIQUE KEY `listing_id` (`listing_id`),
                            UNIQUE KEY `listing_image_id` (`listing_image_id`),
                            KEY `listing_status` (`listing_status`,`renew_flag`,`delete_flag`),
                            KEY `id_product_attribute` (`id_product_attribute`),
                            KEY `id_etsy_profiles` (`id_etsy_profiles`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
        $this->db->query($productTableSQL);

        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_product_option` (
                            `etsy_product_option_id` int(11) NOT NULL auto_increment,
                            `id_product` int(11) NOT NULL,
                            `color_option_value_id` int(11) NOT NULL,
                            `size_option_value_id` int(11) NOT NULL,
                            PRIMARY KEY  (`etsy_product_option_id`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($createTableSQL);

        $profileTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_profiles` (
                            `id_etsy_profiles` int(10) NOT NULL AUTO_INCREMENT,
                            `profile_title` varchar(255) NOT NULL,
                            `etsy_category_code` int(10) NOT NULL,
                            `etsy_category_text` varchar(200) NOT NULL,
                            `etsy_currency` varchar(100) NOT NULL DEFAULT 'USD',
                            `store_categories` text,
                            `id_etsy_shipping_profiles` int(10) NOT NULL,
                            `is_customizable` enum('1','0') NOT NULL DEFAULT '0',
                            `who_made` enum('i_did','collective','someone_else') NOT NULL DEFAULT 'i_did',
                            `when_made` enum('made_to_order','2010_2019','2000_2009','1998_1999','before_1998','1990_1997','1980s','1970s','1960s','1950s','1940s','1930s','1920s','1910s','1900s','1800s','1700s','before_1700') NOT NULL DEFAULT 'made_to_order',
                            `is_supply` enum('1','0') NOT NULL DEFAULT '0',
                            `recipient` varchar(50) DEFAULT NULL,
                            `occassion` varchar(50) DEFAULT NULL,
                            `active` enum('1','0') NOT NULL DEFAULT '1',
                            `date_added` datetime NOT NULL,
                            `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id_etsy_profiles`),
                            KEY `etsy_category_code` (`etsy_category_code`,`id_etsy_shipping_profiles`,`is_customizable`,`who_made`,`when_made`,`is_supply`,`recipient`(1),`occassion`(1))
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $this->db->query($profileTableSQL);


        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_regions` (
                                `id_etsy_regions` int(10) NOT NULL auto_increment,
                                `region_id` int(10) NOT NULL,
                                `region_name` varchar(255) NOT NULL,
                                PRIMARY KEY  (`id_etsy_regions`),
                                KEY `region_id` (`region_id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($createTableSQL);

        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_shipping_profiles` (
                            `id_etsy_shipping_profiles` int(10) NOT NULL AUTO_INCREMENT,
                            `shipping_profile_id` bigint(25) DEFAULT NULL,
                            `shipping_profile_title` varchar(255) NOT NULL,
                            `shipping_origin_country_id` int(10) NOT NULL,
                            `shipping_origin_country` varchar(255) NOT NULL,
                            `shipping_primary_cost` decimal(15,2) NOT NULL,
                            `shipping_secondary_cost` decimal(15,2) NOT NULL,
                            `shipping_min_process_days` int(2) NOT NULL,
                            `shipping_max_process_days` int(2) NOT NULL,
                            `renew_flag` enum('0','1') NOT NULL DEFAULT '0',
                            `delete_flag` enum('0','1') NOT NULL DEFAULT '0',
                            `shipping_date_added` datetime NOT NULL,
                            `shipping_date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id_etsy_shipping_profiles`),
                            UNIQUE KEY `shipping_profile_id` (`shipping_profile_id`),
                            KEY `renew_flag` (`renew_flag`,`delete_flag`)
                        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
        $this->db->query($createTableSQL);

        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_shipping_profiles_entries` (
                                `id_etsy_shipping_profiles_entries` int(10) NOT NULL AUTO_INCREMENT,
                                `id_etsy_shipping_profiles` int(10) NOT NULL,
                                `shipping_profile_entry_id` bigint(25) DEFAULT NULL,
                                `shipping_entry_destination_country_id` int(10) DEFAULT NULL,
                                `shipping_entry_destination_country` varchar(255) DEFAULT NULL,
                                `shipping_entry_primary_cost` decimal(15,2) NOT NULL,
                                `shipping_entry_secondary_cost` decimal(15,2) NOT NULL,
                                `shipping_entry_destination_region_id` int(10) DEFAULT NULL,
                                `shipping_entry_destination_region` varchar(255) DEFAULT NULL,
                                `renew_flag` enum('0','1') NOT NULL DEFAULT '0',
                                `delete_flag` enum('0','1') NOT NULL DEFAULT '0',
                                `shipping_entry_date_added` datetime NOT NULL,
                                `shipping_entry_date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                PRIMARY KEY (`id_etsy_shipping_profiles_entries`),
                                UNIQUE KEY `shipping_profile_entry_id` (`shipping_profile_entry_id`),
                                KEY `id_etsy_shipping_profiles` (`id_etsy_shipping_profiles`),
                                KEY `renew_flag` (`renew_flag`,`delete_flag`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
        $this->db->query($createTableSQL);

        $createTableSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_translation` (
                            `translation_id` int(11) NOT NULL auto_increment,
                            `id_product` int(11) NOT NULL,
                            `listing_id` int(11) NOT NULL,
                            `status` enum('Listed','Pending','Update') NOT NULL,
                            `lang_code` varchar(5) NOT NULL,
                            `date_added` datetime NOT NULL,
                            `date_updated` datetime NOT NULL,
                            `translation_error` text,
                            PRIMARY KEY  (`translation_id`),
                            UNIQUE KEY `id_product_lang_code` (`id_product`,`lang_code`)
                          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
        $this->db->query($createTableSQL);

        $shopSectionSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_shop_sections` (
                            `shop_section_id` int(11) NOT NULL AUTO_INCREMENT,
                            `etsy_shop_section_id` varchar(50) DEFAULT NULL,
                            `title` varchar(250) NOT NULL,
                            `shop_id` varchar(50) NOT NULL,
                            `renew_flag` enum('0','1') NOT NULL DEFAULT '0',
                            `delete_flag` enum('0','1') NOT NULL DEFAULT '0',
                            PRIMARY KEY (`shop_section_id`)
                          ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
        $this->db->query($shopSectionSQL);
        
        $dataTypeSQL = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "etsy_data_types` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `data_key` varchar(100) NOT NULL,
                            `data` varchar(100) NOT NULL,
                            `type` enum('Recipient','WhenMade','WhoMade','Occasion') NOT NULL,
                            `language` varchar(100) NOT NULL,
                            PRIMARY KEY (`id`)
                          ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
        $this->db->query($dataTypeSQL);
        
        $results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "etsy_data_types`");
        if ($results->num_rows <= 0) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "etsy_data_types` (`id`, `data_key`, `data`, `type`, `language`) VALUES
                (1, 'men', 'men', 'Recipient', 'en'),
                (2, 'women', 'women', 'Recipient', 'en'),
                (3, 'unisex_adults', 'unisex_adults', 'Recipient', 'en'),
                (4, 'teen_boys', 'teen_boys', 'Recipient', 'en'),
                (5, 'teen_girls', 'teen_girls', 'Recipient', 'en'),
                (6, 'teens', 'teens', 'Recipient', 'en'),
                (7, 'boys', 'boys', 'Recipient', 'en'),
                (8, 'girls', 'girls', 'Recipient', 'en'),
                (9, 'children', 'children', 'Recipient', 'en'),
                (10, 'baby_boys', 'baby_boys', 'Recipient', 'en'),
                (11, 'baby_girls', 'baby_girls', 'Recipient', 'en'),
                (12, 'babies', 'babies', 'Recipient', 'en'),
                (13, 'birds', 'birds', 'Recipient', 'en'),
                (14, 'cats', 'cats', 'Recipient', 'en'),
                (15, 'dogs', 'dogs', 'Recipient', 'en'),
                (16, 'pets', 'pets', 'Recipient', 'en'),
                (17, 'not_specified', 'not_specified', 'Recipient', 'en'),
                (18, 'men', 'hommes', 'Recipient', 'fr'),
                (19, 'women', 'femmes', 'Recipient', 'fr'),
                (20, 'unisex_adults', 'adultes_unisexe', 'Recipient', 'fr'),
                (21, 'teen_boys', 'ados_garons', 'Recipient', 'fr'),
                (22, 'teen_girls', 'ados_filles', 'Recipient', 'fr'),
                (23, 'teens', 'adolescents', 'Recipient', 'fr'),
                (24, 'boys', 'garons', 'Recipient', 'fr'),
                (25, 'girls', 'filles', 'Recipient', 'fr'),
                (26, 'children', 'enfants', 'Recipient', 'fr'),
                (27, 'baby_boys', 'bbs_garons', 'Recipient', 'fr'),
                (28, 'baby_girls', 'bbs_filles', 'Recipient', 'fr'),
                (29, 'babies', 'bbs', 'Recipient', 'fr'),
                (30, 'birds', 'oiseaux', 'Recipient', 'fr'),
                (31, 'cats', 'chats', 'Recipient', 'fr'),
                (32, 'dogs', 'chiens', 'Recipient', 'fr'),
                (33, 'pets', 'animaux_domestiques', 'Recipient', 'fr'),
                (34, 'not_specified', 'not_specified', 'Recipient', 'fr'),
                (35, 'men', 'mnner', 'Recipient', 'de'),
                (36, 'women', 'frauen', 'Recipient', 'de'),
                (37, 'unisex_adults', 'unisex_erwachsene', 'Recipient', 'de'),
                (38, 'teen_boys', 'teenager__jungen', 'Recipient', 'de'),
                (39, 'teen_girls', 'teenager__mdchen', 'Recipient', 'de'),
                (40, 'teens', 'jugendliche', 'Recipient', 'de'),
                (41, 'boys', 'jungs', 'Recipient', 'de'),
                (42, 'girls', 'mdchen', 'Recipient', 'de'),
                (43, 'children', 'kinder', 'Recipient', 'de'),
                (44, 'baby_boys', 'babys__jungen', 'Recipient', 'de'),
                (45, 'baby_girls', 'babys__mdchen', 'Recipient', 'de'),
                (46, 'babies', 'babys', 'Recipient', 'de'),
                (47, 'birds', 'vgel', 'Recipient', 'de'),
                (48, 'cats', 'katzen', 'Recipient', 'de'),
                (49, 'dogs', 'hunde', 'Recipient', 'de'),
                (50, 'pets', 'haustiere', 'Recipient', 'de'),
                (51, 'not_specified', 'not_specified', 'Recipient', 'de'),
                (52, 'men', 'hombre', 'Recipient', 'es'),
                (53, 'women', 'mujer', 'Recipient', 'es'),
                (54, 'unisex_adults', 'unisex_adulto', 'Recipient', 'es'),
                (55, 'teen_boys', 'nios_adolescentes', 'Recipient', 'es'),
                (56, 'teen_girls', 'nias_adolescentes', 'Recipient', 'es'),
                (57, 'teens', 'adolescentes', 'Recipient', 'es'),
                (58, 'boys', 'nios', 'Recipient', 'es'),
                (59, 'girls', 'nias', 'Recipient', 'es'),
                (60, 'children', '', 'Recipient', 'es'),
                (61, 'baby_boys', 'bebs_nios', 'Recipient', 'es'),
                (62, 'baby_girls', 'beb_nia', 'Recipient', 'es'),
                (63, 'babies', 'bebs', 'Recipient', 'es'),
                (64, 'birds', 'pjaros', 'Recipient', 'es'),
                (65, 'cats', 'gatos', 'Recipient', 'es'),
                (66, 'dogs', 'perros', 'Recipient', 'es'),
                (67, 'pets', 'mascotas', 'Recipient', 'es'),
                (68, 'not_specified', 'not_specified', 'Recipient', 'es'),
                (69, 'men', 'uomini', 'Recipient', 'it'),
                (70, 'women', 'donne', 'Recipient', 'it'),
                (71, 'unisex_adults', 'adulti_unisex', 'Recipient', 'it'),
                (72, 'teen_boys', 'ragazzi_adolescenti', 'Recipient', 'it'),
                (73, 'teen_girls', 'ragazze_adolescenti', 'Recipient', 'it'),
                (74, 'teens', 'ragazzi', 'Recipient', 'it'),
                (75, 'boys', 'bambini_48', 'Recipient', 'it'),
                (76, 'girls', 'bambine_48', 'Recipient', 'it'),
                (77, 'children', 'bambini', 'Recipient', 'it'),
                (78, 'baby_boys', 'bimbo_03', 'Recipient', 'it'),
                (79, 'baby_girls', 'bimba_03', 'Recipient', 'it'),
                (80, 'babies', 'bimbi', 'Recipient', 'it'),
                (81, 'birds', 'uccelli', 'Recipient', 'it'),
                (82, 'cats', 'gatti', 'Recipient', 'it'),
                (83, 'dogs', 'cani', 'Recipient', 'it'),
                (84, 'pets', 'animali_domestici', 'Recipient', 'it'),
                (85, 'not_specified', 'non_specificato', 'Recipient', 'it'),
                (86, 'men', 'mannen', 'Recipient', 'nl'),
                (87, 'women', 'vrouwen', 'Recipient', 'nl'),
                (88, 'unisex_adults', 'unisex_volwassenen', 'Recipient', 'nl'),
                (89, 'teen_boys', 'tienerjongens', 'Recipient', 'nl'),
                (90, 'teen_girls', 'tienermeisjes', 'Recipient', 'nl'),
                (91, 'teens', 'tieners', 'Recipient', 'nl'),
                (92, 'boys', 'jongens', 'Recipient', 'nl'),
                (93, 'girls', 'meisjes', 'Recipient', 'nl'),
                (94, 'children', 'kinderen', 'Recipient', 'nl'),
                (95, 'baby_boys', 'babyjongentjes', 'Recipient', 'nl'),
                (96, 'baby_girls', 'babymeisjes', 'Recipient', 'nl'),
                (97, 'babies', 'babys', 'Recipient', 'nl'),
                (98, 'birds', 'vogels', 'Recipient', 'nl'),
                (99, 'cats', 'katten', 'Recipient', 'nl'),
                (100, 'dogs', 'honden', 'Recipient', 'nl'),
                (101, 'pets', 'huisdieren', 'Recipient', 'nl'),
                (102, 'not_specified', 'niet_aangegeven', 'Recipient', 'nl'),
                (103, 'men', 'mczyni', 'Recipient', 'pl'),
                (104, 'women', 'kobiety', 'Recipient', 'pl'),
                (105, 'unisex_adults', 'doroli_uniseks', 'Recipient', 'pl'),
                (106, 'teen_boys', 'nastolatki_chopcy', 'Recipient', 'pl'),
                (107, 'teen_girls', 'nastolatki_dziewczta', 'Recipient', 'pl'),
                (108, 'teens', 'nastolatki', 'Recipient', 'pl'),
                (109, 'boys', 'chopcy', 'Recipient', 'pl'),
                (110, 'girls', 'dziewczynki', 'Recipient', 'pl'),
                (111, 'children', 'dzieci', 'Recipient', 'pl'),
                (112, 'baby_boys', 'niemowlta_chopcy', 'Recipient', 'pl'),
                (113, 'baby_girls', 'niemowlta_dziewczynki', 'Recipient', 'pl'),
                (114, 'babies', 'niemowlta', 'Recipient', 'pl'),
                (115, 'birds', 'ptaki', 'Recipient', 'pl'),
                (116, 'cats', 'koty', 'Recipient', 'pl'),
                (117, 'dogs', 'psy', 'Recipient', 'pl'),
                (118, 'pets', 'zwierzta_domowe', 'Recipient', 'pl'),
                (119, 'not_specified', 'nie_okrelono', 'Recipient', 'pl'),
                (120, 'men', 'homens', 'Recipient', 'pt'),
                (121, 'women', 'mulheres', 'Recipient', 'pt'),
                (122, 'unisex_adults', 'adultos_unisexo', 'Recipient', 'pt'),
                (123, 'teen_boys', 'rapazes_adolescentes', 'Recipient', 'pt'),
                (124, 'teen_girls', 'raparigas_adolescentes', 'Recipient', 'pt'),
                (125, 'teens', 'adolescentes', 'Recipient', 'pt'),
                (126, 'boys', 'rapazes', 'Recipient', 'pt'),
                (127, 'girls', 'raparigas', 'Recipient', 'pt'),
                (128, 'children', 'crianas', 'Recipient', 'pt'),
                (129, 'baby_boys', 'bebmenino', 'Recipient', 'pt'),
                (130, 'baby_girls', 'bebs_do_sexo_feminino', 'Recipient', 'pt'),
                (131, 'babies', 'bebs', 'Recipient', 'pt'),
                (132, 'birds', 'pssaros', 'Recipient', 'pt'),
                (133, 'cats', 'gatos', 'Recipient', 'pt'),
                (134, 'dogs', 'ces', 'Recipient', 'pt'),
                (135, 'pets', 'animais_de_estimao', 'Recipient', 'pt'),
                (136, 'not_specified', 'not_specified', 'Recipient', 'pt'),
                (137, 'anniversary', 'anniversary', 'Occasion', 'en'),
                (138, 'baptism', 'baptism', 'Occasion', 'en'),
                (139, 'bar_or_bat_mitzvah', 'bar_or_bat_mitzvah', 'Occasion', 'en'),
                (140, 'birthday', 'birthday', 'Occasion', 'en'),
                (141, 'canada_day', 'canada_day', 'Occasion', 'en'),
                (142, 'chinese_new_year', 'chinese_new_year', 'Occasion', 'en'),
                (143, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'en'),
                (144, 'confirmation', 'confirmation', 'Occasion', 'en'),
                (145, 'christmas', 'christmas', 'Occasion', 'en'),
                (146, 'day_of_the_dead', 'day_of_the_dead', 'Occasion', 'en'),
                (147, 'easter', 'easter', 'Occasion', 'en'),
                (148, 'eid', 'eid', 'Occasion', 'en'),
                (149, 'engagement', 'engagement', 'Occasion', 'en'),
                (150, 'fathers_day', 'fathers_day', 'Occasion', 'en'),
                (151, 'get_well', 'get_well', 'Occasion', 'en'),
                (152, 'graduation', 'graduation', 'Occasion', 'en'),
                (153, 'halloween', 'halloween', 'Occasion', 'en'),
                (154, 'hanukkah', 'hanukkah', 'Occasion', 'en'),
                (155, 'housewarming', 'housewarming', 'Occasion', 'en'),
                (156, 'kwanzaa', 'kwanzaa', 'Occasion', 'en'),
                (157, 'prom', 'prom', 'Occasion', 'en'),
                (158, 'july_4th', 'july_4th', 'Occasion', 'en'),
                (159, 'mothers_day', 'mothers_day', 'Occasion', 'en'),
                (160, 'new_baby', 'new_baby', 'Occasion', 'en'),
                (161, 'new_years', 'new_years', 'Occasion', 'en'),
                (162, 'quinceanera', 'quinceanera', 'Occasion', 'en'),
                (163, 'retirement', 'retirement', 'Occasion', 'en'),
                (164, 'st_patricks_day', 'st_patricks_day', 'Occasion', 'en'),
                (165, 'sweet_16', 'sweet_16', 'Occasion', 'en'),
                (166, 'sympathy', 'sympathy', 'Occasion', 'en'),
                (167, 'thanksgiving', 'thanksgiving', 'Occasion', 'en'),
                (168, 'valentines', 'valentines', 'Occasion', 'en'),
                (169, 'wedding', 'wedding', 'Occasion', 'en'),
                (170, 'anniversary', 'anniversaire_de_mariage', 'Occasion', 'fr'),
                (171, 'baptism', 'baptme', 'Occasion', 'fr'),
                (172, 'bar_or_bat_mitzvah', 'bar_ou_bat_mitzvah', 'Occasion', 'fr'),
                (173, 'birthday', 'anniversaire', 'Occasion', 'fr'),
                (174, 'canada_day', 'fte_du_canada', 'Occasion', 'fr'),
                (175, 'chinese_new_year', 'nouvel_an_chinois', 'Occasion', 'fr'),
                (176, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'fr'),
                (177, 'confirmation', 'confirmation', 'Occasion', 'fr'),
                (178, 'christmas', 'nol', 'Occasion', 'fr'),
                (179, 'day_of_the_dead', 'fte_des_morts', 'Occasion', 'fr'),
                (180, 'easter', 'pques', 'Occasion', 'fr'),
                (181, 'eid', 'ad', 'Occasion', 'fr'),
                (182, 'engagement', 'fianaille', 'Occasion', 'fr'),
                (183, 'fathers_day', 'fte_des_pres', 'Occasion', 'fr'),
                (184, 'get_well', 'voeux_de_bon_rtablissement', 'Occasion', 'fr'),
                (185, 'graduation', 'remise_des_diplmes', 'Occasion', 'fr'),
                (186, 'halloween', 'halloween', 'Occasion', 'fr'),
                (187, 'hanukkah', 'hanoucca', 'Occasion', 'fr'),
                (188, 'housewarming', 'pendaison_de_crmaillre', 'Occasion', 'fr'),
                (189, 'kwanzaa', 'kwanzaa', 'Occasion', 'fr'),
                (190, 'prom', 'bal_de_promo', 'Occasion', 'fr'),
                (191, 'july_4th', 'jour_de_lindpendance_des_etatsunis', 'Occasion', 'fr'),
                (192, 'mothers_day', 'fte_des_mres', 'Occasion', 'fr'),
                (193, 'new_baby', 'nouveaun', 'Occasion', 'fr'),
                (194, 'new_years', 'nouvel_an', 'Occasion', 'fr'),
                (195, 'quinceanera', 'fte_des_15_ans', 'Occasion', 'fr'),
                (196, 'retirement', 'retraite', 'Occasion', 'fr'),
                (197, 'st_patricks_day', 'fte_de_la_saintpatrick', 'Occasion', 'fr'),
                (198, 'sweet_16', 'majorit', 'Occasion', 'fr'),
                (199, 'sympathy', 'amiti', 'Occasion', 'fr'),
                (200, 'thanksgiving', 'thanksgiving', 'Occasion', 'fr'),
                (201, 'valentines', 'saintvalentin', 'Occasion', 'fr'),
                (202, 'wedding', 'mariage', 'Occasion', 'fr'),
                (203, 'anniversary', 'jubilum', 'Occasion', 'de'),
                (204, 'baptism', 'taufe', 'Occasion', 'de'),
                (205, 'bar_or_bat_mitzvah', 'bar_oder_bat_mizwa', 'Occasion', 'de'),
                (206, 'birthday', 'geburtstag', 'Occasion', 'de'),
                (207, 'canada_day', 'canada_day', 'Occasion', 'de'),
                (208, 'chinese_new_year', 'chinesisches_neujahr', 'Occasion', 'de'),
                (209, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'de'),
                (210, 'confirmation', 'konfirmation', 'Occasion', 'de'),
                (211, 'christmas', 'weihnachten', 'Occasion', 'de'),
                (212, 'day_of_the_dead', 'day_of_the_dead', 'Occasion', 'de'),
                (213, 'easter', 'ostern', 'Occasion', 'de'),
                (214, 'eid', 'eid', 'Occasion', 'de'),
                (215, 'engagement', 'verlobung', 'Occasion', 'de'),
                (216, 'fathers_day', 'vatertag', 'Occasion', 'de'),
                (217, 'get_well', 'gute_besserung', 'Occasion', 'de'),
                (218, 'graduation', 'abschluss', 'Occasion', 'de'),
                (219, 'halloween', 'halloween', 'Occasion', 'de'),
                (220, 'hanukkah', 'chanukka', 'Occasion', 'de'),
                (221, 'housewarming', 'hauseinweihung', 'Occasion', 'de'),
                (222, 'kwanzaa', 'kwanzaa', 'Occasion', 'de'),
                (223, 'prom', 'prom', 'Occasion', 'de'),
                (224, 'july_4th', 'der_4_juli', 'Occasion', 'de'),
                (225, 'mothers_day', 'muttertag', 'Occasion', 'de'),
                (226, 'new_baby', 'neugeborenes', 'Occasion', 'de'),
                (227, 'new_years', 'neujahr', 'Occasion', 'de'),
                (228, 'quinceanera', 'quinceanera', 'Occasion', 'de'),
                (229, 'retirement', 'ruhestand', 'Occasion', 'de'),
                (230, 'st_patricks_day', 'st_patricks_day', 'Occasion', 'de'),
                (231, 'sweet_16', 'sweet_16', 'Occasion', 'de'),
                (232, 'sympathy', 'anteilnahme', 'Occasion', 'de'),
                (233, 'thanksgiving', 'thanksgiving', 'Occasion', 'de'),
                (234, 'valentines', 'valentinstag', 'Occasion', 'de'),
                (235, 'wedding', 'hochzeit', 'Occasion', 'de'),
                (236, 'anniversary', 'aniversario', 'Occasion', 'es'),
                (237, 'baptism', 'bautizo', 'Occasion', 'es'),
                (238, 'bar_or_bat_mitzvah', 'bar_o_bat_mitzvah', 'Occasion', 'es'),
                (239, 'birthday', 'cumpleaos', 'Occasion', 'es'),
                (240, 'canada_day', 'da_de_canad', 'Occasion', 'es'),
                (241, 'chinese_new_year', 'ao_nuevo_chino', 'Occasion', 'es'),
                (242, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'es'),
                (243, 'confirmation', 'confirmacin', 'Occasion', 'es'),
                (244, 'christmas', 'navidad', 'Occasion', 'es'),
                (245, 'day_of_the_dead', 'da_de_los_muertos', 'Occasion', 'es'),
                (246, 'easter', 'pascua', 'Occasion', 'es'),
                (247, 'eid', 'eid', 'Occasion', 'es'),
                (248, 'engagement', 'compromiso', 'Occasion', 'es'),
                (249, 'fathers_day', 'da_del_padre', 'Occasion', 'es'),
                (250, 'get_well', 'que_te_mejores', 'Occasion', 'es'),
                (251, 'graduation', 'graduacin', 'Occasion', 'es'),
                (252, 'halloween', 'halloween', 'Occasion', 'es'),
                (253, 'hanukkah', 'januc', 'Occasion', 'es'),
                (254, 'housewarming', 'inauguracin', 'Occasion', 'es'),
                (255, 'kwanzaa', 'kwanzaa', 'Occasion', 'es'),
                (256, 'prom', 'promocin', 'Occasion', 'es'),
                (257, 'july_4th', '4_de_julio', 'Occasion', 'es'),
                (258, 'mothers_day', 'da_de_la_madre', 'Occasion', 'es'),
                (259, 'new_baby', 'recin_nacido', 'Occasion', 'es'),
                (260, 'new_years', 'ao_nuevo', 'Occasion', 'es'),
                (261, 'quinceanera', 'quinceaera', 'Occasion', 'es'),
                (262, 'retirement', 'jubilacin', 'Occasion', 'es'),
                (263, 'st_patricks_day', 'da_de_san_patricio', 'Occasion', 'es'),
                (264, 'sweet_16', 'dulces_16', 'Occasion', 'es'),
                (265, 'sympathy', 'condolencias', 'Occasion', 'es'),
                (266, 'thanksgiving', 'accin_de_gracias', 'Occasion', 'es'),
                (267, 'valentines', 'san_valentn', 'Occasion', 'es'),
                (268, 'wedding', 'boda', 'Occasion', 'es'),
                (269, 'anniversary', 'anniversario', 'Occasion', 'it'),
                (270, 'baptism', 'battesimo', 'Occasion', 'it'),
                (271, 'bar_or_bat_mitzvah', 'bar_or_bat_mitzvah', 'Occasion', 'it'),
                (272, 'birthday', 'compleanno', 'Occasion', 'it'),
                (273, 'canada_day', 'canada_day', 'Occasion', 'it'),
                (274, 'chinese_new_year', 'nuovo_anno_cinese', 'Occasion', 'it'),
                (275, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'it'),
                (276, 'confirmation', 'cresima', 'Occasion', 'it'),
                (277, 'christmas', 'natale', 'Occasion', 'it'),
                (278, 'day_of_the_dead', 'giorno_dei_morti', 'Occasion', 'it'),
                (279, 'easter', 'pasqua', 'Occasion', 'it'),
                (280, 'eid', 'giuramento', 'Occasion', 'it'),
                (281, 'engagement', 'fidanzamento', 'Occasion', 'it'),
                (282, 'fathers_day', 'festa_del_pap', 'Occasion', 'it'),
                (283, 'get_well', 'guarigione', 'Occasion', 'it'),
                (284, 'graduation', 'laurea', 'Occasion', 'it'),
                (285, 'halloween', 'halloween', 'Occasion', 'it'),
                (286, 'hanukkah', 'hanukkah', 'Occasion', 'it'),
                (287, 'housewarming', 'inaugurazione', 'Occasion', 'it'),
                (288, 'kwanzaa', 'kwanzaa', 'Occasion', 'it'),
                (289, 'prom', 'ballo_studentesco', 'Occasion', 'it'),
                (290, 'july_4th', '4_luglio', 'Occasion', 'it'),
                (291, 'mothers_day', 'festa_della_mamma', 'Occasion', 'it'),
                (292, 'new_baby', 'nuovo_nato', 'Occasion', 'it'),
                (293, 'new_years', 'capodanno', 'Occasion', 'it'),
                (294, 'quinceanera', 'quinceanera', 'Occasion', 'it'),
                (295, 'retirement', 'pensione', 'Occasion', 'it'),
                (296, 'st_patricks_day', 'festa_di_san_patrizio', 'Occasion', 'it'),
                (297, 'sweet_16', 'sweet_16', 'Occasion', 'it'),
                (298, 'sympathy', 'condoglianze', 'Occasion', 'it'),
                (299, 'thanksgiving', 'giorno_del_ringraziamento', 'Occasion', 'it'),
                (300, 'valentines', 'san_valentino', 'Occasion', 'it'),
                (301, 'wedding', 'matrimonio', 'Occasion', 'it'),
                (302, 'anniversary', 'jubileum', 'Occasion', 'nl'),
                (303, 'baptism', 'doop', 'Occasion', 'nl'),
                (304, 'bar_or_bat_mitzvah', 'bar_mitzvah', 'Occasion', 'nl'),
                (305, 'birthday', 'verjaardag', 'Occasion', 'nl'),
                (306, 'canada_day', 'canadese_feestdag', 'Occasion', 'nl'),
                (307, 'chinese_new_year', 'chinees_nieuwjaar', 'Occasion', 'nl'),
                (308, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'nl'),
                (309, 'confirmation', 'vormsel', 'Occasion', 'nl'),
                (310, 'christmas', 'kerst', 'Occasion', 'nl'),
                (311, 'day_of_the_dead', 'dag_van_de_doden', 'Occasion', 'nl'),
                (312, 'easter', 'pasen', 'Occasion', 'nl'),
                (313, 'eid', 'suikerfeest', 'Occasion', 'nl'),
                (314, 'engagement', 'verloving', 'Occasion', 'nl'),
                (315, 'fathers_day', 'vaderdag', 'Occasion', 'nl'),
                (316, 'get_well', 'beterschap', 'Occasion', 'nl'),
                (317, 'graduation', 'geslaagd', 'Occasion', 'nl'),
                (318, 'halloween', 'halloween', 'Occasion', 'nl'),
                (319, 'hanukkah', 'chanoeka', 'Occasion', 'nl'),
                (320, 'housewarming', 'housewarming', 'Occasion', 'nl'),
                (321, 'kwanzaa', 'kwanzaa', 'Occasion', 'nl'),
                (322, 'prom', 'gala', 'Occasion', 'nl'),
                (323, 'july_4th', 'amerikaanse_independence_day', 'Occasion', 'nl'),
                (324, 'mothers_day', 'moederdag', 'Occasion', 'nl'),
                (325, 'new_baby', 'geboorte', 'Occasion', 'nl'),
                (326, 'new_years', 'nieuwjaar', 'Occasion', 'nl'),
                (327, 'quinceanera', 'quinceanera', 'Occasion', 'nl'),
                (328, 'retirement', 'pensioen', 'Occasion', 'nl'),
                (329, 'st_patricks_day', 'st_patricks_dag', 'Occasion', 'nl'),
                (330, 'sweet_16', 'sweet_16', 'Occasion', 'nl'),
                (331, 'sympathy', 'oprechte_deelneming', 'Occasion', 'nl'),
                (332, 'thanksgiving', 'thanksgiving', 'Occasion', 'nl'),
                (333, 'valentines', 'valentijnsdag', 'Occasion', 'nl'),
                (334, 'wedding', 'trouwdag', 'Occasion', 'nl'),
                (335, 'anniversary', 'rocznica', 'Occasion', 'pl'),
                (336, 'baptism', 'chrzciny', 'Occasion', 'pl'),
                (337, 'bar_or_bat_mitzvah', 'bar_lub_bat_micwa', 'Occasion', 'pl'),
                (338, 'birthday', 'urodziny', 'Occasion', 'pl'),
                (339, 'canada_day', 'wito_narodowe_kanady', 'Occasion', 'pl'),
                (340, 'chinese_new_year', 'chiski_nowy_rok', 'Occasion', 'pl'),
                (341, 'cinco_de_mayo', 'cinco_de_mayo', 'Occasion', 'pl'),
                (342, 'confirmation', 'konfirmacja_ibierzmowanie', 'Occasion', 'pl'),
                (343, 'christmas', 'boe_narodzenie', 'Occasion', 'pl'),
                (344, 'day_of_the_dead', 'wito_zmarych', 'Occasion', 'pl'),
                (345, 'easter', 'wielkanoc', 'Occasion', 'pl'),
                (346, 'eid', 'id', 'Occasion', 'pl'),
                (347, 'engagement', 'zarczyny', 'Occasion', 'pl'),
                (348, 'fathers_day', 'dzie_ojca', 'Occasion', 'pl'),
                (349, 'get_well', 'yczenia_zdrowia', 'Occasion', 'pl'),
                (350, 'graduation', 'ukoczenie_szkoy', 'Occasion', 'pl'),
                (351, 'halloween', 'halloween', 'Occasion', 'pl'),
                (352, 'hanukkah', 'chanuka', 'Occasion', 'pl'),
                (353, 'housewarming', 'parapetwka', 'Occasion', 'pl'),
                (354, 'kwanzaa', 'kwanzaa', 'Occasion', 'pl'),
                (355, 'prom', 'bal_szkolny', 'Occasion', 'pl'),
                (356, 'july_4th', '4_lipca', 'Occasion', 'pl'),
                (357, 'mothers_day', 'dzie_matki', 'Occasion', 'pl'),
                (358, 'new_baby', 'narodziny_dziecka', 'Occasion', 'pl'),
                (359, 'new_years', 'nowy_rok', 'Occasion', 'pl'),
                (360, 'quinceanera', 'quinceaera', 'Occasion', 'pl'),
                (361, 'retirement', 'przejcie_na_emerytur', 'Occasion', 'pl'),
                (362, 'st_patricks_day', 'dzie_w_patryka', 'Occasion', 'pl'),
                (363, 'sweet_16', '16_urodziny', 'Occasion', 'pl'),
                (364, 'sympathy', 'wyrazy_wspczucia', 'Occasion', 'pl'),
                (365, 'thanksgiving', 'wito_dzikczynienia', 'Occasion', 'pl'),
                (366, 'valentines', 'walentynki', 'Occasion', 'pl'),
                (367, 'wedding', 'lub_i_wesel', 'Occasion', 'pl'),
                (368, 'anniversary', 'aniversrio', 'Occasion', 'pt'),
                (369, 'baptism', 'batizado', 'Occasion', 'pt'),
                (370, 'bar_or_bat_mitzvah', 'bar_ou_bat_mitzvah', 'Occasion', 'pt'),
                (371, 'birthday', '', 'Occasion', 'pt'),
                (372, 'canada_day', 'dia_do_canad', 'Occasion', 'pt'),
                (373, 'chinese_new_year', 'ano_novo_chins', 'Occasion', 'pt'),
                (374, 'cinco_de_mayo', 'cinco_de_maio', 'Occasion', 'pt'),
                (375, 'confirmation', 'confirmao', 'Occasion', 'pt'),
                (376, 'christmas', 'natal', 'Occasion', 'pt'),
                (377, 'day_of_the_dead', 'dia_dos_mortos', 'Occasion', 'pt'),
                (378, 'easter', 'pscoa', 'Occasion', 'pt'),
                (379, 'eid', 'eid', 'Occasion', 'pt'),
                (380, 'engagement', 'noivado', 'Occasion', 'pt'),
                (381, 'fathers_day', 'dia_do_pai', 'Occasion', 'pt'),
                (382, 'get_well', 'as_melhoras', 'Occasion', 'pt'),
                (383, 'graduation', 'formatura', 'Occasion', 'pt'),
                (384, 'halloween', 'dia_das_bruxas', 'Occasion', 'pt'),
                (385, 'hanukkah', 'hanukkah', 'Occasion', 'pt'),
                (386, 'housewarming', 'prendas_de_inaugurao', 'Occasion', 'pt'),
                (387, 'kwanzaa', 'kwanzaa', 'Occasion', 'pt'),
                (388, 'prom', 'baile_de_finalistas', 'Occasion', 'pt'),
                (389, 'july_4th', '4_de_julho', 'Occasion', 'pt'),
                (390, 'mothers_day', 'dia_da_me', 'Occasion', 'pt'),
                (391, 'new_baby', 'novo_beb', 'Occasion', 'pt'),
                (392, 'new_years', 'ano_novo', 'Occasion', 'pt'),
                (393, 'quinceanera', 'quinceanera', 'Occasion', 'pt'),
                (394, 'retirement', 'reforma', 'Occasion', 'pt'),
                (395, 'st_patricks_day', 'dia_de_so_patrcio', 'Occasion', 'pt'),
                (396, 'sweet_16', 'ocasio_festa_de_15_anos', 'Occasion', 'pt'),
                (397, 'sympathy', 'simpatia', 'Occasion', 'pt'),
                (398, 'thanksgiving', 'aco_de_graas', 'Occasion', 'pt'),
                (399, 'valentines', 'dia_dos_namorados', 'Occasion', 'pt'),
                (400, 'wedding', 'casamento', 'Occasion', 'pt')");
        }

        $price_type = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="price_type"';
        $price_type_result = $this->db->query($price_type);
        if ($price_type_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `price_type` INT(10) DEFAULT 0');
        }

        $price_management = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="price_management"';
        $price_management_result = $this->db->query($price_management);
        if ($price_management_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `price_management` INT(2) NOT NULL');
        }

        $increase_decrease = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="increase_decrease"';
        $increase_decrease_result = $this->db->query($increase_decrease);
        if ($increase_decrease_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `increase_decrease` enum("0","1") NOT NULL');
        }

        $product_price = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="product_price"';
        $product_price_result = $this->db->query($product_price);
        if ($product_price_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `product_price` FLOAT(11) NOT NULL');
        }

        $percentage_fixed = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="percentage_fixed"';
        $percentage_fixed_result = $this->db->query($percentage_fixed);
        if ($percentage_fixed_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `percentage_fixed` enum("0","1") NOT NULL');
        }

        $auto_renew = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="auto_renew"';
        $auto_renew_result = $this->db->query($auto_renew);
        if ($auto_renew_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `auto_renew` enum("0","1") NOT NULL');
        }
        
        $shop_section = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_profiles" AND column_name="shop_section_id"';
        $shop_section_result = $this->db->query($shop_section);
        if ($shop_section_result->num_rows <= 0) {
            $this->db->query('ALTER TABLE ' . DB_PREFIX . 'etsy_profiles ADD COLUMN `shop_section_id` VARCHAR(50) NULL');
        }

        $error_flag_column  = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_products_list" AND column_name="error_flag"';
        $error_flag_column_result = $this->db->query($error_flag_column);
        if ($error_flag_column_result->num_rows <= 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "etsy_products_list` ADD `error_flag` SMALLINT(1) NOT NULL DEFAULT '0' AFTER `listing_error`, ADD `is_disabled` SMALLINT(1) NOT NULL DEFAULT '0' AFTER `error_flag`");
        }
        
        $sold_flag_column  = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="' . DB_DATABASE . '" AND TABLE_NAME="' . DB_PREFIX . 'etsy_products_list" AND column_name="sold_flag"';
        $sold_flag_column_result = $this->db->query($sold_flag_column);
        if ($sold_flag_column_result->num_rows <= 0) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "etsy_products_list` ADD `sold_flag` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `is_disabled`, ADD `expiry_date` DATETIME NULL DEFAULT NULL AFTER `sold_flag`");
        }
        
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "etsy_products_list` CHANGE `listing_status` `listing_status` ENUM('Pending','Listed','Inactive','Expired','Draft','Deletion Pending','Updated','Sold Out','Relisting') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Pending'");
        $this->db->query("ALTER TABLE `" . DB_PREFIX . "etsy_profiles` CHANGE `when_made` `when_made` VARCHAR(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'made_to_order'");
        
        $this->load->model('setting/setting');
        if(empty($this->model_setting_setting->getSetting('jgetsy_secure_key'))) {
            $secure_key['jgetsy_secure_key'] = $this->generateRandomString();
            $this->model_setting_setting->editSetting('jgetsy_secure_key', $secure_key);
        }
    }
    
    private function generateRandomString($length = 20)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function insertCategories($sql)
    {
        //$this->db->query($sql);
    }

    public function uninstall()
    {
        //$this->db->query('TRUNCATE TABLE `" . DB_PREFIX . "etsy_categories`');
    }

    public function getProductOptions($product_id)
    {

        $query = $this->db->query("SELECT ov.product_option_code as barcode, ovn.name as size_name,ov.quantity as option_quantity FROM " . DB_PREFIX . "product_option_value AS ov
                                            INNER JOIN " . DB_PREFIX . "option_value_description as ovn on (ov.option_value_id = ovn.option_value_id) and ov.product_id = " . $product_id . "");
        return $query->rows;
    }

    public function get_etsy_product_category($productID)
    {
        $EtsyProduct = $this->db->query("SELECT p.product_id, p.sku, cd.name, cd.itemType, cd.productType FROM " . DB_PREFIX . "product_to_category as p2c 
                                                INNER JOIN " . DB_PREFIX . "product as p on (p2c.product_id = p.product_id)
                                                INNER JOIN " . DB_PREFIX . "category as c on (p2c.category_id = c.category_id)
                                                INNER JOIN " . DB_PREFIX . "category_description as cd on (c.category_id = cd.category_id and cd.itemType !='' ) 
                                                AND p.product_id = '" . $productID . "'  ");
        $etsy_products_category = $EtsyProduct->rows;
        return $etsy_products_category;
    }

    public function getProductSpecials($product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int) $product_id . "' ORDER BY priority, price");
        return $query->rows;
    }

    public function getShippingTemplateById($id_etsy_shipping_profiles)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = " . $id_etsy_shipping_profiles);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function shippingProfileUpdate($data)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_countries WHERE country_id = " . (int) $data['etsy']['template']['origin_country']);
        if ($query->num_rows > 0) {
            $country_name = $query->row['country_name'];
        } else {
            $country_name = "";
        }
        if (isset($data['id_etsy_shipping_profiles']) && $data['id_etsy_shipping_profiles'] != "") {
            $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_profiles SET shipping_profile_title = '" . $data['etsy']['template']['template_title'] . "', shipping_origin_country_id = '" . $data['etsy']['template']['origin_country'] . "',shipping_origin_country = '" . $country_name . "', shipping_primary_cost = " . $data['etsy']['template']['primary_cost'] . ", shipping_secondary_cost = " . $data['etsy']['template']['secondary_cost'] . ", shipping_min_process_days = " . $data['etsy']['template']['min_process_days'] . ", shipping_max_process_days = " . $data['etsy']['template']['max_process_days'] . ", renew_flag = '1' WHERE id_etsy_shipping_profiles = " . $data['id_etsy_shipping_profiles']);
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shipping_profiles SET shipping_profile_title = '" . $data['etsy']['template']['template_title'] . "', shipping_origin_country_id = '" . $data['etsy']['template']['origin_country'] . "',shipping_origin_country = '" . $country_name . "', shipping_primary_cost = " . $data['etsy']['template']['primary_cost'] . ", shipping_secondary_cost = " . $data['etsy']['template']['secondary_cost'] . ", shipping_min_process_days = " . $data['etsy']['template']['min_process_days'] . ", shipping_max_process_days = " . $data['etsy']['template']['max_process_days']);
        }
    }

    public function shippingProfileEntryUpdate($data)
    {
        $destination_region_name = '';
        $destination_country_name = '';
        $shipping_entry_destination_country_id = '';
        $shipping_entry_destination_region_id = '';

        if ($data['etsy']['template']['shipping_entry_destination_region_id'] != '') {
            $destination_region_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_regions WHERE region_id = " . (int) $data['etsy']['template']['shipping_entry_destination_region_id']);
            $destination_region_name = $destination_region_query->rows[0]['region_name'];
            $shipping_entry_destination_region_id = $data['etsy']['template']['shipping_entry_destination_region_id'];
        } else if ($data['etsy']['template']['shipping_entry_destination_country_id'] != '') {
            $destination_country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_countries WHERE country_id = " . (int) $data['etsy']['template']['shipping_entry_destination_country_id']);
            $destination_country_name = $destination_country_query->rows[0]['country_name'];
            $shipping_entry_destination_country_id = $data['etsy']['template']['shipping_entry_destination_country_id'];
        }
        if (isset($data['id_etsy_shipping_profiles_entries']) && $data['id_etsy_shipping_profiles_entries'] != "" && $data['id_etsy_shipping_profiles_entries'] != 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_profiles_entries SET shipping_entry_destination_region_id = '" . (int) $shipping_entry_destination_region_id . "', shipping_entry_destination_region = '" . $this->db->escape($destination_region_name) . "', shipping_entry_primary_cost = '" . (float) $data['etsy']['template']['primary_cost'] . "', shipping_entry_secondary_cost = '" . (float) $data['etsy']['template']['secondary_cost'] . "', shipping_entry_destination_country_id = '" . $shipping_entry_destination_country_id . "', shipping_entry_destination_country = '" . $this->db->escape($destination_country_name) . "', renew_flag = '1' WHERE id_etsy_shipping_profiles_entries = '" . (int) $data['id_etsy_shipping_profiles_entries'] . "'");
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shipping_profiles_entries VALUES (NULL, '" . (int) $data['id_etsy_shipping_profiles'] . "', NULL, '" . (int) $shipping_entry_destination_country_id . "', '" . $this->db->escape($destination_country_name) . "', '" . (float) $data['etsy']['template']['primary_cost'] . "', '" . (float) $data['etsy']['template']['secondary_cost'] . "', " . (int) $shipping_entry_destination_region_id . ", '" . $this->db->escape($destination_region_name) . "', '0', '0', NOW(), NOW())");
        }
    }

    public function deleteShippingTemplateEntries($id_etsy_shipping_profiles_entries = 0)
    {
        $checkSQL = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles_entries = '" . (int) $id_etsy_shipping_profiles_entries . "'");
        if ($checkSQL->num_rows > 0) {
            if ($checkSQL->row['shipping_profile_entry_id'] == NULL) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles_entries = '" . (int) $id_etsy_shipping_profiles_entries . "' LIMIT 1");
            } else {
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_profiles_entries set delete_flag = '1' WHERE id_etsy_shipping_profiles_entries = '" . (int) $id_etsy_shipping_profiles_entries . "'");
            }
        }
    }

    public function checkProfileMapping($id_etsy_shipping_profiles = 0)
    {
        $profileMappingExistence = $this->db->query("SELECT count(*) as count FROM " . DB_PREFIX . "etsy_profiles WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'");
        if ($profileMappingExistence->row['count'] == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteShippingTemplate($id_etsy_shipping_profiles = 0)
    {
        $checkShippingTemplateSQL = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'");
        if ($checkShippingTemplateSQL->num_rows > 0) {
            if ($checkShippingTemplateSQL->row['shipping_profile_id'] == NULL) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "' LIMIT 1");
                $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'");
            } else {
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_profiles set delete_flag = '1' WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'");
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_profiles_entries set delete_flag = '1' WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'");
            }
        }
        return true;
    }

    public function getAttributeDetails($profile_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_attribute_mapping WHERE id_etsy_profiles = " . $profile_id);
        return $query->rows;
    }

    public function getProductImages($product_id)
    {
        $query = $this->db->query("SELECT pi.*,p.erotic FROM " . DB_PREFIX . "product_image pi left join " . DB_PREFIX . "product p on (p.product_id = pi.product_id) WHERE pi.product_id = '" . (int) $product_id . "' ORDER BY sort_order ASC");
        return $query->rows;
    }

    public function getProduct($product_id)
    {
        $result = $this->db->query("SELECT p.product_id, p.sku,p.manufacturer_id, pd.name as product_name, p.color,p.amazon_search_terms, pov.product_option_code,od.name as option_name,od.option_id,ovd.option_value_id,ovd.name as size_name, pov.quantity, 
                            m.name as manufacturer,m.size_chart, p.cost, p.price, p.weight, p.bin, p.image, pd.description FROM " . DB_PREFIX . "product as p
                            INNER JOIN " . DB_PREFIX . "product_description AS pd ON ( p.product_id = pd.product_id )
                            INNER JOIN " . DB_PREFIX . "product_option_value as pov on (p.product_id = pov.product_id)
                            INNER JOIN " . DB_PREFIX . "option_description as od on (pov.option_id = od.option_id)    
                            INNER JOIN " . DB_PREFIX . "option_value_description as ovd on (pov.option_value_id = ovd.option_value_id)
                            LEFT JOIN  " . DB_PREFIX . "manufacturer as m on (p.manufacturer_id = m.manufacturer_id) where p.product_id = '" . $product_id . "'");

        return $result->row;
    }

    public function getProductSpecial($product_id)
    {
        $result = $this->db->query("SELECT min(price) as special,date_start,date_end FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = '" . $product_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < '" . date('Y-m-d H:i:s') . "') AND (ps.date_end = '0000-00-00' OR ps.date_end > '" . date('Y-m-d H:i:s') . "'))");
        return $result->row;
    }

    public function getEtsyProducts($data = array())
    {
        $sql = "SELECT p.*, pd.* FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)  AND p.erotic = 0";
        $sql .= " RIGHT JOIN " . DB_PREFIX . "etsy_feed_products pf ON (p.product_id = pf.product_id) and pf.etsy_submission_status in('New','Added','Submited')";
        if (!empty($data['filter_category'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price >= '" . (double) $data['filter_price'] . "'";
        }

        if (!empty($data['filter_price_to'])) {
            $sql .= " AND p.price <= '" . (double) $data['filter_price_to'] . "'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity >= '" . $this->db->escape($data['filter_quantity']) . "'";
        }

        if (isset($data['filter_quantity_to']) && !is_null($data['filter_quantity_to'])) {
            $sql .= " AND p.quantity <= '" . $this->db->escape($data['filter_quantity_to']) . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        if (isset($data['filter_sku']) && !is_null($data['filter_sku'])) {
            $sql .= " AND p.sku != ''";
        }

        if (isset($data['filter_desc']) && !is_null($data['filter_desc'])) {
            $sql .= " AND pd.description != ''";
        }

        if (isset($data['custom_bit']) && $data['custom_bit']) {
            $sql .= " AND p.custom_bit = '1'";
        }

        if (isset($data['filter_manufacturer']) && !is_null($data['filter_manufacturer'])) {
            $sql .= " AND p.manufacturer_id = '" . (int) $data['filter_manufacturer'] . "'";
        }

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.price',
            'p.quantity',
            'p.status',
            'p.sort_order'
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalEtsyProducts($data = array())
    {

        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) AND p.erotic = 0";
        $sql .= " RIGHT JOIN " . DB_PREFIX . "etsy_feed_products pf ON (p.product_id = pf.product_id) and pf.etsy_submission_status in('New','Added','Submited')";
        if (!empty($data['filter_category'])) {
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
        }

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price >= '" . (double) $data['filter_price'] . "'";
        }

        if (!empty($data['filter_price_to'])) {
            $sql .= " AND p.price <= '" . (double) $data['filter_price_to'] . "'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity >= '" . $this->db->escape($data['filter_quantity']) . "'";
        }

        if (isset($data['filter_quantity_to']) && !is_null($data['filter_quantity_to'])) {
            $sql .= " AND p.quantity <= '" . $this->db->escape($data['filter_quantity_to']) . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        if (isset($data['filter_sku']) && !is_null($data['filter_sku'])) {
            $sql .= " AND p.sku != ''";
        }

        if (isset($data['filter_desc']) && !is_null($data['filter_desc'])) {
            $sql .= " AND pd.description != ''";
        }

        if (isset($data['custom_bit']) && $data['custom_bit']) {
            $sql .= " AND p.custom_bit = '1'";
        }

        if (isset($data['filter_manufacturer']) && !is_null($data['filter_manufacturer'])) {
            $sql .= " AND p.manufacturer_id = '" . (int) $data['filter_manufacturer'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function editSetting($code, $data, $store_id = 0)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int) $store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

        foreach ($data as $key => $value) {
            if (substr($key, 0, strlen($code)) == $code) {
                if (!is_array($value)) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int) $store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int) $store_id . "', `code` = '" . $this->db->escape($code) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value)) . "', serialized = '1'");
                }
            }
        }
    }

    public function getSetting($code, $store_id = 0)
    {
        $setting_data = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int) $store_id . "' AND `code` = '" . $this->db->escape($code) . "'");

        foreach ($query->rows as $result) {
            if (!$result['serialized']) {
                $setting_data[$result['key']] = $result['value'];
            } else {
                $setting_data[$result['key']] = json_decode($result['value'], true);
            }
        }

        return $setting_data;
    }

    public function getProfileTotal($data = array())
    {
        $sql = "SELECT COUNT(DISTINCT p.id_etsy_profiles) AS total FROM " . DB_PREFIX . "etsy_profiles p LEFT JOIN " . DB_PREFIX . "etsy_shipping_profiles st ON (st.id_etsy_shipping_profiles = p.id_etsy_shipping_profiles) LEFT JOIN " . DB_PREFIX . "etsy_category_mapping ecm ON (ecm.id_etsy_profiles = p.id_etsy_profiles) LEFT JOIN " . DB_PREFIX . "etsy_categories ec ON (ec.category_code = ecm.etsy_category_code) where p.id_etsy_profiles > 0";

        if (isset($data['filter_profile_name']) && !is_null($data['filter_profile_name'])) {
            $sql .= " AND p.profile_title LIKE '" . $this->db->escape($data['filter_profile_name']) . "%'";
        }

        if (isset($data['filter_shipping_name']) && !is_null($data['filter_shipping_name'])) {
            $sql .= " AND st.shipping_profile_title LIKE '" . $this->db->escape($data['filter_shipping_name']) . "%'";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int) $data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getProfileDetails($data, $id_etsy_profiles = '')
    {
        if (!empty($id_etsy_profiles)) {
            $sql = "SELECT p.*,st.shipping_profile_title,ec.category_name FROM " . DB_PREFIX . "etsy_profiles p LEFT JOIN " . DB_PREFIX . "etsy_shipping_profiles st ON (st.id_etsy_shipping_profiles = p.id_etsy_shipping_profiles) LEFT JOIN " . DB_PREFIX . "etsy_category_mapping ecm ON (ecm.id_etsy_profiles = p.id_etsy_profiles) LEFT JOIN " . DB_PREFIX . "etsy_categories ec ON (ec.category_code = ecm.etsy_category_code) where p.id_etsy_profiles = " . $id_etsy_profiles;
        } else {
            $sql = "SELECT p.*,st.shipping_profile_title,ec.category_name FROM " . DB_PREFIX . "etsy_profiles p LEFT JOIN " . DB_PREFIX . "etsy_shipping_profiles st ON (st.id_etsy_shipping_profiles = p.id_etsy_shipping_profiles) LEFT JOIN " . DB_PREFIX . "etsy_category_mapping ecm ON (ecm.id_etsy_profiles = p.id_etsy_profiles) LEFT JOIN " . DB_PREFIX . "etsy_categories ec ON (ec.category_code = ecm.etsy_category_code) where p.id_etsy_profiles > 0";
        }

        if (isset($data['filter_profile_name']) && !is_null($data['filter_profile_name'])) {
            $sql .= " AND p.profile_title LIKE '" . $this->db->escape($data['filter_profile_name']) . "%'";
        }

        if (isset($data['filter_shipping_name']) && !is_null($data['filter_shipping_name'])) {
            $sql .= " AND st.shipping_profile_title LIKE '" . $this->db->escape($data['filter_shipping_name']) . "%'";
        }

        if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
            $sql .= " AND p.quantity = '" . (int) $data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }
        $sort_data = array(
            'p.id_etsy_profiles',
            'p.profile_title',
            'p.etsy_category_code',
            'shipping_origin_country',
            'st.shipping_profile_title',
            'p.active',
            'p.date_added',
            'p.date_updated',
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.id_etsy_profiles";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getShippingTemplateTotal($data, $id_etsy_shipping_profiles = '')
    {
        if (!empty($id_etsy_shipping_profiles)) {
            $sql = "SELECT count(id_etsy_shipping_profiles) as total FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'";
        } else {
            $sql = "SELECT count(id_etsy_shipping_profiles) as total FROM " . DB_PREFIX . "etsy_shipping_profiles where id_etsy_shipping_profiles > 0 and delete_flag = '0'";
        }
        if (isset($data['filter_shipping_name']) && !is_null($data['filter_shipping_name'])) {
            $sql .= " AND shipping_profile_title LIKE '" . $this->db->escape($data['filter_shipping_name']) . "%'";
        }
        if (isset($data['filter_shipping_country']) && !is_null($data['filter_shipping_country'])) {
            $sql .= " AND shipping_origin_country LIKE '" . $this->db->escape($data['filter_shipping_country']) . "%'";
        }
        if (isset($data['filter_min_proc_days']) && !is_null($data['filter_min_proc_days'])) {
            $sql .= " AND shipping_min_process_days = " . $this->db->escape($data['filter_min_proc_days']);
        }
        if (isset($data['filter_max_proc_days']) && !is_null($data['filter_max_proc_days'])) {
            $sql .= " AND shipping_max_process_days = " . $this->db->escape($data['filter_max_proc_days']);
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function checkTemplateExists($title)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE shipping_profile_title = '" . $title . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return true;
        }
        return false;
    }

    public function getShippingTemplateDetails($data, $id_etsy_shipping_profiles = '', $fields_list = '*')
    {
        if (!empty($id_etsy_shipping_profiles)) {
            $sql = "SELECT " . $fields_list . " FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "'";
        } else {
            $sql = "SELECT " . $fields_list . " FROM " . DB_PREFIX . "etsy_shipping_profiles where id_etsy_shipping_profiles > 0 and delete_flag = '0'";
        }
        if (isset($data['filter_shipping_name']) && !is_null($data['filter_shipping_name'])) {
            $sql .= " AND shipping_profile_title LIKE '" . $this->db->escape($data['filter_shipping_name']) . "%'";
        }
        if (isset($data['filter_shipping_country']) && !is_null($data['filter_shipping_country'])) {
            $sql .= " AND shipping_origin_country LIKE '" . $this->db->escape($data['filter_shipping_country']) . "%'";
        }
        if (isset($data['filter_min_proc_days']) && !is_null($data['filter_min_proc_days'])) {
            $sql .= " AND shipping_min_process_days = " . $this->db->escape($data['filter_min_proc_days']);
        }
        if (isset($data['filter_max_proc_days']) && !is_null($data['filter_max_proc_days'])) {
            $sql .= " AND shipping_max_process_days = " . $this->db->escape($data['filter_max_proc_days']);
        }
        $sort_data = array(
            'id_etsy_shipping_profiles',
            'shipping_profile_title',
            'shipping_origin_country',
            'shipping_min_process_days',
            'shipping_max_process_days',
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY id_etsy_shipping_profiles";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getAuditLogTotal($data, $id_etsy_audit_log = '', $fields_list = '*')
    {
        if (!empty($id_etsy_audit_log)) {
            $sql = "SELECT count(id_etsy_audit_log) as total FROM " . DB_PREFIX . "etsy_audit_log WHERE id_etsy_audit_log = '" . (int) $id_etsy_audit_log . "'";
        } else {
            $sql = "SELECT count(id_etsy_audit_log) as total FROM " . DB_PREFIX . "etsy_audit_log where id_etsy_audit_log > 0";
        }
        if (isset($data['filter_id_etsy_audit_log']) && !is_null($data['filter_id_etsy_audit_log'])) {
            $sql .= " AND filter_id_etsy_audit_log LIKE '" . $this->db->escape($data['filter_id_etsy_audit_log']) . "%'";
        }
        if (isset($data['filter_log_entry']) && !is_null($data['filter_log_entry'])) {
            $sql .= " AND log_entry LIKE '" . $this->db->escape($data['filter_log_entry']) . "%'";
        }
        if (isset($data['filter_log_class_method']) && !is_null($data['filter_log_class_method'])) {
            $sql .= " AND log_class_method = '" . $this->db->escape($data['filter_log_class_method']) . "'";
        }
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getAuditLog($data, $id_etsy_audit_log = '', $fields_list = '*')
    {
        if (!empty($id_etsy_audit_log)) {
            $sql = "SELECT " . $fields_list . " FROM " . DB_PREFIX . "etsy_audit_log WHERE id_etsy_audit_log = '" . (int) $id_etsy_audit_log . "'";
        } else {
            $sql = "SELECT " . $fields_list . " FROM " . DB_PREFIX . "etsy_audit_log where id_etsy_audit_log > 0";
        }
        if (isset($data['filter_id_etsy_audit_log']) && !is_null($data['filter_id_etsy_audit_log'])) {
            $sql .= " AND filter_id_etsy_audit_log LIKE '" . $this->db->escape($data['filter_id_etsy_audit_log']) . "%'";
        }
        if (isset($data['filter_log_entry']) && !is_null($data['filter_log_entry'])) {
            $sql .= " AND log_entry LIKE '" . $this->db->escape($data['filter_log_entry']) . "%'";
        }
        if (isset($data['filter_log_class_method']) && !is_null($data['filter_log_class_method'])) {
            $sql .= " AND log_class_method = '" . $this->db->escape($data['filter_log_class_method']) . "'";
        }
        $sort_data = array(
            'id_etsy_shipping_profiles',
            'shipping_profile_title',
            'shipping_origin_country',
            'shipping_min_process_days',
            'shipping_max_process_days',
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY id_etsy_audit_log";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getEtsyCountries()
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_countries";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getEtsyCountriesCount()
    {
        $sql = "SELECT count(*) as total FROM " . DB_PREFIX . "etsy_countries";
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getEtsyRegions()
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_regions";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getEtsyCategoryByCode($category_code)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_categories WHERE category_code='" . $category_code . "'";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getShipTemplates()
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles where delete_flag ='0'";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getAttributes($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "option_description WHERE language_id = '" . (int) $this->config->get('config_language_id') . "'";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function UpdateEtsyProducts($data)
    {
        if ($data['etsy_action'] == 'delete') {
            $etsy_status = 'Delete';
        } else {
            $etsy_status = 'Update';
        }
        foreach ($data as $products_data_array) {
            foreach ($products_data_array as $product_key => $productvariation) {
                $total_product_id = $product_key;
                $fecth_products_data = $this->db->query("SELECT p.product_id, p.sku, pd.name as product_name, p.color, pov.product_option_code,od.name as option_name,od.option_id,ovd.option_value_id,ovd.name as size_name, pov.quantity, 
                                                m.name as manufacturer, p.cost, p.price, p.weight, p.bin, p.image, pd.description FROM " . DB_PREFIX . "product as p
                                                INNER JOIN " . DB_PREFIX . "product_description AS pd ON ( p.product_id = pd.product_id )
                                                INNER JOIN " . DB_PREFIX . "product_option_value as pov on (p.product_id = pov.product_id)
                                                INNER JOIN " . DB_PREFIX . "option_description as od on (pov.option_id = od.option_id)    
                                                INNER JOIN " . DB_PREFIX . "option_value_description as ovd on (pov.option_value_id = ovd.option_value_id)
                                                LEFT JOIN  " . DB_PREFIX . "manufacturer as m on (p.manufacturer_id = m.manufacturer_id) where p.product_id = '" . $product_key . "'");
                $etsy_product_category = $this->get_etsy_product_category($product_key);
                $product_specials = $this->getProductSpecials($product_key);
                $special_price = '0.0000';
                $special_start_data = '0000-00-00 00:00:00';
                $special_end_data = '0000-00-00 00:00:00';
                foreach ($product_specials as $product_special) {
                    if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] <= date('Y-m-d h:m:s')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d h:m:s'))) {
                        $special_price = $product_special['price'];
                        $special_start_data = $product_special['date_start'];
                        $special_end_data = $product_special['date_end'];
                        break;
                    }
                }
                if ($fecth_products_data->rows) {
                    $etsy_product_category[0]['productType'] = strtolower($etsy_product_category[0]['productType']);
                    $etsy_product_category[0]['productType'] = ucwords($etsy_product_category[0]['productType']);
                    $etsy_product_category[0]['itemType'] = str_replace('_', ' ', $etsy_product_category[0]['itemType']);
                    $etsy_product_category[0]['itemType'] = ucwords($etsy_product_category[0]['itemType']);
                    $swatch_image = $fecth_products_data->row['sku'];
                    $swatch_image = preg_replace('/\s+/', '-', $swatch_image);
                    $swatch_image = str_replace('/', '-', $swatch_image);
                    $swatch_image = 'data/' . $swatch_image . '-swatch.jpg';
                    $product_id = $fecth_products_data->row['product_id'];
                    $product_name = addslashes($fecth_products_data->row['product_name']);
                    $product_sku = $fecth_products_data->row['sku'];
                    $product_color = $fecth_products_data->row['color'];
                    $product_manufacturers_name = $fecth_products_data->row['manufacturer'];
                    $product_price = $fecth_products_data->row['price'];
                    $product_image_path = $fecth_products_data->row['image'];
                    $product_description = addslashes($fecth_products_data->row['description']);
                    $product_type = $$etsy_product_category[0]['productType'];
                    $item_type = $etsy_product_category[0]['itemType'];
                    $product_special_price = $special_price;
                    $product_special_start_date = $special_start_data;
                    $product_special_end_date = $special_end_data;
                    $checkEtsyProduct = $this->db->query("SELECT feed_product_id,product_id FROM " . DB_PREFIX . "etsy_feed_products where product_id = '" . (int) $product_id . "'");
                    if ($checkEtsyProduct->rows) {
                        $this->db->query("Update  " . DB_PREFIX . "etsy_feed_products  SET  `product_sku` = '" . $product_sku . "', `product_name` = '" . $product_name . "', `product_description` = '" . $product_description . "',`product_type` = '" . $product_type . "',`item_type` = '" . $item_type . "',`manufacturer_name` = '" . $product_manufacturers_name . "', `product_image_link` = '" . $product_image_path . "', `product_base_price` = '" . $product_price . "', `product_special_price` = '" . $product_special_price . "', `special_start_date` = '" . $product_special_start_date . "', `special_end_date` = '" . $product_special_end_date . "',etsy_submission_status = '" . $etsy_status . "',swatchImageUrl = '" . $swatch_image . "' Where product_id = '" . (int) $product_id . "'");
                        foreach ($fecth_products_data->rows as $products_data) {
                            if ($productvariation[$products_data['product_option_code']]) {
                                $attributeVal = $products_data['size_name'];
                                if (eregi('XXXL', $attributeVal)) {
                                    $attributeValue = 'XXXL';
                                } elseif (eregi('Small/Medium', $attributeVal)) {
                                    $attributeValue = 'S/M';
                                } elseif (eregi('Large/Xlarge', $attributeVal)) {
                                    $attributeValue = 'L/XL';
                                } else if (eregi('XXL', $attributeVal)) {
                                    $attributeValue = 'XXL';
                                } else if (eregi('XL', $attributeVal)) {
                                    $attributeValue = 'XL';
                                } else if (eregi('Large', $attributeVal)) {
                                    $attributeValue = 'L';
                                } else if (eregi('Medium', $attributeVal)) {
                                    $attributeValue = 'M';
                                } else if (eregi('Small', $attributeVal)) {
                                    $attributeValue = 'S';
                                } elseif (eregi('One', $attributeVal)) {
                                    $attributeValue = 'OS';
                                } else {
                                    $attributeValue = $attributeVal;
                                }
                                $product_variation_sku = $products_data['sku'] . "-" . $attributeValue;

                                $this->db->query("Update " . DB_PREFIX . "etsy_feed_products_variations  SET  `product_sku` = '" . $product_variation_sku . "', `barcode` = '" . $products_data['product_option_code'] . "',`product_quantity` = " . (int) $products_data['quantity'] . ", `product_option_id` = '" . $products_data['option_id'] . "', `product_option_name` = '" . $products_data['size_name'] . "', `product_option_value_id` = '" . $products_data['option_value_id'] . "', `product_option_value_name` = '" . $products_data['option_name'] . "' ,product_color = '" . $product_color . "', `date_modified` = now(),etsy_submission_status = '" . $etsy_status . "' Where product_id = '" . (int) $products_data['product_id'] . "' and `barcode` = '" . $products_data['product_option_code'] . "'");
                            } else {
                                
                            }
                        }
                    }
                }
            }
        }
    }

    public function getOrders($data = array())
    {
        $sql = "SELECT eol.*, o.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, (SELECT os.name FROM " . DB_PREFIX . "order_status os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int) $this->config->get('config_language_id') . "') AS status, o.shipping_code, o.total, o.currency_code, o.currency_value, o.date_added, o.date_modified FROM " . DB_PREFIX . "etsy_orders_list eol LEFT JOIN " . DB_PREFIX . "order o on eol.id_order = o.order_id ";

        if (isset($data['filter_order_status'])) {
            $implode = array();

            $order_statuses = explode(',', $data['filter_order_status']);

            foreach ($order_statuses as $order_status_id) {
                $implode[] = "o.order_status_id = '" . (int) $order_status_id . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            } else {
                
            }
        } else {
            $sql .= " WHERE o.order_status_id > '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND o.order_id = '" . (int) $data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if (!empty($data['filter_total'])) {
            $sql .= " AND o.total = '" . (float) $data['filter_total'] . "'";
        }

        $sort_data = array(
            'o.order_id',
            'customer',
            'status',
            'o.date_added',
            'o.date_modified',
            'o.total',
            'eol.id_etsy_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY o.date_added";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalOrders($data = array())
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "etsy_orders_list eol LEFT JOIN " . DB_PREFIX . "order o on eol.id_order = o.order_id ";

        if (!empty($data['filter_order_status'])) {
            $implode = array();

            $order_statuses = explode(',', $data['filter_order_status']);

            foreach ($order_statuses as $order_status_id) {
                $implode[] = "order_status_id = '" . (int) $order_status_id . "'";
            }

            if ($implode) {
                $sql .= " WHERE (" . implode(" OR ", $implode) . ")";
            }
        } else {
            $sql .= " WHERE order_status_id > '0'";
        }

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND order_id = '" . (int) $data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_customer'])) {
            $sql .= " AND CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_customer']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if (!empty($data['filter_total'])) {
            $sql .= " AND total = '" . (float) $data['filter_total'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getProducts($data = array())
    {
        $sql = "SELECT p.*, epl.*, pd.name, profile_title FROM " . DB_PREFIX . "etsy_products_list epl "
                . "LEFT JOIN " . DB_PREFIX . "etsy_profiles ep ON (epl.id_etsy_profiles = ep.id_etsy_profiles) "
                . "LEFT JOIN " . DB_PREFIX . "product_description pd ON (epl.id_product = pd.product_id) "
                . "LEFT JOIN " . DB_PREFIX . "product p on p.product_id = pd.product_id "
                . "WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "' ";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_listed_on']) && $data['filter_listed_on'] !== null) {
            $sql .= " AND date(epl.date_listed) = '" . $data['filter_listed_on'] . "'";
        }

        if (isset($data['filter_listing_id']) && $data['filter_listing_id'] !== null) {
            $sql .= " AND epl.listing_id = '" . $data['filter_listing_id'] . "'";
        }

        if (isset($data['filter_listing_status']) && $data['filter_listing_status'] !== null) {
            if($data['filter_listing_status'] == 'Disabled') {
                $sql .= " AND epl.is_disabled = 1 ";
            } else {
                $sql .= " AND epl.is_disabled = 0 AND epl.listing_status = '" . $data['filter_listing_status'] . "'";
            }
        }

        if (isset($data['filter_id_etsy_profiles']) && $data['filter_id_etsy_profiles'] !== null) {
            $sql .= " AND ep.id_etsy_profiles = '" . $data['filter_id_etsy_profiles'] . "'";
        }
        //$sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'id_etsy_products_list',
            'pd.name',
            'p.product_id',
            'p.model',
            'p.quantity',
            'epl.listing_status',
            'epl.listing_id',
            'epl.renew_flag',
            'epl.update_flag',
            'epl.date_listed',
            'ep.id_etsy_profiles'
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalProducts($data = array())
    {
        $sql = "SELECT COUNT(p.product_id) AS total FROM " . DB_PREFIX . "etsy_products_list epl "
                . "LEFT JOIN " . DB_PREFIX . "etsy_profiles ep ON (epl.id_etsy_profiles = ep.id_etsy_profiles) "
                . "LEFT JOIN " . DB_PREFIX . "product_description pd ON (epl.id_product = pd.product_id) "
                . "LEFT JOIN " . DB_PREFIX . "product p on p.product_id = pd.product_id ";

        $sql .= " WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
        }

        if (!empty($data['filter_price'])) {
            $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
        }

        if (isset($data['filter_quantity']) && $data['filter_quantity'] !== null) {
            $sql .= " AND p.quantity = '" . (int) $data['filter_quantity'] . "'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== null) {
            $sql .= " AND p.status = '" . (int) $data['filter_status'] . "'";
        }

        if (isset($data['filter_listed_on']) && $data['filter_listed_on'] !== null) {
            $sql .= " AND date(epl.date_listed) = '" . $data['filter_listed_on'] . "'";
        }

        if (isset($data['filter_listing_id']) && $data['filter_listing_id'] !== null) {
            $sql .= " AND epl.listing_id = '" . $data['filter_listing_id'] . "'";
        }

        if (isset($data['filter_listing_status']) && $data['filter_listing_status'] !== null) {
            if($data['filter_listing_status'] == 'Disabled') {
                $sql .= " AND epl.is_disabled = 1 ";
            } else {
                $sql .= " AND epl.is_disabled = 0 AND epl.listing_status = '" . $data['filter_listing_status'] . "'";
            }
        }

        if (isset($data['filter_id_etsy_profiles']) && $data['filter_id_etsy_profiles'] !== null) {
            $sql .= " AND ep.id_etsy_profiles = '" . $data['filter_id_etsy_profiles'] . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTotalShippingTemplateEntries($data)
    {
        $sql = "SELECT count(*) as count FROM " . DB_PREFIX . "etsy_shipping_profiles_entries a "
                . " LEFT JOIN `" . DB_PREFIX . "etsy_shipping_profiles` st ON (a.id_etsy_shipping_profiles = st.id_etsy_shipping_profiles) "
                . "WHERE a.delete_flag = '0'";
        if ($data['id_etsy_shipping_profiles']) {
            $sql .= " AND a.id_etsy_shipping_profiles='" . $data['id_etsy_shipping_profiles'] . "'";
        }
        if (isset($data['filter_shipping_name']) && !is_null($data['filter_shipping_name'])) {
            $sql .= " AND shipping_profile_title LIKE '" . $this->db->escape($data['filter_shipping_name']) . "%'";
        }
        if (isset($data['filter_shipping_country']) && !is_null($data['filter_shipping_country'])) {
            $sql .= " AND shipping_origin_country LIKE '" . $this->db->escape($data['filter_shipping_country']) . "%'";
        }
        if (isset($data['filter_destination_country']) && !is_null($data['filter_destination_country'])) {
            $sql .= " AND shipping_entry_destination_country = '" . $this->db->escape($data['filter_destination_country']) . "'";
        }
        if (isset($data['filter_destination_region']) && !is_null($data['filter_destination_region'])) {
            $sql .= " AND shipping_entry_destination_region = '" . $this->db->escape($data['filter_destination_region']) . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['count'];
    }

    public function getShippingTemplateEntries($data)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries a "
                . "LEFT JOIN `" . DB_PREFIX . "etsy_shipping_profiles` st "
                . "ON (a.id_etsy_shipping_profiles = st.id_etsy_shipping_profiles) "
                . "WHERE a.delete_flag = '0'";

        if (!empty($data['id_etsy_shipping_profiles'])) {
            $sql .= " AND a.id_etsy_shipping_profiles='" . $data['id_etsy_shipping_profiles'] . "'";
        }

        if (!empty($data['id_etsy_shipping_profiles_entries'])) {
            $sql .= " AND a.id_etsy_shipping_profiles_entries='" . $data['id_etsy_shipping_profiles_entries'] . "'";
        }

        if (isset($data['filter_shipping_name']) && !is_null($data['filter_shipping_name'])) {
            $sql .= " AND shipping_profile_title LIKE '" . $this->db->escape($data['filter_shipping_name']) . "%'";
        }
        if (isset($data['filter_shipping_country']) && !is_null($data['filter_shipping_country'])) {
            $sql .= " AND shipping_origin_country LIKE '" . $this->db->escape($data['filter_shipping_country']) . "%'";
        }
        if (isset($data['filter_destination_country']) && !is_null($data['filter_destination_country'])) {
            $sql .= " AND shipping_entry_destination_country = '" . $this->db->escape($data['filter_destination_country']) . "'";
        }
        if (isset($data['filter_destination_region']) && !is_null($data['filter_destination_region'])) {
            $sql .= " AND shipping_entry_destination_region = '" . $this->db->escape($data['filter_destination_region']) . "'";
        }
        $sort_data = array(
            'a.id_etsy_shipping_profiles',
            'shipping_profile_title',
            'shipping_origin_country',
            'shipping_entry_destination_region',
            'shipping_entry_destination_country',
        );
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY a.id_etsy_shipping_profiles_entries";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function checkDestinationCountryExists($shipping_entry_destination_country_id, $id_etsy_shipping_profiles, $id = '')
    {
        if (isset($id) && $id != '') {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "' AND id_etsy_shipping_profiles_entries != " . (int) $id . " AND shipping_entry_destination_country_id = '" . $shipping_entry_destination_country_id . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = " . (int) $id_etsy_shipping_profiles . " AND shipping_entry_destination_country_id = '" . $shipping_entry_destination_country_id . "'";
        }
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    public function checkDestinationRegionExists($shipping_entry_destination_region_id, $id_etsy_shipping_profiles, $id = '')
    {
        if (isset($id) && $id != '') {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = '" . (int) $id_etsy_shipping_profiles . "' AND id_etsy_shipping_profiles_entries != " . (int) $id . " AND shipping_entry_destination_region_id = '" . $shipping_entry_destination_region_id . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = " . (int) $id_etsy_shipping_profiles . " AND shipping_entry_destination_region_id = '" . $shipping_entry_destination_region_id . "'";
        }
        $query = $this->db->query($sql);
        return $query->num_rows;
    }

    public function getMappedOption($etsy_product_option_id)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_product_option WHERE etsy_product_option_id = '" . (int) $etsy_product_option_id . "'";
        $query = $this->db->query($sql);
        return $query->row;
    }

    public function getMappedOptionDetails($product_id, $option_value_id = 0)
    {
        $product_option_value_data = array();


        $sql = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int) $product_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'";
        if ($option_value_id != '') {
            $sql .= " AND pov.option_value_id  ='" . $option_value_id . "' ";
        }
        $sql .= " ORDER BY ov.sort_order";

        $product_option_value_query = $this->db->query($sql);

        foreach ($product_option_value_query->rows as $product_option_value) {
            $product_option_value_data = array(
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'option_value_id' => $product_option_value['option_value_id'],
                'product_option_id' => $product_option_value['product_option_id'],
                'option_id' => $product_option_value['option_id'],
                'name' => $product_option_value['name'],
                'image' => $product_option_value['image'],
                'quantity' => $product_option_value['quantity'],
                'subtract' => $product_option_value['subtract'],
                'price' => $product_option_value['price'],
                'price_prefix' => $product_option_value['price_prefix'],
                'weight' => $product_option_value['weight'],
                'weight_prefix' => $product_option_value['weight_prefix']
            );
        }

        if (count($product_option_value_data) > 0) {
            return $product_option_value_data;
        } else {
            return false;
        }
    }

    public function deleteSetting($code, $store_id = 0)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int) $store_id . "' AND `code` = '" . $this->db->escape($code) . "'");
    }

}

?>