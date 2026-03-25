<?php
class ControllerSocialAutoPilotScheduledPost extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('social_autopilot/scheduled_post');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/scheduled_post');

		$this->getList();
	}

	public function delete() {
		$this->load->language('social_autopilot/scheduled_post');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/scheduled_post');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $scheduled_post_id) {
				$this->model_social_autopilot_scheduled_post->deleteScheduledPost($scheduled_post_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_published'])) {
				$url .= '&filter_published=' . $this->request->get['filter_published'];
			}

			if (isset($this->request->get['filter_date_schedule'])) {
				$url .= '&filter_date_schedule=' . $this->request->get['filter_date_schedule'];
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

			$this->response->redirect($this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_published'])) {
			$filter_published = $this->request->get['filter_published'];
		} else {
			$filter_published = null;
		}

		if (isset($this->request->get['filter_date_schedule'])) {
			$filter_date_schedule = $this->request->get['filter_date_schedule'];
		} else {
			$filter_date_schedule = null;
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
			$sort = 'sp.scheduled_post_id';
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_published'])) {
			$url .= '&filter_published=' . $this->request->get['filter_published'];
		}

		if (isset($this->request->get['filter_date_schedule'])) {
			$url .= '&filter_date_schedule=' . $this->request->get['filter_date_schedule'];
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
			'href' => $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add'] = $this->url->link('social_autopilot/scheduled_post/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('social_autopilot/scheduled_post/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['scheduled_posts'] = array();

		$filter_data = array(
			'filter_status'               => $filter_status,
			'filter_date_schedule'        => $filter_date_schedule,
			'filter_date_added'           => $filter_date_added,
			'filter_date_modified'        => $filter_date_modified,
			'sort'                        => $sort,
			'order'                       => $order,
			'start'                       => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                       => $this->config->get('config_limit_admin')
		);

		$scheduled_post_total = $this->model_social_autopilot_scheduled_post->getTotalScheduledPosts($filter_data);

		$results = $this->model_social_autopilot_scheduled_post->getScheduledPosts($filter_data);

		foreach ($results as $result) {
			$data['scheduled_posts'][] = array(
				'scheduled_post_id' => $result['scheduled_post_id'],
				'message'           => (utf8_strlen($result['message']) >= 75) ? utf8_substr(html_entity_decode($result['message'], ENT_QUOTES, 'UTF-8'), 0, 80) . '...' : html_entity_decode($result['message'], ENT_QUOTES, 'UTF-8'),
				'link'              => html_entity_decode($result['link'], ENT_QUOTES, 'UTF-8'),
				'item_type'         => ($result['item_type']) ? $result['item_type'] : false,
				'item_id'           => ($result['item_id']) ? $result['item_id'] : false,
				'status'            => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'enabled'           => ($result['status']) ? true : false,
				'date_schedule'     => date($this->language->get('datetime_format'), strtotime($result['date_schedule'])),
				'date_added'        => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'date_modified'     => date($this->language->get('datetime_format'), strtotime($result['date_modified']))
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

		$data['column_message'] = $this->language->get('column_message');
		$data['column_link'] = $this->language->get('column_link');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_published'] = $this->language->get('column_published');
		$data['column_date_schedule'] = $this->language->get('column_date_schedule');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_published'] = $this->language->get('entry_published');
		$data['entry_date_schedule'] = $this->language->get('entry_date_schedule');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['help_publish'] = $this->language->get('help_publish');
		$data['help_progress'] = $this->language->get('help_progress');
		$data['help_fail'] = $this->language->get('help_fail');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_view'] = $this->language->get('button_view');
		$data['button_enable'] = $this->language->get('button_enable');
		$data['button_disable'] = $this->language->get('button_disable');
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_published'])) {
			$url .= '&filter_published=' . $this->request->get['filter_published'];
		}

		if (isset($this->request->get['filter_date_schedule'])) {
			$url .= '&filter_date_schedule=' . $this->request->get['filter_date_schedule'];
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

		$data['sort_status'] = $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . '&sort=sp.status' . $url, true);
		$data['sort_published'] = $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . '&sort=sp.published' . $url, true);
		$data['sort_date_schedule'] = $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . '&sort=sp.date_schedule' . $url, true);
		$data['sort_date_added'] = $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . '&sort=t.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . '&sort=t.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_published'])) {
			$url .= '&filter_published=' . $this->request->get['filter_published'];
		}

		if (isset($this->request->get['filter_date_schedule'])) {
			$url .= '&filter_date_schedule=' . $this->request->get['filter_date_schedule'];
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
		$pagination->total = $scheduled_post_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($scheduled_post_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($scheduled_post_total - $this->config->get('config_limit_admin'))) ? $scheduled_post_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $scheduled_post_total, ceil($scheduled_post_total / $this->config->get('config_limit_admin')));

		$data['filter_status'] = $filter_status;
		$data['filter_published'] = $filter_published;
		$data['filter_date_schedule'] = $filter_date_schedule;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/scheduled_post_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/scheduled_post')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function setStatus() {
		$json = array();

		if (isset($this->request->post['scheduled_post_id']) && isset($this->request->post['status'])) {
			$this->load->model('social_autopilot/scheduled_post');

			$this->model_social_autopilot_scheduled_post->setStatus($this->request->post['scheduled_post_id'], $this->request->post['status']);

			$json['success'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
