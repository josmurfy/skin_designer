<?php
namespace Opencart\Admin\Controller\Shopmanager;

class CategorySpecific extends \Opencart\System\Engine\Controller {
    private $error = array();

	public function index(): void {
		$lang = $this->load->language('shopmanager/catalog/category_specific');
		$data = $data ?? [];
		$data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/catalog/category_specific');

		$this->getList();
	}

	protected function getList() {
		$this->document->addScript('view/javascript/shopmanager/catalog/category_specific_list.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		$this->document->addScript('view/javascript/shopmanager/alert_popup.js');

	
		// Sorting and pagination parameters
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'specific_name';
		}
	
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
	
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
	
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 20;
		}
	
		// Filter parameters
		if (isset($this->request->get['filter_specific_name'])) {
			$filter_specific_name = $this->request->get['filter_specific_name'];
		} else {
			$filter_specific_name = null;
		}
	
		// Language handling
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		// Assurez-vous de passer les langues au template
		$data['languages'] = $languages;	

				
		$url = '';
	
		// Add sorting and pagination params to the URL
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
	
		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		} else {
			$data['limit'] = 20;
		}
	
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}
	
		if (isset($this->request->get['filter_specific_name'])) {
			$url .= '&filter_specific_name=' . urlencode(html_entity_decode($this->request->get['filter_specific_name'], ENT_QUOTES, 'UTF-8'));
		}
	
		// Breadcrumbs
		$data['breadcrumbs'] = array();
	
		$data['breadcrumbs'][] = [
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];
	
		$data['breadcrumbs'][] = array(
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('shopmanager/catalog/category_specific', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
	
		$data['add'] = $this->url->link('shopmanager/catalog/category_specific/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('shopmanager/catalog/category_specific/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
	
		$data['category_specifics'] = array();
	
		$filter_data = array(
			'filter_specific_name' => $filter_specific_name,
			'sort'                 => $sort,
			'order'                => $order,
			'start'                => ($page - 1) * $limit,
			'limit'                => $limit
		);
	
		$category_specific_total = $this->model_shopmanager_catalog_category_specific->getTotalCategorySpecifics($filter_data);
	
		$results = $this->model_shopmanager_catalog_category_specific->getCategorySpecifics($filter_data);
	
		foreach ($results as $result) {
			$translations = json_decode($result['translations'], true);

			$language_data = array();

			foreach ($languages as $language) {
				$language_data[$language['code']] = isset($translations[$language['code']]) ? $translations[$language['code']] : '';
			}

			$data['category_specifics'][] = array(
				'category_specifics_id' => $result['category_specifics_id'],
				'specific_name' => $result['specific_name'],
				'exclude' => $result['exclude']==1?($lang['text_exclude'] ?? ''):($lang['text_not_exclude'] ?? ''),
				'languages'     => $language_data,
				'edit'          => $this->url->link('shopmanager/catalog/category_specific/edit', 'user_token=' . $this->session->data['user_token'] . '&specific_name=' . urlencode($result['specific_name']), true),
				'delete'        => $this->url->link('shopmanager/catalog/category_specific/delete', 'user_token=' . $this->session->data['user_token'] . '&specific_name=' . urlencode($result['specific_name']), true)
			);

		}
	
		// Set language text
		$data['heading_title'] = ($lang['heading_title'] ?? '');
	
		$data['text_list'] = ($lang['text_list'] ?? '');
		$data['text_no_results'] = ($lang['text_no_results'] ?? '');
		$data['text_confirm'] = ($lang['text_confirm'] ?? '');
	
		$data['column_specific_name'] = ($lang['column_specific_name'] ?? '');
		
		foreach ($languages as $language) {
			$data['column_translation_' . $language['code']] = $language['name'];
		}
		$data['column_action'] = ($lang['column_action'] ?? '');
	
		$data['button_add'] = ($lang['button_add'] ?? '');
		$data['button_edit'] = ($lang['button_edit'] ?? '');
		$data['button_delete'] = ($lang['button_delete'] ?? '');
		$data['button_filter'] = ($lang['button_filter'] ?? '');
		$data['button_search'] = ($lang['button_search'] ?? '');
		$data['button_save'] = ($lang['button_save'] ?? '');


		$data['search_action'] = $this->url->link('shopmanager/catalog/category_specific', 'user_token=' . $this->session->data['user_token'], true);
		$data['edit_action'] = $this->url->link('shopmanager/catalog/category_specific/edit', 'user_token=' . $this->session->data['user_token'], true);
	
		$data['entry_specific_name'] = ($lang['entry_specific_name'] ?? '');
		$data['entry_limit'] = ($lang['entry_limit'] ?? '');

	
		$data['per_page_options'] = [20, 50, 100, 200];
	
		$data['user_token'] = $this->session->data['user_token'];
	
		// Error and success messages
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
	
		// Sorting URLs
		$data['sort_specific_name'] = $this->url->link('shopmanager/catalog/category_specific', 'user_token=' . $this->session->data['user_token'] . '&sort=specific_name' . $url, true);
	
		// Pagination
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $category_specific_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('shopmanager/catalog/category_specific', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}&limit=' . $limit, true)
		]);
		$data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($category_specific_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($category_specific_total - $limit)) ? $category_specific_total : ((($page - 1) * $limit) + $limit), $category_specific_total, ceil($category_specific_total / $limit));
	
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;
		$data['filter_specific_name'] = $filter_specific_name;
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');
	
		$this->response->setOutput($this->load->view('shopmanager/catalog/category_specific_list', $data));
	}
	

    public function edit() {
        $lang = $this->load->language('shopmanager/catalog/category_specific');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/catalog/category_specific');

        if (isset($this->request->get['specific_name'])) {
            $specific_name = $this->request->get['specific_name'];

            $languages = $this->model_localisation_language->getLanguages();
            foreach ($languages as $language) {
                if (isset($this->request->post['translation_' . $language['code']][$specific_name])) {
                    $translated_value = $this->request->post['translation_' . $language['code']][$specific_name];
                    $this->model_shopmanager_catalog_category_specific->editTranslation($specific_name, $language['code'], $translated_value);
                }
            }

            $this->session->data['success'] = ($lang['text_success'] ?? '');
        }

        $this->response->redirect($this->url->link('shopmanager/catalog/category_specific', 'user_token=' . $this->session->data['user_token'], true));
    }

	public function excludeSpecific() {
        $json = array();

        // Vérifie si les paramètres nécessaires sont fournis
        if (isset($this->request->post['specific_name'])) {
         
            $specific_name = $this->request->post['specific_name'];

            // Charge le modèle
            $this->load->model('shopmanager/catalog/category_specific');

            // Appelle la fonction du modèle pour exclure le "specific"
            $result = $this->model_shopmanager_catalog_category_specific->excludeSpecific($specific_name);

            if ($result) {
                $json['success'] = 'Specific successfully excluded.';
            } else {
                $json['error'] = 'Failed to exclude specific.';
            }
        } else {
            $json['error'] = 'Missing category_id or specific_name.';
        }

        // Retourne le résultat au format JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
	public function delete() {
		$lang = $this->load->language('shopmanager/catalog/category_specific');
		$data = $data ?? [];
		$data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/catalog/category_specific');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $category_specific_id) {
				$this->model_shopmanager_catalog_category_specific->deleteCategorySpecific($category_specific_id);
			}

			$this->session->data['success'] = ($lang['text_success'] ?? '');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('shopmanager/catalog/category_specific', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'shopmanager/catalog/category_specific')) {
			$this->error['warning'] = ($lang['error_permission'] ?? '');
		}

		return !$this->error;
	}
}
?>
