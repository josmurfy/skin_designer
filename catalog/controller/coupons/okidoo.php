<?php
class ControllerCouponsOkidoo extends Controller {
	public function index() {
		$this->session->data['coupon']="OKIDOO";
		
		header("location: http://phoenixliquidation.ca"); 
	}
}