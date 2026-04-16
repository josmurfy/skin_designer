<?php
// Original: warehouse/tools/store.php
namespace Opencart\Admin\Controller\Warehouse\Tools;

class Store extends \Opencart\System\Engine\Controller {
    
	private $error = array();
	
	protected function checkStatus($url) {
			if (!$this->customer->isLogged()) { 
				$this->session->data['redirect'] = $this->url->link($url, '', true); 
				$this->response->redirect($this->url->link('warehouse/account/login', '', true));
			}
	}
	public function add() {

		
		$this->load->model('warehouse/dashboard');
		$this->load->model('warehouse/tools/store');
		$this->load->language('warehouse/marketplace/connection');
		$data = [];
		
        $json = array();
		$data = array();
		$data = array_merge($data, $this->load->language('warehouse'));
        $url = '';
       // echo "allo331";
		$this->document->setTitle(($lang['heading_title_add'] ?? ''));
        $data= array(
            'version'=>$this->request->post['version'],
            'SS-UserName'=>$this->request->post['userkey'],
            'SS-Password'=>$this->request->post['regkey'],
            'url'=> $this->request->post['url']
        );
        //print("<pre>".print_r ($data,true )."</pre>");
        $status=$this->model_warehouse_tools_store->getStatus($data);

        if(!isset($status['result']['error'])){
            $filter= array(
                'filter_user_id' => $this->request->post['userkey']
            );
            $opencartAccount=$this->model_warehouse_tools_store->getAccount($filter);
        //print("<pre>".print_r ($opencartAccount,true )."</pre>");
            $account_info_db=$this->model_warehouse_tools_store->getUser($data);
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
                $this->model_warehouse_tools_store->editAccount($account_info);
                $this->session->data['success'] = ($lang['text_success_edit'] ?? '');

            }else{
            //print("<pre>".print_r ($account_info,true )."</pre>");
                $this->model_warehouse_tools_store->addAccount($account_info);
                $this->session->data['success'] = ($lang['text_success_add'] ?? '');
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
            
                        $this->response->redirect($this->url->link('warehouse/marketplace/connection', $url, true));
        }else{
            $json['result']=$status;//'<i class="fas fa-check-circle fa-2x" style="color:green"></i>'
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));	
        }
        
	}

    
    public function openInstruction(){
		$json = array();
        $this->load->model('tool/image');
		$this->load->language('warehouse/tools/store');
		$data['opencart']=$this->model_tool_image->resize('/catalog/marketplace/opencart.png',300, 100);
		$data['text_title']			 = ($lang['text_title'] ?? '');

        //entry
        $data['entry_version']	      = ($lang['entry_version'] ?? '');
        $data['entry_userkey']	   	  = ($lang['entry_userkey'] ?? '');
        $data['entry_regkey']		 = ($lang['entry_regkey'] ?? '');
        $data['entry_url']			  = ($lang['entry_url'] ?? '');

        //text
        $data['text_select']			= ($lang['text_select'] ?? '');
        $data['text_ver1']		       = ($lang['text_ver1'] ?? '');
        $data['text_ver2']      	    = ($lang['text_ver2'] ?? '');
        $data['text_ver23']		   = ($lang['text_ver23'] ?? '');
        $data['text_ver3']			   = ($lang['text_ver3'] ?? '');

        //error
        $data['error_version']			= ($lang['error_version'] ?? '');
        $data['error_userkey']			= ($lang['error_userkey'] ?? '');
        $data['error_regkey']	       = ($lang['error_regkey'] ?? '');
        $data['error_url']	         	= ($lang['error_url'] ?? '');
        $data['error_connection']	    = ($lang['error_connection'] ?? '');

        $data['success_connection']	    = ($lang['success_connection'] ?? '');

        $data['button_back']	        = ($lang['button_back'] ?? '');
        $data['button_testconnection']	= ($lang['button_testconnection'] ?? '');
        $data['button_cancel']	       = ($lang['button_cancel'] ?? '');
        $data['button_connect']	    = ($lang['button_connect'] ?? '');
        $data['userkey']="";
        $data['regkey']="";
        $data['url']="";
        $data['version']="";


			$json['html'] = $this->load->view('warehouse/tools/store', $data);
			//echo $json['html'];
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));	
	}
    public function testConnection(){
        $this->load->model('warehouse/tools/store');
        $json = array();
        $data= array(
            'version'=>$this->request->post['version'],
            'SS-UserName'=>$this->request->post['userkey'],
            'SS-Password'=>$this->request->post['regkey'],
            'url'=> $this->request->post['url']
        );
        $result=$this->model_warehouse_tools_store->getStatus($data);
        //print("<pre>".print_r ($result,true )."</pre>");
        $json['result']=$result;//'<i class="fas fa-check-circle fa-2x" style="color:green"></i>'
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));	
    }
}

?>