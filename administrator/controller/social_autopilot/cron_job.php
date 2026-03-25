<?php
class ControllerSocialAutoPilotCronJob extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('social_autopilot/cron_job');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/cron_job');

		$this->getList();
	}

	public function add() {
		$this->load->language('social_autopilot/cron_job');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/social-autopilot.css');

		$this->load->model('social_autopilot/cron_job');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_social_autopilot_cron_job->addCronJob($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('social_autopilot/cron_job');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/social-autopilot.css');

		$this->load->model('social_autopilot/cron_job');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_social_autopilot_cron_job->editCronJob($this->request->get['cron_job_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('social_autopilot/cron_job');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/cron_job');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $cron_job_id) {
				$this->model_social_autopilot_cron_job->deleteCronJob($cron_job_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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

			$this->response->redirect($this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
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

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'cj.cron_job_id';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('social_autopilot/cron_job/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('social_autopilot/cron_job/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['cron_jobs'] = array();

		$filter_data = array(
			'filter_name'          => $filter_name,
			'filter_status'        => $filter_status,
			'filter_date_added'    => $filter_date_added,
			'filter_date_modified' => $filter_date_modified,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin')
		);

		$cron_job_total = $this->model_social_autopilot_cron_job->getTotalCronJobs();

		$results = $this->model_social_autopilot_cron_job->getCronJobs($filter_data);

		foreach ($results as $result) {
			$data['cron_jobs'][] = array(
				'cron_job_id' 	  => $result['template_id'],
				'name'           => $result['name'],
				'message'        => $result['message'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified'  => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'           => $this->url->link('social_autopilot/cron_job/edit', 'token=' . $this->session->data['token'] . '&cron_job_id=' . $result['cron_job_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . '&sort=cj.name' . $url, true);
		$data['sort_status'] = $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . '&sort=cj.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . '&sort=cj.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . '&sort=cj.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $cron_job_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($cron_job_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($cron_job_total - $this->config->get('config_limit_admin'))) ? $cron_job_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $cron_job_total, ceil($cron_job_total / $this->config->get('config_limit_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/cron_job_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['cron_job_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');

		$data['legend_when'] = $this->language->get('legend_when');
		$data['legend_what'] = $this->language->get('legend_what');
		$data['legend_where'] = $this->language->get('legend_where');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_month'] = $this->language->get('entry_month');
		$data['entry_day'] = $this->language->get('entry_day');
		$data['entry_hour'] = $this->language->get('entry_hour');
		$data['entry_minute'] = $this->language->get('entry_minute');
		$data['entry_weekday'] = $this->language->get('entry_weekday');

		$data['help_name'] = $this->language->get('help_name');
		$data['help_month'] = $this->language->get('help_month');
		$data['help_day'] = $this->language->get('help_day');
		$data['help_hour'] = $this->language->get('help_hour');
		$data['help_minute'] = $this->language->get('help_minute');
		$data['help_weekday'] = $this->language->get('help_weekday');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['month'])) {
			$data['error_month'] = $this->error['month'];
		} else {
			$data['error_month'] = '';
		}

		if (isset($this->error['day'])) {
			$data['error_day'] = $this->error['day'];
		} else {
			$data['error_day'] = '';
		}

		if (isset($this->error['hour'])) {
			$data['error_hour'] = $this->error['hour'];
		} else {
			$data['error_hour'] = '';
		}

		if (isset($this->error['minute'])) {
			$data['error_minute'] = $this->error['minute'];
		} else {
			$data['error_minute'] = '';
		}

		if (isset($this->error['weekday'])) {
			$data['error_weekday'] = $this->error['weekday'];
		} else {
			$data['error_weekday'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['template_id'])) {
			$data['action'] = $this->url->link('social_autopilot/cron_job/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('social_autopilot/cron_job/edit', 'token=' . $this->session->data['token'] . '&template_id=' . $this->request->get['template_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('social_autopilot/cron_job', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['cron_job_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$cron_job_info = $this->model_social_autopilot_cron_job->getCronJob($this->request->get['cron_job_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($cron_job_info)) {
			$data['name'] = $cron_job_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($cron_job_info)) {
			$data['status'] = $cron_job_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['month'])) {
			$data['month'] = $this->request->post['month'];
		} elseif (!empty($cron_job_info)) {
			$data['month'] = $cron_job_info['month'];
		} else {
			$data['month'] = array(1,2,3,4,5,6,7,8,9,10,11,12);
		}

		if (isset($this->request->post['day'])) {
			$data['day'] = $this->request->post['day'];
		} elseif (!empty($cron_job_info)) {
			$data['day'] = $cron_job_info['day'];
		} else {
			$data['day'] = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31);
		}

		if (isset($this->request->post['hour'])) {
			$data['hour'] = $this->request->post['hour'];
		} elseif (!empty($cron_job_info)) {
			$data['hour'] = $cron_job_info['day'];
		} else {
			$data['hour'] = array(9,10,11,12,13,14,15,16,17,18,19,20,21,22);
		}

		if (isset($this->request->post['minute'])) {
			$data['minute'] = $this->request->post['minute'];
		} elseif (!empty($cron_job_info)) {
			$data['minute'] = $cron_job_info['minute'];
		} else {
			$data['minute'] = '';
		}

		if (isset($this->request->post['weekday'])) {
			$data['weekday'] = $this->request->post['weekday'];
		} elseif (!empty($cron_job_info)) {
			$data['weekday'] = $cron_job_info['weekday'];
		} else {
			$data['weekday'] = array(1,2,3,4,5,6,7);
		}

		// --------------------------------------------------------

		$data['months_of_year'] = array();

		for ($index = 1; $index <= 12; $index++) {
			$data['months_of_year'][$index] = array(
				'code'       => $index,
				'name'       => $this->language->get('text_' . utf8_strtolower(date('F', strtotime("1 December + " . $index . " Months")))),
				'short_name' => utf8_strtoupper(utf8_substr($this->language->get('text_' . utf8_strtolower(date('F', strtotime("1 December + " . $index . " Months")))), 0, 3)),
				'days'       => date('t', strtotime("1 December + " . $index . " Months"))
			);
		}

		$data['minute_step'] = 15;

		$data['days_of_week'] = array();

		for ($index = 1; $index <= 7; $index++) {
			$data['days_of_week'][$index] = array(
				'code'  => $index,
				'name'  => $this->language->get('text_' . utf8_strtolower(date('l', strtotime("Sunday + " . $index . " Days"))))
			);
		}

		$this->load->model('social_autopilot/template_category');

		$data['template_categories'] = $this->model_social_autopilot_template_category->getTemplateCategories();

		$data['channel_permissions'] = array();

		$this->load->model('social_autopilot/channel_permission');

		$filter_data = array(
			'filter_status' => 1
		);

		$channel_permissions = $this->model_social_autopilot_channel_permission->getPermissions($filter_data);

		if ($channel_permissions) {
			foreach ($channel_permissions as $channel_permission) {
				$data['channel_permissions'][] = array(
					'permission_id' => $channel_permission['permission_id'],
					'name' 			 => $channel_permission['name'],
					'page_id'	    => $channel_permission['page_id'],
					'channel_code'  => $channel_permission['channel_code']
				);
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/cron_job_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/cron_job')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['name']) < 3 || utf8_strlen($this->request->post['name']) > 64) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/cron_job')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
