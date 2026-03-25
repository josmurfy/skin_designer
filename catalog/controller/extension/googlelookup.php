<?php
class ControllerExtensionGoogleLookup extends Controller {
	public function getcountry() {
		$this->load->model('extension/google_lookup');
		$json = array();
		if(isset($this->request->get['countryname'])){
			$countryname = $this->request->get['countryname'];
		}else{
			$countryname = '';
		}
		
		$json['country'] = $this->model_extension_google_lookup->getcountryy($countryname);
	
		$json['success'] = true;
		
	
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function getzone() {
		$this->load->model('extension/google_lookup');
		$json = array();
		if(isset($this->request->get['zonename'])){
			$zonename = $this->request->get['zonename'];
		}else{
			$zonename = '';
		}
		
		$json['zone'] = $this->model_extension_google_lookup->getzones($zonename);
		$json['success'] = true;
		
		
		$this->response->setOutput(json_encode($json));
		
	}
}