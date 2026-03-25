<?php

namespace Opencart\Catalog\Model\Extension\Mpproductsgrid;

class Category extends \Opencart\System\Engine\Model {
	public function getCategoryPath(int $category_id): array {

		$path = [];
		$query = $this->db->query("SELECT * FROM `". DB_PREFIX ."category_path` WHERE category_id = '". (int)$category_id ."' ORDER BY `level` ASC ");
		foreach ($query->rows as $row) {
			if($row['path_id'] != $category_id) {
				$path[] = $row['path_id'];
			}
		}
		return $path;
	}
}