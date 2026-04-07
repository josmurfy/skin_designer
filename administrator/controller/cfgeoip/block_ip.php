<?php
class ControllerCfgeoipBlockIp extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('cfgeoip/block_ip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cfgeoip/block_ip');

		$this->getList();
	}
	
	public function info() {
		$this->load->model('cfgeoip/block_ip');

		if (isset($this->request->get['block_ip_id'])) {
			$block_ip_id = $this->request->get['block_ip_id'];
		} else {
			$block_ip_id = 0;
		}

		$block_ip_info = $this->model_cfgeoip_block_ip->getBlockedIp($block_ip_id);

		if ($block_ip_info) {
			$this->load->language('cfgeoip/block_ip');

			$this->document->setTitle($this->language->get('heading_title'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'], true)
			);
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_detail'] = $this->language->get('text_detail');
			
			$data['entry_user_ip'] = $this->language->get('entry_user_ip');
			$data['entry_access_page'] = $this->language->get('entry_access_page');
			$data['entry_access_date'] = $this->language->get('entry_access_date');
			$data['entry_error_msg'] = $this->language->get('entry_error_msg');
			$data['entry_country_iso_code'] = $this->language->get('entry_country_iso_code');
			$data['entry_country_name'] = $this->language->get('entry_country_name');
			$data['entry_subdivision_name'] = $this->language->get('entry_subdivision_name');
			$data['entry_subdivision_iso_code'] = $this->language->get('entry_subdivision_iso_code');
			$data['entry_postal_code'] = $this->language->get('entry_postal_code');
			$data['entry_latitude'] = $this->language->get('entry_latitude');
			$data['entry_longitude'] = $this->language->get('entry_longitude');
			
			
			$data['button_cancel'] = $this->language->get('button_cancel');

			$data['cancel'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'], true);

			$data['token'] = $this->session->data['token'];

			$data['block_ip_id'] = $this->request->get['block_ip_id'];
			
			$data['user_ip'] = $block_ip_info['user_ip'];
			$data['country_iso_code'] = $block_ip_info['country_iso_code'];
			$data['country_name'] = $block_ip_info['country_name'];
			$data['subdivision_name'] = $block_ip_info['subdivision_name'];
			$data['subdivision_iso_code'] = $block_ip_info['subdivision_iso_code'];
			$data['postal_code'] = $block_ip_info['postal_code'];
			$data['latitude'] = $block_ip_info['latitude'];
			$data['longitude'] = $block_ip_info['longitude'];
			$data['access_page'] = $block_ip_info['access_page'];
			$data['access_date'] = $block_ip_info['access_date'];
			$data['error_msg'] = $block_ip_info['error_msg'];
			
			//echo '<pre>'; print_r($block_ip_info); echo '</pre>';


			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('cfgeoip/block_ip_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}
	

	public function delete() {
		$this->load->language('cfgeoip/block_ip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('cfgeoip/block_ip');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $blocked_ip_id) {
				$this->model_cfgeoip_block_ip->deleteBlockedIp($blocked_ip_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_user_ip'])) {
				$url .= '&filter_user_ip=' . urlencode(html_entity_decode($this->request->get['filter_user_ip'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_country_iso_code'])) {
				$url .= '&filter_country_iso_code=' . urlencode(html_entity_decode($this->request->get['filter_country_iso_code'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_country_name'])) {
				$url .= '&filter_country_name=' . $this->request->get['filter_country_name'];
			}

			if (isset($this->request->get['filter_subdivision_name'])) {
				$url .= '&filter_subdivision_name=' . $this->request->get['filter_subdivision_name'];
			}

			if (isset($this->request->get['filter_subdivision_iso_code'])) {
				$url .= '&filter_subdivision_iso_code=' . $this->request->get['filter_subdivision_iso_code'];
			}

			if (isset($this->request->get['filter_access_date'])) {
				$url .= '&filter_access_date=' . $this->request->get['filter_access_date'];
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

			$this->response->redirect($this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_user_ip'])) {
			$filter_user_ip = $this->request->get['filter_user_ip'];
		} else {
			$filter_user_ip = '';
		}

		if (isset($this->request->get['filter_country_iso_code'])) {
			$filter_country_iso_code = $this->request->get['filter_country_iso_code'];
		} else {
			$filter_country_iso_code = '';
		}

		if (isset($this->request->get['filter_country_name'])) {
			$filter_country_name = $this->request->get['filter_country_name'];
		} else {
			$filter_country_name = '';
		}

		if (isset($this->request->get['filter_subdivision_name'])) {
			$filter_subdivision_name = $this->request->get['filter_subdivision_name'];
		} else {
			$filter_subdivision_name = '';
		}

		if (isset($this->request->get['filter_subdivision_iso_code'])) {
			$filter_subdivision_iso_code = $this->request->get['filter_subdivision_iso_code'];
		} else {
			$filter_subdivision_iso_code = '';
		}

		if (isset($this->request->get['filter_access_date'])) {
			$filter_access_date = $this->request->get['filter_access_date'];
		} else {
			$filter_access_date = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'access_date';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_user_ip'])) {
			$url .= '&filter_user_ip=' . urlencode(html_entity_decode($this->request->get['filter_user_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_country_iso_code'])) {
			$url .= '&filter_country_iso_code=' . urlencode(html_entity_decode($this->request->get['filter_country_iso_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_country_name'])) {
			$url .= '&filter_country_name=' . $this->request->get['filter_country_name'];
		}

		if (isset($this->request->get['filter_subdivision_name'])) {
			$url .= '&filter_subdivision_name=' . $this->request->get['filter_subdivision_name'];
		}

		if (isset($this->request->get['filter_subdivision_iso_code'])) {
			$url .= '&filter_subdivision_iso_code=' . $this->request->get['filter_subdivision_iso_code'];
		}

		if (isset($this->request->get['filter_access_date'])) {
			$url .= '&filter_access_date=' . $this->request->get['filter_access_date'];
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
			'href' => $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . $url, true)
		);
		
		$data['delete'] = $this->url->link('cfgeoip/block_ip/delete', 'user_token=' . $this->session->data['token'] . $url, true);

		
		$data['blocked_ips'] = array();

		$filter_data = array(
			'filter_user_ip'				=> $filter_user_ip,
			'filter_country_iso_code'		=> $filter_country_iso_code,
			'filter_country_name'			=> $filter_country_name,
			'filter_subdivision_name'		=> $filter_subdivision_name,
			'filter_access_date'			=> $filter_access_date,
			'filter_subdivision_iso_code'	=> $filter_subdivision_iso_code,
			'sort'                     		=> $sort,
			'order'                    		=> $order,
			'start'                    		=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    		=> $this->config->get('config_limit_admin')
		);

		$blocked_ip_total = $this->model_cfgeoip_block_ip->getTotalBlockedIp($filter_data);

		$results = $this->model_cfgeoip_block_ip->getBlockedIps($filter_data);

		foreach ($results as $result) {
			
			$data['blocked_ips'][] = array(
				'id'    => $result['id'],
				'user_ip'    => $result['user_ip'],
				'country_iso_code'           => $result['country_iso_code'],
				'country_name'          => $result['country_name'],
				'subdivision_name' => $result['subdivision_name'],
				'subdivision_iso_code'=> $result['subdivision_iso_code'],
				'access_date'     => date($this->language->get('date_format_short'), strtotime($result['access_date'])),
				'view'           => $this->url->link('cfgeoip/block_ip/info', 'user_token=' . $this->session->data['token'] . '&block_ip_id=' . $result['id'] . $url, true)
			);
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_no_results'] = $this->language->get('text_no_results');
		
		$data['entry_user_ip'] = $this->language->get('entry_user_ip');
		$data['entry_country_name'] = $this->language->get('entry_country_name');
		$data['entry_country_iso_code'] = $this->language->get('entry_country_iso_code');
		$data['entry_subdivision_name'] = $this->language->get('entry_subdivision_name');
		$data['entry_subdivision_iso_code'] = $this->language->get('entry_subdivision_iso_code');
		$data['entry_access_date'] = $this->language->get('entry_access_date');
		
		
		$data['column_user_ip'] = $this->language->get('column_user_ip');
		$data['column_country_iso_code'] = $this->language->get('column_country_iso_code');
		$data['column_country_name'] = $this->language->get('column_country_name');
		$data['column_subdivision_name'] = $this->language->get('column_subdivision_name');
		$data['column_subdivision_iso_code'] = $this->language->get('column_subdivision_iso_code');
		$data['column_access_date'] = $this->language->get('column_access_date');
		$data['column_action'] = $this->language->get('column_action');
		
		
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
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

		if (isset($this->request->get['filter_user_ip'])) {
			$url .= '&filter_user_ip=' . urlencode(html_entity_decode($this->request->get['filter_user_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_country_iso_code'])) {
			$url .= '&filter_country_iso_code=' . urlencode(html_entity_decode($this->request->get['filter_country_iso_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_country_name'])) {
			$url .= '&filter_country_name=' . $this->request->get['filter_country_name'];
		}

		if (isset($this->request->get['filter_subdivision_name'])) {
			$url .= '&filter_subdivision_name=' . $this->request->get['filter_subdivision_name'];
		}

		if (isset($this->request->get['filter_subdivision_iso_code'])) {
			$url .= '&filter_subdivision_iso_code=' . $this->request->get['filter_subdivision_iso_code'];
		}

		if (isset($this->request->get['filter_access_date'])) {
			$url .= '&filter_access_date=' . $this->request->get['filter_access_date'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_user_ip'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . '&sort=user_ip' . $url, true);
		$data['sort_country_iso_code'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . '&sort=country_iso_code' . $url, true);
		$data['sort_country_name'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . '&sort=country_name' . $url, true);
		$data['sort_subdivision_name'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . '&sort=subdivision_name' . $url, true);
		$data['sort_subdivision_iso_code'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . '&sort=subdivision_iso_code' . $url, true);
		$data['sort_access_date'] = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . '&sort=access_date' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_user_ip'])) {
			$url .= '&filter_user_ip=' . urlencode(html_entity_decode($this->request->get['filter_user_ip'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_country_iso_code'])) {
			$url .= '&filter_country_iso_code=' . urlencode(html_entity_decode($this->request->get['filter_country_iso_code'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_country_name'])) {
			$url .= '&filter_country_name=' . $this->request->get['filter_country_name'];
		}

		if (isset($this->request->get['filter_subdivision_name'])) {
			$url .= '&filter_subdivision_name=' . $this->request->get['filter_subdivision_name'];
		}

		if (isset($this->request->get['filter_subdivision_iso_code'])) {
			$url .= '&filter_subdivision_iso_code=' . $this->request->get['filter_subdivision_iso_code'];
		}

		if (isset($this->request->get['filter_access_date'])) {
			$url .= '&filter_access_date=' . $this->request->get['filter_access_date'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $blocked_ip_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('cfgeoip/block_ip', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($blocked_ip_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blocked_ip_total - $this->config->get('config_limit_admin'))) ? $blocked_ip_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blocked_ip_total, ceil($blocked_ip_total / $this->config->get('config_limit_admin')));

		$data['filter_user_ip'] = $filter_user_ip;
		$data['filter_country_iso_code'] = $filter_country_iso_code;
		$data['filter_country_name'] = $filter_country_name;
		$data['filter_subdivision_name'] = $filter_subdivision_name;
		$data['filter_subdivision_iso_code'] = $filter_subdivision_iso_code;
		$data['filter_access_date'] = $filter_access_date;
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('cfgeoip/block_ip_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'cfgeoip/block_ip')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
