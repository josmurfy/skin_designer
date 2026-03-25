<?php
class ControllerCommonSlider extends Controller {
	public function index() {
		// Analytics

		$this->load->language('common/slider');
		$data['text_maintext'] = $this->language->get('text_maintext');
		$data['image_front'] =$this->language->get('image_front');
		return $this->load->view('common/slider', $data);
	}
}
