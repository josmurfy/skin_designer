<?php
class ControllerExtensionModuleNivoslider extends Controller {
	private $error = array();

	public function install() {
	}

	public function uninstall() {
	}
	
	public function index() {
		$this->load->language('extension/module/nivoslider');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule('nivoslider', $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_banner'] = $this->language->get('entry_banner');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_effect'] = $this->language->get('entry_effect');
		$data['entry_slices'] = $this->language->get('entry_slices');
		$data['entry_boxcols'] = $this->language->get('entry_boxcols');
		$data['entry_boxrows'] = $this->language->get('entry_boxrows');
		$data['entry_animspeed'] = $this->language->get('entry_animspeed');
		$data['entry_pausetime'] = $this->language->get('entry_pausetime');
		$data['entry_startslide'] = $this->language->get('entry_startslide');
		$data['entry_directionnav'] = $this->language->get('entry_directionnav');
		$data['entry_controlnav'] = $this->language->get('entry_controlnav');
		$data['entry_usethumbnails'] = $this->language->get('entry_usethumbnails');
		$data['entry_pauseonhover'] = $this->language->get('entry_pauseonhover');
		$data['entry_forcemanualtrans'] = $this->language->get('entry_forcemanualtrans');
		$data['entry_prevtext'] = $this->language->get('entry_prevtext');
		$data['entry_nexttext'] = $this->language->get('entry_nexttext');
		$data['entry_theme'] = $this->language->get('entry_theme');
		$data['help_animspeed'] = $this->language->get('help_animspeed');
		$data['help_pausetime'] = $this->language->get('help_pausetime');
		$data['help_startslide'] = $this->language->get('help_startslide');
		$data['help_directionnav'] = $this->language->get('help_directionnav');
		$data['help_controlnav'] = $this->language->get('help_controlnav');
		$data['help_usethumbnails'] = $this->language->get('help_usethumbnails');
		$data['help_pauseonhover'] = $this->language->get('help_pauseonhover');

		
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
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/nivoslider', 'token=' . $this->session->data['token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/nivoslider', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/nivoslider', 'token=' . $this->session->data['token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/nivoslider', 'token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['banner_id'])) {
			$data['banner_id'] = $this->request->post['banner_id'];
		} elseif (!empty($module_info)) {
			$data['banner_id'] = $module_info['banner_id'];
		} else {
			$data['banner_id'] = '';
		}

		$this->load->model('design/banner');

		$data['banners'] = $this->model_design_banner->getBanners();

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['effect'])) {
			$data['effect'] = $this->request->post['effect'];
		} elseif (!empty($module_info)) {
			$data['effect'] = $module_info['effect'];
		} else {
			$data['effect'] = 'random';
		}
		
		if (isset($this->request->post['theme'])) {
			$data['theme'] = $this->request->post['theme'];
		} elseif (!empty($module_info)) {
			$data['theme'] = $module_info['theme'];
		} else {
			$data['theme'] = 'default';
		}
				
		if (isset($this->request->post['slices'])) {
			$data['slices'] = $this->request->post['slices'];
		} elseif (!empty($module_info)) {
			$data['slices'] = $module_info['slices'];
		} else {
			$data['slices'] = '15';
		}
		
		if (isset($this->request->post['boxcols'])) {
			$data['boxcols'] = $this->request->post['boxcols'];
		} elseif (!empty($module_info)) {
			$data['boxcols'] = $module_info['boxcols'];
		} else {
			$data['boxcols'] = '8';
		}
		
		if (isset($this->request->post['boxrows'])) {
			$data['boxrows'] = $this->request->post['boxrows'];
		} elseif (!empty($module_info)) {
			$data['boxrows'] = $module_info['boxrows'];
		} else {
			$data['boxrows'] = '4';
		}
		
		if (isset($this->request->post['animspeed'])) {
			$data['animspeed'] = $this->request->post['animspeed'];
		} elseif (!empty($module_info)) {
			$data['animspeed'] = $module_info['animspeed'];
		} else {
			$data['animspeed'] = '500';
		}
		
		if (isset($this->request->post['pausetime'])) {
			$data['pausetime'] = $this->request->post['pausetime'];
		} elseif (!empty($module_info)) {
			$data['pausetime'] = $module_info['pausetime'];
		} else {
			$data['pausetime'] = '3000';
		}
		
		if (isset($this->request->post['startslide'])) {
			$data['startslide'] = $this->request->post['startslide'];
		} elseif (!empty($module_info)) {
			$data['startslide'] = $module_info['startslide'];
		} else {
			$data['startslide'] = '0';
		}
		
		if (isset($this->request->post['nexttext'])) {
			$data['nexttext'] = $this->request->post['nexttext'];
		} elseif (!empty($module_info)) {
			$data['nexttext'] = $module_info['nexttext'];
		} else {
			$data['nexttext'] = 'Next';
		}		
		
		if (isset($this->request->post['prevtext'])) {
			$data['prevtext'] = $this->request->post['prevtext'];
		} elseif (!empty($module_info)) {
			$data['prevtext'] = $module_info['prevtext'];
		} else {
			$data['prevtext'] = 'Prev';
		}
		
		if (isset($this->request->post['directionnav'])) {
			$data['directionnav'] = $this->request->post['directionnav'];
		} elseif (!empty($module_info)) {
			$data['directionnav'] = $module_info['directionnav'];
		} else {
			$data['directionnav'] = '1';
		}
		
		if (isset($this->request->post['controlnav'])) {
			$data['controlnav'] = $this->request->post['controlnav'];
		} elseif (!empty($module_info)) {
			$data['controlnav'] = $module_info['controlnav'];
		} else {
			$data['controlnav'] = '1';
		}
		
		if (isset($this->request->post['usethumbnails'])) {
			$data['usethumbnails'] = $this->request->post['usethumbnails'];
		} elseif (!empty($module_info)) {
			$data['usethumbnails'] = $module_info['usethumbnails'];
		} else {
			$data['usethumbnails'] = '0';
		}
		
		if (isset($this->request->post['pauseonhover'])) {
			$data['pauseonhover'] = $this->request->post['pauseonhover'];
		} elseif (!empty($module_info)) {
			$data['pauseonhover'] = $module_info['pauseonhover'];
		} else {
			$data['pauseonhover'] = '1';
		}
		
		if (isset($this->request->post['forcemanualtrans'])) {
			$data['forcemanualtrans'] = $this->request->post['forcemanualtrans'];
		} elseif (!empty($module_info)) {
			$data['forcemanualtrans'] = $module_info['forcemanualtrans'];
		} else {
			$data['forcemanualtrans'] = '0';
		}
		//echo("<pre>");print_r($data);die("</pre>");
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/nivoslider', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/nivoslider')) {
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
