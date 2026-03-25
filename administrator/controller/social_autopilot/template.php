<?php
class ControllerSocialAutoPilotTemplate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('social_autopilot/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/template');

		$this->getList();
	}

	public function add() {
		$this->load->language('social_autopilot/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/template');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_social_autopilot_template->addTemplate($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_template_category_id'])) {
				$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_default'])) {
				$url .= '&filter_default=' . $this->request->get['filter_default'];
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

			$this->response->redirect($this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('social_autopilot/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/template');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_social_autopilot_template->editTemplate($this->request->get['template_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_template_category_id'])) {
				$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_default'])) {
				$url .= '&filter_default=' . $this->request->get['filter_default'];
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

			$this->response->redirect($this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('social_autopilot/template');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/template');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $template_id) {
				$this->model_social_autopilot_template->deleteTemplate($template_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_template_category_id'])) {
				$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_default'])) {
				$url .= '&filter_default=' . $this->request->get['filter_default'];
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

			$this->response->redirect($this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_template_category_id'])) {
			$filter_template_category_id = $this->request->get['filter_template_category_id'];
		} else {
			$filter_template_category_id = null;
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

		if (isset($this->request->get['filter_default'])) {
			$filter_default = $this->request->get['filter_default'];
		} else {
			$filter_default = null;
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
			$sort = 't.template_id';
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

		if (isset($this->request->get['filter_template_category_id'])) {
			$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_default'])) {
			$url .= '&filter_default=' . $this->request->get['filter_default'];
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
			'href' => $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('social_autopilot/template/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('social_autopilot/template/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['templates'] = array();

		$filter_data = array(
			'filter_template_category_id' => $filter_template_category_id,
			'filter_name'                => $filter_name,
			'filter_status'               => $filter_status,
			'filter_default'              => $filter_default,
			'filter_date_added'           => $filter_date_added,
			'filter_date_modified'        => $filter_date_modified,
			'sort'                        => $sort,
			'order'                       => $order,
			'start'                       => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                       => $this->config->get('config_limit_admin')
		);

		$template_total = $this->model_social_autopilot_template->getTotalTemplates($filter_data);

		$results = $this->model_social_autopilot_template->getTemplates($filter_data);

		foreach ($results as $result) {
			$data['templates'][] = array(
				'template_id' 	  => $result['template_id'],
				'category_id' 	  => $result['template_category_id'],
				'category_code'  => $result['category_code'],
				'category_name'  => $result['category_name'],
				'name'           => $result['name'],
				'message'        => $result['message'],
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'default'        => ($result['default'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'edit'           => $this->url->link('social_autopilot/template/edit', 'token=' . $this->session->data['token'] . '&template_id=' . $result['template_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_category'] = $this->language->get('column_category');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_default'] = $this->language->get('column_default');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_default'] = $this->language->get('entry_default');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

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

		if (isset($this->request->get['filter_template_category_id'])) {
			$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_default'])) {
			$url .= '&filter_default=' . $this->request->get['filter_default'];
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

		$data['sort_name'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . '&sort=t.name' . $url, true);
		$data['sort_template_category_id'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . '&sort=t.template_category_id' . $url, true);
		$data['sort_status'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . '&sort=t.status' . $url, true);
		$data['sort_default'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . '&sort=t.default' . $url, true);
		$data['sort_date_added'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . '&sort=t.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . '&sort=t.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_template_category_id'])) {
			$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_default'])) {
			$url .= '&filter_default=' . $this->request->get['filter_default'];
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
		$pagination->total = $template_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($template_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($template_total - $this->config->get('config_limit_admin'))) ? $template_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $template_total, ceil($template_total / $this->config->get('config_limit_admin')));

		$data['filter_template_category_id'] = $filter_template_category_id;
		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_default'] = $filter_default;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('social_autopilot/channel');

		$data['channels'] = $this->model_social_autopilot_channel->getChannels();

		$this->load->model('social_autopilot/template_category');

		$data['template_categories'] = $this->model_social_autopilot_template_category->getTemplateCategories();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/template_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['template_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_select'] = $this->language->get('text_select');

		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_default'] = $this->language->get('entry_default');

		$data['help_channel'] = $this->language->get('help_channel');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_name'] = $this->language->get('help_name');
		$data['help_default'] = $this->language->get('help_default');

		$data['help_special_keyword'] = $this->language->get('help_special_keyword');

		$this->load->model('social_autopilot/template_category');

		$template_categories = $this->model_social_autopilot_template_category->getTemplateCategories();

		if ($template_categories) {
			foreach ($template_categories as $template_category) {
				$data['help_' . $template_category['code'] . '_special_keyword'] = $this->language->get('help_' . $template_category['code'] . '_special_keyword');
			}
		}

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['template_category_id'])) {
			$data['error_template_category_id'] = $this->error['template_category_id'];
		} else {
			$data['error_template_category_id'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['message'])) {
			$data['error_message'] = $this->error['message'];
		} else {
			$data['error_message'] = '';
		}

		if (isset($this->error['status'])) {
			$data['error_status'] = $this->error['status'];
		} else {
			$data['error_status'] = '';
		}

		if (isset($this->error['default'])) {
			$data['error_default'] = $this->error['default'];
		} else {
			$data['error_default'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_template_category_id'])) {
			$url .= '&filter_template_category_id=' . $this->request->get['filter_template_category_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_default'])) {
			$url .= '&filter_default=' . $this->request->get['filter_default'];
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
			'href' => $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['template_id'])) {
			$data['action'] = $this->url->link('social_autopilot/template/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('social_autopilot/template/edit', 'token=' . $this->session->data['token'] . '&template_id=' . $this->request->get['template_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['template_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$template_info = $this->model_social_autopilot_template->getTemplate($this->request->get['template_id']);
		}

		$data['token'] = $this->session->data['token'];

		if (isset($this->request->post['template_category_id'])) {
			$data['template_category_id'] = $this->request->post['template_category_id'];
		} elseif (!empty($template_info)) {
			$data['template_category_id'] = $template_info['template_category_id'];
		} else {
			$data['template_category_id'] = '';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($template_info)) {
			$data['name'] = $template_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['message'])) {
			$data['message'] = $this->request->post['message'];
		} elseif (!empty($template_info)) {
			$data['message'] = $template_info['message'];
		} else {
			$data['message'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($template_info)) {
			$data['status'] = $template_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['default'])) {
			$data['default'] = $this->request->post['default'];
		} elseif (!empty($template_info)) {
			$data['default'] = $template_info['default'];
		} else {
			$data['default'] = true;
		}

		$this->load->model('social_autopilot/channel');

		$data['channels'] = $this->model_social_autopilot_channel->getChannels();

		// used up in help special keywords
		$data['template_categories'] = $template_categories;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/template_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['template_category_id']) < 1) {
			$this->error['template_category_id'] = $this->language->get('error_template_category_id');
		}

		if (utf8_strlen($this->request->post['name']) < 3 || utf8_strlen($this->request->post['name']) > 64) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (utf8_strlen($this->request->post['message']) < 3) {
			$this->error['message'] = $this->language->get('error_message');
		}

		if ($this->request->post['default'] && !$this->request->post['status']) {
			$this->error['status'] = $this->language->get('error_status');
		}

		if ($this->request->post['template_category_id'] && !$this->request->post['default']) {
			$total_category_templates = $this->model_social_autopilot_template->getTotalTemplatesByCategoryId($this->request->post['template_category_id']);

			if (!$total_category_templates) {
				$this->error['default'] = $this->language->get('error_default_first');
			}

			// on edit current template_id is in GET
			if (isset($this->request->get['template_id'])) {
				$current_template_id = $this->request->get['template_id'];
				$default_template_id = $this->model_social_autopilot_template->getDefaultTemplateIdByCategoryId($this->request->post['template_category_id']);

				if ($current_template_id == $default_template_id) {
					$this->error['default'] = $this->language->get('error_default_change');
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['selected'] as $template_id) {
			if ($this->model_social_autopilot_template->isDefault($template_id)) {
				$this->error['warning'] = $this->language->get('error_default_delete');
			}
		}

		return !$this->error;
	}
}
