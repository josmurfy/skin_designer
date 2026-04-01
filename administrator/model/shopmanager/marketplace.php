<?php
namespace Opencart\Admin\Model\Shopmanager;

use Guzzle\Plugin\Backoff\TruncatedBackoffStrategy;

/**
 * Model class for managing marketplace accounts and product listings on marketplaces.
 */	

class Marketplace extends \Opencart\System\Engine\Model {


	/**
	 * [getMarketplaceAccount to get Marketplace Account list or particular account details]
	 * @param  array  $data [filter data array]
	 * @return [type]       [list of marketplace accounts]
	 */
	public function getMarketplaceAccount($data = array(), $type = false) {

		if (!empty($data['customer_id'])) {
			$customer_id=	(int)$data['customer_id'];
		}else{
			$customer_id=	(int)$this->customer->getId();
		}

		$sql = "SELECT *,m.image as marketplace_image,m.name as marketplace_name  FROM `" . DB_PREFIX . "marketplace_accounts` ca LEFT JOIN `oc_marketplace` m ON (m.marketplace_id=ca.marketplace_id) WHERE ca.status=1 AND ca.customer_id = '" . $customer_id . "'";

		if (!empty($data['filter_marketplace_account_id'])) {
			$sql .= " AND ca.marketplace_account_id  = " . (int)$data['filter_marketplace_account_id'] . "";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND ca.store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}else if (!empty($data['filter_store_name']) && $type) {
			$sql .= " AND ca.store_name = '" . $this->db->escape($data['filter_store_name']) . "'";
		}

		if (!empty($data['filter_user_id'])) {
			$sql .= " AND ca.user_id LIKE '%" . $this->db->escape($data['filter_user_id']) . "%'";
		}

		if (!empty($data['filter_marketplace_id'])) {
			$sql .= " AND ca.marketplace_id LIKE '%" . $this->db->escape($data['filter_marketplace_id']) . "%'";
		}

		if (!empty($data['filter_language_id'])) {
			$sql .= " AND ca.language_id = '" . $this->db->escape($data['filter_language_id']) . "'";
		}

		$sort_data = array(
			'marketplace_account_id ',
			'store_name',
			'user_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY ca." . $data['sort'];
		} else {
			$sql .= " ORDER BY ca.marketplace_account_id ";
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
		//print("<pre>".print_r ($sql,true )."</pre>");
		$query = $this->db->query($sql);

		$rows=[];
		if ($query->num_rows>0) {
			
			if($query->num_rows==1){
				$row=$query->row;
				$row['site_setting']=json_decode($row['site_setting'],true);
				$row['site_id'] = $row['site_setting']['site_id'];
				//print("<pre>".print_r ($row,true )."</pre>");
				return  $row;

			}else{
				foreach($query->rows as $row){
					$row['site_setting']=json_decode($row['site_setting'],true);
					$row['site_id'] = $row['site_setting']['site_id'];
					$rows[$row['marketplace_account_id']]= $row;
					
				}
			}
		}
		
		return $rows;
	}

	/**
	 * [getTotalMarketplaceAccount to get the total number of marketplace account]
	 * @param  array  $data [filter data array]
	 * @return [type]       [total number of marketplace account records]
	 */
	public function getTotalMarketplaceAccount($data = array()) { 
		$sql = "SELECT COUNT(DISTINCT marketplace_account_id ) AS total FROM " . DB_PREFIX . "marketplace_accounts WHERE `customer_id` = " . (int)$this->customer->getId() . "";

		if (!empty($data['filter_marketplace_account_id'])) {
			$sql .= " AND `marketplace_account_id ` = " . (int)$data['filter_marketplace_account_id'] . "";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND `store_name` LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		if (!empty($data['filter_user_id'])) {
			$sql .= " AND `user_id` LIKE '%" . $this->db->escape($data['filter_user_id']) . "%'";
		}
		if (!empty($data['filter_marketplace_id'])) {
			$sql .= " AND marketplace_id LIKE '%" . $this->db->escape($data['filter_marketplace_id']) . "%'";
		}
		$query = $this->db->query($sql);

		return $query->row['total']; 
	}

	/**
	 * [addMarketplaceAccount to add/update the marketplace account details]
	 * @param  array  $data [details of marketplace account]
	 * @return [type]       [description]
	 */
	public function addMarketplaceAccount($data = array()) {

		if (isset($data['marketplace_account_id']) && $data['marketplace_account_id']!="") {

			$this->db->query("UPDATE `" . DB_PREFIX . "marketplace_accounts` SET `marketplace_id` = 1,`store_name` = '" . $this->db->escape($data['store_name']) . "',  `user_id` = '" . $this->db->escape($data['user_id']) . "', `connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', `connector_application_id` = '" . $this->db->escape($data['connector_application_id']) . "', `connector_developer_id` = '" . $this->db->escape($data['connector_developer_id']) . "', `connector_certification_id` = '" . $this->db->escape($data['connector_certification_id']) . "', `connector_currency` = '" . $this->db->escape($data['connector_currency']) . "', `connector_shop_postal_code` = '" . $this->db->escape($data['connector_shop_postal_code']) . "',date_modified=now() WHERE `marketplace_account_id ` = '" . (int)$data['marketplace_account_id'] . "' AND `customer_id` = " . (int)$this->customer->getId() . "");
		//	echo "oui";
		//echo "UPDATE `" . DB_PREFIX . "marketplace_accounts` SET `store_name` = '" . $this->db->escape($data['store_name']) . "',  `user_id` = '" . $this->db->escape($data['user_id']) . "', `connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', `connector_application_id` = '" . $this->db->escape($data['connector_application_id']) . "', `connector_developer_id` = '" . $this->db->escape($data['connector_developer_id']) . "', `connector_certification_id` = '" . $this->db->escape($data['connector_certification_id']) . "', `connector_currency` = '" . $this->db->escape($data['connector_currency']) . "', `connector_shop_postal_code` = '" . $this->db->escape($data['connector_shop_postal_code']) . "',date_modified=now() WHERE `marketplace_account_id ` = '" . (int)$data['marketplace_account_id'] . "' AND `customer_id` = " . (int)$this->customer->getId() . "";
			/* $query = $this->db->query("SELECT `marketplace_account_id ` FROM `" . DB_PREFIX . "wk_marketplace_shipping_details` WHERE `marketplace_account_id` = " .(int)$data['marketplace_account_id'] . "");

			if ($query->num_rows) {
				$this->db->query("UPDATE `" . DB_PREFIX . "wk_marketplace_shipping_details` SET `marketplace_account_id` = " . (int)$data['marketplace_account_id'] . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . ", `customer_id` = " . (int)$this->customer->getId() . " WHERE `marketplace_account_id ` = " . (int)$query->row['marketplace_account_id '] . "");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "wk_marketplace_shipping_details` SET `marketplace_account_id` = " . (int)$data['marketplace_account_id'] . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . ",  `customer_id` = " . (int)$this->customer->getId() . "");
			} */
			return "modif";
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "marketplace_accounts` SET `marketplace_id` = 1,`customer_id` = " . (int)$this->customer->getId() . ", `store_name` = '" . $this->db->escape($data['store_name']) . "',  `user_id` = '" . $this->db->escape($data['user_id']) . "', `connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', `connector_application_id` = '" . $this->db->escape($data['connector_application_id']) . "', `connector_developer_id` = '" . $this->db->escape($data['connector_developer_id']) . "', `connector_certification_id` = '" . $this->db->escape($data['connector_certification_id']) . "', `connector_currency` = '" . $this->db->escape($data['connector_currency']) . "', `connector_shop_postal_code` = '" . $this->db->escape($data['connector_shop_postal_code']) . "' ");
			//echo "INSERT INTO `" . DB_PREFIX . "marketplace_accounts` SET `customer_id` = " . (int)$this->customer->getId() . ", `store_name` = '" . $this->db->escape($data['store_name']) . "',  `user_id` = '" . $this->db->escape($data['user_id']) . "', `connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', `connector_application_id` = '" . $this->db->escape($data['connector_application_id']) . "', `connector_developer_id` = '" . $this->db->escape($data['connector_developer_id']) . "', `connector_certification_id` = '" . $this->db->escape($data['connector_certification_id']) . "', `connector_currency` = '" . $this->db->escape($data['connector_currency']) . "', `connector_shop_postal_code` = '" . $this->db->escape($data['connector_shop_postal_code']) . "' ";
			$marketplace_account_id = $this->db->getLastId();

			//$this->db->query("INSERT INTO `" . DB_PREFIX . "wk_marketplace_shipping_details` SET `marketplace_account_id` = " . (int)$marketplace_account_id . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . ", `customer_id` = " . (int)$this->customer->getId() . "");
			return "add";
		}
	}

	public function editMarketplaceAccount($data = array()) {

		if (isset($data['marketplace_account_id']) && $data['marketplace_account_id']!="") {
//`customer_id` = '" . $this->db->escape($data['customer_id']) . "', 
			$this->db->query("UPDATE `" . DB_PREFIX . "marketplace_accounts` SET 
							`marketplace_id` = '" . $this->db->escape($data['marketplace_id']) . "',  
							
							`store_name` = '" . $this->db->escape($data['store_name']) . "', 
							`user_id` = '" . $this->db->escape($data['user_id']) . "',
							`postcode` = '" . $this->db->escape($data['postcode']) . "',  
							`city` = '" . $this->db->escape($data['city']) . "', 
							`auth_token` = '" . $this->db->escape($data['auth_token']) . "', 
							`application_id` = '" . $this->db->escape($data['application_id']) . "',
							`developer_id` = '" . $this->db->escape($data['developer_id']) . "', 
							`certification_id` = '" . $this->db->escape($data['certification_id']) . "', 
							`client_id` = '" . $this->db->escape($data['client_id']) . "', 
							`client_secret` = '" . $this->db->escape($data['client_secret']) . "', 
							`refresh_token` = '" . $this->db->escape($data['refresh_token']) . "', 
							`site_setting` = '" . $this->db->escape(json_encode($data['site_setting'])) . "', 
							`status` = '" . $this->db->escape($data['status']) . "', 
							`sync_json` = NULL, 
							date_modified=now() 
							WHERE `marketplace_account_id` = '" . (int)$data['marketplace_account_id'] . "' AND `customer_id` = " . $data['customer_id'] . "");

			return "modif";
		} else {
			/*$this->db->query("INSERT INTO `" . DB_PREFIX . "marketplace_accounts` SET `marketplace_id` = 1,`customer_id` = " . (int)$this->customer->getId() . ", `store_name` = '" . $this->db->escape($data['store_name']) . "',  `user_id` = '" . $this->db->escape($data['user_id']) . "', `connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', `connector_application_id` = '" . $this->db->escape($data['connector_application_id']) . "', `connector_developer_id` = '" . $this->db->escape($data['connector_developer_id']) . "', `connector_certification_id` = '" . $this->db->escape($data['connector_certification_id']) . "', `connector_currency` = '" . $this->db->escape($data['connector_currency']) . "', `connector_shop_postal_code` = '" . $this->db->escape($data['connector_shop_postal_code']) . "' ");
			//echo "INSERT INTO `" . DB_PREFIX . "marketplace_accounts` SET `customer_id` = " . (int)$this->customer->getId() . ", `store_name` = '" . $this->db->escape($data['store_name']) . "',  `user_id` = '" . $this->db->escape($data['user_id']) . "', `connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', `connector_application_id` = '" . $this->db->escape($data['connector_application_id']) . "', `connector_developer_id` = '" . $this->db->escape($data['connector_developer_id']) . "', `connector_certification_id` = '" . $this->db->escape($data['connector_certification_id']) . "', `connector_currency` = '" . $this->db->escape($data['connector_currency']) . "', `connector_shop_postal_code` = '" . $this->db->escape($data['connector_shop_postal_code']) . "' ";
			$marketplace_account_id = $this->db->getLastId();

			//$this->db->query("INSERT INTO `" . DB_PREFIX . "wk_marketplace_shipping_details` SET `marketplace_account_id` = " . (int)$marketplace_account_id . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . ", `customer_id` = " . (int)$this->customer->getId() . "");
			return "add";*/
			return false;
		}
	}

/* 	public function getShippingDetails($marketplace_account_id) {
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_marketplace_shipping_details` WHERE `marketplace_account_id` = " . (int)$marketplace_account_id . " AND `customer_id` = " . (int)$this->customer->getId() . "")->row;
	} */

	/**
	 * [deleteAccount to delete the marketplace account]
	 * @param  boolean $marketplace_account_id [marketplace account marketplace_account_id ]
	 * @return [type]              [description]
	 */
	public function deleteAccount($marketplace_account_id = false) {
		if ($marketplace_account_id) {

			$this->db->query("DELETE FROM ".DB_PREFIX."marketplace_accounts WHERE marketplace_account_id  = '".(int)$marketplace_account_id."' "); 
		}
	}
	public function getProducts($data = array()) {
		// Vérifier si customer_id est défini (obligatoire)
		if (empty($data['customer_id'])) {
			$data['customer_id']=10;
		}
	
		$sql = "SELECT *
				FROM " . DB_PREFIX . "product_marketplace pm
				WHERE status=1 AND pm.customer_id = '" . (int)$data['customer_id'] . "'";
	
		// Ajouter les filtres dynamiques
		$filters = array();
	
		if (empty($data['filter_marketplace_item_id'])) {
			$filters[] = "(pm.marketplace_item_id IS NOT NULL AND pm.marketplace_item_id > 0)";
			if (!empty($data['filter_specifics'])) {
				$filters[] = "(pm.specifics IS NULL OR pm.specifics = '')";
			}
		
			if (empty($data['filter_to_update'])) {
				$filters[] = "(pm.to_update = 9)";
			}

			if (empty($data['filter_marketplace_account_id'])) {
				$filters[] = "(pm.marketplace_account_id = 2)";
			}
			if (!empty($data['filter_price'])) {
				$filters[] = "(pm.price IS NULL OR pm.price = 0)";
			}
		
			if (!empty($data['filter_quantity_listed'])) {
				$filters[] = "(pm.quantity_listed = 0)";
			} 
	
			if (!empty($data['filter_quantity_sold'])) {
				$filters[] = "pm.quantity_sold = 0";
			}
		
			if (!empty($data['filter_date_modified'])) {
				$filters[] = "pm.date_modified < DATE_SUB(NOW(), INTERVAL 30 DAY)";
			}
		}else{
			$filters[] = "(pm.marketplace_item_id = '".$data['filter_marketplace_item_id']."')";
		}

		
	
		if (!empty($filters)) {
			$sql .= " AND (" . implode(" AND ", $filters) . ")"; 
		}
	
		// Group by product_id et customer_id
	//	$sql .= " GROUP BY pm.product_id";
	
		// Ordre des résultats
		$sort_data = array('pm.product_id');
	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY product_id";
			//$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pm.date_modified ";
		}
	
		if (isset($data['order']) && ($data['order'] == 'DESC' || $data['order'] == 'ASC')) {
			$sql .= " " . $data['order'];
		} else {
			$sql .= " DESC";
		}
	
		// Pagination (limit)
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
	
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$data['limit'] = 2;
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$sql .= " LIMIT 3000";
		//print("<pre>".print_r ($sql ,true )."</pre>");
		//die();
	//	//print("<pre>".print_r ($data ,true )."</pre>");
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
public function getMarketplace($data = array()) {
	//print("<pre>".print_r ($data ,true )."</pre>");
	$sql = "SELECT 
				
				ma.marketplace_account_id,
				ma.customer_id,
				ma.postcode,
				ma.city,
				pm.id,
				pm.product_id,
				pm.marketplace_item_id,
				pm.quantity_listed,
				pm.quantity_sold,
				pm.date_added,
				pm.date_modified,
				pm.date_ended,
				pm.category_id,
				pm.specifics,
				pm.price,
				pm.currency,
				pm.to_update,
				pm.status,
				pm.error,
				pm.quantity_listed,
				pm.quantity_sold,
				m.marketplace_id,
				m.name AS marketplace_name,
				m.url_product,
				m.currency_code,
				m.country_code,
				m.image,
				m.url_connexion,
				m.sort_order,
				ma.site_setting
			FROM " . DB_PREFIX . "marketplace_accounts ma
			LEFT JOIN " . DB_PREFIX . "marketplace m ON ma.marketplace_id = m.marketplace_id
			LEFT JOIN " . DB_PREFIX . "product_marketplace pm 
				ON ( pm.marketplace_id = ma.marketplace_id AND pm.product_id = '" . (isset($data['product_id']) ? (int)$data['product_id'] : 0) . "' )";	//AND pm.product_id = '" . (isset($data['product_id']) ? (int)$data['product_id'] : 0) . "'
			$sql .= "	WHERE 1 ";	

	/*if (!empty($data['product_id'])) {
		$sql .= " AND pm.product_id = '" . (int)$data['product_id'] . "'";
	}*/
    // Ajout dynamique des filtres en fonction des valeurs présentes dans $data
    if (!empty($data['marketplace_account_id'])) {
        $sql .= " AND ma.marketplace_account_id = '" . (int)$data['marketplace_account_id'] . "'";
    }
    if (!empty($data['marketplace_item_id'])) {
        $sql .= " AND pm.marketplace_item_id = '" . $this->db->escape($data['marketplace_item_id']) . "'";
    }
    if (!empty($data['customer_id'])) {
        $sql .= " AND ma.customer_id = '" . (int)$data['customer_id'] . "'";
    } else {
        $sql .= " AND ma.customer_id = '10'";
    }
	if (!empty($data['status'])) {
        $sql .= " AND ma.status = '" . (int)$data['status'] . "'";
    } else {
        $sql .= " AND ma.status = '1'";
    }

	/*if (!empty($data['limit'])) {
        $sql .= " LIMIT  " . (int)$data['limit'] ;
    } else {
        $sql .= " LIMIT  25" ;
    }*/

    $query = $this->db->query($sql);

    $result = [];
//print("<pre>" . print_r(value: '1481:product') . "</pre>");
//print("<pre>".print_r ($sql,true )."</pre>");
	//print("<pre>" . print_r(value: '1481:product') . "</pre>");
	//print("<pre>".print_r ($query,true )."</pre>");
    if ($query->num_rows > 0) {
        foreach ($query->rows as $row) {
            // Utiliser COALESCE pour éviter les NULL
            $result[$row['marketplace_account_id']] = [
                'product_id' => $data['product_id'],
                'customer_id' => $row['customer_id'],
                'marketplace_account_id' => $row['marketplace_account_id'],
                'marketplace_id' => $row['marketplace_id'],
                'marketplace_item_id' => $row['marketplace_item_id'] ?? '',
                'quantity_listed' => $row['quantity_listed'] ?? 0,
                'quantity_sold' => $row['quantity_sold'] ?? 0,
                'date_added' => $row['date_added'] ?? '',
                'date_modified' => $row['date_modified'] ?? '',
				'date_ended' => $row['date_ended'] ?? '',
				'category_id' => $row['category_id'] ?? 0,
				'specifics' => $row['specifics'] ?? '',
				'price' => $row['price'] ?? 0,
				'currency' => $row['currency'] ?? '',
				'to_update' => $row['to_update'] ?? 0,
				'status' => $row['status'] ?? 1,
				'error' => json_decode($row['error']?? '[]', true) ,
                'name' => $row['marketplace_name'],
                'currency_code' => $row['currency_code'] ?? '',
                'country_code' => $row['country_code'] ?? '',
                'image' => $row['image'] ?? 'catalog/marketplace/default.png',
                'url_connexion' => $row['url_connexion'] ?? '',
                'url_product' => $row['url_product'] ?? '',
                'sort_order' => $row['sort_order'] ?? 0,
                'postcode' => $row['postcode'] ?? '',
                'city' => $row['city'] ?? '',
                'marketplace_name' => $row['marketplace_name'],
				'site_setting'=>json_decode($row['site_setting'],true)
            ];
        }
    }
//print("<pre>" . print_r(value: '1512:product') . "</pre>");
//print("<pre>".print_r ($result,true )."</pre>");
    return $result;
}



public function addProductMarketplace($data) {

	
    // Construction de la requête SQL
    $query = "
        INSERT INTO " . DB_PREFIX . "product_marketplace 
        SET 
			`product_id` = '" . (int)$data['product_id'] . "',
			`customer_id` = '" . (isset($data['customer_id']) ? (int)$data['customer_id'] : 10) . "',
			`marketplace_id` = '" . (int)$data['marketplace_id'] . "',
			`marketplace_item_id` = '" . ($data['marketplace_item_id'] ?? '') . "',
			`marketplace_account_id` = '" . ($data['marketplace_account_id']) . "',
			`category_id` = '" . (isset($data['category_id']) ? (int)$data['category_id'] : 'DEFAULT') . "',
			`currency` = '" . $this->db->escape($data['currency'] ?? 'USD') . "',
			`price` = '" . (isset($data['price']) ? (float)$data['price'] : 0.00) . "',
			`specifics` = '" . $this->db->escape($data['specifics'] ?? '') . "',
			`error` = '',
			`status` = 1,
			`to_update` = '" . (isset($data['to_update']) ? (int)$data['to_update'] : 1) . "',
			`quantity_listed` = '" . (isset($data['quantity_listed']) ? (int)$data['quantity_listed'] : 0) . "',
			`quantity_sold` = '" . (isset($data['quantity_sold']) ? (int)$data['quantity_sold'] : 0) . "',
			`ebay_image_count` = '" . (isset($data['ebay_image_count']) ? (int)$data['ebay_image_count'] : 0) . "',
			`date_added` = NOW(),
			`date_modified` = NOW()
        ON DUPLICATE KEY UPDATE 
            `marketplace_item_id` = VALUES(`marketplace_item_id`),
            `quantity_listed` = VALUES(`quantity_listed`),
            `quantity_sold` = VALUES(`quantity_sold`),
            `date_modified` = NOW()
    ";

    // Exécuter la requête et capturer une éventuelle erreur
    $result = @$this->db->query($query); // Utilisation de @ pour éviter les warnings

    // Vérifier si la requête a échoué en regardant la dernière erreur SQL
    $error_check = $this->db->query("SHOW WARNINGS");
    if ($error_check->num_rows > 0) {
        $error_message = $error_check->row['Message']; // Récupération du message d'erreur SQL
		//error_log("Erreur SQL : " . $error_message); // Journalisation de l'erreur

        return [
            'error' => true,
            'message' => $error_message,
            'product_id' => $data['product_id'],
            'marketplace_item_id' => $data['marketplace_item_id']
        ];
    }

    return $this->db->getLastId();
}




public function editProductSpecifics($product_id, $marketplace_account_id,$specificsjson){
    // Vérifier si l'entrée existe déjà dans la base de données
   
        // Si l'entrée existe, on met à jour
        $this->db->query("
            UPDATE " . DB_PREFIX . "product_marketplace 
            SET 
                specifics = '" . $this->db->escape($specificsjson) . "',
                error = ''
            WHERE product_id = '" . (int)$product_id . "' 
            AND marketplace_account_id = '" . (int)$marketplace_account_id . "'
        ");
		
}

public function editProductMarketplace($data = []) {

	//print("<pre>" . print_r($data, true) . "</pre>");
    // Vérifier si l'entrée existe déjà dans la base de données
    $existingEntry = $this->getMarketplace([
        'product_id' => $data['product_id'],
        'marketplace_id' => $data['marketplace_id'],
		'marketplace_account_id' => $data['marketplace_account_id'],
		'marketplace_item_id' => $data['marketplace_item_id']
    ]);
	
    if (!empty($existingEntry[$data['product_id']])) {

		if (!empty($existingEntry[$data['product_id']]) && count($existingEntry[$data['product_id']]) === 1) {
			$existingEntry = reset($existingEntry[$data['product_id']]); // Transforme `[['key' => 'value']]` en `['key' => 'value']`
		}
		//print("<pre>" . print_r($existingEntry, true) . "</pre>");
        // Si l'entrée existe, mise à jour // " . ($data['to_update'] ?? $existingEntry['to_update']) . "'
		//print("<pre>" . print_r(value: '1620:product') . "</pre>");
		
        $this->db->query("
            UPDATE " . DB_PREFIX . "product_marketplace pm
            SET 
                pm.marketplace_item_id = '" . $this->db->escape($data['marketplace_item_id'] ?? $existingEntry['marketplace_item_id']) . "',
                pm.category_id = '" . $this->db->escape($data['category_id'] ?? $existingEntry['category_id']) . "',
                pm.currency = '" . $this->db->escape($data['currency'] ?? $existingEntry['currency']) . "',
                pm.price = '" . (isset($data['price']) ? (float)$data['price'] : (float)$existingEntry['price']) . "',
                pm.quantity_listed = '" . (isset($data['quantity_listed']) ? (int)$data['quantity_listed'] : (int)$existingEntry['quantity_listed']) . "',
                pm.quantity_sold = '" . (isset($data['quantity_sold']) ? (int)$data['quantity_sold'] : (int)$existingEntry['quantity_sold']) . "',
                pm.specifics = '" . (!empty($data['specifics']) ? $this->db->escape($data['specifics']) : $this->db->escape($existingEntry['specifics'])) . "',
                pm.status = '" . (isset($data['status']) ? (int)$data['status'] : (int)$existingEntry['status']) . "',
                pm.date_added = " . (isset($data['date_added']) ? "'" . $this->db->escape($data['date_added']) . "'" : "'" . $this->db->escape($existingEntry['date_added']) . "'") . ",
                pm.date_ended = " . (isset($data['date_ended']) && !empty($data['date_ended']) ? "'" . $this->db->escape($data['date_ended']) . "'" : (isset($existingEntry['date_ended']) ? "'" . $this->db->escape($existingEntry['date_ended']) . "'" : "NULL")) . ",
                pm.date_modified = NOW(),
                pm.error = '" . (isset($data['error']) ? $this->db->escape($data['error']) : $this->db->escape($existingEntry['error'])) . "',
                pm.ebay_image_count = " . (isset($data['ebay_image_count']) ? (int)$data['ebay_image_count'] : "ebay_image_count") . ",
                pm.to_update = '2' 
            
           WHERE product_id = '" . (isset($data['product_id']) ? (int)$data['product_id'] : (int)$existingEntry['product_id']) . "'
		AND marketplace_id = '" . (isset($data['marketplace_id']) ? (int)$data['marketplace_id'] : (int)$existingEntry['marketplace_id']) . "'

        "); 
		
    } else {
        // Sinon, on ajoute une nouvelle entrée en utilisant la fonction existante
        $this->addProductMarketplace($data);
    }
}


public function editProductMarketplaceERROR($product_id= null,$marketplace_item_id = null, $error = '') {

	//print("<pre>" . print_r($product_id, true) . "</pre>");
	//print("<pre>" . print_r($marketplace_item_id, true) . "</pre>");
	//print("<pre>" . print_r($error, true) . "</pre>");
    // Vérifier si l'entrée existe déjà dans la base de données

        if(isset($marketplace_item_id)){// Si l'entrée existe, on met à jour
				$this->db->query("
				UPDATE " . DB_PREFIX . "product_marketplace 
				 
				SET error = '" . (isset($error) ? $this->db->escape($error) : '') . "',
				to_update = '" . (!empty($error) ? 9 : 2) . "'
				WHERE marketplace_item_id = '" . (int)$marketplace_item_id . "'");
		}elseif(isset($product_id)){
				$this->db->query("
				UPDATE " . DB_PREFIX . "product_marketplace 
				SET error = '" . (isset($error) ? $this->db->escape($error) : '') . "',
				to_update = '" . (!empty($error) ? 9 : 2) . "'
				WHERE product_id = '" . (int)$product_id . "'");
		}
}

public function getMarketplaceERROR($product_id = null) {

	//print("<pre>" . print_r($product_id, true) . "</pre>");
	//print("<pre>" . print_r($marketplace_item_id, true) . "</pre>");
	//print("<pre>" . print_r($error, true) . "</pre>");
    // Vérifier si l'entrée existe déjà dans la base de données

        if(isset($product_id)){// Si l'entrée existe, on met à jour
			$sql="
				SELECT error FROM " . DB_PREFIX . "product_marketplace 
				WHERE error is not null AND error !='' AND product_id = '" . (int)$product_id . "' GROUP BY product_id";
				//print("<pre>" . print_r($sql, true) . "</pre>");
				$query=$this->db->query($sql);
				$row=$query->row;
				return json_decode($row['error']?? '[]', true);
		}else{
				return [];
		}
}


public function deleteProductMarketplaceOLD($product_id) {
   

    $this->db->query("
        DELETE FROM " . DB_PREFIX . "product_marketplace 
        WHERE  `product_id` = '" . (int)$product_id . "'
    ");

    return $this->db->countAffected(); // Retourne le nombre de lignes supprimées
}

public function deleteProductMarketplaceItemId($marketplace_item_id) {
   

    $this->db->query("
        DELETE FROM " . DB_PREFIX . "product_marketplace 
        WHERE  `marketplace_item_id` = '" . (int)$marketplace_item_id . "'
    ");

    return $this->db->countAffected(); // Retourne le nombre de lignes supprimées
}

public function addToMarketplace($product_id, $marketplace_account_id=null, $marketplace_name= 'ebay') {
	
	// Charger les modèles nécessaires
	//$this->load->model('shopmanager/ebay');
	//$this->load->model('shopmanager/walmart');
	//$this->load->model('shopmanager/amazon');
	$this->load->model('shopmanager/catalog/product');

	$this->load->model(
		"shopmanager/" . strtolower($marketplace_name) . ""
	);

	//print("<pre>" . print_r(value: '1620:product') . "</pre>");
	//print("<pre>" . print_r($marketplace_data, true) . "</pre>");
	// Récupérer les informations du produit
	$product = $this->model_shopmanager_catalog_product->getProduct($product_id);
	$product['marketplace_accounts_id'] = $this->getMarketplace(['product_id' => $product_id]);
	$product['product_description'] = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
	
	//print("<pre>" . print_r(value: '1625:product') . "</pre>");
    //print("<pre>" . print_r($product['marketplace_accounts_id'][$marketplace_account_id], true) . "</pre>");
	if(($product['quantity']+$product['unallocated_quantity'])>0){
		$site_setting=$product['marketplace_accounts_id'][$marketplace_account_id]['site_setting'];
		
	//print("<pre>" . print_r(value: '1625:product') . "</pre>"); 
	//print("<pre>" . print_r($site_setting, true) . "</pre>");
		$quantity = $product['quantity']+$product['unallocated_quantity'];
		unset($product['marketplace_accounts_id']);
		//print("<pre>" . print_r($marketplace_name, true) . "</pre>");
		$result = $this->{"model_shopmanager_" .
			strtolower($marketplace_name)}->add($product,$quantity,$site_setting,$marketplace_account_id);
		
		$result['quantity_listed'] = $quantity;
		$result['quantity_sold'] = 0;
		return $result;
	}else{
		return 'error: Quantity a zero';
	}
}

public function editQuantityToMarketplace($product_id,$marketplace_account_id = 1){
	
	// Charger les modèles nécessaires
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/catalog/product');

	//print("<pre>" . print_r(value: '1620:product') . "</pre>");
	//print("<pre>" . print_r($marketplace_data, true) . "</pre>");
	// Récupérer les informations du produit
	$product = $this->model_shopmanager_catalog_product->getProduct($product_id);
	$product['marketplace_accounts_id'] = $this->getMarketplace(['product_id' => $product_id]);

	//$product['product_description'] = $this->model_shopmanager_catalog_product->getDescriptions($product_id);

	
	//print("<pre>" . print_r(value: '1625:product') . "</pre>");

	$site_setting=$product['marketplace_accounts_id'][$marketplace_account_id]['site_setting'];
	$marketplace_item_id = $product['marketplace_accounts_id'][$marketplace_account_id]['marketplace_item_id'];
	$made_in_country_id = $product['made_in_country_id'];
	//print("<pre>" . print_r($product['marketplace_accounts_id'][$marketplace_account_id], true) . "</pre>");
	//print("<pre>" . print_r(value: '1625:product') . "</pre>");
	//print("<pre>" . print_r($site_setting, true) . "</pre>");
	//print("<pre>" . print_r($product['quantity']+$product['unallocated_quantity'], true) . "</pre>");
	$total_quantity = $product['quantity']+$product['unallocated_quantity'];
	//print("<pre>" . print_r($total_quantity, true) . "</pre>");
	
	$marketplace_accounts[$marketplace_account_id] =$product['marketplace_accounts_id'][$marketplace_account_id];
	
	$result = $this->model_shopmanager_ebay->editQuantity($marketplace_item_id,$total_quantity,$made_in_country_id,$product_id,$marketplace_account_id,$site_setting);

	// Mettre à jour quantity_listed en DB si eBay confirme le succès
	// quantity_sold remis à 0 : on vient de redéfinir la quantité disponible sur eBay,
	// la baseline repart de zéro (sinon la formule mismatch quantity_listed-quantity_sold serait fausse)
	if (isset($result['Ack']) && ($result['Ack'] === 'Success' || $result['Ack'] === 'Warning')) {
		$this->db->query("UPDATE " . DB_PREFIX . "product_marketplace 
						  SET quantity_listed = " . (int)$total_quantity . ", quantity_sold = 0, last_sync = NOW() 
						  WHERE product_id = " . (int)$product_id . " AND marketplace_id = " . (int)$marketplace_account_id);
	}

	return $result;
}
public function editToMarketplace($product_id, $marketplace_account_id=null) {
	
	// Charger les modèles nécessaires
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/catalog/product');
	//print("<pre>" . print_r(value: '1620:product') . "</pre>");
	//print("<pre>" . print_r($marketplace_data, true) . "</pre>");
	// Récupérer les informations du produit
	$product = $this->model_shopmanager_catalog_product->getProduct($product_id);
	$product['marketplace_accounts_id'] = $this->getMarketplace(['product_id' => $product_id]);
	$product['product_description'] = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
	
	//print("<pre>" . print_r(value: '1625:product') . "</pre>");

	$site_setting=$product['marketplace_accounts_id'][$marketplace_account_id]['site_setting'];
	$marketplace_accounts = [];
	$marketplace_accounts[$marketplace_account_id] =$product['marketplace_accounts_id'][$marketplace_account_id];
//	//print("<pre>" . print_r($product['marketplace_accounts_id'][$marketplace_account_id], true) . "</pre>");
//print("<pre>" . print_r(value: '1625:product') . "</pre>"); 
//print("<pre>" . print_r($site_setting, true) . "</pre>");
	$quantity = $product['quantity']+$product['unallocated_quantity'];
	unset($product['marketplace_accounts_id']);
	$result = $this->model_shopmanager_ebay->edit($product,$quantity,$site_setting,$marketplace_accounts);

	
	$result['quantity_listed'] = $quantity;
	$result['quantity_sold'] = 0;
	return $result;
}

	public function syncMarketplaceProduct($marketplace_item_id = null){
		
		//$filter_data['filter_marketplace_item_id']=304154046908;
		$filter_data = [];
		$marketplace_item = [];
		if(isset($marketplace_item_id)){
			$filter_data['filter_marketplace_item_id']=$marketplace_item_id;
		}
		$items=$this->getProducts($filter_data);
	//	//print("<pre>".print_r ($items ,true )."</pre>");
		$this->load->model('shopmanager/ebay');
	//	die();
		foreach($items as $item){

		//	//print("<pre>".print_r ($item ,true )."</pre>");
			$result=$this->model_shopmanager_ebay->getProduct($item['marketplace_item_id'],$item['marketplace_account_id']);
			//$result['product_description'] = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
		//	$result=$this->model_shopmanager_ebay->getProduct(304154046908,$item['marketplace_account_id']);
			//297016130518
		//	//print("<pre>".print_r ($result ,true )."</pre>"); 
		if(isset($result[0]['Item'])){
			$result=$result[0]['Item'];
		}else{
			//print("<pre>".print_r ($result ,true )."</pre>"); 
		//	//print("<pre>".print_r ($item ,true )."</pre>"); 
			$marketplace_item = array (
				'marketplace_item_id' => $item['marketplace_item_id'],
				'marketplace_account_id' => $item['marketplace_account_id'],
				'category_id' => $item['category_id'],
				'currency' => $item['currency'],
				'price' =>$item['price'],
				'quantity_listed' => $item['quantity_listed'],
				'quantity_sold' => $item['quantity_sold'],
				'specifics' =>json_encode($result),
				'status' => $item['status'],
				'date_added' => $item['date_added'], // Conversion ici
				'date_ended' => $item['date_ended'], // Conversion ici
				'error' => json_encode($result),
				'product_id' => $item['product_id'],
				'marketplace_id' => $item['marketplace_id']


			);
		}
			$specifics=[];
			unset($result['Description']);
		//	//print("<pre>".print_r (1385 ,true )."</pre>");
	//		//print("<pre>".print_r ($result['ItemSpecifics']['NameValueList'] ,true )."</pre>");
	//	//print("<pre>".print_r ($item['marketplace_item_id'] ,true )."</pre>");

			if(isset($result['ItemSpecifics']['NameValueList'])){
				$result['ItemSpecifics']['NameValueList']=(isset($result['ItemSpecifics']['NameValueList'][0]))?$result['ItemSpecifics']['NameValueList']:[$result['ItemSpecifics']['NameValueList']]??NULL;

				foreach($result['ItemSpecifics']['NameValueList'] as $specific){
				
					unset($specific['Source']);
					$specifics[$specific['Name']] =$specific;
				}
				$specifics=json_encode($specifics);
			

			// Compter les images eBay (PictureURL peut être string ou array)
			$ebay_picture_urls = $result['PictureDetails']['PictureURL'] ?? [];
			if (is_string($ebay_picture_urls) && !empty($ebay_picture_urls)) {
				$ebay_image_count = 1;
			} elseif (is_array($ebay_picture_urls)) {
				$ebay_image_count = count($ebay_picture_urls);
			} else {
				$ebay_image_count = 0;
			}

			$marketplace_item = array (
				'marketplace_item_id' => $item['marketplace_item_id'],
				'marketplace_account_id' => $item['marketplace_account_id'],
				'category_id' => $result['PrimaryCategory']['CategoryID'],
				'currency' => $result['Currency'],
				'price' => $result['SellingStatus']['CurrentPrice'],
				'quantity_listed' => $result['Quantity'],
				'quantity_sold' => $result['SellingStatus']['QuantitySold'],
				'specifics' => $specifics,
				'status' => isset($result['ListingDetails']['EndingReason'])?0:1,
				'date_added' => date('Y-m-d H:i:s', strtotime($result['ListingDetails']['StartTime'])), // Conversion ici
				'date_ended' => isset($result['ListingDetails']['EndingReason'])?date('Y-m-d H:i:s', strtotime($result['ListingDetails']['EndTime'])):'NULL', // Conversion ici
				'error' => '',
				'ebay_image_count' => $ebay_image_count,
				'product_id' => $item['product_id'],
				'marketplace_id' => $item['marketplace_id']


			);
		//	//print("<pre>".print_r ($marketplace_item ,true )."</pre>");
			
		}else{
			$marketplace_item = array (
				'marketplace_item_id' => $item['marketplace_item_id'],
				'marketplace_account_id' => $item['marketplace_account_id'],
				'category_id' => $item['category_id'],
				'currency' => $item['currency'],
				'price' =>$item['price'],
				'quantity_listed' => $item['quantity_listed'],
				'quantity_sold' => $item['quantity_sold'],
				'specifics' => $item['specifics'],
				'status' => $item['status'],
				'date_added' => $item['date_added'], // Conversion ici
				'date_ended' => $item['date_ended'], // Conversion ici
				'error' => json_encode($result),
				'product_id' => $item['product_id'],
				'marketplace_id' => $item['marketplace_id']


			);
		
		}
		 $this->editProductMarketplace($marketplace_item);
		}
	}
	public function syncMarketplaceProductSpecifics() {
		$filter_data = [];
		$marketplace_item = [];
		$items = $this->getProducts($filter_data);
		$this->load->model('shopmanager/ebay');
		//print("<pre>".print_r ($items,true )."</pre>");
		
		foreach ($items as $item) {
			//print("<pre>".print_r ($item,true )."</pre>");
			if($item['marketplace_account_id']==2){
				$marketplace_account_id_switch=1;
			}elseif($item['marketplace_account_id'] == 1){
				$marketplace_account_id_switch=2;
			}
			
			$specifics = $this->getSpecifics($item['product_id'], $marketplace_account_id_switch);
			//print("<pre>".print_r ($specifics,true )."</pre>");
			/*if (isset($result[0]['Item'])) {
				$result = $result[0]['Item'];
			}*/

			if(isset($specifics)){
				$specificsjson=json_encode($specifics);
	//die();
				$this->editProductSpecifics($item['product_id'], $item['marketplace_account_id'],$specificsjson);
				$this->editToMarketplace($item['product_id'], $item['marketplace_account_id']);
			}
		}
	}
	public function getSpecifics($product_id=0,$marketplace_account_id = 1){
        $query = $this->db->query("SELECT specifics FROM " . DB_PREFIX . "product_marketplace WHERE product_id = '" . (int)$product_id . "' AND marketplace_account_id = '" . (int)$marketplace_account_id . "'");
        $rows = $query->rows;
		$data_return = [];
	//print("<pre>".print_r ($rows,true )."</pre>");
        if(isset($rows)){
		//	$english_specifics=json_decode($rows[1]['specifics'],true);
			foreach($rows as $data){
				
					
				
						if(isset($data['specifics'])){
							$specifics=json_decode(trim(html_entity_decode($data['specifics'], ENT_QUOTES | ENT_HTML5)),true);
							//print("<pre>".print_r ($specifics,true )."</pre>");
							return $specifics;
						}else{
							return array();
						}
					
			}
		//print("<pre>".print_r ($data_return,true )."</pre>");
            

        }else{
            return array();
        }
    }

	public function getSyncJSON($marketplace_account_id = NULL){
		if(isset($marketplace_account_id)){
			$query = $this->db->query("SELECT sync_json FROM " . DB_PREFIX . "marketplace_accounts WHERE marketplace_account_id = '" . (int)$marketplace_account_id . "'");
			$rows = $query->rows;
		
			if(isset($rows)){
				foreach($rows as $data){

							if(isset($data['sync_json'])){
								$sync_json=json_decode(trim(html_entity_decode($data['sync_json'], ENT_QUOTES | ENT_HTML5)),true);
								//print("<pre>".print_r ($specifics,true )."</pre>");
								return $sync_json;
							}else{
								return array();
							}
						
				}
			}else{
				return array();
			}
		}else{
			return array();
		}
    }

	public function editProductSyncJSON($marketplace_items = [], $marketplace_account_id = NULL){
		if(isset($marketplace_items) && !empty($marketplace_items) && isset($marketplace_account_id)){
			$this->db->query("UPDATE  " . DB_PREFIX . "marketplace_accounts
							SET sync_json = '" . $this->db->escape(json_encode($marketplace_items)) . "'
							WHERE marketplace_account_id = '" . (int)$marketplace_account_id . "'");
   		 }
	}

	/**
	 * Get product_id from SKU in phoenixsupplies database
	 */
	public function getProductIdBySku($sku, $db_connection) {
		$sql = "SELECT product_id FROM oc_product WHERE sku = '" . mysqli_real_escape_string($db_connection, $sku) . "' LIMIT 1";
		$result = mysqli_query($db_connection, $sql);
		if ($result && mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			return (int)$row['product_id'];
		}
		return 0;
	}

	/**
	 * Get existing marketplace data (category_id, condition_id, specifics)
	 */
	public function getMarketplaceExistingData($product_id, $marketplace_id = 1, $db_connection = null) {
		if ($db_connection) {
			// phoenixsupplies
				$sql = "SELECT category_id, condition_id, specifics, ebay_image_count FROM oc_product_marketplace
								WHERE product_id = " . (int)$product_id . " AND marketplace_id = " . (int)$marketplace_id . " LIMIT 1";
				$result = mysqli_query($db_connection, $sql);
				if ($result && mysqli_num_rows($result) > 0) {
						return mysqli_fetch_assoc($result);
				}
		} else {
				// phoenixliquidation
				$query = $this->db->query("SELECT category_id, condition_id, specifics, ebay_image_count
					FROM " . DB_PREFIX . "product_marketplace
					WHERE product_id = " . (int)$product_id . " AND marketplace_id = " . (int)$marketplace_id . " LIMIT 1");
		}
		return null;
	}

	/**
	 * Insert or update product marketplace data
	 */
	public function upsertProductMarketplace($marketplace_data, $db_connection = null) {
		if ($db_connection) {
			// phoenixsupplies database
			$specifics_escaped = $marketplace_data['specifics'] ? "'" . mysqli_real_escape_string($db_connection, $marketplace_data['specifics']) . "'" : "NULL";
			$category_id_escaped = $marketplace_data['category_id'] ? (int)$marketplace_data['category_id'] : "NULL";
			$condition_id_escaped = $marketplace_data['condition_id'] ? (int)$marketplace_data['condition_id'] : "NULL";
			
			$sql = "
				INSERT INTO oc_product_marketplace 
				(product_id, customer_id, marketplace_id, marketplace_account_id, marketplace_item_id, 
				 category_id, condition_id, currency, price, price_usd, quantity_listed, quantity_sold, specifics, status, is_com, ebay_image_count, date_added, date_ended, last_import)
				VALUES 
				(" . (int)$marketplace_data['product_id'] . ", 
				 " . (int)$marketplace_data['customer_id'] . ",
				 " . (int)$marketplace_data['marketplace_id'] . ",
				 " . (int)$marketplace_data['marketplace_account_id'] . ", 
				 '" . mysqli_real_escape_string($db_connection, $marketplace_data['marketplace_item_id']) . "',
				 " . $category_id_escaped . ",
				 " . $condition_id_escaped . ",
				 '" . mysqli_real_escape_string($db_connection, $marketplace_data['currency']) . "',
				 " . (float)$marketplace_data['price'] . ", 
				 " . (float)$marketplace_data['price_usd'] . ", 
				 " . (int)$marketplace_data['quantity_listed'] . ", 
				 " . (int)$marketplace_data['quantity_sold'] . ",
				 " . $specifics_escaped . ",
				 " . (int)$marketplace_data['status'] . ",
				 " . (int)$marketplace_data['is_com'] . ",
				 " . (int)($marketplace_data['ebay_image_count'] ?? 0) . ",
				 " . ($marketplace_data['date_added'] ? "'" . mysqli_real_escape_string($db_connection, $marketplace_data['date_added']) . "'" : "NULL") . ",
				 " . ($marketplace_data['date_ended'] ? "'" . mysqli_real_escape_string($db_connection, $marketplace_data['date_ended']) . "'" : "NULL") . ",
				 '" . mysqli_real_escape_string($db_connection, $marketplace_data['last_import_time']) . "')
				ON DUPLICATE KEY UPDATE 
				marketplace_item_id = '" . mysqli_real_escape_string($db_connection, $marketplace_data['marketplace_item_id']) . "',
				category_id = IFNULL(" . $category_id_escaped . ", category_id),
				condition_id = IFNULL(" . $condition_id_escaped . ", condition_id),
				currency = '" . mysqli_real_escape_string($db_connection, $marketplace_data['currency']) . "',
				price = " . (float)$marketplace_data['price'] . ",
				price_usd = " . (float)$marketplace_data['price_usd'] . ",
				quantity_listed = " . (int)$marketplace_data['quantity_listed'] . ",
				quantity_sold = " . (int)$marketplace_data['quantity_sold'] . ",
				specifics = IFNULL(" . $specifics_escaped . ", specifics),
				status = " . (int)$marketplace_data['status'] . ",
				is_com = " . (int)$marketplace_data['is_com'] . ",
				ebay_image_count = " . (int)($marketplace_data['ebay_image_count'] ?? 0) . ",
				last_import = '" . mysqli_real_escape_string($db_connection, $marketplace_data['last_import_time']) . "'
			";
			return mysqli_query($db_connection, $sql);
		} else {
			// phoenixliquidation database
			$specifics_sql = $marketplace_data['specifics'] ? "'" . $this->db->escape($marketplace_data['specifics']) . "'" : "NULL";
			$category_id_sql = $marketplace_data['category_id'] ? (int)$marketplace_data['category_id'] : "NULL";
			$condition_id_sql = $marketplace_data['condition_id'] ? (int)$marketplace_data['condition_id'] : "NULL";
			$date_added_sql = $marketplace_data['date_added'] ? "'" . $this->db->escape($marketplace_data['date_added']) . "'" : "NULL";
			$date_ended_sql = $marketplace_data['date_ended'] ? "'" . $this->db->escape($marketplace_data['date_ended']) . "'" : "NULL";
			$last_import_sql = "'" . $this->db->escape($marketplace_data['last_import_time']) . "'";
			
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_marketplace 
				(product_id, customer_id, marketplace_id, marketplace_account_id, marketplace_item_id, 
				 category_id, condition_id, currency, price, price_usd, quantity_listed, quantity_sold, specifics, status, is_com, ebay_image_count, date_added, date_ended, last_import)
				VALUES 
				(" . (int)$marketplace_data['product_id'] . ", 
				 " . (int)$marketplace_data['customer_id'] . ",
				 " . (int)$marketplace_data['marketplace_id'] . ",
				 " . (int)$marketplace_data['marketplace_account_id'] . ", 
				 '" . $this->db->escape($marketplace_data['marketplace_item_id']) . "',
				 " . $category_id_sql . ",
				 " . $condition_id_sql . ",
				 '" . $this->db->escape($marketplace_data['currency']) . "',
				 " . (float)$marketplace_data['price'] . ", 
				 " . (float)$marketplace_data['price_usd'] . ", 
				 " . (int)$marketplace_data['quantity_listed'] . ", 
				 " . (int)$marketplace_data['quantity_sold'] . ",
				 " . $specifics_sql . ",
				 " . (int)$marketplace_data['status'] . ",
				 " . (int)$marketplace_data['is_com'] . ",
				 " . (int)($marketplace_data['ebay_image_count'] ?? 0) . ",
				 " . $date_added_sql . ",
				 " . $date_ended_sql . ",
				 " . $last_import_sql . ")
				ON DUPLICATE KEY UPDATE 
				marketplace_item_id = '" . $this->db->escape($marketplace_data['marketplace_item_id']) . "',
				category_id = IFNULL(" . $category_id_sql . ", category_id),
				condition_id = IFNULL(" . $condition_id_sql . ", condition_id),
				currency = '" . $this->db->escape($marketplace_data['currency']) . "',
				price = " . (float)$marketplace_data['price'] . ",
				price_usd = " . (float)$marketplace_data['price_usd'] . ",
				quantity_listed = " . (int)$marketplace_data['quantity_listed'] . ",
				quantity_sold = " . (int)$marketplace_data['quantity_sold'] . ",
				specifics = IFNULL(" . $specifics_sql . ", specifics),
				status = " . (int)$marketplace_data['status'] . ",
				is_com = " . (int)$marketplace_data['is_com'] . ",
				ebay_image_count = " . (int)($marketplace_data['ebay_image_count'] ?? 0) . ",
				last_import = " . $last_import_sql . "
			");
			return true;
		}
	}

	/**
	 * Get product price
	 */
	public function getProductPrice($product_id) {
		$query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);
		return $query->num_rows ? $query->row : null;
	}

	/**
	 * Get product quantities
	 */
	public function getProductQuantities($product_id) {
		$query = $this->db->query("SELECT quantity, unallocated_quantity FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id);
		return $query->num_rows ? $query->row : null;
	}

	/**
	 * Get marketplace item details
	 */
	public function getMarketplaceItem($product_id, $marketplace_id = 1) {
		$query = $this->db->query("SELECT marketplace_item_id, marketplace_account_id, price, quantity_listed, quantity_sold, specifics, category_id 
								   FROM " . DB_PREFIX . "product_marketplace 
								   WHERE product_id = " . (int)$product_id . " AND marketplace_id = " . (int)$marketplace_id);
		return $query->num_rows ? $query->row : null;
	}

	/**
	 * Get product specifics from description
	 */
	public function getProductSpecifics($product_id, $language_id = 1) {
		$query = $this->db->query("SELECT specifics FROM " . DB_PREFIX . "product_description 
								   WHERE product_id = " . (int)$product_id . " AND language_id = " . (int)$language_id);
		return $query->num_rows ? $query->row : null;
	}


	/**
	 * Update marketplace price
	 */
	public function updateMarketplacePrice($product_id, $price) {
		$this->db->query("UPDATE " . DB_PREFIX . "product_marketplace 
						  SET price = " . (float)$price . ", date_modified = NOW() 
						  WHERE product_id = " . (int)$product_id);
	}

	/**
	 * Update product price
	 */
	public function updateProductPrice($product_id, $price) {
		$this->db->query("UPDATE " . DB_PREFIX . "product 
						  SET price = " . (float)$price . ", date_modified = NOW() 
						  WHERE product_id = " . (int)$product_id);
	}

	/**
	 * Update marketplace quantity
	 */
	public function updateMarketplaceQuantity($product_id, $quantity) {
		$this->db->query("UPDATE " . DB_PREFIX . "product_marketplace 
						  SET quantity_listed = " . (int)$quantity . ", date_modified = NOW() 
						  WHERE product_id = " . (int)$product_id);
	}

	/**
	 * Update product quantity
	 */
	public function updateProductQuantity($product_id, $quantity) {
		$this->db->query("UPDATE " . DB_PREFIX . "product 
						  SET quantity = " . (int)$quantity . ", unallocated_quantity = 0, date_modified = NOW() 
						  WHERE product_id = " . (int)$product_id);
	}

	/**
	 * Update marketplace specifics
	 */
	public function updateMarketplaceSpecifics($product_id, $specifics) {
		$this->db->query("UPDATE " . DB_PREFIX . "product_marketplace 
						  SET specifics = '" . $this->db->escape(json_encode($specifics)) . "', date_modified = NOW() 
						  WHERE product_id = " . (int)$product_id);
	}

	/**
	 * Update product description specifics
	 */
	public function updateProductDescriptionSpecifics($product_id, $specifics, $language_id = 1) {
		$this->db->query("UPDATE " . DB_PREFIX . "product_description 
						  SET specifics = '" . $this->db->escape($specifics) . "' 
						  WHERE product_id = " . (int)$product_id . " AND language_id = " . (int)$language_id);
	}

	/**
	 * Reset ebay_image_count to 0 (forces re-fetch on next eBay import) and set to_update = 1
	 */
	public function resetEbayImageCount(int $product_id, int $marketplace_id = 1): void {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product_marketplace
			SET ebay_image_count = 0, to_update = 1
			WHERE product_id = '" . $product_id . "'
			  AND marketplace_id = '" . $marketplace_id . "'"
		);
	}

	/**
	 * Get ebay_image_count stored in product_marketplace for a product
	 */
	public function getEbayImageCount(int $product_id, int $marketplace_id = 1): int {
		$query = $this->db->query("
			SELECT ebay_image_count
			FROM " . DB_PREFIX . "product_marketplace
			WHERE product_id = '" . $product_id . "'
			  AND marketplace_id = '" . $marketplace_id . "'
			LIMIT 1"
		);
		return (int)($query->row['ebay_image_count'] ?? 0);
	}

	/**
	 * Get product for refresh (with quantities)
	 */
	public function getProductForRefresh($product_id) {
		$query = $this->db->query("
			SELECT p.product_id, p.sku, p.quantity, p.unallocated_quantity, pd.name
			FROM " . DB_PREFIX . "product p 
			LEFT JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id AND pd.language_id = 1
			WHERE p.product_id = " . (int)$product_id
		);
		return $query->num_rows ? $query->row : null;
	}

	/**
	 * Get marketplace data for refresh
	 */
	public function getMarketplaceForRefresh($product_id) {
		$query = $this->db->query("
			SELECT marketplace_item_id, marketplace_account_id, is_com 
			FROM " . DB_PREFIX . "product_marketplace 
			WHERE product_id = " . (int)$product_id . " 
			AND marketplace_id = 1
			LIMIT 1
		");
		return $query->num_rows ? $query->row : null;
	}

	/**
	 * Update marketplace quantity listed and last sync
	 */
	public function updateMarketplaceQuantityListed($product_id, $quantity) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product_marketplace 
			SET quantity_listed = " . (int)$quantity . ",
				last_sync = NOW()
			WHERE product_id = " . (int)$product_id . "
			AND marketplace_id = 1
		");
	}

	/**
	 * Update marketplace last sync timestamp
	 */
	public function updateMarketplaceLastSync($product_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product_marketplace 
			SET last_sync = NOW() 
			WHERE product_id = " . (int)$product_id . " 
			AND marketplace_id = 1
		");
	}

	/**
	 * Reset sync state — force le produit à apparaître dans l'onglet "not synced"
	 * Appelé après n'importe quel fix (image, qty, prix, catégorie, condition, specifics)
	 * afin qu'il soit re-scanné par le prochain importMarketplace.
	 */
	public function resetSyncState(int $product_id, int $marketplace_id = 1): void {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product_marketplace 
			SET last_sync = NULL, last_import = NULL
			WHERE product_id = " . (int)$product_id . "
			AND marketplace_id = " . (int)$marketplace_id . "
		");
	}

	/**
	 * Update marketplace full refresh data (price, quantity, category, specifics, dates)
	 */
	public function updateMarketplaceFullRefresh($product_id, $data) {
		$date_added_sql = isset($data['date_added']) && $data['date_added'] ? "'" . $this->db->escape($data['date_added']) . "'" : "NULL";
		$date_ended_sql = isset($data['date_ended']) && $data['date_ended'] ? "'" . $this->db->escape($data['date_ended']) . "'" : "NULL";
		$specifics_sql = isset($data['specifics']) && $data['specifics'] ? "'" . $this->db->escape($data['specifics']) . "'" : "NULL";
		
		$this->db->query("
			UPDATE " . DB_PREFIX . "product_marketplace 
			SET 
				price = " . (float)$data['price'] . ",
				currency = '" . $this->db->escape($data['currency']) . "',
				quantity_listed = " . (int)$data['quantity_listed'] . ",
				quantity_sold = " . (int)$data['quantity_sold'] . ",
				category_id = " . (int)$data['category_id'] . ",
				specifics = " . $specifics_sql . ",
				date_added = " . $date_added_sql . ",
				date_ended = " . $date_ended_sql . ",
				date_modified = NOW(),
				last_sync = NOW()
			WHERE product_id = " . (int)$product_id . "
			AND marketplace_id = 1
		");
	}

	/**
	 * Update Marketplace Listings (eBay, etc.) after product save
	 * 
	 * @param int $product_id
	 * @param array $product_info
	 * @return void
	 */
	public function updateMarketplaceListings(int $product_id): void {
		try {
			// Get all active marketplace listings with account settings
			$query = $this->db->query("
				SELECT 
					pm.marketplace_item_id,
					pm.marketplace_id,
					pm.marketplace_account_id,
					ma.site_setting
				FROM " . DB_PREFIX . "product_marketplace pm
				LEFT JOIN " . DB_PREFIX . "marketplace_accounts ma 
					ON pm.marketplace_account_id = ma.marketplace_account_id
				WHERE pm.product_id = '" . (int)$product_id . "'
				AND pm.marketplace_item_id IS NOT NULL
				AND pm.marketplace_item_id != ''
				AND pm.marketplace_item_id != '0'
			");
			
			$marketplace_items = $query->rows;
			
			if (!empty($marketplace_items)) {
				// Load required models
				$this->load->model('shopmanager/ebay');
				$this->load->model('shopmanager/catalog/product');
				
				// Get full product data with descriptions
				$product = $this->model_shopmanager_catalog_product->getProduct($product_id);
				$product['product_description'] = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
				
				foreach ($marketplace_items as $item) {
					// Only update eBay listings (marketplace_id = 1 for eBay)
					if ($item['marketplace_id'] == 1 && !empty($item['marketplace_item_id'])) {
						try {
							// Decode site_setting JSON
							$site_setting = json_decode($item['site_setting'] ?? '[]', true);
							
							// Build marketplace_accounts array in the format expected by edit()
							$marketplace_accounts = [
								$item['marketplace_account_id'] => [
									'marketplace_account_id' => $item['marketplace_account_id'],
									'marketplace_id' => $item['marketplace_id'],
									'marketplace_item_id' => $item['marketplace_item_id'],
									'site_setting' => $site_setting
								]
							];
							
							// Calculate quantity (total + unallocated)
							$quantity = ($product['quantity'] ?? 0) + ($product['unallocated_quantity'] ?? 0);
							
							// Call eBay edit function with proper parameters
							$this->model_shopmanager_ebay->edit(
								$product,
								$quantity,
								$site_setting,
								$marketplace_accounts
							);
							
							//$this->log->write('eBay listing updated successfully for product_id: ' . $product_id . ', item_id: ' . $item['marketplace_item_id']);
						} catch (\Exception $e) {
							// Log error but don't fail the save
							$this->log->write('Error updating eBay listing for product_id: ' . $product_id . ' - ' . $e->getMessage());
							error_log('[ShopManager][updateMarketplaceListings][item] product_id=' . (int)$product_id . ', marketplace_item_id=' . (string)($item['marketplace_item_id'] ?? '') . ', account_id=' . (int)($item['marketplace_account_id'] ?? 0) . ', error=' . $e->getMessage());
						}
					}
				}
			}
		} catch (\Exception $e) {
			// Log error but don't fail the product save
			$this->log->write('Error in updateMarketplaceListings: ' . $e->getMessage());
			error_log('[ShopManager][updateMarketplaceListings][fatal] product_id=' . (int)$product_id . ', error=' . $e->getMessage());
		}
	}

	public function editCardListingERROR($listing_id, $ebay_item_id, $error) {
		// This function is called when editCardListing fails
		// For now, just log the error - can be extended to update database status
		$this->log->write('Edit Card Listing Error - Listing ID: ' . $listing_id . ', eBay Item ID: ' . $ebay_item_id . ', Error: ' . json_encode($error));
	}
	
}
