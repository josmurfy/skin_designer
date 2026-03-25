<?php
class ControllerSocialAutoPilotTask extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('social_autopilot/task');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addScript('view/javascript/jquery/jquery.json-viewer.js');

		$this->load->model('social_autopilot/task');

		$this->getList();
	}

	public function delete() {
		$this->load->language('social_autopilot/task');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('social_autopilot/task');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $task_id) {
				$this->model_social_autopilot_task->deleteTask($task_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_channel_id'])) {
				$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
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

			$this->response->redirect($this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_channel_id'])) {
			$filter_channel_id = $this->request->get['filter_channel_id'];
		} else {
			$filter_channel_id = null;
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
			$sort = 'st.task_id';
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

		if (isset($this->request->get['filter_channel_id'])) {
			$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
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
			'href' => $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['delete'] = $this->url->link('social_autopilot/task/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['tasks'] = array();

		$filter_data = array(
			'filter_channel_id'           => $filter_channel_id,
			'filter_status'               => $filter_status,
			'filter_date_added'           => $filter_date_added,
			'filter_date_modified'        => $filter_date_modified,
			'sort'                        => $sort,
			'order'                       => $order,
			'start'                       => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                       => $this->config->get('config_limit_admin')
		);

		$task_total = $this->model_social_autopilot_task->getTotalTasks($filter_data);

		$results = $this->model_social_autopilot_task->getTasks($filter_data);

		foreach ($results as $result) {
			$channel_pages_data = array();

			$channel_pages = $this->model_social_autopilot_task->getTaskChannelPages($result['task_id']);

			if ($channel_pages) {
				foreach ($channel_pages as $channel_page) {
					$profile_link = $this->getProfileURL($channel_page);

					$channel_pages_data[] = array(
						'page_id' => $channel_page['page_id'],
						'name'    => $channel_page['page_name'],
						'href'	 => $profile_link,
					);
				}
			}

			$data['tasks'][] = array(
				'task_id' 		 => $result['task_id'],
				'request_uid'   => $result['request_uid'],
				'channel_id'    => $result['channel_id'],
				'channel_code'  => $result['channel_code'],
				'channel_name'  => $result['channel_name'],
				'message'       => (utf8_strlen($result['message']) >= 75) ? utf8_substr(html_entity_decode($result['message'], ENT_QUOTES, 'UTF-8'), 0, 80) . '...' : html_entity_decode($result['message'], ENT_QUOTES, 'UTF-8'),
				'link'          => html_entity_decode($result['link'], ENT_QUOTES, 'UTF-8'),
				'channel_pages' => $channel_pages_data,
				'processed'     => ($result['processed']) ? true : false,
				'success_rate'  => (float)$result['success_rate'],
				'show_log'      => ($result['success_rate'] == 0 || $result['success_rate'] < 100) ? true : false,
 				'date_added'    => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('datetime_format'), strtotime($result['date_modified']))
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
		$data['text_task_progress'] = $this->language->get('text_task_progress');
		$data['text_task_partial_success'] = $this->language->get('text_task_partial_success');
		$data['text_task_success'] = $this->language->get('text_task_success');
		$data['text_task_fail'] = $this->language->get('text_task_fail');

		$data['column_message'] = $this->language->get('column_message');
		$data['column_link'] = $this->language->get('column_link');
		$data['column_channel'] = $this->language->get('column_channel');
		$data['column_channel_page'] = $this->language->get('column_channel_page');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_channel'] = $this->language->get('entry_channel');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['help_progress'] = $this->language->get('help_progress');
		$data['help_fail'] = $this->language->get('help_fail');
		$data['help_success_partial'] = $this->language->get('help_success_partial');
		$data['help_success'] = $this->language->get('help_success');

		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_view_log'] = $this->language->get('button_view_log');

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

		$data['sort_channel'] = $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . '&sort=st.channel_id' . $url, true);
		$data['sort_status'] = $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . '&sort=st.success_rate' . $url, true);
		$data['sort_date_added'] = $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . '&sort=st.date_added' . $url, true);
		$data['sort_date_modified'] = $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . '&sort=st.date_modified' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_channel_id'])) {
			$url .= '&filter_channel_id=' . $this->request->get['filter_channel_id'];
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
		$pagination->total = $task_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($task_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($task_total - $this->config->get('config_limit_admin'))) ? $task_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $task_total, ceil($task_total / $this->config->get('config_limit_admin')));

		$data['filter_channel_id'] = $filter_channel_id;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->load->model('social_autopilot/channel');

		$data['channels'] = $this->model_social_autopilot_channel->getChannels();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('social_autopilot/task_list', $data));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'social_autopilot/task')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	private function getProfileURL($channel_page) {
		$profile_link = html_entity_decode($channel_page['channel_link'], ENT_QUOTES, 'UTF-8');

		if ($channel_page['channel_code'] == 'facebook') {
			$profile_link .= $channel_page['page_id'];
		}

		if ($channel_page['channel_code'] == 'twitter') {
			$profile_link .= $channel_page['page_name'];
		}

		if ($channel_page['channel_code'] == 'linkedin') {
			if ($channel_page['page_type'] == 'personal-profile') {
				$profile_link .= '';
			} else {
				$profile_link .= 'company/' . $channel_page['page_id'];
			}
		}

		if ($channel_page['channel_code'] == 'pinterest') {
			$extra_setting = unserialize($channel_page['extra']);

			$account_username = (isset($extra_setting['account']['username'])) ? $extra_setting['account']['username'] : '';
			$board_keyword = (isset($extra_setting['board']['keyword'])) ? $extra_setting['board']['keyword'] : '';

			$profile_link .= $account_username . '/' . $board_keyword;
		}

		return $profile_link;
	}

	public function getLog() {
		$this->load->language('social_autopilot/task');

		$json = array();

		$task_id = isset($this->request->post['task_id']) ? $this->request->post['task_id'] : 0;

		if ($task_id) {
			$this->load->model('social_autopilot/task');

			$task_info = $this->model_social_autopilot_task->getTask($task_id);

			if ($task_info) {
				$data['heading_task_log'] = $this->language->get('heading_task_log');

				$data['text_total_pages'] = $this->language->get('text_total_pages');
				$data['text_total_success_pages'] = $this->language->get('text_total_success_pages');
				$data['text_success_rate'] = $this->language->get('text_success_rate');

				$task_log = array();

				$data['total_pages'] = 0;
				$data['total_success_pages'] = 0;
				$data['success_rate'] = 0;

				$task_response = unserialize($task_info['response']);

				if ($task_response) {
					if (isset($task_response['total_pages'])) {
						$data['total_pages'] = $task_response['total_pages'];
					}

					if (isset($task_response['total_success_pages'])) {
						$data['total_success_pages'] = $task_response['total_success_pages'];
					}

					if (isset($task_response['success_rate'])) {
						$data['success_rate'] = (float) $task_response['success_rate'];
					}

					// check which log details to show
					if (isset($task_response['log'])) {
						$task_log = $task_response['log'];
					} elseif (isset($task_response['error'])) {
						$task_log = $task_response['error'];
					} else {
						$task_log = $task_response;
					}
				}

				$data['task_log'] = json_encode($task_log);

				$data['button_close'] = $this->language->get('button_close');

				$json['output'] = $this->load->view('social_autopilot/task_log_modal', $data);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
