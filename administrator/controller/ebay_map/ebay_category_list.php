<?php
class ControllerEbayMapEbayCategoryList extends Controller {
	private $error = array();

	public function index() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_category_list'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ebay_map/ebay_map_category');

		$this->getList();
	}

	public function delete() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_category_list'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('ebay_map/ebay_map_category');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ebay_row_id) {
				$this->model_ebay_map_ebay_map_category->deleteCategory($ebay_row_id);
			}

			$this->session->data['success'] = $this->language->get('text_success_delete_category');

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

			$this->response->redirect($this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_category_list'));

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$filter_ebay_category_name = $this->request->get['filter_ebay_category_name'];
		} else {
			$filter_ebay_category_name = '';
		}

		if (isset($this->request->get['filter_category_level'])) {
			$filter_category_level = $this->request->get['filter_category_level'];
		} else {
			$filter_category_level = '';
		}

		if (isset($this->request->get['filter_ebay_site_id'])) {
			$filter_ebay_site_id = $this->request->get['filter_ebay_site_id'];
		} else {
			$filter_ebay_site_id = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ebay_category_id';
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

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_category_level'])) {
			$url .= '&filter_category_level=' . $this->request->get['filter_category_level'];
		}

		if (isset($this->request->get['filter_ebay_site_id'])) {
			$url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
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
			'href' => $this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'] . $url, true)
		);

		$this->load->model('ebay_map/ebay_account');

		$data['ebay_accounts'] = $this->model_ebay_map_ebay_account->getEbayAccount();

		$data['clear'] = $this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'], true);

		$data['delete'] = $this->url->link('ebay_map/ebay_category_list/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['token'] = $this->session->data['token'];

		$data['ebay_categories'] = array();

		$filter_data = array(
			'filter_category_id'	  			=> $filter_category_id,
			'filter_ebay_category_name'	  => $filter_ebay_category_name,
			'filter_category_level'				=> $filter_category_level,
			'filter_ebay_site_id' 				=> $filter_ebay_site_id,
			'sort'  											=> $sort,
			'order' 											=> $order,
			'start' 											=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 											=> $this->config->get('config_limit_admin')
		);

		$data['ebaySites'] = $ebaySites = $this->Ebayconnector->getEbaySiteList();

		$ebay_category_total = $this->model_ebay_map_ebay_map_category->get_TotalEbayCategoryList($filter_data);

		$results = $this->model_ebay_map_ebay_map_category->get_EbayCategoryList($filter_data);

		foreach ($results as $result) {
				$ebay_site_name = '';
				if(isset($ebaySites['ebay_sites']) && isset($ebaySites['ebay_sites'][$result['ebay_site_id']])){
						$ebay_site_name = $ebaySites['ebay_sites'][$result['ebay_site_id']];
				}

			$data['ebay_categories'][] = array(
				'id' 											=> $result['id'],
				'ebay_category_id' 				=> $result['ebay_category_id'],
				'ebay_category_name'      => $result['ebay_category_name'],
				'ebay_category_level'     => $result['ebay_category_level'],
				'ebay_site_name'     			=> $ebay_site_name,
				// 'delete'      => $this->url->link('catalog/category/delete', 'token=' . $this->session->data['token'] . '&category_id=' . $result['category_id'] . $url, true)
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

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_category_level'])) {
			$url .= '&filter_category_level=' . $this->request->get['filter_category_level'];
		}

		if (isset($this->request->get['filter_ebay_site_id'])) {
			$url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'] . '&sort=ebay_category_name' . $url, true);
		$data['sort_ebay_category_id'] = $this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'] . '&sort=ebay_category_id' . $url, true);
		$data['sort_ebay_category_level'] = $this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'] . '&sort=ebay_category_level' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}

		if (isset($this->request->get['filter_category_level'])) {
			$url .= '&filter_category_level=' . $this->request->get['filter_category_level'];
		}

		if (isset($this->request->get['filter_ebay_site_id'])) {
			$url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $ebay_category_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_category_list', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ebay_category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ebay_category_total - $this->config->get('config_limit_admin'))) ? $ebay_category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ebay_category_total, ceil($ebay_category_total / $this->config->get('config_limit_admin')));

		$data['filter_ebay_category_name'] 	= $filter_ebay_category_name;
		$data['filter_category_id'] 				= $filter_category_id;
		$data['filter_category_level'] 			= $filter_category_level;
		$data['filter_ebay_site_id'] 				= $filter_ebay_site_id;
		$data['sort'] 	= $sort;
		$data['order'] 	= $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ebay_map/ebay_category_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_category_list')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if(!empty($this->request->post['selected'])){
				foreach ($this->request->post['selected'] as $key => $filter_row_id) {
						$deleteStatus = $this->model_ebay_map_ebay_map_category->deleteEbayCategory(array('filter_row_id' => $filter_row_id));
						if(!empty($deleteStatus) && isset($deleteStatus['opencart_category_id']) && $deleteStatus['opencart_category_id']){
								$this->error['error_delete_category_map'] = sprintf($this->language->get('error_delete_category_map'), $deleteStatus['ebay_category_name'], $deleteStatus['name']);
						}
				}
		}
		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$this->load->model('ebay_map/ebay_map_category');

			$filter_data = array(
				'filter_ebay_category_name' => $this->request->get['filter_ebay_category_name'],
				'sort'        => 'ebay_category_name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_ebay_map_ebay_map_category->get_EbayCategoryList($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['ebay_category_id'],
					'name'        => strip_tags(html_entity_decode($result['ebay_category_name'], ENT_QUOTES, 'UTF-8'))
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
