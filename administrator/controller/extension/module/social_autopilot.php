<?php
class ControllerExtensionModuleSocialAutoPilot extends Controller {
	private $version = '1.5.2';
	private $error = array();

	public function install() {
		$this->load->model('extension/module/social_autopilot');
		$this->load->model('extension/event');

		$this->model_extension_module_social_autopilot->createTables();

		$this->model_extension_event->addEvent('social_autopilot', 'admin/view/common/column_left/before', 'extension/module/social_autopilot/eventMenu');
		$this->model_extension_event->addEvent('social_autopilot', 'admin/controller/common/header/before', 'extension/module/social_autopilot/eventHeaderSetup');

		// ACTION EVENTS
		// - Products
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/product/addProduct/after', 'social_autopilot/event/eventAddProduct');
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/product/editProduct/after', 'social_autopilot/event/eventEditProduct');

		// - Reviews
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/review/addReview/after', 'social_autopilot/event/eventAddReview');
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/review/editReview/after', 'social_autopilot/event/eventEditReview');

		// - Categories
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/category/addCategory/after', 'social_autopilot/event/eventAddCategory');
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/category/editCategory/after', 'social_autopilot/event/eventEditCategory');

		// - Infomations
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/information/addInformation/after', 'social_autopilot/event/eventAddInformation');
		$this->model_extension_event->addEvent('social_autopilot', 'admin/model/catalog/information/editInformation/after', 'social_autopilot/event/eventEditInformation');
	}

	public function uninstall() {
      $this->load->model('extension/module/social_autopilot');
		$this->load->model('extension/event');

		$this->model_extension_module_social_autopilot->removeTables();

      $this->model_extension_event->deleteEvent('social_autopilot');
	}

	public function index() {
		$this->load->language('extension/module/social_autopilot');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/social-autopilot.css');

		// check update | if new version is available
		$this->document->addScript('https://www.oc-extensions.com/catalog/view/javascript/api/js/update.min.js?extension_version=' . $this->version . '&oc_version=' . VERSION . '&email=' . $this->config->get('config_email'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('social_autopilot', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title') . ' ' . $this->version;

		// Tabs
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_help'] = $this->language->get('tab_help');

		// Legend
		$data['legend_language'] = $this->language->get('legend_language');
		$data['legend_autopost'] = $this->language->get('legend_autopost');
		$data['legend_rating_star_code'] = $this->language->get('legend_rating_star_code');
		$data['legend_timezone'] = $this->language->get('legend_timezone');

		// Text
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_select'] = $this->language->get('text_select');

		$data['text_pixel'] = $this->language->get('text_pixel');

		$data['text_mode_manual'] = $this->language->get('text_mode_manual');
		$data['text_mode_auto'] = $this->language->get('text_mode_auto');
		$data['text_mode_ask'] = $this->language->get('text_mode_ask');

		$data['text_minute'] = $this->language->get('text_minute');

		// Entry
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_api_key'] = $this->language->get('entry_api_key');
		$data['entry_language'] = $this->language->get('entry_language');
		$data['entry_autopost'] = $this->language->get('entry_autopost');
		$data['entry_rating_star_code'] = $this->language->get('entry_rating_star_code');
		$data['entry_timezone_difference'] = $this->language->get('entry_timezone_difference');

		$data['entry_image_width'] = $this->language->get('entry_image_width');
		$data['entry_image_height'] = $this->language->get('entry_image_height');

		// Help
		$data['help_status'] = $this->language->get('help_status');
		$data['help_api_key'] = $this->language->get('help_api_key');

		$data['help_rating_star_code'] = $this->language->get('help_rating_star_code');
		$data['help_timezone_difference'] = $this->language->get('help_timezone_difference');
		$data['help_image_width'] = $this->language->get('help_image_width');
		$data['help_image_height'] = $this->language->get('help_image_height');

		$this->load->model('social_autopilot/timer');

		$data['help_mysql_time'] = sprintf($this->language->get('help_mysql_time'), $this->model_social_autopilot_timer->getNow());

		// Buttons
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		// Error Messages
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['api_key'])) {
			$data['error_api_key'] = $this->error['api_key'];
		} else {
			$data['error_api_key'] = '';
		}

		if (isset($this->error['rating_star_code'])) {
			$data['error_rating_star_code'] = $this->error['rating_star_code'];
		} else {
			$data['error_rating_star_code'] = '';
		}

		if (isset($this->error['timezone_difference'])) {
			$data['error_timezone_difference'] = $this->error['timezone_difference'];
		} else {
			$data['error_timezone_difference'] = '';
		}

		if (isset($this->error['image_width'])) {
			$data['error_image_width'] = $this->error['image_width'];
		} else {
			$data['error_image_width'] = '';
		}

		if (isset($this->error['image_height'])) {
			$data['error_image_height'] = $this->error['image_height'];
		} else {
			$data['error_image_height'] = '';
		}

		// Breadcrumbs
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_extension'),
			'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/social_autopilot', 'token=' . $this->session->data['token'], true)
		);

