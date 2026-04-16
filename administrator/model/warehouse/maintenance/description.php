<?php
// Original: shopmanager/maintenance/product_description.php
namespace Opencart\Admin\Model\Shopmanager\Maintenance;

class ProductDescription extends \Opencart\System\Engine\Model {
	
	/**
	 * Get products with outdated descriptions or missing supplemental fields
	 * 
	 * @param array $data Filter data with start and limit
	 * @return array Array of products
	 */
	public function getOutdatedProducts(array $data = []): array {
		$sql = "SELECT 
			p.product_id,
			p.model,
			p.sku,
			p.image,
			p.quantity,
			p.date_modified,
			pd.name,
			pd.description,
			pd.included_accessories,
			pd.condition_supp,
			pd.description_supp
		FROM `" . DB_PREFIX . "product` p
		LEFT JOIN `" . DB_PREFIX . "product_description` pd 
			ON (p.product_id = pd.product_id)
		WHERE pd.language_id = '1'";

		$conditions = [];

		// Filter by product_id
		if (!empty($data['filter_product_id'])) {
			$conditions[] = "p.product_id = '" . (int)$data['filter_product_id'] . "'";
		}

		// Filter by name
		if (!empty($data['filter_name'])) {
			$conditions[] = "pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		// Filter by quantity
		if (isset($data['filter_quantity_from']) && $data['filter_quantity_from'] !== '') {
			$conditions[] = "p.quantity >= '" . (int)$data['filter_quantity_from'] . "'";
		}

		if (isset($data['filter_quantity_to']) && $data['filter_quantity_to'] !== '') {
			$conditions[] = "p.quantity <= '" . (int)$data['filter_quantity_to'] . "'";
		}

		// Filter by missing type
		if (!empty($data['filter_missing_type'])) {
			switch ($data['filter_missing_type']) {
				case 'all':
					$conditions[] = "(pd.included_accessories = '' OR pd.included_accessories IS NULL)";
					$conditions[] = "(pd.condition_supp = '' OR pd.condition_supp IS NULL)";
					$conditions[] = "(pd.description_supp = '' OR pd.description_supp IS NULL)";
					break;
				case 'included_accessories':
					$conditions[] = "(pd.included_accessories = '' OR pd.included_accessories IS NULL)";
					break;
				case 'condition_supp':
					$conditions[] = "(pd.condition_supp = '' OR pd.condition_supp IS NULL)";
					break;
				case 'description_supp':
					$conditions[] = "(pd.description_supp = '' OR pd.description_supp IS NULL)";
					break;
				case 'complete':
					$conditions[] = "pd.included_accessories != '' AND pd.included_accessories IS NOT NULL";
					$conditions[] = "pd.condition_supp != '' AND pd.condition_supp IS NOT NULL";
					$conditions[] = "pd.description_supp != '' AND pd.description_supp IS NOT NULL";
					break;
			}
		}

		// Filter by outdated
		if (!empty($data['filter_outdated'])) {
			switch ($data['filter_outdated']) {
				case '6months':
					$conditions[] = "p.date_modified < DATE_SUB(NOW(), INTERVAL 6 MONTH)";
					break;
				case '1year':
					$conditions[] = "p.date_modified < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
					break;
				case 'recent':
					$conditions[] = "p.date_modified >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
					break;
			}
		}

		// If no filters, apply default condition (outdated or missing fields)
		if (empty($data['filter_product_id']) && empty($data['filter_name']) && empty($data['filter_missing_type']) && empty($data['filter_outdated'])) {
			$conditions[] = "(
				p.date_modified < DATE_SUB(NOW(), INTERVAL 6 MONTH)
				OR pd.included_accessories = ''
				OR pd.included_accessories IS NULL
				OR pd.condition_supp = ''
				OR pd.condition_supp IS NULL
				OR pd.description_supp = ''
				OR pd.description_supp IS NULL
			)";
		}

		if (!empty($conditions)) {
			$sql .= " AND (" . implode(' AND ', $conditions) . ")";
		}

		// Sorting
		$sort_data = [
			'p.product_id',
			'pd.name',
			'p.quantity',
			'p.date_modified',
			'missing_fields'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'missing_fields') {
				// Calculate missing fields for sorting
				$sql .= " ORDER BY (
					CASE WHEN (pd.included_accessories = '' OR pd.included_accessories IS NULL) THEN 1 ELSE 0 END +
					CASE WHEN (pd.condition_supp = '' OR pd.condition_supp IS NULL) THEN 1 ELSE 0 END +
					CASE WHEN (pd.description_supp = '' OR pd.description_supp IS NULL) THEN 1 ELSE 0 END
				)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.date_modified";
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

	/**
	 * Get total count of outdated products
	 * 
	 * @param array $data Filter data
	 * @return int Total count
	 */
	public function getTotalOutdatedProducts(array $data = []): int {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total
			FROM `" . DB_PREFIX . "product` p
			LEFT JOIN `" . DB_PREFIX . "product_description` pd 
				ON (p.product_id = pd.product_id)
			WHERE pd.language_id = '1'";

		$conditions = [];

		// Filter by product_id
		if (!empty($data['filter_product_id'])) {
			$conditions[] = "p.product_id = '" . (int)$data['filter_product_id'] . "'";
		}

		// Filter by name
		if (!empty($data['filter_name'])) {
			$conditions[] = "pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		// Filter by quantity
		if (isset($data['filter_quantity_from']) && $data['filter_quantity_from'] !== '') {
			$conditions[] = "p.quantity >= '" . (int)$data['filter_quantity_from'] . "'";
		}

		if (isset($data['filter_quantity_to']) && $data['filter_quantity_to'] !== '') {
			$conditions[] = "p.quantity <= '" . (int)$data['filter_quantity_to'] . "'";
		}

		// Filter by missing type
		if (!empty($data['filter_missing_type'])) {
			switch ($data['filter_missing_type']) {
				case 'all':
					$conditions[] = "(pd.included_accessories = '' OR pd.included_accessories IS NULL)";
					$conditions[] = "(pd.condition_supp = '' OR pd.condition_supp IS NULL)";
					$conditions[] = "(pd.description_supp = '' OR pd.description_supp IS NULL)";
					break;
				case 'included_accessories':
					$conditions[] = "(pd.included_accessories = '' OR pd.included_accessories IS NULL)";
					break;
				case 'condition_supp':
					$conditions[] = "(pd.condition_supp = '' OR pd.condition_supp IS NULL)";
					break;
				case 'description_supp':
					$conditions[] = "(pd.description_supp = '' OR pd.description_supp IS NULL)";
					break;
				case 'complete':
					$conditions[] = "pd.included_accessories != '' AND pd.included_accessories IS NOT NULL";
					$conditions[] = "pd.condition_supp != '' AND pd.condition_supp IS NOT NULL";
					$conditions[] = "pd.description_supp != '' AND pd.description_supp IS NOT NULL";
					break;
			}
		}

		// Filter by outdated
		if (!empty($data['filter_outdated'])) {
			switch ($data['filter_outdated']) {
				case '6months':
					$conditions[] = "p.date_modified < DATE_SUB(NOW(), INTERVAL 6 MONTH)";
					break;
				case '1year':
					$conditions[] = "p.date_modified < DATE_SUB(NOW(), INTERVAL 1 YEAR)";
					break;
				case 'recent':
					$conditions[] = "p.date_modified >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
					break;
			}
		}

		// If no filters, apply default condition
		if (empty($data['filter_product_id']) && empty($data['filter_name']) && empty($data['filter_missing_type']) && empty($data['filter_outdated'])) {
			$conditions[] = "(
				p.date_modified < DATE_SUB(NOW(), INTERVAL 6 MONTH)
				OR pd.included_accessories = ''
				OR pd.included_accessories IS NULL
				OR pd.condition_supp = ''
				OR pd.condition_supp IS NULL
				OR pd.description_supp = ''
				OR pd.description_supp IS NULL
			)";
		}

		if (!empty($conditions)) {
			$sql .= " AND (" . implode(' AND ', $conditions) . ")";
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}

	/**
	 * Get product description details by product_id
	 * 
	 * @param int $product_id Product ID
	 * @param int $language_id Language ID (default 1 for English)
	 * @return array Product description details
	 */
	public function getProductDescription(int $product_id, int $language_id = 1): array {
		$query = $this->db->query("SELECT 
			pd.name,
			pd.description,
			pd.included_accessories,
			pd.condition_supp,
			pd.description_supp,
			p.date_modified
		FROM `" . DB_PREFIX . "product_description` pd
		LEFT JOIN `" . DB_PREFIX . "product` p ON (pd.product_id = p.product_id)
		WHERE pd.product_id = '" . (int)$product_id . "'
		AND pd.language_id = '" . (int)$language_id . "'");

		return $query->row;
	}

	/**
	 * Update supplemental description fields
	 * 
	 * @param int $product_id Product ID
	 * @param array $data Data to update
	 * @return void
	 */
	public function updateSupplementalFields(int $product_id, array $data): void {
		$sql_parts = [];

		if (isset($data['included_accessories'])) {
			$sql_parts[] = "included_accessories = '" . $this->db->escape($data['included_accessories']) . "'";
		}

		if (isset($data['condition_supp'])) {
			$sql_parts[] = "condition_supp = '" . $this->db->escape($data['condition_supp']) . "'";
		}

		if (isset($data['description_supp'])) {
			$sql_parts[] = "description_supp = '" . $this->db->escape($data['description_supp']) . "'";
		}

		if (!empty($sql_parts)) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product_description` 
				SET " . implode(', ', $sql_parts) . "
				WHERE product_id = '" . (int)$product_id . "'");
		}

		// Update product date_modified
		$this->db->query("UPDATE `" . DB_PREFIX . "product` 
			SET date_modified = NOW() 
			WHERE product_id = '" . (int)$product_id . "'");
	}
}
