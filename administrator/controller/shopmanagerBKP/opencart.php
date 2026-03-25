<?php
class ControllerShopmanagerOpencart extends Controller {
    
	private $error = array();
	
	protected function checkStatus($url) {
			if (!$this->customer->isLogged()) { 
				$this->session->data['redirect'] = $this->url->link($url, '', true); 
				$this->response->redirect($this->url->link('shopmanager/account/login', '', true));
			}
	}
	public function add() {

		
		$this->load->model('shopmanager/dashboard');
		$this->load->model('shopmanager/opencart');
		$this->language->load('shopmanager/connect');
        $json = array();
		$data = array();
		$data = array_merge($data, $this->load->language('shopmanager'));
        $url = '';
       // echo "allo331";
		$this->document->setTitle($this->language->get('heading_title_add'));
        $data= array(
            'version'=>$this->request->post['version'],
            'SS-UserName'=>$this->request->post['userkey'],
            'SS-Password'=>$this->request->post['regkey'],
            'url'=> $this->request->post['url']
        );
        //print("<pre>".print_r ($data,true )."</pre>");
        $status=$this->model_shopmanager_opencart->getStatus($data);

        if(!isset($status['result']['error'])){
            $filter= array(
                'filter_user_id' => $this->request->post['userkey']
            );
            $opencartAccount=$this->model_shopmanager_opencart->getAccount($filter);
        //print("<pre>".print_r ($opencartAccount,true )."</pre>");
            $account_info_db=$this->model_shopmanager_opencart->getUser($data);
            $account_info_db['connector_sites']=0;
            $account_info_db['url']=$this->request->post['url'];
            $account_info_db['version']=$this->request->post['version'];
            $account_info=array(
                'connector_user_id'=>$this->request->post['userkey'],
                'connector_auth_token'=>$this->request->post['regkey'],
                'connector_store_name'=>$account_info_db['config_name']? $account_info_db['config_name']:$account_info_db['config_owner'],
                'marketplace_id'=>8,
                'value'=>$account_info_db
            );
            //print("<pre>".print_r ($account_info_db,true )."</pre>");
            
            if(isset($opencartAccount[0]['id'])){
            //	echo "existant";
                $account_info['id']=$opencartAccount[0]['id'];
            //	//print("<pre>".print_r ($account_info,true )."</pre>");
                $this->model_shopmanager_opencart->editAccount($account_info);
                $this->session->data['success'] = $this->language->get('text_success_edit');

            }else{
            //print("<pre>".print_r ($account_info,true )."</pre>");
                $this->model_shopmanager_opencart->addAccount($account_info);
                $this->session->data['success'] = $this->language->get('text_success_add');
            }
            $url = '';

                        if (isset($this->request->get['sort'])) {
                            $url .= '&sort=' . $this->request->get['sort'];
                        }

                        if (isset($this->request->get['order'])) {
                            $url .= '&order=' . $this->request->get['order'];
                        }

                        if (isset($this->request->get['page'])) {
                            $url .= '&page=' . $this->request->get['page'];
                        }
            
                        $this->response->redirect($this->url->link('shopmanager/connect', $url, true));
        }else{
            $json['result']=$status;//'<i class="fas fa-check-circle fa-2x" style="color:green"></i>'
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));	
        }
        
	}

    
    public function openInstruction(){
		$json = array();
        $this->load->model('tool/image');
		$data = $this->load->language('shopmanager/opencart');
		$data['opencart']=$this->model_tool_image->resize('/catalog/marketplace/opencart.png',300, 100);
		$data['text_title']			 = $this->language->get('text_title');

        //entry
        $data['entry_version']	      = $this->language->get('entry_version');
        $data['entry_userkey']	   	  = $this->language->get('entry_userkey');
        $data['entry_regkey']		 = $this->language->get('entry_regkey');
        $data['entry_url']			  = $this->language->get('entry_url');

        //text
        $data['text_select']			= $this->language->get('text_select');
        $data['text_ver1']		       = $this->language->get('text_ver1');
        $data['text_ver2']      	    = $this->language->get('text_ver2');
        $data['text_ver23']		   = $this->language->get('text_ver23');
        $data['text_ver3']			   = $this->language->get('text_ver3');

        //error
        $data['error_version']			= $this->language->get('error_version');
        $data['error_userkey']			= $this->language->get('error_userkey');
        $data['error_regkey']	       = $this->language->get('error_regkey');
        $data['error_url']	         	= $this->language->get('error_url');
        $data['error_connection']	    = $this->language->get('error_connection');

        $data['success_connection']	    = $this->language->get('success_connection');

        $data['button_back']	        = $this->language->get('button_back');
        $data['button_testconnection']	= $this->language->get('button_testconnection');
        $data['button_cancel']	       = $this->language->get('button_cancel');
        $data['button_connect']	    = $this->language->get('button_connect');
        $data['userkey']="";
        $data['regkey']="";
        $data['url']="";
        $data['version']="";


			$json['html'] = $this->load->view('shopmanager/opencart', $data);
			//echo $json['html'];
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));	
	}
    public function testConnection(){
        $this->load->model('shopmanager/opencart');
        $json = array();
        $data= array(
            'version'=>$this->request->post['version'],
            'SS-UserName'=>$this->request->post['userkey'],
            'SS-Password'=>$this->request->post['regkey'],
            'url'=> $this->request->post['url']
        );
        $result=$this->model_shopmanager_opencart->getStatus($data);
        //print("<pre>".print_r ($result,true )."</pre>");
        $json['result']=$result;//'<i class="fas fa-check-circle fa-2x" style="color:green"></i>'
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));	
    }
}

?>