		// Actions
		$data['action'] = $this->url->link('extension/module/social_autopilot', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		// Extension settings
		if (isset($this->request->post['social_autopilot_status'])) {
			$data['social_autopilot_status'] = $this->request->post['social_autopilot_status'];
		} elseif ($this->config->get('social_autopilot_status')) {
			$data['social_autopilot_status'] = $this->config->get('social_autopilot_status');
		} else {
			$data['social_autopilot_status'] = '';
		}

		if (isset($this->request->post['social_autopilot_api_key'])) {
			$data['social_autopilot_api_key'] = $this->request->post['social_autopilot_api_key'];
		} elseif ($this->config->get('social_autopilot_api_key')) {
			$data['social_autopilot_api_key'] = $this->config->get('social_autopilot_api_key');
		} else {
			$data['social_autopilot_api_key'] = '';
		}

		if (isset($this->request->post['social_autopilot_language_id'])) {
			$data['social_autopilot_language_id'] = $this->request->post['social_autopilot_language_id'];
		} elseif ($this->config->get('social_autopilot_language_id')) {
			$data['social_autopilot_language_id'] = $this->config->get('social_autopilot_language_id');
		} else {
			$data['social_autopilot_language_id'] = '';
		}

		if (isset($this->request->post['social_autopilot_autopost'])) {
			$data['social_autopilot_autopost'] = $this->request->post['social_autopilot_autopost'];
		} elseif ($this->config->get('social_autopilot_autopost')) {
			$data['social_autopilot_autopost'] = $this->config->get('social_autopilot_autopost');
		} else {
			$data['social_autopilot_autopost'] = '';
		}

		if (isset($this->request->post['social_autopilot_rating_star_code'])) {
			$data['social_autopilot_rating_star_code'] = $this->request->post['social_autopilot_rating_star_code'];
		} elseif ($this->config->get('social_autopilot_rating_star_code')) {
			$data['social_autopilot_rating_star_code'] = $this->config->get('social_autopilot_rating_star_code');
		} else {
			$data['social_autopilot_rating_star_code'] = '&#9733;';
		}

		if (isset($this->request->post['social_autopilot_timezone_difference'])) {
			$data['social_autopilot_timezone_difference'] = $this->request->post['social_autopilot_timezone_difference'];
		} elseif (!is_null($this->config->get('social_autopilot_timezone_difference'))) {
			$data['social_autopilot_timezone_difference'] = $this->config->get('social_autopilot_timezone_difference');
		} else {
			$data['social_autopilot_timezone_difference'] = '0';
		}

		if (isset($this->request->post['social_autopilot_image_width'])) {
			$data['social_autopilot_image_width'] = $this->request->post['social_autopilot_image_width'];
		} elseif ($this->config->get('social_autopilot_image_width')) {
			$data['social_autopilot_image_width'] = $this->config->get('social_autopilot_image_width');
		} else {
			$data['social_autopilot_image_width'] = '600';
		}

		if (isset($this->request->post['social_autopilot_image_height'])) {
			$data['social_autopilot_image_height'] = $this->request->post['social_autopilot_image_height'];
		} elseif ($this->config->get('social_autopilot_image_height')) {
			$data['social_autopilot_image_height'] = $this->config->get('social_autopilot_image_height');
		} else {
			$data['social_autopilot_image_height'] = '315';
		}

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['token'] = $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/social_autopilot', $data));
	}

	private function validate() {
		$tab_error = array();

		if (!$this->user->hasPermission('modify', 'extension/module/social_autopilot')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['social_autopilot_api_key']) != 35) {
			$this->error['api_key'] = $this->language->get('error_api_key');
		}

		if (!is_numeric($this->request->post['social_autopilot_timezone_difference'])) {
			$this->error['timezone_difference'] = $this->language->get('error_timezone_difference');
		}

		return !$this->error;
	}

	// EVENTS

	public function eventMenu($route, &$data) {
		$menu = array();

		$this->language->load('extension/module/social_autopilot');

		if ($this->user->hasPermission('access', 'extension/module/social_autopilot')) {
			$menu[] = array(
				'name'	   => $this->language->get('menu_setting'),
				'href'     => $this->url->link('extension/module/social_autopilot', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_channel'),
				'href'     => $this->url->link('social_autopilot/channel_permission', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_template'),
				'href'     => $this->url->link('social_autopilot/template', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_scheduled_post'),
				'href'     => $this->url->link('social_autopilot/scheduled_post', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);

			$menu[] = array(
				'name'	  => $this->language->get('menu_task'),
				'href'     => $this->url->link('social_autopilot/task', 'token=' . $this->session->data['token'], true),
				'children' => array()
			);

         $menu[] = array(
				'name'	  => $this->language->get('menu_help'),
				'href'     => 'http://www.oc-extensions.com/OpenCart-Social-AutoPilot-Opencart-2.x-Help',
				'children' => array()
			);
		}

		if ($menu) {
			$data['menus'][] = array(
				'id'       => 'menu-social-autopilot',
				'icon'	   => 'fa-share-square-o',
				'name'	   => $this->language->get('menu_social_autopilot'),
				'href'     => '',
				'children' => $menu
			);
		}
	}

	public function eventHeaderSetup() {
		$this->document->addScript('view/javascript/jquery/social-autopilot.js');
		$this->document->addStyle('view/stylesheet/social-autopilot.css');
	}
}
?>
