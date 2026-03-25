<?php

	/********************************************************************
	Version 1.0
		RapidCart Connector for OpenCart
		Copyright 2014 Purple Turtle Productions. All rights reserved.
		license	GNU General Public License version 3 or later; see GPLv3.txt
	By: Keneto 2014-11
	********************************************************************/

class ModelModuleCartproductfeed extends Model {

	//Example function to get customer firstnames:
	function getCustomerFirstnames() {
		return;
		$query = "SELECT firstname FROM " . DB_PREFIX . "customer";
		$result = $this->db->query($query);
		return $result->rows;
	}
	
}

?>