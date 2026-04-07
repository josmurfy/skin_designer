<?php
class ControllerEbayMapEbaySpecificationList extends Controller {
	private $error = array();

	public function index() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_specification_list'));
		$this->document->setTitle($this->language->get('heading_title'));
		$this->getList();
	}

	public function delete() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_specification_list'));

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ebay_row_id) {
				$this->Ebayconnector->deleteEbaySpecification($ebay_row_id);
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

			$this->response->redirect($this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_specification_list'));

		if (isset($this->request->get['filter_specification_id'])) {
			$filter_specification_id = $this->request->get['filter_specification_id'];
		} else {
			$filter_specification_id = '';
		}

		if (isset($this->request->get['filter_specification_name'])) {
			$filter_specification_name = $this->request->get['filter_specification_name'];
		} else {
			$filter_specification_name = '';
		}

		if (isset($this->request->get['filter_specification_group_name'])) {
			$filter_specification_group_name = $this->request->get['filter_specification_group_name'];
		} else {
			$filter_specification_group_name = '';
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
			$sort = 'a.attribute_id';
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

		if (isset($this->request->get['filter_specification_id'])) {
			$url .= '&filter_specification_id=' . $this->request->get['filter_specification_id'];
		}

		if (isset($this->request->get['filter_specification_name'])) {
			$url .= '&filter_specification_name=' . urlencode(html_entity_decode($this->request->get['filter_specification_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_specification_group_name'])) {
			$url .= '&filter_specification_group_name=' . urlencode(html_entity_decode($this->request->get['filter_specification_group_name'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . $url, true)
		);

		$data['clear'] = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'], true);

		$data['delete'] = $this->url->link('ebay_map/ebay_specification_list/delete', 'user_token=' . $this->session->data['token'] . $url, true);

		$data['token'] = $this->session->data['token'];

		$data['ebay_specifications'] = array();

		$filter_data = array(
			'filter_specification_id'	  	=> $filter_specification_id,
			'filter_specification_name'	  => $filter_specification_name,
			'filter_specification_group_name'=> $filter_specification_group_name,
			'filter_ebay_category_name'	  => $filter_ebay_category_name,
			'filter_oc_category_name'	  	=> $filter_oc_category_name,
			'sort'  											=> $sort,
			'order' 											=> $order,
			'start' 											=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 											=> $this->config->get('config_limit_admin')
		);

    $specification_total  = $this->Ebayconnector->_getEbaySpecificationListTotal($filter_data);

		$results  = $this->Ebayconnector->_getEbaySpecificationList($filter_data);

		foreach ($results as $result) {

			$data['ebay_specifications'][$result['attribute_id']] = array(
        'specification_row_id' 		=> $result['id'].'_'.$result['attribute_id'],
				'attribute_id' 						=> $result['attribute_id'],
				'attribute_name' 				  => $result['attribute_name'],
        'attribute_group_id' 			=> $result['attribute_group_id'],
				'attribute_group_name' 		=> $result['attribute_group_name'],
				'oc_category_name'        => $result['oc_category_name'],
				'ebay_category_name'      => $result['ebay_category_name'],
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

		if (isset($this->request->get['filter_specification_id'])) {
			$url .= '&filter_specification_id=' . $this->request->get['filter_specification_id'];
		}

		if (isset($this->request->get['filter_specification_name'])) {
			$url .= '&filter_specification_name=' . urlencode(html_entity_decode($this->request->get['filter_specification_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_specification_group_name'])) {
			$url .= '&filter_specification_group_name=' . urlencode(html_entity_decode($this->request->get['filter_specification_group_name'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_attribute_id'] = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . '&sort=a.attribute_id' . $url, true);
		$data['sort_attribute_name'] = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . '&sort=ad.name' . $url, true);
		$data['sort_attribute_group_name'] = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . '&sort=agd.name' . $url, true);
		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . '&sort=sm.ebay_category_name' . $url, true);
		$data['sort_oc_category_name'] = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . '&sort=cd.name' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_specification_id'])) {
			$url .= '&filter_specification_id=' . $this->request->get['filter_specification_id'];
		}

		if (isset($this->request->get['filter_specification_name'])) {
			$url .= '&filter_specification_name=' . urlencode(html_entity_decode($this->request->get['filter_specification_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_specification_group_name'])) {
			$url .= '&filter_specification_group_name=' . urlencode(html_entity_decode($this->request->get['filter_specification_group_name'], ENT_QUOTES, 'UTF-8'));
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
		$pagination->total = $specification_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_specification_list', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($specification_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($specification_total - $this->config->get('config_limit_admin'))) ? $specification_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $specification_total, ceil($specification_total / $this->config->get('config_limit_admin')));

		$data['filter_specification_id'] 				= $filter_specification_id;
		$data['filter_specification_name'] 			= $filter_specification_name;
		$data['filter_specification_group_name']= $filter_specification_group_name;
		$data['filter_ebay_category_name'] 			= $filter_ebay_category_name;
		$data['filter_oc_category_name'] 				= $filter_oc_category_name;
		$data['sort'] 	= $sort;
		$data['order'] 	= $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ebay_map/ebay_specification_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_specification_list')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_ebay_category_name']) || isset($this->request->get['filter_specification_name']) || isset($this->request->get['filter_specification_group_name']) || isset($this->request->get['filter_oc_category_name'])) {

			$indexGet = '';
			if(isset($this->request->get['filter_ebay_category_name'])){
					$filter_ebay_category_name = $this->request->get['filter_ebay_category_name'];
					$indexGet = 'filter_ebay_category_name';
			}else{
					$filter_ebay_category_name = '';
			}

			if(isset($this->request->get['filter_specification_name'])){
					$filter_specification_name = $this->request->get['filter_specification_name'];
					$indexGet = 'filter_specification_name';
			}else{
					$filter_specification_name = '';
			}

			if(isset($this->request->get['filter_specification_group_name'])){
					$filter_specification_group_name = $this->request->get['filter_specification_group_name'];
					$indexGet = 'filter_specification_group_name';
			}else{
					$filter_specification_group_name = '';
			}

			if(isset($this->request->get['filter_oc_category_name'])){
					$filter_oc_category_name = $this->request->get['filter_oc_category_name'];
					$indexGet = 'filter_oc_category_name';
			}else{
					$filter_oc_category_name = '';
			}

			$filter_data = array(
				'filter_ebay_category_name' 			=> $filter_ebay_category_name,
				'filter_specification_name' 			=> $filter_specification_name,
				'filter_specification_group_name' => $filter_specification_group_name,
				'filter_oc_category_name' 				=> $filter_oc_category_name,
				'sort'        => 'a.attribute_id',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->Ebayconnector->_getSpecificationFilter($filter_data, $indexGet);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['filter_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
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
