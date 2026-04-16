<?php
// Original: warehouse/product/category.php
namespace Opencart\Admin\Model\Warehouse\Product;
/**
 * Class Category
 *
 * Can be loaded using $this->load->model('warehouse/product/category');
 *
 * @package Opencart\Admin\Model\Shopmanager\Catalog
 */
class Category extends \Opencart\System\Engine\Model {
	/**
	 * Add Category
	 *
	 * Create a new category record in the database.
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new category record
	 *
	 * @example
	 *
	 * $category_data = [
	 *     'category_description' => [],
	 *     'image'                => 'category_image',
	 *     'parent_id'            => 0,
	 *     'sort_order'           => 0,
	 *     'status'               => 0,
	 * ];
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $category_id = $this->model_warehouse_product_category->addCategory($category_data);
	 */
	public function addCategory(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category` SET `image` = '" . $this->db->escape((string)$data['image']) . "', `parent_id` = '" . (int)$data['parent_id'] . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (bool)($data['status'] ?? 0) . "'");

		$category_id = $this->db->getLastId();

		foreach ($data['category_description'] as $language_id => $category_description) {
			$this->model_warehouse_product_category->addDescription($category_id, $language_id, $category_description);
		}

		$level = 0;

		// MySQL Hierarchical Data Closure Table Pattern
		$results = $this->model_warehouse_product_category->getPaths($data['parent_id']);

		foreach ($results as $result) {
			$this->model_warehouse_product_category->addPath($category_id, $result['path_id'], $level);

			$level++;
		}

		$this->model_warehouse_product_category->addPath($category_id, $category_id, $level);

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $filter_id) {
				$this->model_warehouse_product_category->addFilter($category_id, $filter_id);
			}
		}

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->model_warehouse_product_category->addStore($category_id, $store_id);
			}
		}

		// Seo urls on categories need to be done differently to they include the full keyword path
		$parent_path = $this->model_warehouse_product_category->getPath($data['parent_id']);

		if (!$parent_path) {
			$path = $category_id;
		} else {
			$path = $parent_path . '_' . $category_id;
		}

		// SEO
		$this->load->model('design/seo_url');

		foreach ($data['category_seo_url'] as $store_id => $language) {
			foreach ($language as $language_id => $keyword) {
				$seo_url_info = $this->model_design_seo_url->getSeoUrlByKeyValue('path', $parent_path, $store_id, $language_id);

				if ($seo_url_info) {
					$keyword = $seo_url_info['keyword'] . '/' . $keyword;
				}

				$this->model_design_seo_url->addSeoUrl('path', $path, $keyword, $store_id, $language_id);
			}
		}

		// Set which layout to use with this category
		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				if ($layout_id) {
					$this->model_warehouse_product_category->addLayout($category_id, $store_id, $layout_id);
				}
			}
		}

		return $category_id;
	}

	/**
	 * Edit Category
	 *
	 * Edit category record in the database.
	 *
	 * @param int                  $category_id primary key of the category record
	 * @param array<string, mixed> $data        array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $category_data = [
	 *     'category_description' => [],
	 *     'image'                => 'category_image',
	 *     'parent_id'            => 0,
	 *     'sort_order'           => 0,
	 *     'status'               => 1,
	 * ];
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $this->model_warehouse_product_category->editCategory($category_id, $category_data);
	 */

	public function editCategory(int $category_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "category` SET `image` = '" . $this->db->escape((string)$data['image']) . "', `parent_id` = '" . (int)$data['parent_id'] . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (bool)($data['status'] ?? 0) . "' WHERE `category_id` = '" . (int)$category_id . "'");
		$old_specifics = $this->getSpecific($category_id);

		$this->model_warehouse_product_category->deleteDescriptions($category_id);

		foreach ($data['category_description'] as $language_id => $category_description) {
			$old_specific_data = (!empty($old_specifics[$language_id]['specifics']) && is_array($old_specifics[$language_id]['specifics'])) ? $old_specifics[$language_id]['specifics'] : [];
			$category_description['specifics'] = $this->model_warehouse_product_category->cleanSpecifics($old_specific_data, $category_description['specifics']);	
			$this->model_warehouse_product_category->addDescription($category_id, $language_id, $category_description);
		}

		// Path
		$path_old = $this->model_warehouse_product_category->getPath($category_id);

		$path_parent = '';

		if (!empty($data['parent_id'])) {
			$path_parent = $this->model_warehouse_product_category->getPath($data['parent_id']);
		}

		$path_new = $path_parent ? implode('_', [$path_parent, $category_id]) : $category_id;

		// Delete the category paths
		$this->model_warehouse_product_category->deletePaths($category_id);

		// Delete paths
		$results = $this->model_warehouse_product_category->getPathsByPathId($category_id);

		$paths = [];

		// Build new path
		$results = $this->model_warehouse_product_category->getPaths($data['parent_id']);

		foreach ($results as $result) {
			$paths[] = $result['path_id'];
		}

		// Get what's left of the nodes current path
		$results = $this->model_warehouse_product_category->getPaths($category_id);

		foreach ($results as $result) {
			$paths[] = $result['path_id'];
		}

		// Combine the paths with a new level
		$level = 0;

		foreach ($paths as $path_id) {
			$this->model_warehouse_product_category->addPath($category_id, $path_id, $level);

			$level++;
		}

		$this->model_warehouse_product_category->addPath($category_id, $category_id, $level);

		// Clean an build new path for childs
		$this->model_warehouse_product_category->repairCategories($category_id);

		// Seo urls on categories need to be done differently to they include the full keyword path
		$seo_urls = [];

		$this->load->model('design/seo_url');

		// Get parent category path and keywords
		$keywords_parent = [];

		if (!empty($data['parent_id'])) {
			$keywords_parent = $this->model_design_seo_url->getSeoUrlsByKeyValue('path', $path_parent);
		
		// Si le parent n'a pas de SEO URLs, les créer automatiquement
		if (empty($keywords_parent)) {
			$this->load->model('setting/store');
			$parent_descriptions = $this->model_warehouse_product_category->getDescriptions($data['parent_id']);

			foreach ($parent_descriptions as $language_id => $description) {
				$parent_keyword = strtolower(str_replace([' ', '&', '/'], ['-', 'and', '-'], $description['name']));
				$parent_keyword = preg_replace('/[^a-z0-9\-]/', '', $parent_keyword);
				$parent_keyword = preg_replace('/-+/', '-', $parent_keyword);
				$parent_keyword = trim($parent_keyword, '-');

				// Default store (store_id 0)
				$this->model_design_seo_url->addSeoUrl('path', $path_parent, $parent_keyword, 0, $language_id);
				$keywords_parent[0][$language_id] = $parent_keyword;

				// Additional stores
				foreach ($this->model_setting_store->getStores() as $store) {
					$this->model_design_seo_url->addSeoUrl('path', $path_parent, $parent_keyword, (int)$store['store_id'], $language_id);
					$keywords_parent[(int)$store['store_id']][$language_id] = $parent_keyword;
				}
			}
		}
	}

	// Build new category path and keywords based on parent
	foreach ($data['category_seo_url'] as $store_id => $language) {
		foreach ($language as $language_id => $keyword) {
			if ($path_parent && !empty($keywords_parent[$store_id][$language_id])) {
				$keyword = implode('/', [$keywords_parent[$store_id][$language_id], $keyword]);
			}

			$seo_urls[$store_id][$language_id][$path_new] = $keyword;
		}
	}

	// Build new child paths and keywords based on new category path and seo_url
	$keywords_old = $this->model_design_seo_url->getSeoUrlsByKeyValue('path', $path_old);

		$filter_data = [
			'filter_key'   => 'path',
			'filter_value' => $path_old . '\_%'
		];

		$results = $this->model_design_seo_url->getSeoUrls($filter_data);

		foreach ($results as $result) {
			// Replace path with new parents
			$path = implode('_', [$path_new, substr($result['value'], strlen($path_old) + 1)]);

			// Replace keyword with new parents
			$keyword = implode('/', [
				$seo_urls[$result['store_id']][$result['language_id']][$path_new], oc_substr(
					$result['keyword'],
					oc_strlen($keywords_old[$result['store_id']][$result['language_id']]) + 1
				)
			]);

			$seo_urls[$result['store_id']][$result['language_id']][$path] = $keyword;

			// Delete old childs keywords from oc_seo_url table
			$this->model_design_seo_url->deleteSeoUrlsByKeyValue('path', str_replace('_', '\_', $result['value']));
		}

		// Delete old category keywords from oc_seo_url table
		$this->model_design_seo_url->deleteSeoUrlsByKeyValue('path', str_replace('_', '\_', $path_old));

		// Insert new keywords tree into oc_seo_url table
		foreach ($seo_urls as $store_id => $language) {
			foreach ($language as $language_id => $paths) {
				foreach ($paths as $value => $keyword) {
					$this->model_design_seo_url->addSeoUrl('path', $value, $keyword, $store_id, $language_id);
				}
			}
		}

		// Filters
		$this->model_warehouse_product_category->deleteFilters($category_id);

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $filter_id) {
				$this->model_warehouse_product_category->addFilter($category_id, $filter_id);
			}
		}

		// Stores
		$this->model_warehouse_product_category->deleteStores($category_id);

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->model_warehouse_product_category->addStore($category_id, $store_id);
			}
		}

		// Layouts
		$this->model_warehouse_product_category->deleteLayouts($category_id);

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				if ($layout_id) {
					$this->model_warehouse_product_category->addLayout($category_id, $store_id, $layout_id);
				}
			}
		}
	}

	/**
	 * Delete Category
	 *
	 * Delete category record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $this->model_warehouse_product_category->deleteCategory($category_id);
	 */
	public function deleteCategory(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category` WHERE `category_id` = '" . (int)$category_id . "'");

		$this->model_warehouse_product_category->deleteDescriptions($category_id);
		$this->model_warehouse_product_category->deleteFilters($category_id);
		$this->model_warehouse_product_category->deleteStores($category_id);
		$this->model_warehouse_product_category->deleteLayouts($category_id);

		// Product
		$this->load->model('warehouse/product/product');

		$this->model_warehouse_product_product->deleteCategoriesByCategoryId($category_id);

		// Coupon
		$this->load->model('marketing/coupon');

		$this->model_marketing_coupon->deleteCategoriesByCategoryId($category_id);

		// SEO
		$this->load->model('design/seo_url');

		$path = $this->model_warehouse_product_category->getPath($category_id);

		$this->model_design_seo_url->deleteSeoUrlsByKeyValue('path', str_replace('_', '\_', $path));
		$this->model_design_seo_url->deleteSeoUrlsByKeyValue('path', str_replace('_', '\_', $path . '_%'));

		// Delete connected paths
		$results = $this->model_warehouse_product_category->getPathsByPathId($category_id);

		foreach ($results as $result) {
			if ($result['category_id'] != $category_id) {
				$this->model_warehouse_product_category->deleteCategory($result['category_id']);
			}
		}

		$this->model_warehouse_product_category->deletePaths($category_id);
		$this->cache->delete('category');
	}

	/**
	 * Repair Categories
	 *
	 * Repair any erroneous categories that are not in the category path table.
	 *
	 * @param int $parent_id primary key of the parent category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $this->model_warehouse_product_category->repairCategories();
	 */
	public function repairCategories(int $parent_id = 0): void {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category` WHERE `parent_id` = '" . (int)$parent_id . "'");

		// Delete the path below the current one
		foreach ($query->rows as $category) {
			// Delete the path below the current one
			$this->model_warehouse_product_category->deletePaths($category['category_id']);

			// Fix for records with no paths
			$level = 0;

			$paths = $this->model_warehouse_product_category->getPaths($parent_id);

			foreach ($paths as $path) {
				$this->model_warehouse_product_category->addPath($category['category_id'], $path['path_id'], $level);

				$level++;
			}

			$this->model_warehouse_product_category->addPath($category['category_id'], $category['category_id'], $level);
			$this->model_warehouse_product_category->repairCategories($category['category_id']);
		}
	}

	/**
	 * Get Category
	 *
	 * Get the record of the category record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return array<string, mixed> category record that has category ID
	 *
	 * @example
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $category_info = $this->model_warehouse_product_category->getCategory($category_id);
	 */
	public function getCategory(int $category_id):	array {
		$query = $this->db->query("SELECT DISTINCT *, 
		(SELECT GROUP_CONCAT(`cd1`.`name` ORDER BY `level` SEPARATOR ' > ') 

		FROM " . DB_PREFIX . "category_path cp 
		LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id AND cp.category_id != cp.path_id) 
			WHERE cp.category_id = c.category_id AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' 
			GROUP BY cp.category_id) AS path, 
			c.leaf 
		FROM " . DB_PREFIX . "category c 
		LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) 
		WHERE c.category_id = '" . (int)$category_id . "' 
		AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
	
		return $query->row;
	}
	public function getCategoryDEFAULT(int $category_id): array {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT GROUP_CONCAT(`cd1`.`name` ORDER BY `level` SEPARATOR ' > ') , 
			c.leaf FROM `" . DB_PREFIX . "category_path` `cp` LEFT JOIN `" . DB_PREFIX . "category_description` `cd1` ON (`cp`.`path_id` = cd1.`category_id` AND `cp`.`category_id` != `cp`.`path_id`) WHERE `cp`.`category_id` = `c`.`category_id` AND `cd1`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' GROUP BY `cp`.`category_id`) AS `path` FROM `" . DB_PREFIX . "category` `c` LEFT JOIN `" . DB_PREFIX . "category_description` `cd2` ON (`c`.`category_id` = `cd2`.`category_id`) WHERE `c`.`category_id` = '" . (int)$category_id . "' AND `cd2`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Get Categories
	 *
	 * Get the record of the category records in the database.
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<int, array<string, mixed>> category records
	 *
	 * @example
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $results = $this->model_warehouse_product_category->getCategories();
	 */

	public function getCategories(array $data = []): array {
		
		$sql = "SELECT `cp`.`category_id` AS `category_id`, `c1`.`image`, `c1`.`parent_id`, `c1`.`sort_order`, `c1`.`leaf`, `c1`.`status`, `cd2`.`specifics_error`, `cd2`.`specifics`, GROUP_CONCAT(`cd1`.`name` ORDER BY `cp`.`level` SEPARATOR ' > ') AS `name` FROM `" . DB_PREFIX . "category_path` `cp` LEFT JOIN `" . DB_PREFIX . "category` `c1` ON (`cp`.`category_id` = `c1`.`category_id`) LEFT JOIN `" . DB_PREFIX . "category` `c2` ON (`cp`.`path_id` = `c2`.`category_id`) LEFT JOIN `" . DB_PREFIX . "category_description` `cd1` ON (`cp`.`path_id` = `cd1`.`category_id`) LEFT JOIN `" . DB_PREFIX . "category_description` `cd2` ON (`cp`.`category_id` = `cd2`.`category_id`) WHERE `cd1`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `cd2`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(`cd2`.`name`) LIKE '" . $this->db->escape(oc_strtolower((string)$data['filter_name'])) . "'";
		}

		if (!empty($data['filter_category_id'])) {
			$sql .= " AND `cp`.`category_id` = '" . (int)$data['filter_category_id'] . "'";
		}

		if (isset($data['filter_parent_id'])) {
			$sql .= " AND `c1`.`parent_id` = '" . (int)$data['filter_parent_id'] . "'";
		}

		if (!empty($data['filter_specifics'])) {
			if ($data['filter_specifics'] == 1) {
				$sql .= " AND `c1`.`leaf` = 1 AND `cd2`.`specifics` IS NOT NULL";
			} elseif ($data['filter_specifics'] == 0) {
				$sql .= " AND `c1`.`leaf` = 1 AND `cd2`.`specifics` IS NULL";
			} elseif ($data['filter_specifics'] == 2) {
				$sql .= " AND `c1`.`leaf` = 1 AND `cd2`.`specifics_error` IS NOT NULL";
			}
		} elseif (isset($data['filter_leaf']) && !is_null($data['filter_leaf'])) {
			$sql .= " AND `c1`.`leaf` = '" . (int)$data['filter_leaf'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND `c1`.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
				$sql .= " AND (`c1`.`image` IS NOT NULL AND `c1`.`image` <> '' AND `c1`.`image` <> 'no_image.png')";
			} else {
				$sql .= " AND (`c1`.`image` IS NULL OR `c1`.`image` = '' OR `c1`.`image` = 'no_image.png')";
			}
		}

		$sql .= " GROUP BY `cp`.`category_id`";

		// path name filter in category list "Components > Monitors > test 1" or "Components > Monitors" or "Monitors" or "test 1"
		if (!empty($data['filter_name'])) {
			$implode = [];

			// split category path, clear > symbols and extra spaces
			$words = explode(' ', trim(preg_replace('/\s+/', ' ', str_ireplace([' &gt; ', ' > '], ' ', (string)$data['filter_name']))));

			foreach ($words as $word) {
				$implode[] = "LCASE(`name`) LIKE '" . $this->db->escape('%' . oc_strtolower($word) . '%') . "'";
			}

			if ($implode) {
				$sql .= " HAVING ((" . implode(" AND ", $implode) . ") OR LCASE(`name`) LIKE '" . $this->db->escape(oc_strtolower((string)$data['filter_name'])) . "')";
			}
		}

		$sort_data = [
			'name',
			'sort_order',
			'c1.leaf',
			'category_id',
			'c1.status'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `sort_order`";
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
	 * Get Total Categories
	 *
	 * Get the total number of category records in the database.
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return int total number of category records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'filter_name'   => 'Filter Name',
	 *     'filter_status' => 1,
	 *     'sort'          => 'name',
	 *     'order'         => 'DESC',
	 *     'start'         => 0,
	 *     'limit'         => 10
	 * ];
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $category_total = $this->model_warehouse_product_category->getTotalCategories($filter_data);
	 */

	
	public function getTotalCategories(array $data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "category` `c` LEFT JOIN `" . DB_PREFIX . "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) WHERE `cd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(`cd`.`name`) LIKE '" . $this->db->escape(oc_strtolower((string)$data['filter_name'])) . "'";
		}

		if (!empty($data['filter_category_id'])) {
			$sql .= " AND `c`.`category_id` = '" . (int)$data['filter_category_id'] . "'";
		}

		if (isset($data['filter_parent_id'])) {
			$sql .= " AND `c`.`parent_id` = '" . (int)$data['filter_parent_id'] . "'";
		}

		if (!empty($data['filter_specifics'])) {
			if ($data['filter_specifics'] == 1) {
				$sql .= " AND `c`.`leaf` = 1 AND `cd`.`specifics` IS NOT NULL";
			} elseif ($data['filter_specifics'] == 0) {
				$sql .= " AND `c`.`leaf` = 1 AND `cd`.`specifics` IS NULL";
			} elseif ($data['filter_specifics'] == 2) {
				$sql .= " AND `c`.`leaf` = 1 AND `cd`.`specifics_error` IS NOT NULL";
			}
		} elseif (isset($data['filter_leaf']) && !is_null($data['filter_leaf'])) {
			$sql .= " AND `c`.`leaf` = '" . (int)$data['filter_leaf'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND `c`.`status` = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
				$sql .= " AND (`c`.`image` IS NOT NULL AND `c`.`image` <> '' AND `c`.`image` <> 'no_image.png')";
			} else {
				$sql .= " AND (`c`.`image` IS NULL OR `c`.`image` = '' OR `c`.`image` = 'no_image.png')";
			}
		}

		$query = $this->db->query($sql);

		return (int)$query->row['total'];
	}
	/**
	 * Add Description
	 *
	 * Create a new category description record in the database.
	 *
	 * @param int                  $category_id primary key of the category record
	 * @param int                  $language_id primary key of the language record
	 * @param array<string, mixed> $data        array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $category_data['category_description'] = [
	 *     'name'             => 'Category Name',
	 *     'description'      => 'Category Description',
	 *     'meta_title'       => 'Meta Title',
	 *     'meta_description' => 'Meta Description',
	 *     'meta_keyword'     => 'Meta Keyword'
	 * ];
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->addDescription($category_id, $language_id, $category_data);
	 */
	public function addDescription(int $category_id, int $language_id, array $data): void {
		$specifics_sql = '';
		
		// Add specifics if they exist in the data
		if (isset($data['specifics']) && !empty($data['specifics'])) {
			$specifics_sql = ", `specifics` = '" . $this->db->escape(json_encode($data['specifics'])) . "'";
		}
		
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_description` SET `category_id` = '" . (int)$category_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($data['name']) . "', `description` = '" . $this->db->escape($data['description']) . "', `meta_title` = '" . $this->db->escape($data['meta_title']) . "', `meta_description` = '" . $this->db->escape($data['meta_description']) . "', `meta_keyword` = '" . $this->db->escape($data['meta_keyword']) . "'" . $specifics_sql);
	}

	/**
	 * Delete Descriptions
	 *
	 * Delete category description records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $this->model_warehouse_product_category->deleteDescriptions($category_id);
	 */
	public function deleteDescriptions(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Delete Descriptions By Language ID
	 *
	 * Delete category descriptions by language records in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('catalog/category');
	 *
	 * $this->model_warehouse_product_category->deleteDescriptionsByLanguageId($language_id);
	 */
	public function deleteDescriptionsByLanguageId(int $language_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_description` WHERE `language_id` = '" . (int)$language_id . "'");
	}

	/**
	 * Get Descriptions
	 *
	 * Get the record of the category description records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return array<int, array<string, string>> description records that have category ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $category_description = $this->model_warehouse_product_category->getDescriptions($category_id);
	 */

	public function getDescriptions(int $category_id): array {
		$category_description_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			// Decode specifics JSON if exists
			$result['specifics'] = !empty($result['specifics']) ? json_decode($result['specifics'], true) : [];
			
			$category_description_data[$result['language_id']] = $result;
		}

		return $category_description_data;
	}

	/**
	 * Get Descriptions By Language ID
	 *
	 * Get the record of the category descriptions by language records in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return array<int, array<string, string>> description records that have language ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $results = $this->model_warehouse_product_category->getDescriptionsByLanguageId($language_id);
	 */
	public function getDescriptionsByLanguageId(int $language_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_description` WHERE `language_id` = '" . (int)$language_id . "'");

		return $query->rows;
	}

	/**
	 * Add Path
	 *
	 * Create a new category path record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 * @param int $path_id     primary key of the category path record
	 * @param int $level
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->addPath($category_id, $path_id, $level);
	 */
	public function addPath(int $category_id, int $path_id, int $level): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$path_id . "', `level` = '" . (int)$level . "'");
	}

	/**
	 * Delete Paths
	 *
	 * Delete category path records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deletePaths($category_id);
	 */
	public function deletePaths(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Delete Paths By Level
	 *
	 * Delete category path record by levels in the database.
	 *
	 * @param int $category_id primary key of the category record
	 * @param int $level
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deletePathsByLevel($category_id, $level);
	 */
	public function deletePathsByLevel(int $category_id, int $level = 0): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = '" . (int)$category_id . "' AND `level` < '" . (int)$level . "'");
	}

	/**
	 * Get Path
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return string
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $path = $this->model_warehouse_product_category->getPath($category_id);
	 */
	public function getPath(int $category_id): string {
		return implode('_', array_column($this->model_warehouse_product_category->getPaths($category_id), 'path_id'));
	}

	/**
	 * Get Paths
	 *
	 * Get the record of the category path records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return array<int, array<string, mixed>> path records that have category ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $results = $this->model_warehouse_product_category->getPaths($parent_id);
	 */
	public function getPaths(int $category_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `category_id` = '" . (int)$category_id . "' ORDER BY `level` ASC");

		return $query->rows;
	}

	/**
	 * Get Paths By Path ID
	 *
	 * Get the record of the category paths by path records in the database.
	 *
	 * @param int $path_id primary key of the category path record
	 *
	 * @return array<int, array<string, mixed>> path records that have path ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $results = $this->model_warehouse_product_category->getPathsByPathId($category_id);
	 */
	public function getPathsByPathId(int $path_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE `path_id` = '" . (int)$path_id . "' ORDER BY `level` ASC");

		return $query->rows;
	}

	/**
	 * Add Filter
	 *
	 * Create a new category filter record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 * @param int $filter_id   primary key of the filter record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->addFilter($category_id, $filter_id);
	 */
	public function addFilter(int $category_id, int $filter_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_filter` SET `category_id` = '" . (int)$category_id . "', `filter_id` = '" . (int)$filter_id . "'");
	}

	/**
	 * Delete Filters
	 *
	 * Delete filter records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteFilters($category_id);
	 */
	public function deleteFilters(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_filter` WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Delete Filters By Filter ID
	 *
	 * Delete filters by filter records in the database.
	 *
	 * @param int $filter_id primary key of the filter record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteFiltersByFilterId($filter_id);
	 */
	public function deleteFiltersByFilterId(int $filter_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_filter` WHERE `filter_id` = '" . (int)$filter_id . "'");
	}

	/**
	 * Get Filters
	 *
	 * Get the record of the category filter records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return array<int, int> filter records that have category ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $filters = $this->model_warehouse_product_category->getFilters($category_id);
	 */
	public function getFilters(int $category_id): array {
		$category_filter_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_filter` WHERE `category_id` = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_filter_data[] = $result['filter_id'];
		}

		return $category_filter_data;
	}

	/**
	 * Add Store
	 *
	 * Create a new category store record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 * @param int $store_id    primary key of the store record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->addStore($category_id, $store_id);
	 */
	public function addStore(int $category_id, int $store_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_to_store` SET `category_id` = '" . (int)$category_id . "', `store_id` = '" . (int)$store_id . "'");
	}

	/**
	 * Delete Stores
	 *
	 * Delete category store records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteStores($category_id);
	 */
	public function deleteStores(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_to_store` WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Delete Stores By Store ID
	 *
	 * Delete category stores by store records in the database.
	 *
	 * @param int $store_id primary key of the store record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteStoresByStoreId($store_id);
	 */
	public function deleteStoresByStoreId(int $store_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_to_store` WHERE `store_id` = '" . (int)$store_id . "'");
	}

	/**
	 * Get Stores
	 *
	 * Get the record of the category store records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return array<int, int> store records that have category ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $category_store = $this->model_warehouse_product_category->getStores($category_id);
	 */
	public function getStores(int $category_id): array {
		$category_store_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_to_store` WHERE `category_id` = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_store_data[] = $result['store_id'];
		}

		return $category_store_data;
	}

	/**
	 * Add Layout
	 *
	 * Create a new category layout record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 * @param int $store_id    primary key of the store record
	 * @param int $layout_id   primary key of the layout record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->addLayout($category_id, $store_id, $layout_id);
	 */
	public function addLayout(int $category_id, int $store_id, int $layout_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_to_layout` SET `category_id` = '" . (int)$category_id . "', `store_id` = '" . (int)$store_id . "', `layout_id` = '" . (int)$layout_id . "'");
	}

	/**
	 * Delete Layouts
	 *
	 * Delete category layout records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteLayouts($category_id);
	 */
	public function deleteLayouts(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_to_layout` WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Delete Layouts By Layout ID
	 *
	 * Delete category layouts by layout records in the database.
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteLayoutsByLayoutId($layout_id);
	 */
	public function deleteLayoutsByLayoutId(int $layout_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");
	}

	/**
	 * Delete Layouts By Store ID
	 *
	 * Delete category layouts by store records in the database.
	 *
	 * @param int $store_id primary key of the store record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $this->model_warehouse_product_category->deleteLayoutsByStoreId($store_id);
	 */
	public function deleteLayoutsByStoreId(int $store_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "category_to_layout` WHERE `store_id` = '" . (int)$store_id . "'");
	}

	/**
	 * Get Layouts
	 *
	 * Get the record of the category layout records in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return array<int, int> layout records that have category ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $category_layout = $this->model_warehouse_product_category->getLayouts($category_id);
	 */
	public function getLayouts(int $category_id): array {
		$category_layout_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_to_layout` WHERE `category_id` = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$category_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $category_layout_data;
	}

	/**
	 * Get Total Layouts By Layout ID
	 *
	 * Get the total number of category layout by layout records in the database.
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return int total number of layout records that have layout ID
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $category_total = $this->model_warehouse_product_category->getTotalLayoutsByLayoutId($layout_id);
	 */
	public function getTotalLayoutsByLayoutId(int $layout_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "category_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Leaf Status
	 *
	 * Get the leaf status value for a specific category.
	 * Leaf categories are end-level categories that cannot have subcategories.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return int leaf status (1 for leaf category, 0 for non-leaf)
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * $leaf_status = $this->model_warehouse_product_category->getLeaf($category_id);
	 */
	public function getLeaf($category_id) {
		$query = $this->db->query("SELECT `leaf` FROM `" . DB_PREFIX . "category` WHERE `category_id` = '" . (int)$category_id . "'");

		return $query->row['leaf'];
	}

	/**
	 * Is Category Leaf
	 *
	 * Check if a specific category is a leaf (end-level) category.
	 * Returns a boolean indicating whether the category can have subcategories.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return bool true if category is a leaf, false otherwise
	 *
	 * @example
	 *
	 * $this->load->model('warehouse/product/category');
	 *
	 * if ($this->model_warehouse_product_category->isCategoryLeaf($category_id)) {
	 *     // Category is a leaf - cannot have subcategories
	 * }
	 */
	public function isCategoryLeaf($category_id) {
		$query = $this->db->query(
			"SELECT 1 FROM `" . DB_PREFIX . "category` 
			WHERE `category_id` = '" . (int)$category_id . "' 
			AND `leaf` = 1"
		);
		
		return $query->num_rows > 0;
	}

	public function getCategoriesLeaf_WRONG($language_id = 1) {
		$query = $this->db->query("SELECT `cd`.`name`, `c`.`category_id` 
		FROM `" . DB_PREFIX . "category` `c`
		LEFT JOIN `" . DB_PREFIX . "category_description` `cd` ON (`c`.`category_id` = `cd`.`category_id`) 
		WHERE `c`.`leaf` = 1 AND `cd`.`language_id` = '" . (int)$language_id . "' 
		GROUP BY `cd`.`name`, `c`.`category_id`
		ORDER BY `c`.`category_id` 
		");
		echo strlen(serialize($query->rows));
		$categories=[];
		foreach($query->rows as $row){
			$categories[$row['category_id']]=$row['name'];
		}
		echo '<br>'.strlen(serialize($categories));
		return $categories;
	
	}



	public function editSpecifics($category_id, $language_id, $data) {
	
			$this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET `specifics` = '" .  $this->db->escape($data) . "'
			WHERE `language_id` = '" . (int)$language_id . "' AND `category_id` = '" . (int)$category_id . "'");
	}

	public function getSpecific($category_id, $language_id = null) {
		$language_id_condition = (isset($language_id)) ? " AND `language_id`='" . $language_id . "' " : "";
		$category_description_data = array();
		
		$query = $this->db->query("SELECT `specifics`, `language_id` FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = '" . (int)$category_id . "' " . $language_id_condition);
		
		foreach ($query->rows as $result) {
			if (empty($result['specifics']) || is_null($result['specifics']) || $result['specifics'] === '') {
				return null; // Retourne null si specifics est vide, nul ou chaîne vide
			}
			$category_description_data[$result['language_id']] = array(
				'specifics' => json_decode($result['specifics'], true)
			);
		}
		
		// Validation: détecter les aspectValues SELECTION_ONLY FR non-traduits (identiques à EN)
		if (!$language_id && isset($category_description_data[1]['specifics']) && isset($category_description_data[2]['specifics'])) {
			$en_specifics = $category_description_data[1]['specifics'];
			$fr_specifics = $category_description_data[2]['specifics'];
			foreach ($en_specifics as $key => $en_data) {
				if (isset($en_data['aspectConstraint']['aspectMode']) &&
					$en_data['aspectConstraint']['aspectMode'] === 'SELECTION_ONLY' &&
					!empty($en_data['aspectValues'])) {
					$en_values = array_column($en_data['aspectValues'], 'localizedValue');
					$fr_values = isset($fr_specifics[$key]['aspectValues']) ? array_column($fr_specifics[$key]['aspectValues'], 'localizedValue') : [];
					if ($en_values === $fr_values) {
						// Skip aspects where all values are numeric/symbolic (no translation needed)
						$all_numeric = true;
						foreach ($en_values as $v) {
							if (preg_match('/[a-zA-Z]/', $v)) {
								$all_numeric = false;
								break;
							}
						}
						if (!$all_numeric) {
							$this->log->write('getSpecific: category_id=' . $category_id . ' aspect "' . $key . '" FR values identical to EN — needs sync refresh');
						}
					}
				}
			}
		}
		
		// Auto-compléter les aspects "Year" avec les années manquantes jusqu'à l'année courante
		$current_year = (int)date('Y');
		foreach ($category_description_data as $lang_id => &$lang_data) {
			if (empty($lang_data['specifics']) || !is_array($lang_data['specifics'])) continue;
			$updated = false;
			foreach ($lang_data['specifics'] as $key => &$aspect_data) {
				if (stripos($key, 'year') === false) continue;
				if (($aspect_data['aspectConstraint']['aspectMode'] ?? '') !== 'SELECTION_ONLY') continue;
				if (empty($aspect_data['aspectValues'])) continue;
				
				// Extraire les années numériques existantes (4 digits)
				$existing_years = [];
				foreach ($aspect_data['aspectValues'] as $av) {
					$v = $av['localizedValue'] ?? '';
					if (preg_match('/^\d{4}$/', $v)) {
						$existing_years[] = (int)$v;
					}
				}
				if (empty($existing_years)) continue;
				
				$max_year = max($existing_years);
				if ($max_year >= $current_year) continue;
				
				// Ajouter les années manquantes (max_year+1 ... current_year) en début de liste
				$new_entries = [];
				for ($y = $current_year; $y > $max_year; $y--) {
					$new_entries[] = ['localizedValue' => (string)$y];
				}
				// Insérer après le dernier élément non-numérique du début, ou au tout début
				// Les années récentes vont en premier (desc order typiquement)
				$first_val = $aspect_data['aspectValues'][0]['localizedValue'] ?? '';
				if (preg_match('/^\d{4}$/', $first_val) && (int)$first_val > $max_year - 5) {
					// Liste triée desc — insérer au début
					array_splice($aspect_data['aspectValues'], 0, 0, $new_entries);
				} else {
					// Liste triée asc ou mixte — insérer à la fin
					$aspect_data['aspectValues'] = array_merge($aspect_data['aspectValues'], array_reverse($new_entries));
				}
				$updated = true;
			}
			unset($aspect_data);
			
			if ($updated) {
				// Sauvegarder les specifics mises à jour en DB
				$json = json_encode($lang_data['specifics'], JSON_UNESCAPED_UNICODE);
				$this->db->query("UPDATE `" . DB_PREFIX . "category_description` SET `specifics` = '" . $this->db->escape($json) . "' WHERE `category_id` = '" . (int)$category_id . "' AND `language_id` = '" . (int)$lang_id . "'");
			}
		}
		unset($lang_data);
		
		return $category_description_data;
	}
	

public function cleanSpecifics($old_specifics,$specifics){

		$return_specifics=array();
		
		// Validation: $old_specifics doit être un array
		if (!is_array($old_specifics)) {
			$old_specifics = [];
		}
		
		if (!is_array($specifics)) {
			return $return_specifics;
		}
		
		foreach($specifics as $key=>$specific){
			$decoded_key = html_entity_decode($key);
			if (array_key_exists($decoded_key, $old_specifics)) {
				$return_specifics[$decoded_key] = $old_specifics[$decoded_key];
			}else{
				//error_log("cleanSpecifics - Key non trouvé: ".$key);
			}
		}
		
		return $return_specifics;
	}

	
	public function uploadImageFromLink($category_id, $piclink) {
			$uploads_dir = 'catalog/category';
			$sqldir = 'catalog/category';
	
		// Nettoyage du lien (supprimer les paramètres après .png/.jpg, etc.)
		$pics = $piclink; // Par défaut, utiliser le lien complet
		if (strpos($piclink, "?") !== false) {
			$filterimage = explode("?", ($piclink));
			$pics = $filterimage[0];
		}

		// Détermine l'extension de l'image
		$extension_new = $this->getImageExtension($pics);

		$filename = $category_id . $extension_new;
		$imagepath = DIR_IMAGE . $uploads_dir . '/' . $filename;

		// Téléchargement de l'image
		if ($this->saveImage($piclink, $imagepath)) {
			// Met à jour la base de données avec le chemin de l'image
			$this->db->query("UPDATE `" . DB_PREFIX . "category` SET `image` = '" . $this->db->escape($sqldir . '/' . $filename) . "' WHERE `category_id` = '" . (int)$category_id . "'");
			return ['success' => true, 'image_url' => $sqldir . '/' . $filename];
		} else {
			return ['success' => false, 'error' => 'Failed to download the image'];
		}
	}

	private function getImageExtension($piclink) {
		$extensions = ['.webp', '.jpeg', '.png', '.jpg', '.gif'];
			foreach ($extensions as $ext) {
				if (strpos(strtolower($piclink), $ext) !== false) {
					return '' . $ext;
				}
			}
			return '.jpg'; // Default to .jpg if no known extension is found
		}
		private function saveImage($piclink, $imagepath) {
			// Initialiser cURL
			$ch = curl_init($piclink);
			
			// Configurer les options cURL
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Suivre les redirections
		
			// Exécuter la requête
			$imageContent = curl_exec($ch);
		
			// Vérifier les erreurs cURL
			if (curl_errno($ch)) {
			// Log error instead of echo (AJAX call - no echo allowed in models)
			//error_log('DALL-E Image Download Error: ' . curl_error($ch));
			}
		
			// Fermer cURL
			
		
			// Sauvegarder l'image sur le disque
			if ($imageContent !== false) {
				return file_put_contents($imagepath, $imageContent);
			}
			return false;
		}
		public function getSpecificNameByLanguage($specific_name, $language_code) {
			// Requête SQL pour récupérer les traductions basées sur le specific_name
			$query = $this->db->query("SELECT `translations`, `exclude`, `to_translate` FROM `" . DB_PREFIX . "category_specifics` WHERE `specific_name` = '" . $this->db->escape($specific_name) . "'");
	
			if ($query->num_rows) {
				$translations = json_decode($query->row['translations'], true);
				if ($query->row['exclude']==1) {
					return 'exclude';
				}
				// Vérification de la langue dans le JSON
				if (isset($translations[$language_code])) {
					return $translations[$language_code];
				}
			}
	
			// Retourne null si aucune traduction n'est trouvée
			return null;
		}

			public function isSpecificValueToTranslated($specific_name) {
		if ($specific_name === null || $specific_name === '') {
			return 0;
		}
		// Requête SQL pour récupérer les traductions basées sur le specific_name
		$query = $this->db->query("SELECT `to_translate` FROM `" . DB_PREFIX . "category_specifics` WHERE `specific_name` = '" . $this->db->escape((string)$specific_name) . "'");
			if ($query->num_rows) {
					return $query->row['to_translate'];
			}
	
			// Retourne null si aucune traduction n'est trouvée
			return 0;
		}

		public function addSpecificTranslation($specific_name, $language_code, $translated_value) {
			// Récupérer l'entrée existante, le cas échéant
			$query = $this->db->query("SELECT `translations` FROM `" . DB_PREFIX . "category_specifics` WHERE `specific_name` = '" . $this->db->escape($specific_name) . "'");
		
			if ($query->num_rows) {
				// Si une entrée existe, mettre à jour les traductions existantes
				$translations = json_decode($query->row['translations'], true);
				$translations[$language_code] = $translated_value;
		
				// Mise à jour de la base de données avec le nouveau JSON
				$this->db->query("UPDATE `" . DB_PREFIX . "category_specifics` SET `translations` = '" . $this->db->escape(json_encode($translations)) . "', `updated_at` = NOW() WHERE `specific_name` = '" . $this->db->escape($specific_name) . "'");
			} else {
				// Si aucune entrée n'existe, insérer une nouvelle ligne
				$translations = json_encode([$language_code => $translated_value]);
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_specifics` SET `specific_name` = '" . $this->db->escape($specific_name) . "', `translations` = '" . $this->db->escape($translations) . "', `created_at` = NOW(), `updated_at` = NOW()");
			}
		}
		
		public function editSpecificTranslation($specific_name, $language_code, $translated_value) {
			// Récupérer l'entrée existante, le cas échéant
			$query = $this->db->query("SELECT `translations` FROM `" . DB_PREFIX . "category_specifics` WHERE `specific_name` = '" . $this->db->escape($specific_name) . "'");
		
			if ($query->num_rows) {
				// Si une entrée existe, récupérer les traductions actuelles
				$translations = json_decode($query->row['translations'], true);
		
				// Vérifier si la traduction pour la langue existe
				if (isset($translations[$language_code])) {
					// Mettre à jour la traduction pour cette langue
					$translations[$language_code] = $translated_value;
		
					// Mise à jour de la base de données avec le nouveau JSON
					$this->db->query("UPDATE `" . DB_PREFIX . "category_specifics` SET `translations` = '" . $this->db->escape(json_encode($translations)) . "', `updated_at` = NOW() WHERE `specific_name` = '" . $this->db->escape($specific_name) . "'");
				}
			}
		}
		public function getDetails($category_id) {
            // Construire l'URL pour l'appel du contrôleur
			$category_info = $this->model_warehouse_product_category->getCategory($category_id);
	
			if ($category_info) {
				 // Décoder les entités HTML dans le nom de la catégorie
				 $bare_name = html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8');
				 $bare_path = html_entity_decode($category_info['path'] ?? '', ENT_QUOTES, 'UTF-8');

				 // name_category = just the category name (same as initial product_form load)
				 $category_info['name_category'] = $bare_name;

				 // name = "path > name" format (same as product_categories[].name in product_form)
				 $category_info['name'] = $bare_path ? $bare_path . ' > ' . $bare_name : $bare_name;

				 // Initialiser le tableau des parents dans la catégorie
				 $category_info['parents'] = array();
		 
				 // Récupérer tous les parents de la catégorie
				 $parents = $this->model_warehouse_product_category->getPaths($category_id);
				 $category_specific_info = $this->model_warehouse_product_category->getSpecific($category_id,1);
				 $raw_specifics = $category_specific_info[1]['specifics'] ?? '{}';
				 $category_specifics = is_array($raw_specifics) ? $raw_specifics : (json_decode($raw_specifics, true) ?? []);
				 $category_specific_key = [];
				 $category_specific_names = [];
	 
				 foreach($category_specifics as $key => $specific) {
					 $value = stripslashes($key);
					 $category_specific_names[] = $value;
				 }
	 
				 // Trier $category_specific_names par ordre alphabétique
				 //sort($category_specific_names);
	 
				 $category_info['category_specific_names'] = $category_specific_names;
		
				foreach ($parents as $parent) {
					if ($parent['path_id'] != $category_id) {
						$parent_info = $this->model_warehouse_product_category->getCategory($parent['path_id']);
						if ($parent_info) {
							$category_info['parents'][] = array(
								'id' => $parent_info['category_id'],
								'name' => $parent_info['name']
							);
						}
					}
				}

			//	$category_info = json_decode($response, true);
			
				return $category_info;

			}else{
				return null;
			}
           
        }

		public function  getSpecifics($category_id ) {
		$query = $this->db->query("SELECT `specifics`, `language_id` FROM `" . DB_PREFIX . "category_description` WHERE `category_id` = '" . (int)$category_id . "'");
			$rows = $query->rows;
			$data_return = [];
		
		//	$this->load->model('warehouse/product/category');
		//	//print("<pre>".print_r ($rows,true )."</pre>");
			if(isset($rows)){
		//	$english_specifics=json_decode($rows[1]['specifics'],true);
		//$category_specifics=$this->model_warehouse_product_category->getSpecifics($category_id);
	//	//print("<pre>".print_r ($rows,true )."</pre>");
			foreach($rows as $data){

				$data_return[$data['language_id']]['specifics']=json_decode($data['specifics'] ?? '{}',true);
			}
			//print("<pre>".print_r ($data_return,true )."</pre>");
		return $data_return;
		} else {
			return [];
		}
	}
	
	public function editCategoryEbay_site_id($category_id, $site_id) {

		//print("<pre>".print_r($data, true)."</pre>");
		
		$this->db->query("UPDATE `" . DB_PREFIX . "category` SET 
		`site_id` = '".$site_id."' ,
		`date_modified` = NOW() WHERE `category_id` = '" . (int)$category_id . "'");
		$affected_rows = $this->db->countAffected();
		if ($affected_rows === 0) {
			// Aucun enregistrement n'a été mis à jour
			return 'Category ID:'.$category_id.' does not exist or no changes were made';
		}
		return 1;

	}
}
