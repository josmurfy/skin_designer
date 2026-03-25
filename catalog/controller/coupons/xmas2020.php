<?php
class ControllerCouponsXmas2020 extends Controller {
	public function index() {
		$this->session->data['coupon']="XMAS2020";
		header("location: http://phoenixliquidation.ca"); 
	}
}