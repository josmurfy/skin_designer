<?php
class ModelVieVie extends Model {
	public function addResources() {
		$this->document->addStyle('view/vie/styles/all.css');

		$this->document->addScript('view/vie/scripts/all.js');
		$this->document->addScript('view/vie/scripts/templates-controls.js');
		$this->document->addScript('view/vie/scripts/directives/controls.js');
		$this->document->addScript('view/vie/scripts/vie.js');
	}

	public function getStores() {
		$this->load->model('setting/store');

		$rows = $this->model_setting_store->getStores();

		$stores = array(
			'0' => $this->config->get('config_name') . Vie::_('text_default')
		);

		foreach ($rows as $row) {
			$stores[$row['store_id']] = $row['name'];
		}

		return $stores;
	}

	public function getStoreBase($store_id) {
		if ($store_id == 0) {
			return $this->getFrontBase();
		} else {
			$store = $this->model_setting_setting->getSetting('config', $store_id);

			return $store['config_url'];
		}
	}


	public function getFrontBase() {
		return isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_CATALOG : HTTP_CATALOG;
	}

	public function getLanguages() {
		$this->load->model('localisation/language');

		return $this->model_localisation_language->getLanguages();
	}

	public function createLink($route, array $params = array()) {
		$params['token'] = $this->session->data['token'];

		return urldecode(str_replace('&amp;', '&', $this->url->link($route, http_build_query($params), 'SSL')));
	}
}
