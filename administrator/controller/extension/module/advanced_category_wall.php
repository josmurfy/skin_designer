<?php
/* === Advanced Category Wall Module v1.1.0 for OpenCart by vytasmk at gmail === */

class ControllerExtensionModuleAdvancedCategoryWall extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/advanced_category_wall');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('advanced_category_wall', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['heading_title']			= $this->language->get('heading_title');

		$data['text_edit'] 				= $this->language->get('text_edit');
		$data['text_enabled'] 			= $this->language->get('text_enabled');
		$data['text_disabled'] 			= $this->language->get('text_disabled');
		$data['text_show'] 				= $this->language->get('text_show');
		$data['text_hide'] 				= $this->language->get('text_hide');

		$data['entry_name'] 			= $this->language->get('entry_name');
		$data['entry_title'] 			= $this->language->get('entry_title');
		$data['entry_filter'] 			= $this->language->get('entry_filter');
		$data['entry_category'] 		= $this->language->get('entry_category');
		$data['entry_empty'] 			= $this->language->get('entry_empty');
		$data['entry_limit'] 			= $this->language->get('entry_limit');
		$data['entry_columns'] 			= $this->language->get('entry_columns');
		$data['entry_show_catname']		= $this->language->get('entry_show_catname');
		$data['entry_width'] 			= $this->language->get('entry_width');
		$data['entry_height'] 			= $this->language->get('entry_height');
		$data['entry_status'] 			= $this->language->get('entry_status');

		$data['help_title'] 			= $this->language->get('help_title');
		$data['help_limit'] 			= $this->language->get('help_limit');
		$data['help_empty'] 			= $this->language->get('help_empty');
		$data['help_category'] 			= $this->language->get('help_category');

		$data['button_save'] 			= $this->language->get('button_save');
		$data['button_cancel'] 			= $this->language->get('button_cancel');


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

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = '';
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/advanced_category_wall', 'token=' . $this->session->data['token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/advanced_category_wall', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/advanced_category_wall', 'token=' . $this->session->data['token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/advanced_category_wall', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}

		$data['token'] = $this->session->data['token'];

		// Load avialable languages
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		// Category filters list
		$data['filters'] = array(
			'filter_all' 		=> $this->language->get('text_filter_all'),
			'filter_selected'	=> $this->language->get('text_filter_selected')
		);

		// DEFAULT VALUES
		$default_params = array(
			'name' 			=> '',
			'title'			=> '', // Deprecated since 2019.03.24
			'title_lang'	=> array(),
			'filter'		=> 'filter_all',
			'category'		=> array(),
			'show_empty'	=> 1,
			'show_catname'	=> 1,
			'limit' 		=> 0,
			'columns' 		=> 4,
			'width' 		=> 200,
			'height' 		=> 200,
			'status' 		=> ''
		);

		// Set all parameters to POST, saved or default values
		foreach ($default_params as $param_name => $param_default_val) {
			$data[$param_name] = 
				(isset($this->request->post[$param_name])) 
					? $this->request->post[$param_name] 
					: (isset($module_info[$param_name]))
						? $module_info[$param_name]
						: $param_default_val;
		}

		// Selected category list with additional data for template
		$data['categories'] = array();

		// If category list is not empty
		if (!empty($data['category'])) {
			// Load category module
			$this->load->model('catalog/category');
			// Fill category data
			foreach ($data['category'] as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);
				if ($category_info) {
					$data['categories'][] = array(
						'category_id'	=> $category_info['category_id'],
						'name'  		=> $category_info['name']
					);
				}
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/advanced_category_wall.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/advanced_category_wall')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}
}