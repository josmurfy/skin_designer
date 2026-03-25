<?php
namespace Opencart\Admin\Controller\Shopmanager;

class CategoryEbay extends \Opencart\System\Engine\Controller {
    private $error = array();

    public function index(): void {
        $lang = $this->load->language('shopmanager/catalog/category_ebay');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/category_ebay');

        $this->getList();
    }

    public function add() {
        $lang = $this->load->language('shopmanager/catalog/category_ebay');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/category_ebay');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_shopmanager_catalog_category_ebay->addCategoryEbay($this->request->post);

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

            $this->response->redirect($this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $lang = $this->load->language('shopmanager/catalog/category_ebay');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/category_ebay');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_shopmanager_catalog_category_ebay->editCategoryEbay($this->request->get['category_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $lang = $this->load->language('shopmanager/catalog/category_ebay');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/category_ebay');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $category_id) {
                $this->model_shopmanager_catalog_category_ebay->deleteCategoryEbay($category_id);
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

            $this->response->redirect($this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    public function repair() {
        $lang = $this->load->language('shopmanager/catalog/category_ebay');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/category_ebay');

        if ($this->validateRepair()) {
            $this->model_shopmanager_catalog_category_ebay->repairCategories();

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

            $this->response->redirect($this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {

        $this->document->addScript('view/javascript/shopmanager/catalog/category_ebay_list.js');
        $this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
        $this->document->addScript('view/javascript/shopmanager/alert_popup.js');


        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'name';
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

        if (isset($this->request->get['filter_category_id'])) {
            $filter_category_id = $this->request->get['filter_category_id'];
        } else {
            $filter_category_id = null;
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

        if (isset($this->request->get['filter_leaf'])) {
            $filter_leaf = $this->request->get['filter_leaf'];
        } else {
            $filter_leaf = null;
        }

        if (isset($this->request->get['filter_specifics'])) {
            $filter_specifics = $this->request->get['filter_specifics'];
        } else {
            $filter_specifics = null;
        }

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

        if (isset($this->request->get['limit'])){
            $url .= '&limit=' . $this->request->get['limit'];
            $data['limit'] = $this->request->get['limit'];
        }else{
            $data['limit'] = 20;
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }
        
        if (isset($this->request->get['limit'])){
            $url .= '&limit=' . $this->request->get['limit'];
        } 
        if (isset($this->request->get['filter_category_id'])) {
            $url = '&filter_category_id=' . $this->request->get['filter_category_id'];
        } 

        if (isset($this->request->get['filter_name'])) {
            $url = '&filter_name=' . $this->request->get['filter_name'];
        } 

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_leaf'])) {
            $url .= '&filter_leaf=' . $this->request->get['filter_leaf'];
        }

        if (isset($this->request->get['filter_specifics'])) {
            $url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = array(
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('shopmanager/catalog/category_ebay/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('shopmanager/catalog/category_ebay/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['repair'] = $this->url->link('shopmanager/catalog/category_ebay/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['categories'] = array();

        $filter_data = array(

            'filter_name'  => $filter_name,
            'filter_category_id'  => $filter_category_id,
            'filter_status' =>	$filter_status, 
            'filter_leaf' =>	$filter_leaf, 
            'filter_specifics' => $filter_specifics,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit'           => $limit //$this->config->get('config_limit_admin')
        );

        $category_total = $this->model_shopmanager_catalog_category_ebay->getTotalCategories($filter_data);

        $results = $this->model_shopmanager_catalog_category_ebay->getCategories($filter_data);
        
        foreach ($results as $result) {
        //	//print("<pre>".print_r ($result,true )."</pre>");
            if($result['leaf'] && !$result['specifics'] && $result['specifics_error']){
                $specifics= ($lang['text_specifics_error'] ?? '');
            }elseif($result['leaf'] && !$result['specifics'] && !$result['specifics_error']){
                $specifics= ($lang['text_specifics_not_set'] ?? '');
            }elseif($result['leaf'] && $result['specifics'] && !$result['specifics_error']){
                $specifics= ($lang['text_specifics_set'] ?? '');
            }else{
                $specifics=($lang['text_specifics_na'] ?? '');
            }
            $data['categories'][] = array(
                'category_id' => $result['category_id'],
                'name'        => $result['name'],
                'leaf'      => $result['leaf']? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
                'specifics'      => $specifics ,
                //'status'      => $result['status'],
                'sort_order'  => $result['sort_order'],
                'status_id'   => $result['status'],
                'status'     => $result['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
                'edit'        => $this->url->link('shopmanager/catalog/category_ebay/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url, true),
                'delete'      => $this->url->link('shopmanager/catalog/category_ebay/delete', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url, true)
            );
        }

        $data['heading_title'] = ($lang['heading_title'] ?? '');

        $data['text_list'] = ($lang['text_list'] ?? '');
        $data['text_no_results'] = ($lang['text_no_results'] ?? '');
        $data['text_confirm'] = ($lang['text_confirm'] ?? '');
        $data['text_enabled'] = ($lang['text_enabled'] ?? '');
        $data['text_disabled'] = ($lang['text_disabled'] ?? '');
        $data['text_specifics_not_set'] = ($lang['text_specifics_not_set'] ?? '');
        $data['text_specifics_set'] = ($lang['text_specifics_set'] ?? '');
        $data['text_specifics_error'] = ($lang['text_specifics_error'] ?? '');

        $data['column_name'] = ($lang['column_name'] ?? '');
        $data['column_sort_order'] = ($lang['column_sort_order'] ?? '');
        $data['column_action'] = ($lang['column_action'] ?? '');
        $data['column_category_id'] = ($lang['column_category_id'] ?? '');
        $data['column_status'] = ($lang['column_status'] ?? '');
        $data['column_leaf'] = ($lang['column_leaf'] ?? '');
        $data['column_specifics'] = ($lang['column_specifics'] ?? '');

        $data['button_add'] = ($lang['button_add'] ?? '');
        $data['button_edit'] = ($lang['button_edit'] ?? '');
        $data['button_delete'] = ($lang['button_delete'] ?? '');
        $data['button_rebuild'] = ($lang['button_rebuild'] ?? '');
        $data['button_delete'] = ($lang['button_delete'] ?? '');
        $data['button_enable'] = ($lang['button_enable'] ?? '');
        $data['button_disable'] = ($lang['button_disable'] ?? '');
        $data['button_filter'] = ($lang['button_filter'] ?? '');

        $data['entry_name'] = ($lang['entry_name'] ?? '');
        $data['entry_category_id'] = ($lang['entry_category_id'] ?? '');
        $data['entry_limit'] = ($lang['entry_limit'] ?? '');
        $data['entry_status'] = ($lang['entry_status'] ?? '');
        $data['entry_leaf'] = ($lang['entry_leaf'] ?? '');
        $data['entry_specifics'] = ($lang['entry_specifics'] ?? '');
         

        $data['per_page_options']=[20, 50, 100, 200];


        $data['user_token'] = $this->session->data['user_token'];
        

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

        if (isset($this->request->get['filter_category_id'])) {
            $url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_leaf'])) {
            $url .= '&filter_leaf=' . $this->request->get['filter_leaf'];
        }

        if (isset($this->request->get['filter_specifics'])) {
            $url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])){
            $url .= '&limit=' . $this->request->get['limit'];
        } 

        $data['sort_name'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_order'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . '&sort=order' . $url, true);
        $data['sort_status'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . '&sort=c1.status' . $url, true);
        $data['sort_leaf'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . '&sort=c1.leaf' . $url, true);
        $data['sort_category_id'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . '&sort=category_id' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['limit_link'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}&limit=', true);
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $category_total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}&limit=' . $limit, true)
        ]);

    //	$data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($category_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($category_total - $this->config->get('config_limit_admin'))) ? $category_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $category_total, ceil($category_total / $this->config->get('config_limit_admin')));
        $data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($category_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($category_total - $limit)) ? $category_total : ((($page - 1) * $limit) + $limit), $category_total, ceil($category_total / $limit));

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['limit'] = $limit;
        $data['filter_status'] = $filter_status;
        $data['filter_category_id'] = $filter_category_id;
        $data['filter_name'] = $filter_name;
        $data['filter_leaf'] = $filter_leaf;
        $data['filter_specifics'] = $filter_specifics;
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
        $data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
        $data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

        $this->response->setOutput($this->load->view('shopmanager/catalog/category_ebay_list', $data));
    }

    protected function getForm() {

        $this->document->addScript('view/javascript/shopmanager/catalog/category_ebay_form.js');
        $this->document->addScript('view/javascript/shopmanager/ai.js');
        $this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
        $this->document->addScript('view/javascript/shopmanager/alert_popup.js');



        $data['heading_title'] = ($lang['heading_title'] ?? '');

        $data['text_form'] = !isset($this->request->get['category_id']) ? ($lang['text_add'] ?? '') : ($lang['text_edit'] ?? '');
        $data['text_none'] = ($lang['text_none'] ?? '');
        $data['text_default'] = ($lang['text_default'] ?? '');
        $data['text_enabled'] = ($lang['text_enabled'] ?? '');
        $data['text_disabled'] = ($lang['text_disabled'] ?? '');
    

        $data['entry_name'] = ($lang['entry_name'] ?? '');
        $data['entry_description'] = ($lang['entry_description'] ?? '');
        $data['entry_meta_title'] = ($lang['entry_meta_title'] ?? '');
        $data['entry_meta_description'] = ($lang['entry_meta_description'] ?? '');
        $data['entry_meta_keyword'] = ($lang['entry_meta_keyword'] ?? '');
        $data['entry_keyword'] = ($lang['entry_keyword'] ?? '');
        $data['entry_parent'] = ($lang['entry_parent'] ?? '');
        $data['entry_filter'] = ($lang['entry_filter'] ?? '');
        $data['entry_store'] = ($lang['entry_store'] ?? '');
        $data['entry_image'] = ($lang['entry_image'] ?? '');
        $data['entry_top'] = ($lang['entry_top'] ?? '');
        $data['entry_column'] = ($lang['entry_column'] ?? '');
        $data['entry_sort_order'] = ($lang['entry_sort_order'] ?? '');
        $data['entry_status'] = ($lang['entry_status'] ?? '');
        $data['entry_layout'] = ($lang['entry_layout'] ?? '');
        $data['entry_text'] = ($lang['entry_text'] ?? '');

        $data['help_filter'] = ($lang['help_filter'] ?? '');
        $data['help_keyword'] = ($lang['help_keyword'] ?? '');
        $data['help_top'] = ($lang['help_top'] ?? '');
        $data['help_column'] = ($lang['help_column'] ?? '');

        $data['button_save'] = ($lang['button_save'] ?? '');
        $data['button_cancel'] = ($lang['button_cancel'] ?? '');
        $data['button_ai_description_category_ebay'] = ($lang['button_ai_description_category_ebay'] ?? '');
        $data['button_ai_image'] = ($lang['button_ai_image'] ?? '');

        $data['button_check_all'] = ($lang['button_check_all'] ?? '');
        $data['button_get_specifics'] = ($lang['button_get_specifics'] ?? '');
        $data['button_specifics_add'] = ($lang['button_specifics_add'] ?? '');
        $data['button_remove'] = ($lang['button_remove'] ?? '');
        


        $data['tab_general'] = ($lang['tab_general'] ?? '');
        $data['tab_data'] = ($lang['tab_data'] ?? '');
        $data['tab_design'] = ($lang['tab_design'] ?? '');
        $data['tab_specifics'] = ($lang['tab_specifics'] ?? '');

        $data['column_specifics'] = ($lang['column_specifics'] ?? '');
        $data['column_found_value'] = ($lang['column_found_value'] ?? '');



        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['name'])) {
            $data['error_name'] = $this->error['name'];
        } else {
            $data['error_name'] = array();
        }

        if (isset($this->error['meta_title'])) {
            $data['error_meta_title'] = $this->error['meta_title'];
        } else {
            $data['error_meta_title'] = array();
        }

        if (isset($this->error['keyword'])) {
            $data['error_keyword'] = $this->error['keyword'];
        } else {
            $data['error_keyword'] = '';
        }

        if (isset($this->error['parent'])) {
            $data['error_parent'] = $this->error['parent'];
        } else {
            $data['error_parent'] = '';
        }
        
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

        if (isset($this->request->get['filter_category_id'])) {
            $url = '&filter_category_id=' . $this->request->get['filter_category_id'];
        } 

        if (isset($this->request->get['filter_name'])) {
            $url = '&filter_name=' . $this->request->get['filter_name'];
        } 

        if (isset($this->request->get['filter_status'])) {
            $url = '&filter_status=' . $this->request->get['filter_status'];
        } 

        if (isset($this->request->get['filter_leaf'])) {
            $url = '&filter_leaf=' . $this->request->get['filter_leaf'];
        } 

        if (isset($this->request->get['filter_specifics'])) {
            $url = '&filter_specifics=' . $this->request->get['filter_specifics'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = array(
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        if (!isset($this->request->get['category_id'])) {
            $data['action'] = $this->url->link('shopmanager/catalog/category_ebay/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('shopmanager/catalog/category_ebay/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $this->request->get['category_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('shopmanager/catalog/category_ebay', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['category_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $category_info = $this->model_shopmanager_catalog_category_ebay->getCategoryEbay($this->request->get['category_id']);
        }
    //	//print("<pre>".print_r ($category_info,true )."</pre>");
        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['category_description'])) {
            $data['category_description'] = $this->request->post['category_description'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['category_description'] = $this->model_shopmanager_catalog_category_ebay->getCategoryEbayDescriptions($this->request->get['category_id']);
        } else {
            $data['category_description'] = array();
        }
        
        if (isset($this->request->post['path'])) {
            $data['path'] = $this->request->post['path'];
        } elseif (!empty($category_info)) {
            $data['path'] = $category_info['path'];
        } else {
            $data['path'] = '';
        }

        if (isset($this->request->post['category_id'])) {
            $data['category_id'] = $this->request->post['category_id'];
        } elseif (!empty($category_info)) {
            $data['category_id'] = $category_info['category_id'];
        } else {
            $data['category_id'] = '';
        }

        if (isset($this->request->post['parent_id'])) {
            $data['parent_id'] = $this->request->post['parent_id'];
        } elseif (!empty($category_info)) {
            $data['parent_id'] = $category_info['parent_id'];
        } else {
            $data['parent_id'] = 0;
        }

        $this->load->model('shopmanager/filter');

        if (isset($this->request->post['category_filter'])) {
            $filters = $this->request->post['category_filter'];
        } elseif (isset($this->request->get['category_id'])) {
            $filters = $this->model_shopmanager_catalog_category_ebay->getCategoryEbayFilters($this->request->get['category_id']);
        } else {
            $filters = array();
        }

        $data['category_filters'] = array();

        foreach ($filters as $filter_id) {
            $filter_info = $this->model_shopmanager_filter->getFilter($filter_id);

            if ($filter_info) {
                $data['category_filters'][] = array(
                    'filter_id' => $filter_info['filter_id'],
                    'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
                );
            }
        }

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['category_store'])) {
            $data['category_store'] = $this->request->post['category_store'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['category_store'] = $this->model_shopmanager_catalog_category_ebay->getCategoryEbayStores($this->request->get['category_id']);
        } else {
            $data['category_store'] = array(0);
        }

    /*	if (isset($this->request->post['keyword'])) {
            $data['keyword'] = $this->request->post['keyword'];
        } elseif (!empty($category_info)) {
            $data['keyword'] = $category_info['keyword'];
        } else {
            $data['keyword'] = '';
        }*/

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($category_info)) {
            $data['image'] = $category_info['image'];
        } else {
            $data['image'] = '';
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
        } elseif (!empty($category_info) && is_file(DIR_IMAGE . $category_info['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($category_info['image'], 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->request->post['top'])) {
            $data['top'] = $this->request->post['top'];
        } elseif (!empty($category_info)) {
            $data['top'] = $category_info['top'];
        } else {
            $data['top'] = 0;
        }

        if (isset($this->request->post['column'])) {
            $data['column'] = $this->request->post['column'];
        } elseif (!empty($category_info)) {
            $data['column'] = $category_info['column'];
        } else {
            $data['column'] = 1;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($category_info)) {
            $data['sort_order'] = $category_info['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($category_info)) {
            $data['status'] = $category_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['category_layout'])) {
            $data['category_layout'] = $this->request->post['category_layout'];
        } elseif (isset($this->request->get['category_id'])) {
            $data['category_layout'] = $this->model_shopmanager_catalog_category_ebay->getCategoryEbayLayouts($this->request->get['category_id']);
        } else {
            $data['category_layout'] = array();
        }

        $this->load->model('design/layout');
        //print("<pre>".print_r ($data['category_description'],true )."</pre>");
        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
        $data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
        $data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

        $this->response->setOutput($this->load->view('shopmanager/catalog/category_ebay_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'shopmanager/catalog/category_ebay')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        foreach ($this->request->post['category_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
                $this->error['name'][$language_id] = ($lang['error_name'] ?? '');
            }

            if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
                $this->error['meta_title'][$language_id] = ($lang['error_meta_title'] ?? '');
            }
        }

        if (isset($this->request->get['category_id']) && $this->request->post['parent_id']) {
            $results = $this->model_shopmanager_catalog_category_ebay->getCategoryEbayPath($this->request->post['parent_id']);
            
            foreach ($results as $result) {
                if ($result['path_id'] == $this->request->get['category_id']) {
                    $this->error['parent'] = ($lang['error_parent'] ?? '');
                    
                    break;
                }
            }
        }

    /*	if (utf8_strlen($this->request->post['keyword']) > 0) {
            $this->load->model('shopmanager/url_alias');

            $url_alias_info = $this->model_shopmanager_url_alias->getUrlAlias($this->request->post['keyword']);

            if ($url_alias_info && isset($this->request->get['category_id']) && $url_alias_info['query'] != 'category_id=' . $this->request->get['category_id']) {
                $this->error['keyword'] = sprintf(($lang['error_keyword'] ?? ''));
            }

            if ($url_alias_info && !isset($this->request->get['category_id'])) {
                $this->error['keyword'] = sprintf(($lang['error_keyword'] ?? ''));
            }
        }*/
        
        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = ($lang['error_warning'] ?? '');
        }
        
        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'shopmanager/catalog/category_ebay')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        return !$this->error;
    }

    protected function validateRepair() {
        if (!$this->user->hasPermission('modify', 'shopmanager/catalog/category_ebay')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        return !$this->error;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {
            $this->load->model('shopmanager/catalog/category_ebay');

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'sort'        => 'name',
                'order'       => 'ASC',
                'start'       => 0,
                'limit'       => 5
            );

            $results = $this->model_shopmanager_catalog_category_ebay->getCategories($filter_data);

            foreach ($results as $result) {
                $json[] = array(
                    'category_id' => $result['category_id'],
                    'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
                );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCategoryEbayDetails() {
        $this->load->model('shopmanager/catalog/category_ebay');

        $category_id = $this->request->get['category_id'];
    
        $category_info = $this->model_shopmanager_catalog_category_ebay->getCategoryEbay($category_id);
    
        if ($category_info) {
            $parents = $this->model_shopmanager_catalog_category_ebay->getCategoryEbayPath($category_id);
            $category_info['parents'] = array();
    
            foreach ($parents as $parent) {
                if ($parent['path_id'] != $category_id) {
                    $parent_info = $this->model_shopmanager_catalog_category_ebay->getCategoryEbay($parent['path_id']);
                    if ($parent_info) {
                        $category_info['parents'][] = array(
                            'id' => $parent_info['category_id'],
                            'name' => $parent_info['name']
                        );
                    }
                }
            }
        //	//print("<pre>".print_r ($category_info,true )."</pre>");
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($category_info));
        } else {
            $this->response->addHeader('HTTP/1.0 404 Not Found');
            $this->response->setOutput(json_encode(array()));
        }
    }
    public function uploadFromLink() {
        $lang = $this->load->language('shopmanager/catalog/category_ebay');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/catalog/category_ebay');
        $json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $category_id = $data['category_id'];
            $piclink = $data['piclink'];
			//$category_id = 617;
			//$piclink="https://oaidalleapiprodscus.blob.core.windows.net/private/org-nSt4WnFqJ0wdsi2ZCS3WkgOQ/user-3H4ZAk7jse8UZPaMlnQEAig8/img-oHnOdAtZbETsPgd1p5dhrhNc.png?st=2024-08-15T03%3A14%3A06Z&se=2024-08-15T05%3A14%3A06Z&sp=r&sv=2024-08-04&sr=b&rscd=inline&rsct=image/png&skoid=d505667d-d6c1-4a0a-bac7-5c84a87759f8&sktid=a48cca56-e6da-484e-a814-9c849652bcb3&skt=2024-08-14T18%3A04%3A30Z&ske=2024-08-15T18%3A04%3A30Z&sks=b&skv=2024-08-04&sig=MbtTTQ0xDB/uPVUCJTHcZDwgCNGKZRqIvI82ynsqlt8%3D";
            $result = $this->model_shopmanager_catalog_category_ebay->uploadImageFromLink($category_id, $piclink);

            if ($result['success']) {
                $json['success'] = ($lang['text_success'] ?? '');
                $json['image_url'] = $result['image_url'];
            } else {
                $json['error'] = $result['error'];
            }
        } else {
          $json['error'] = ($lang['error_missing_data'] ?? '');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
	
	
}
