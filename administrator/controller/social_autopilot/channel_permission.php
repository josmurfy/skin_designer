<?php
class ControllerSocialAutoPilotChannelPermission extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('social_autopilot/channel_permission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/channel');
		$this->load->model('social_autopilot/channel_permission');

		$this->getList();
	}

	public function add() {

	}

	public function edit() {

	}

	public function delete() {
		$this->load->language('social_autopilot/channel_permission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/channel_permission');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $permission_id) {
				$this->model_social_autopilot_channel_permission->deletePermission($permission_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_channel_id'])) {
				$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	public function disable() {
		$this->load->language('social_autopilot/channel_permission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/channel_permission');

		if (isset($this->request->get['permission_id']) && $this->validateStatusChange()) {
			$this->model_social_autopilot_channel_permission->updateStatus($this->request->get['permission_id'], 0);


			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_channel_id'])) {
				$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	public function enable() {
		$this->load->language('social_autopilot/channel_permission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/channel_permission');

		if (isset($this->request->get['permission_id']) && $this->validateStatusChange()) {
			$this->model_social_autopilot_channel_permission->updateStatus($this->request->get['permission_id'], 1);


			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_channel_id'])) {
				$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_channel_id'])) {
			$filter_channel_id = $this->request->get['filter_channel_id'];
		} else {
			$filter_channel_id = null;
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'channel_id';
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

		if (isset($this->request->get['filter_channel_id'])) {
			$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('social_autopilot/channel', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['delete'] = $this->url->link('social_autopilot/channel_permission/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['channel_permissions'] = array();

		$filter_data = array(
			'filter_name'              => $filter_name,
			'filter_channel_id' 			=> $filter_channel_id,
			'filter_status'            => $filter_status,
			'filter_date_added'        => $filter_date_added,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                    => $this->config->get('config_limit_admin')
		);

		$channel_permission_total = $this->model_social_autopilot_channel_permission->getTotalPermissions($filter_data);

		$results = $this->model_social_autopilot_channel_permission->getPermissions($filter_data);

		foreach ($results as $result) {
			if ($result['status']) {
				$disable = $this->url->link('social_autopilot/channel_permission/disable', 'token=' . $this->session->data['token'] . '&permission_id=' . $result['permission_id'] . $url, true);
				$enable  = false;
			} else {
				$disable = false;
				$enable = $this->url->link('social_autopilot/channel_permission/enable', 'token=' . $this->session->data['token'] . '&permission_id=' . $result['permission_id'] . $url, true);
			}

			$data['channel_permissions'][] = array(
				'permission_id'       => $result['permission_id'],
				'channel_id'          => $result['channel_id'],
				'channel_name'        => $result['channel_name'],
				'channel_code'        => $result['channel_code'],
				'name'      	       => $result['name'],
				'page_id'             => $result['page_id'],  // page_id, profile_id etc
				'access_token'        => $result['access_token'],
				'access_token_secret' => $result['access_token_secret'],
				'status'              => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'          => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_expire'         => ($result['date_expire'] != '0000-00-00 00:00:00') ? date($this->language->get('datetime_format'), strtotime($result['date_expire'])) : '-',
				'enable'              => $enable,
				'disable'             => $disable
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_channel'] = $this->language->get('column_channel');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_id'] = $this->language->get('column_id');
		$data['column_access_token'] = $this->language->get('column_access_token');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_expire'] = $this->language->get('column_date_expire');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_channel'] = $this->language->get('entry_channel');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');

		$data['help_date_expire'] = $this->language->get('help_date_expire');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_enable'] = $this->language->get('button_enable');
		$data['button_disable'] = $this->language->get('button_disable');

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

		if (isset($this->request->get['filter_channel_id'])) {
			$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_channel_id'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=c.name' . $url, true);
		$data['sort_name'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=cp.name' . $url, true);
		$data['sort_id'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=cp.id' . $url, true);
		$data['sort_access_token'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=cp.access_token' . $url, true);
		$data['sort_status'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=cp.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=cp.date_added' . $url, true);
		$data['sort_date_expire'] = $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'] . '&sort=cp.date_expire' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_channel_id'])) {
			$url .= '&filter_channel_id=' . urlencode(html_entity_decode($this->request->get['filter_channel_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $channel_permission_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('social_autopilot/channel', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($channel_permission_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($channel_permission_total - $this->config->get('config_limit_admin'))) ? $channel_permission_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $channel_permission_total, ceil($channel_permission_total / $this->config->get('config_limit_admin')));

		$data['filter_channel_id'] = $filter_channel_id;
		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;

		$data['channels'] = $this->model_social_autopilot_channel->getChannels();

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/channel_permission_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/channel_permission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateStatusChange() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/channel_permission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
