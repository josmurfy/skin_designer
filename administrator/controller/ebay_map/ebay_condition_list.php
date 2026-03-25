<?php
class ControllerEbayMapEbayConditionList extends Controller {
	private $error = array();

	public function index() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_condition_list'));
		$this->document->setTitle($this->language->get('heading_title'));
		$this->getList();
	}

	public function delete() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_condition_list'));

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ebay_row_id) {
					$this->Ebayconnector->deleteEbayCondition(array('filter_row_id'=>$ebay_row_id));
			}

			$this->session->data['success'] = $this->language->get('text_success_delete');

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

			$this->response->redirect($this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_condition_list'));

		if (isset($this->request->get['filter_condition_value'])) {
			$filter_condition_value = $this->request->get['filter_condition_value'];
		} else {
			$filter_condition_value = '';
		}

		if (isset($this->request->get['filter_condition_name'])) {
			$filter_condition_name = $this->request->get['filter_condition_name'];
		} else {
			$filter_condition_name = '';
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$filter_ebay_category_name = $this->request->get['filter_ebay_category_name'];
		} else {
			$filter_ebay_category_name = '';
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$filter_oc_category_name = $this->request->get['filter_oc_category_name'];
		} else {
			$filter_oc_category_name = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pc.id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_condition_value'])) {
			$url .= '&filter_condition_value=' . urlencode(html_entity_decode($this->request->get['filter_condition_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_condition_name'])) {
			$url .= '&filter_condition_name=' . urlencode(html_entity_decode($this->request->get['filter_condition_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$url .= '&filter_oc_category_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['clear'] = $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'], true);

		$data['delete'] = $this->url->link('ebay_map/ebay_condition_list/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['token'] = $this->session->data['token'];

		$data['ebay_conditions'] = array();

		$filter_data = array(
			'filter_condition_value'	    => $filter_condition_value,
			'filter_condition_name'       => $filter_condition_name,
			'filter_ebay_category_name'	  => $filter_ebay_category_name,
			'filter_oc_category_name'	  	=> $filter_oc_category_name,
			'sort'  											=> $sort,
			'order' 											=> $order,
			'start' 											=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 											=> $this->config->get('config_limit_admin')
		);

    $ebaySites = $this->Ebayconnector->getEbaySiteList();

    $condition_total  = $this->Ebayconnector->getAllEbayConditionTotal($filter_data);

		$results  = $this->Ebayconnector->getAllEbayCondition($filter_data);

		foreach ($results as $result) {
      $ebay_site_name = '';
      if(isset($ebaySites['ebay_sites']) && isset($ebaySites['ebay_sites'][$result['ebay_site_id']])){
          $ebay_site_name = $ebaySites['ebay_sites'][$result['ebay_site_id']];
      }

			$data['ebay_conditions'][$result['id_no']] = array(
        'condition_row_id' 		    => $result['id_no'],
				'condition_value' 				=> $result['condition_value'],
				'name' 				            => $result['name'],
        'ebay_category_name' 			=> $result['ebay_category_name'],
				'oc_category_name' 		    => $result['oc_category_name'],
				'ebay_site_id'            => $result['ebay_site_id'],
        'ebay_site_name'          => $ebay_site_name,
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error_delete_category_map'])) {
			$data['error_warning'] = $this->error['error_delete_category_map'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_condition_value'])) {
			$url .= '&filter_condition_value=' . urlencode(html_entity_decode($this->request->get['filter_condition_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_condition_name'])) {
			$url .= '&filter_condition_name=' . urlencode(html_entity_decode($this->request->get['filter_condition_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$url .= '&filter_oc_category_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_condition_value']   = $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . '&sort=pcv.value' . $url, true);
		$data['sort_condition_name']    = $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . '&sort=pc.name' . $url, true);
		$data['sort_ebay_category_name']= $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . '&sort=ecat.ebay_category_name' . $url, true);
		$data['sort_oc_category_name']  = $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . '&sort=oc_category_name' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_condition_value'])) {
			$url .= '&filter_condition_value=' . urlencode(html_entity_decode($this->request->get['filter_condition_value'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_condition_name'])) {
			$url .= '&filter_condition_name=' . urlencode(html_entity_decode($this->request->get['filter_condition_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$url .= '&filter_oc_category_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $condition_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_condition_list', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($condition_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($condition_total - $this->config->get('config_limit_admin'))) ? $condition_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $condition_total, ceil($condition_total / $this->config->get('config_limit_admin')));

		$data['filter_condition_value'] 			  = $filter_condition_value;
		$data['filter_condition_name']          = $filter_condition_name;
		$data['filter_ebay_category_name'] 			= $filter_ebay_category_name;
		$data['filter_oc_category_name'] 				= $filter_oc_category_name;
		$data['sort'] 	= $sort;
		$data['order'] 	= $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ebay_map/ebay_condition_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_condition_list')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
    // if(!empty($this->request->post['selected'])){
		// 		foreach ($this->request->post['selected'] as $key => $filter_row_id) {
		// 				$deleteStatus = $this->Ebayconnector->deleteEbayCondition(array('filter_row_id' => $filter_row_id));
		// 				if(!empty($deleteStatus) && isset($deleteStatus['opencart_category_id']) && $deleteStatus['opencart_category_id']){
		// 						$this->error['error_delete_category_map'] = sprintf($this->language->get('error_delete_category_map'), $deleteStatus['ebay_category_name'], $deleteStatus['name']);
		// 				}
		// 		}
		// }
		//
    // if(!empty($this->request->post['selected'])){
    //     foreach ($this->request->post['selected'] as $ebay_row_id) {
    //       $this->Ebayconnector->deleteEbayCondition($ebay_row_id);
    //     }
    // }

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_ebay_category_name']) || isset($this->request->get['filter_condition_value']) || isset($this->request->get['filter_condition_name']) || isset($this->request->get['filter_oc_category_name'])) {

			$indexGet = '';
			if(isset($this->request->get['filter_ebay_category_name'])){
					$filter_ebay_category_name = $this->request->get['filter_ebay_category_name'];
					$indexGet = 'filter_ebay_category_name';
			}else{
					$filter_ebay_category_name = '';
			}

			if(isset($this->request->get['filter_condition_value'])){
					$filter_condition_value = $this->request->get['filter_condition_value'];
					$indexGet = 'filter_condition_value';
			}else{
					$filter_condition_value = '';
			}

			if(isset($this->request->get['filter_condition_name'])){
					$filter_condition_name = $this->request->get['filter_condition_name'];
					$indexGet = 'filter_condition_name';
			}else{
					$filter_condition_name = '';
			}

			if(isset($this->request->get['filter_oc_category_name'])){
					$filter_oc_category_name = $this->request->get['filter_oc_category_name'];
					$indexGet = 'filter_oc_category_name';
			}else{
					$filter_oc_category_name = '';
			}

			$filter_data = array(
				'filter_ebay_category_name' 	=> $filter_ebay_category_name,
				'filter_condition_value' 			=> $filter_condition_value,
				'filter_condition_name'       => $filter_condition_name,
				'filter_oc_category_name' 		=> $filter_oc_category_name,
				'sort'        => 'pc.id',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 20
			);

			$results = $this->Ebayconnector->getAllEbayCondition($filter_data);

			foreach ($results as $result) {
        if($indexGet == 'filter_condition_value'){
            $json[$result['condition_value_id']] = array(
              'condition_value_id' => $result['condition_value_id'],
              'name'        => strip_tags(html_entity_decode($result['condition_value'], ENT_QUOTES, 'UTF-8'))
            );
        }
        if($indexGet == 'filter_condition_name'){
            $json[$result['condition_id']] = array(
              'condition_id'=> $result['condition_id'],
              'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
            );
        }
        if($indexGet == 'filter_ebay_category_name'){
            $json[$result['ebay_category_id']] = array(
              'ebay_category_id' => $result['ebay_category_id'],
              'name'              => strip_tags(html_entity_decode($result['ebay_category_name'], ENT_QUOTES, 'UTF-8'))
            );
        }
        if($indexGet == 'filter_oc_category_name'){
            $json[$result['oc_category_id']] = array(
              'oc_category_id' => $result['oc_category_id'],
              'name'              => strip_tags(html_entity_decode($result['oc_category_name'], ENT_QUOTES, 'UTF-8'))
            );
        }
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
