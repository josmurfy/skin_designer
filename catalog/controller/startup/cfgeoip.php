<?php
class ControllerStartupCfgeoip extends Controller {
	public function index()
	{
		// Route
		if (isset($this->request->get['route']) && $this->request->get['route'] == 'error/block_country_ip')
		{
			// Do Nothing to show Block IP Error Message 
		}
		else
		{
			$block_country_ip_status = $this->config->get('module_block_country_ip_status');
			//unset($this->session->data['block_country_ip_verified']);
			if((isset($block_country_ip_status))&&($block_country_ip_status == 1))
			{
				$block_country_ip_redirect = $this->config->get('module_block_country_ip_redirect');
				$this->load->model('extension/module/block_country_ip');
				
				if((isset($this->session->data['block_country_ip_verified']))&&($this->session->data['block_country_ip_verified'] == $this->request->server['REMOTE_ADDR']))
				{
					// Do Nothing as IP is already verified
				}
				else if($this->model_extension_module_block_country_ip->isIpBlock($this->request->server['REMOTE_ADDR']))
				{
					//Redirect if IP is already blocked
					if($block_country_ip_redirect == 1)
					{
						$this->response->redirect($this->config->get('module_block_country_ip_site'));
					}
					else
					{
						$this->response->redirect($this->url->link('error/block_country_ip', '', true));
					}
				}
				else
				{
					$block_country_ip_countries = $this->config->get('module_block_country_ip_country');
					
					if((isset($block_country_ip_countries))&&(empty($block_country_ip_countries) == false))
					{
						require_once(DIR_SYSTEM.'cfgeoip/reader.php');
						$block_country_ip_error = false;
						
						try
						{
							$record = $reader->city($this->request->server['REMOTE_ADDR']);
						}
						catch(\Exception $e)
						{
							$block_country_ip_error = true;
							$block_country_ip_error_msg = $e->getMessage();
						}
						
						if($block_country_ip_error)
						{
							// Write Code for error handling
							$blocked_data['user_ip'] = $this->request->server['REMOTE_ADDR'];
								
							$blocked_data['country_iso_code'] = NULL;
							$blocked_data['country_name'] = NULL;
							$blocked_data['subdivision_name'] = NULL;
							$blocked_data['subdivision_iso_code'] = NULL;
							$blocked_data['city_name'] = NULL;
							$blocked_data['postal_code'] = NULL;
							$blocked_data['latitude'] = NULL;
							$blocked_data['longitude'] = NULL;
							
							$blocked_data['access_page'] = (isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] === 'on' ? 'https' : 'http') . '://'.$this->request->server['HTTP_HOST'].$this->request->server['REQUEST_URI'];
							$blocked_data['error_msg'] = $block_country_ip_error_msg;
							
							$this->model_extension_module_block_country_ip->addBlockIP($blocked_data);
							
							if($block_country_ip_redirect == 1)
							{
								$this->response->redirect($this->config->get('module_block_country_ip_site'));
							}
							else
							{
								$this->response->redirect($this->url->link('error/block_country_ip', '', true));
							}
						}
						else
						{
							$this->load->model('localisation/country');
							$is_ip_block = false;
							foreach($block_country_ip_countries as $country_id)
							{
								$country_data = $this->model_localisation_country->getCountry($country_id);
								
								if($country_data['iso_code_2'] == $record->country->isoCode)
								{
									$is_ip_block = true;
									break;
								}
							}
							
							if($is_ip_block)
							{
								$blocked_data['user_ip'] = $this->request->server['REMOTE_ADDR'];
								
								$blocked_data['country_iso_code'] = $record->country->isoCode;
								$blocked_data['country_name'] = $record->country->name;
								$blocked_data['subdivision_name'] = $record->mostSpecificSubdivision->name;
								$blocked_data['subdivision_iso_code'] = $record->mostSpecificSubdivision->isoCode;
								$blocked_data['city_name'] = $record->city->name;
								$blocked_data['postal_code'] = $record->postal->code;
								$blocked_data['latitude'] = $record->location->latitude;
								$blocked_data['longitude'] = $record->location->longitude;
								
								$blocked_data['access_page'] = (isset($this->request->server['HTTPS']) && $this->request->server['HTTPS'] === 'on' ? 'https' : 'http') . '://'.$this->request->server['HTTP_HOST'].$this->request->server['REQUEST_URI'];
								$blocked_data['error_msg'] = NULL;
								
								$this->model_extension_module_block_country_ip->addBlockIP($blocked_data);
								
								if($block_country_ip_redirect == 1)
								{
									$this->response->redirect($this->config->get('module_block_country_ip_site'));
								}
								else
								{
									$this->response->redirect($this->url->link('error/block_country_ip', '', true));
								}
							}
							else
							{
								$this->session->data['block_country_ip_verified'] = $this->request->server['REMOTE_ADDR'];
							}
						}
					}
					else
					{
						// Do Nothing as No country is blocked.
					}
				}
			}
		}
	}
}
