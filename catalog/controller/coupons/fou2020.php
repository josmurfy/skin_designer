<?php
class ControllerCouponsFou2020 extends Controller {
	public function index() {
		$this->session->data['coupon']="FOU2020";
		
		header("location: http://phoenixliquidation.ca"); 
	}
}