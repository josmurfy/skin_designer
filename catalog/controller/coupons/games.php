<?php
class ControllerCouponsGames extends Controller {
	public function index() {
		$this->session->data['coupon']="BLACK2020";
		
		header("location: https://phoenixliquidation.ca/search-by-category-/video-games-n-consoles"); 
	} 
}