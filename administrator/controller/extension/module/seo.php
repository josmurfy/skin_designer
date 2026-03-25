<?php
require_once(DIR_SYSTEM . 'library/equotix/seo/equotix.php'); 
class ControllerExtensionModuleSEO extends Equotix {
	protected $version = '3.0.0';
	protected $code = 'seo';
	protected $extension = 'SEO Pack'; 
	protected $extension_id = '68';
	protected $purchase_url = 'seo-pack';
	protected $purchase_id = '3682';
	protected $error = array();

	public function index() {
		$this->load->language('extension/module/seo');

		$this->document->setTitle(strip_tags($this->language->get('heading_title')));
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('seo', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
		
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
	
		$data['entry_clear_product'] = $this->language->get('entry_clear_product');
		$data['entry_clear_category'] = $this->language->get('entry_clear_category');
		$data['entry_clear_manufacturer'] = $this->language->get('entry_clear_manufacturer');
		$data['entry_clear_information'] = $this->language->get('entry_clear_information');
		$data['entry_clear_related'] = $this->language->get('entry_clear_related');
		$data['entry_generate_product'] = $this->language->get('entry_generate_product');
		$data['entry_generate_category'] = $this->language->get('entry_generate_category');
		$data['entry_generate_manufacturer'] = $this->language->get('entry_generate_manufacturer');
		$data['entry_generate_information'] = $this->language->get('entry_generate_information');
		$data['entry_generate_related'] = $this->language->get('entry_generate_related');
		$data['entry_related_limit'] = $this->language->get('entry_related_limit');
		$data['entry_related_random'] = $this->language->get('entry_related_random');
		
		$data['text_edit_keyword'] = $this->language->get('text_edit_keyword');
		$data['text_delete'] = $this->language->get('text_delete');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled']	= $this->language->get('text_enabled');
		$data['text_disabled']	= $this->language->get('text_disabled');
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_related'] = $this->language->get('tab_related');
		$data['tab_all_seo'] = $this->language->get('tab_all_seo');
		
		$data['column_query'] = $this->language->get('column_query');
		$data['column_keyword'] = $this->language->get('column_keyword');
		$data['column_action'] = $this->language->get('column_action');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_clear_product'] = $this->language->get('button_clear_product');
		$data['button_clear_category'] = $this->language->get('button_clear_category');
		$data['button_clear_manufacturer'] = $this->language->get('button_clear_manufacturer');
		$data['button_clear_information'] = $this->language->get('button_clear_information');
		$data['button_clear_related'] = $this->language->get('button_clear_related');
		$data['button_generate_product'] = $this->language->get('button_generate_product');
		$data['button_generate_category'] = $this->language->get('button_generate_category');
		$data['button_generate_manufacturer'] = $this->language->get('button_generate_manufacturer');
		$data['button_generate_information'] = $this->language->get('button_generate_information');
		$data['button_generate_related'] = $this->language->get('button_generate_related');
		
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
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], true)
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true)
   		);
		
		$data['action'] = $this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
		$data['token'] = $this->session->data['token'];
		$data['clear_product'] = $this->url->link('extension/module/seo/clearproduct', 'token=' . $this->session->data['token'], true);
		$data['clear_category'] = $this->url->link('extension/module/seo/clearcategory', 'token=' . $this->session->data['token'], true);
		$data['clear_manufacturer'] = $this->url->link('extension/module/seo/clearmanufacturer', 'token=' . $this->session->data['token'], true);
		$data['clear_information'] = $this->url->link('extension/module/seo/clearinformation', 'token=' . $this->session->data['token'], true);
		$data['clear_related'] = $this->url->link('extension/module/seo/clearrelated', 'token=' . $this->session->data['token'], true);
		$data['generate_product'] = $this->url->link('extension/module/seo/generateproduct', 'token=' . $this->session->data['token'], true);
		$data['generate_category'] = $this->url->link('extension/module/seo/generatecategory', 'token=' . $this->session->data['token'], true);
		$data['generate_manufacturer'] = $this->url->link('extension/module/seo/generatemanufacturer', 'token=' . $this->session->data['token'], true);
		$data['generate_information'] = $this->url->link('extension/module/seo/generateinformation', 'token=' . $this->session->data['token'], true);
		
		if (isset($this->request->post['seo_related_limit'])) {
			$data['seo_related_limit'] = $this->request->post['seo_related_limit']; 
		} elseif ($this->config->get('seo_related_limit')) {
			$data['seo_related_limit'] = $this->config->get('seo_related_limit');
		} else {
			$data['seo_related_limit'] = 6;
		}
		
		if(isset($this->request->post['seo_related_random'])) {
			$data['seo_related_random'] = $this->request->post['seo_related_random'];
		} elseif ($this->config->get('seo_related_random')) {
			$data['seo_related_random'] = $this->config->get('seo_related_random');
		} else {
			$data['seo_related_random'] = 1;
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->generateOutput('extension/module/seo', $data);
	}
	
	public function clearproduct() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'product_id=%'");
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function clearcategory() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {			
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'category_id=%'");
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function clearinformation() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'information_id=%'");
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function clearmanufacturer() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query LIKE 'manufacturer_id=%'");
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function clearrelated() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related");
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function generateproduct() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->load->model('catalog/seo');
			
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.quantity>0  AND  pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			
			foreach ($query->rows as $result) {
				$alias = $this->db->query("SELECT query FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$result['product_id'] . "' AND language_id = '" . $result['language_id'] . "'");
				
				if (!$alias->num_rows) {
					$keyword = $this->model_catalog_seo->cleanString($result['name']);//."_product_id=".$result['product_id'];
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");
						
					if ($query->num_rows) {
						$true = true;
						$count = 0;
						
						while ($true) {								
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword . '-' . $count) . "'");
							
							if ($query->num_rows) {
								$count++;
							} else {
								$true = false;
							}
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$result['product_id'] . "', keyword = '" . $this->db->escape($keyword . '-' . $count) . "', language_id = '" . $result['language_id'] . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$result['product_id'] . "', keyword = '" . $this->db->escape($keyword) . "', language_id = '" . $result['language_id'] . "'");
					}
				}
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function generatecategory() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->load->model('catalog/seo');
			
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) WHERE c.status=1 AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			
			foreach ($query->rows as $result) {
				$alias = $this->db->query("SELECT query FROM " . DB_PREFIX . "url_alias WHERE query = 'category_id=" . (int)$result['category_id'] . "' AND language_id = '" . $result['language_id'] . "'");
				
				if (!$alias->num_rows) {
					$keyword = $this->model_catalog_seo->cleanString($result['name']);
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");
						
					if ($query->num_rows) {
						$true = true;
						$count = 0;
						
						while ($true) {								
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword . '-' . $count) . "'");
							
							if ($query->num_rows) {
								$count++;
							} else {
								$true = false;
							}
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$result['category_id'] . "', keyword = '" . $this->db->escape($keyword . '-' . $count) . "', language_id = '" . $result['language_id'] . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'category_id=" . (int)$result['category_id'] . "', keyword = '" . $this->db->escape($keyword) . "', language_id = '" . $result['language_id'] . "'");
					}
				}
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function generatemanufacturer() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->load->model('catalog/seo');
			
			$query = $this->db->query("SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer");
			
			foreach ($query->rows as $result) {
				$alias = $this->db->query("SELECT query FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$result['manufacturer_id'] . "'");
				
				if (!$alias->num_rows) {
					$keyword = $this->model_catalog_seo->cleanString($result['name']);
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");
						
					if ($query->num_rows) {
						$true = true;
						$count = 0;
						
						while ($true) {								
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword . '-' . $count) . "'");
							
							if ($query->num_rows) {
								$count++;
							} else {
								$true = false;
							}
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$result['manufacturer_id'] . "', keyword = '" . $this->db->escape($keyword . '-' . $count) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$result['manufacturer_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function generateinformation() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$this->load->model('catalog/seo');
			
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			
			foreach ($query->rows as $result) {
				$alias = $this->db->query("SELECT query FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$result['information_id'] . "' AND language_id = '" . $result['language_id'] . "'");
				
				if (!$alias->num_rows) {
					$keyword = $this->model_catalog_seo->cleanString($result['title']);
					
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");
						
					if ($query->num_rows) {
						$true = true;
						$count = 0;
						
						while ($true) {								
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword . '-' . $count) . "'");
							
							if ($query->num_rows) {
								$count++;
							} else {
								$true = false;
							}
						}
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'information_id=" . (int)$result['information_id'] . "', language_id = '" . $result['language_id'] . "', keyword = '" . $this->db->escape($keyword . '-' . $count) . "'");
					} else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'information_id=" . (int)$result['information_id'] . "', language_id = '" . $result['language_id'] . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module/seo', 'token=' . $this->session->data['token'], true));
		} else {
			$this->index();
		}
	}
	
	public function generaterelated() {
		$json = array();
		
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			if (isset($this->request->get['page'])) {
				$page = (int)$this->request->get['page'];
			} else {
				$page = 1;
			}
			
			$start = ($page - 1) * 20;
			$limit = 20;
			
			$query = $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE quantity>0");
			
			$count = $query->row['total'];
			
			$total_page = ceil($count / $limit);
			
			if ($page < $total_page) {
				$json['next'] = $page + 1;
				
				$json['success'] = sprintf($this->language->get('text_next'), $start, $count);
			} else {
				$json['success'] = $this->language->get('text_success');
			}
			
			$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE quantity>0 LIMIT " . (int)$start . ", " . $limit);
			
			foreach ($query->rows as $result) {				
				$products = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_to_category WHERE category_id IN (SELECT DISTINCT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$result['product_id'] . "') AND product_id != '" . (int)$result['product_id'] . "' ORDER by category_id DESC limit 1");
				
				foreach ($products->rows as $product) {
					$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$result['product_id'] . "' AND related_id = '" . (int)$product['product_id'] . "'");
					
					if (!$query1->num_rows) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$result['product_id'] . "', related_id = '" . (int)$product['product_id'] . "'");
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product['product_id'] . "', related_id = '" . (int)$result['product_id'] . "'");
					}
				}
				
			}
		} else {
			$json['error'] = $this->error['warning'];
		}
		
		$this->response->setOutput(json_encode($json));	
	}
	
	public function allseo() {
		$json = array();
		
		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$start = ($page - 1) * $this->config->get('config_limit_admin');
		$limit = $this->config->get('config_limit_admin');
		
		$query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "url_alias");
		
		$pagination = new Pagination();
		$pagination->total = $query->row['total'];
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('extension/module/seo/allseo', 'token=' . $this->session->data['token'] . '&page={page}', true);

		$json['pagination'] = $pagination->render();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias LIMIT " . (int)$start . ", " . (int)$limit);
		
		foreach ($query->rows as $result) {
			if (strpos($result['query'], 'product_id=') !== false) {
				$url = 'index.php?route=catalog/product/edit&token=' . $this->session->data['token'] . '&' . $result['query'];
			} elseif (strpos($result['query'], 'category_id=') !== false) {
				$url = 'index.php?route=catalog/category/edit&token=' . $this->session->data['token'] . '&' . $result['query'];
			} elseif (strpos($result['query'], 'information_id=') !== false) {
				$url = 'index.php?route=catalog/information/edit&token=' . $this->session->data['token'] . '&' . $result['query'];
			} elseif (strpos($result['query'], 'manufacturer_id=') !== false) {
				$url = 'index.php?route=catalog/manufacturer/edit&token=' . $this->session->data['token'] . '&' . $result['query'];
			} else {
				$url = '';
			}
			
			$json['urls'][] = array(
				'url_alias_id'		=> $result['url_alias_id'],
				'query'				=> $result['query'],
				'keyword'			=> $result['keyword'],
				'url'				=> $url
			);
		}
	
		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteseo() {
		$this->load->language('extension/module/seo');
		
		if ($this->validate()) {
			$id = (int)$this->request->get['url_alias_id'];
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE url_alias_id = '" . (int)$id . "'");
			
			$this->session->data['success'] = $this->language->get('text_success');
		}
		
		$this->index();
	}
	
	public function install() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$path = substr_replace(DIR_SYSTEM, '', -7);

		if (file_exists($path . 'vqmod/xml/seo_url.xml_') || is_file($path . 'vqmod/xml/seo_url.xml_')) {
			rename($path . 'vqmod/xml/seo_url.xml_', $path . 'vqmod/xml/seo_url.xml');
		}
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$path = substr_replace(DIR_SYSTEM, '', -7);

		if (file_exists($path . 'vqmod/xml/seo_url.xml') || is_file($path . 'vqmod/xml/seo_url.xml')) {
			rename($path . 'vqmod/xml/seo_url.xml', $path . 'vqmod/xml/seo_url.xml_');
		}
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/seo') || !$this->validated()) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}