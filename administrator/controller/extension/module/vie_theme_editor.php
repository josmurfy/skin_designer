<?php
class ControllerExtensionModuleVieThemeEditor extends Controller {
	protected $vie;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->vie = Vie::getInstance($registry);

		$this->load->model('vie/vie');
		$this->model_vie = $this->model_vie_vie;

		Vie::loadTexts($this->language->load('module/vie_theme_editor'));

		$this->load->model('vie/theme_editor');
		$this->model_lte = $this->model_vie_theme_editor;
	}

	public function install() {
		$this->model_lte->createTables();
	}

	public function uninstall() {
		$this->model_lte->dropTables();
	}

	public function index() {
		try {
			$this->load->helper('vie_admin_view');

			// Actions
			$data['module_url']               = $this->model_vie->createLink('module/vie_theme_editor');
			$data['support_url']              = 'http://support.viethemes.com';
			$data['store_url']                = $this->model_vie->createLink('extension/module/vie_theme_editor/store');
			$data['skin_url']                 = $this->model_vie->createLink('extension/module/vie_theme_editor/skin');
			$data['save_url']                 = $this->model_vie->createLink('extension/module/vie_theme_editor/save');
			$data['save_skin_as_url']         = $this->model_vie->createLink('extension/module/vie_theme_editor/saveSkinAs');
			$data['remove_skin_url']          = $this->model_vie->createLink('extension/module/vie_theme_editor/removeSkin');
			$data['export_skins_url']         = $this->model_vie->createLink('extension/module/vie_theme_editor/exportSkins');
			$data['import_skins_url']         = $this->model_vie->createLink('extension/module/vie_theme_editor/importSkins');
			$data['style_panel_url']          = $this->model_vie->createLink('extension/module/vie_theme_editor/stylePanel');
			$data['newsletter_lists_url']     = $this->model_vie->createLink('extension/module/vie_theme_editor/newsletterLists');
			$data['modules_url']               = $this->model_vie->createLink('extension/extension');

			$data['selected_store_id']  = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;
			$data['front_base'] 		= $this->model_vie->getFrontBase();
			$data['stores'] 			= $this->model_vie->getStores();
			$data['languages'] 			= $this->model_vie->getLanguages();
			$data['default_language'] 	= $this->config->get('config_language_id');

			$data['token'] = $this->session->data['token'];

			// Get store id from GET
			$store_id = $data['selected_store_id'];

			// Get current theme from setting by this store
			$theme_id = $this->model_lte->getThemeId($store_id);

			$config_theme_id = $this->model_lte->getConfigTheme($store_id);

			// No Theme installed, let's install it
			if (!$theme_id) {
				$this->model_lte->installTheme($store_id, $config_theme_id);
				$theme_id = $config_theme_id;
			}

			// Other theme is set
			if ($theme_id != $config_theme_id) {
				$this->model_lte->installTheme($store_id, $config_theme_id);
				$theme_id = $config_theme_id;
			}

			// Load theme option and check theme version
			$theme_options = $this->model_lte->loadThemeOptions($theme_id);
			$theme_version = $this->model_lte->getThemeVersion($store_id);

			if ($theme_options['version'] != $theme_version) {
				$this->model_lte->upgradeTheme($theme_id);
				$theme_updated = true;
			}

			// Get skins by the current theme
			$skin_rows = $this->model_lte->getSkinsByThemeId($theme_id);

			// Prepare skins
			$skins = array();
			foreach ($skin_rows as $skin_row) {
				$can_remove = $skin_row['skin_id'] != $skin_row['root_skin_id'];

				$skins[$skin_row['skin_id']] = $skin_row['name'];
			}

			$skin_id = $this->model_lte->getSkinId($store_id);

			// Get skin options by theme id and skin id
			$skins_options = $this->model_lte->getSkinOptions($theme_id, $skin_id);

			$store_url = $this->model_vie->getStoreBase($store_id) . '?vie_preview=1';

			// Fonts
			$data['fonts'] = $this->vie->getFonts();

			$data['theme_name'] =  $theme_options['name'];
			$data['theme_id'] =  $theme_id;
			$data['version'] =  version_compare($theme_options['version'], $theme_version) ? $theme_options['version'] : $theme_version;
			$data['theme_updated'] =  !empty($theme_updated);
			$data['theme_updated_message'] =  Vie::_('text_success_theme_updated', sprintf('You have updated %s to version %s.', $theme_options['name'], $theme_options['version']));
			$data['documentation_url'] =  "http://docs.viethemes.com/{$theme_options['id']}";
			$data['demo_url'] =  "http://demo.viethemes.com/{$theme_options['id']}";
			$data['preview_url'] =  $store_url;
			$data['skins'] =  $skins;
			$data['excluded_options'] =  $theme_options['excluded_options'];
			$data['skin_id'] =  $skin_id;
			$data['option_section'] = $theme_options['options'];
		} catch (Exception $e) {
		}

		$data['debug'] = !empty($this->request->cookie['vie_debug']);

		$data['export_url'] = $this->model_vie->createLink('extension/module/vie_theme_editor/export', array('store_id' => $store_id));
		$data['import_url'] = $this->model_vie->createLink('extension/module/vie_theme_editor/import', array('store_id' => $store_id));

		$this->document->setTitle(Vie::_('heading_vie_module'));		

		$this->document->addStyle('view/javascript/summernote/summernote.css');
		$this->document->addScript('view/javascript/summernote/summernote.js');
		
		$this->model_vie->addResources();
		$this->document->addScript('view/vie/scripts/directives/theme_editor.js');
		$this->document->addScript('view/vie/scripts/controllers/theme_editor.js');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if (version_compare(VERSION, '2.2.0.0_a1', '>=')) {
			$this->response->setOutput($this->load->view('extension/module/vie_theme_editor', $data));
		} else {
			$this->response->setOutput($this->load->view('extension/module/vie_theme_editor.tpl', $data));
		}
	}

	public function skin() {
		// Get store id and skin id from GET
		$store_id = $this->request->get['store_id'];
		$skin_id = $this->request->get['skin_id'];

		try {
			// Get theme id from setting
			$theme_id = $this->model_lte->getThemeId($store_id);

			// Get skin options by theme id and skin id
			$skin_options = $this->model_lte->getSkinOptions($theme_id, $skin_id);

			$result = array(
				'skin_options' => $skin_options
			);
		}
		catch (Exception $e)
		{
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage(),
				'code' => $e->getCode()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	public function save() {
		try {
			$this->assertPostAndPermission();

			// Get inputs from POST
			$store_id = $this->request->post['store_id'];
			$skin_id = $this->request->post['skin_id'];
			$skin_options = $this->request->post['skin_options'];
			$skin_css = $this->request->post['skin_css'];

			// Save skin option into db
			$theme_id = $this->model_lte->getThemeId($store_id);

			$this->model_lte->setSkinForStore($store_id, $theme_id, $skin_id);
			$this->model_lte->updateSkinOptions($theme_id, $skin_id, $skin_options);

			// Process skin CSS
			$skin_css = html_entity_decode($skin_css, ENT_COMPAT, "UTF-8");
			$this->model_lte->setSkinCss($store_id, $skin_css);

			$result = array(
				'status' => 1,
				'message' => Vie::_('text_success_save', 'Success: You have modified module Vie Control Panel!')
			);
		} catch (Exception $e) {
			$this->response->addHeader('HTTP/1.1 500 Error');

			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	public function export() {
		try {
//			$this->assertPermission();

			$result = array();

			$store_id = $this->request->get['store_id'];
			$theme_id = $this->model_lte->getThemeId($store_id);

			// Get all skins form db
			$skins = $this->model_lte->getSkinsByThemeId($theme_id);

			foreach ($skins as $skin) {
				$key = $skin['skin_id'];

				$skin_options = $this->model_lte->getSkinOptions($theme_id, $skin['skin_id']);

				$result[$key] = array(
					'name'          => $skin['name'],
					'root_skin_id'  => $skin['root_skin_id'],
					'options'		=> $skin_options
				);
			}

			$this->response->addheader('Pragma: public');
			$this->response->addheader('Expires: 0');
			$this->response->addheader('Content-Description: File Transfer');
			$this->response->addheader('Content-Type: application/octet-stream');
			$this->response->addheader('Content-Disposition: attachment; filename=vie_theme_editor_backup_' . date('Y-m-d_H-i-s', time()) . '.txt');
			$this->response->addheader('Content-Transfer-Encoding: binary');

			if (ob_get_level()) ob_end_clean();

			$this->response->setOutput(serialize($result));
		} catch (Exception $e) {
			// TODO: Improve error showing better
			throw $e;
		}
	}

	public function import() {
		try {
			$this->assertPermission();

			if (!is_uploaded_file($this->request->files['file']['tmp_name'])) {
				throw new Exception(_t('error_import_file', 'Import file error!'));
			}

			$store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id']: 0;

			// Test contents
			$skins = unserialize(file_get_contents($this->request->files['file']['tmp_name']));

			// Remove existing skins
			$theme_id = $this->model_lte->getThemeId($store_id);

			$this->db->query('DELETE FROM `' . DB_PREFIX ."vie_lte_skin` WHERE theme_id = '" . $theme_id . "'");
			$this->db->query('DELETE FROM `' . DB_PREFIX ."vie_lte_skin_option` WHERE theme_id = '" . $theme_id . "'");

			// Import
			$this->model_lte->importSkinsFromFile($theme_id, $this->request->files['file']['tmp_name']);

			$result = array(
				'status'    => 1,
				'message'   => Vie::_('success_import_skins', 'Success: Import completed!')
			);
		} catch (Exception $e) {
			$result = array(
				'error' => $e->getMessage()
			);
		}

		$this->response->setOutput(json_encode($result));
	}

	protected function assertPostAndPermission(){
		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			throw new Exception(Vie::_('error_request_method', 'Error: The request method must be POST!'));
		}

		$this->assertPermission();
	}

	protected function assertPermission() {
		if (!$this->user->hasPermission('modify', 'extension/module/vie_theme_editor')) {
			throw new Exception(Vie::_('error_permission', 'Warning: You do not have permission to modify module Vie Control Panel!'));
		}
	}
}
