<?php
class ControllerCouponsVideos extends Controller {
	public function index() {
		$this->session->data['coupon']="BLACK2020";
		
		header("location: https://phoenixliquidation.ca/search-by-category-/dvds-n-movies"); 
	}
}