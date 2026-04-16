<?php
// Original: warehouse/inventory/inventory.php
namespace Opencart\Admin\Model\Warehouse\Inventory;

class Inventory extends \Opencart\System\Engine\Model {

    

    public function getProducts($data = array()) {
		// Determine which module is calling
		$isAllocationModule = isset($data['filter_unallocated_only']) && $data['filter_unallocated_only'] === true;
		
		$sql = "SELECT p.*, ptc.category_id, pd.name, p.date_modified, p.date_added
        FROM " . DB_PREFIX . "product p
        LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id)
        LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
        WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ";
		
		// Different default filter based on module
		if ($isAllocationModule) {
			$sql .= " AND p.unallocated_quantity > 0 ";
		} else {
			$sql .= " AND p.quantity > 0 ";
		}
		

		if (!empty($data['filter_sku'])) {
			$sql .= " AND (p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "%' OR p.upc LIKE '" . $this->db->escape($data['filter_sku']) . "%' OR p.product_id LIKE '" . $this->db->escape($data['filter_sku']) . "%') ";
		}

		if (!empty($data['filter_sku_exact'])) {
			$sql .= " AND (p.sku = '" . $this->db->escape($data['filter_sku_exact']) . "' OR p.upc = '" . $this->db->escape($data['filter_sku_exact']) . "') ";
		}

      

		if (!empty($data['filter_category_id'])) {
			$sql .= " AND ptc.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%'";
		}

		

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}
        if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
			$sql .= " AND p.unallocated_quantity = '" . (int)$data['filter_unallocated_quantity'] . "'";
		}
        if (isset($data['filter_location']) && !is_null($data['filter_location']) && $data['filter_location'] !== '') {
			$sql .= " AND p.location = '" . $this->db->escape($data['filter_location']) . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
		
			'pd.name',
			'cts.category_id',
            'p.sku',
			'p.quantity',
            'p.unallocated_quantity',
            'p.location',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2) {
			$sql .= " ORDER BY p.quantity";
		}elseif (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} elseif (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
			$sql .= " ASC";
		}else {
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

		if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
			//$rows=$this->removeProductsNoMissingFile($query->rows);
		}else{
			$rows=$query->rows;
		//	//print("<pre>".print_r ($rows,true )."</pre>");
		}

		return $rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
		$sql	.="LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id) ";
	    $sql	.="WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.unallocated_quantity <> 0"; //

        if (!empty($data['filter_sku'])) {
			$sql .= " AND (p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "' OR p.upc LIKE '" . $this->db->escape($data['filter_sku']) . "') ";
		}

      

		if (!empty($data['filter_marketplace_account'])) {
			$sql .= " AND p.marketplace_item_id = '" . $this->db->escape($data['filter_marketplace_account']) . "'";
		}

		

		if (!empty($data['filter_category_id'])) {
			$sql .= " AND ptc.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%'";
		}

	

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

        if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
			$sql .= " AND p.unallocated_quantity = '" . (int)$data['filter_unallocated_quantity'] . "'";
		}

        if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
			$sql .= " AND p.location = '" . (int)$data['filter_location'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$query = $this->db->query($sql);


			$total=$query->row['total'];

		return $total;
	}

    private function removeProductsNoMissingFile($results){

        $i=0;
        foreach ($results as $product_info) {
            if (is_file(DIR_IMAGE . $product_info['image'])) {
                unset($results[$i]);
            }
            $i++;
        }
        return $results;
}

//updateProductLocation($product_id, $new_location, $old_location, $quantity, $unallocated_quantity);
    public function updateProductLocation($product_id, $new_location, $old_location, $quantity, $unallocated_quantity) {
        $sql = "UPDATE `" . DB_PREFIX . "product`
                SET unallocated_quantity = " . (int)$unallocated_quantity . ",
                    quantity =" . (int)$quantity . ",
                    location = '" . $this->db->escape(strtoupper($new_location)) . "',
                    anc_loc = '" . $this->db->escape(strtoupper($old_location)) . "',
                    date_modified = NOW()
                WHERE product_id = " . (int)$product_id;
        return $this->db->query($sql);
    }
    
    public function getTrimmedProducts() {
        $sql = "SELECT * FROM `" . DB_PREFIX . "product` WHERE anc_loc = '' AND location != '' ORDER BY quantity";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function updateQuantity($product_id, $quantity) {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$quantity . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
    }

    public function updateLocation($product_id, $location) {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET location = '" . $this->db->escape(strtoupper($location)) . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
    }
}
