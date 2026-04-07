<?php
/**
 * @version [Supported opencart version 3.x.x.x.]
 * @category Webkul
 * @package Opencart Opencart eBay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapSellerCategory extends Controller {
  private $error = array();
  private $count = 0;
  public function index() {

    $this->load->language('ebay_map/seller_category');

    $this->document->setTitle($this->language->get('heading_title'));
    // $this->exportToEbay();
    $this->getList();
  }

  public function exportToEbay() {
    $this->load->language('ebay_map/seller_category');

    $this->document->setTitle($this->language->get('text_add'));

    $this->load->model('ebay_map/seller_category');
    // && $this->validateForm()
    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {

      $this->model_ebay_map_seller_category->addEbaySellerCategory($this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

    }

    $this->getForm();
  }

  protected function getForm() {
    $data = $this->load->language('ebay_map/seller_category');
    $data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

    if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

    $url = '';

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'] . $url, true)
		);

    $data['action'] = $this->url->link('ebay_map/seller_category/exportToEbay', 'user_token=' . $this->session->data['token'], true);

    if (isset($this->request->get['id'])) {
      $data['action'] = $this->url->link('ebay_map/seller_category/exportToEbay', 'user_token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'], true);
    }

    $data['cancel'] = $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'], true);

    $errorr_arr = array(
      'warning',
      'destination_parent_category',
      'item_destination_category',
      'name',
      'sort_order'
    );

    foreach ($errorr_arr as $error) {
      if (isset($this->error[$error])) {
        $data['error_' . $error] = $this->error[$error];
      } else {
        $data['error_' . $error] = '';
      }
    }

    $post_data = array(
      'id',
      'account_id',
      'top_parent',
      'name',
      'destination_parent_category_id',
      'destination_parent_category',
      'item_destination_category_id',
      'item_destination_category',
      'status',
      'build',
      'timestamps',
      'sort_order'
    );

    $seller_category = array();

    if (isset($this->request->get['id'])) {
      $seller_category = $this->model_ebay_map_seller_category->getEbaySellerCategory($this->request->get['id']);
    }

    foreach ($post_data as $post) {
      if (isset($this->request->post[$post])) {
        $data[$post] = $this->request->post[$post];
      } else if (isset($seller_category[$post])) {
        $data[$post] = $seller_category[$post];
      } else {
        $data[$post] = '';
      }
    }

    $data['ebay_accounts'] = array();

    $this->load->model('ebay_map/ebay_account');
    $ebay_accounts = $this->model_ebay_map_ebay_account->getEbayAccount();
    if ($ebay_accounts) {
      foreach ($ebay_accounts as $account) {
        $data['ebay_accounts'][] = array(
          'account_id'          => $account['id'],
          'store_name'          => $account['ebay_connector_store_name']
        );
      }
    }

    $data['token'] = $this->session->data['token'];
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('ebay_map/seller_category_form', $data));
  }

  protected function getList() {
    $data = $this->load->language('ebay_map/seller_category');
    $url = '';

    if (isset($this->request->get['filter_account_id'])) {
      $url .= '&filter_account_id=' . $this->request->get['filter_account_id'];
    }

    if (isset($this->request->get['filter_ebay_category_id'])) {
      $url .= '&filter_ebay_category_id=' . $this->request->get['filter_ebay_category_id'];
    }

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
    }

    if (isset($this->request->get['filter_category_level'])) {
      $url .= '&filter_category_level=' . $this->request->get['filter_category_level'];
    }

    if (isset($this->request->get['filter_ebay_category_name'])) {
      $url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
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
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'] . $url, true)
    );

    $data['token'] = $this->session->data['token'];

    $this->load->model('ebay_map/ebay_account');

    $data['accounts'] = $this->model_ebay_map_ebay_account->getEbayAccount();

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

    if (isset($this->request->get['filter_account_id'])) {
      $filter_account_id = $this->request->get['filter_account_id'];
    } else {
      $filter_account_id = '';
    }

    if (isset($this->request->get['filter_ebay_category_id'])) {
      $filter_ebay_category_id = $this->request->get['filter_ebay_category_id'];
    } else {
      $filter_ebay_category_id = '';
    }

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $filter_ebay_site_id = $this->request->get['filter_ebay_site_id'];
    } else {
      $filter_ebay_site_id = '';
    }

    if (isset($this->request->get['filter_category_level'])) {
      $filter_category_level = $this->request->get['filter_category_level'];
    } else {
      $filter_category_level = '';
    }

    if (isset($this->request->get['filter_ebay_category_name'])) {
      $filter_ebay_category_name = $this->request->get['filter_ebay_category_name'];
    } else {
      $filter_ebay_category_name = '';
    }

    if (isset($this->request->get['sort'])) {
      $sort = $this->request->get['sort'];
    } else {
      $sort = null;
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

    $filter_data = array(
      'filter_account_id'          => $filter_account_id,
      'filter_ebay_category_id'    => $filter_ebay_category_id,
      'filter_ebay_site_id'        => $filter_ebay_site_id,
      'filter_category_level'      => $filter_category_level,
      'filter_ebay_category_name'  => $filter_ebay_category_name,
      'sort'                       => $sort,
      'order'                      => $order,
      'start'                      => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit'                      => $this->config->get('config_limit_admin')
    );

    $this->load->model('ebay_map/seller_category');

    $results = $this->model_ebay_map_seller_category->getEbaySellerCategories($filter_data);

    $total = $this->model_ebay_map_seller_category->getTotalEbaySellerCategory($filter_data);

    $data['categories'] = array();


    $data['ebay_sites'] = $this->Ebayconnector->getEbaySiteList();
    $ebay_sites = $data['ebay_sites']['ebay_sites'];
    $data['ebay_sites'] = $ebay_sites;

    foreach ($results as $result) {
      $data['categories'][] = array(
        'id'                          => $result['id'],
        'ebay_category_id'            => $result['ebay_category_id'],
        'ebay_category_name'          => $result['ebay_category_name'],
        'ebay_category_level'         => $result['ebay_category_level'],
        'ebay_site_id'                => $result['ebay_site_id'],
        'ebay_site_name'              => $ebay_sites[$result['ebay_site_id']],
        'ebay_connector_store_name'   => $result['ebay_connector_store_name']
      );
    }

    $url = '';

    if (isset($this->request->get['filter_account_id'])) {
      $url .= '&filter_account_id=' . $this->request->get['filter_account_id'];
    }

    if (isset($this->request->get['filter_ebay_category_id'])) {
      $url .= '&filter_ebay_category_id=' . $this->request->get['filter_ebay_category_id'];
    }

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
    }

    if (isset($this->request->get['filter_category_level'])) {
      $url .= '&filter_category_level=' . $this->request->get['filter_category_level'];
    }

    if (isset($this->request->get['filter_ebay_category_name'])) {
      $url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
    }

    if ($order == 'ASC') {
      $url .= '&order=DESC';
    } else {
      $url .= '&order=ASC';
    }

    if (isset($this->request->get['page'])) {
      $url .= '&page=' . $this->request->get['page'];
    }

    $data['sort_category_name'] = $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'] . $url . '&sort=ebay_category_name', true);
    $data['sort_category_id'] = $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'] . $url . '&sort=wec.ebay_category_id', true);
    $data['sort_category_level'] = $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'] . $url . '&sort=ebay_category_level', true);

    $url = '';

    if (isset($this->request->get['filter_account_id'])) {
      $url .= '&filter_account_id=' . $this->request->get['filter_account_id'];
    }

    if (isset($this->request->get['filter_ebay_category_id'])) {
      $url .= '&filter_ebay_category_id=' . $this->request->get['filter_ebay_category_id'];
    }

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
    }

    if (isset($this->request->get['filter_category_level'])) {
      $url .= '&filter_category_level=' . $this->request->get['filter_category_level'];
    }

    if (isset($this->request->get['filter_ebay_category_name'])) {
      $url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['sort'])) {
      $url .= '&sort=' . $this->request->get['sort'];
    }

    if (isset($this->request->get['order'])) {
      $url .= '&order=' . $this->request->get['order'];
    }

    $pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/seller_category', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

    $data['filter_account_id'] = $filter_account_id;
    $data['filter_ebay_category_id'] = $filter_ebay_category_id;
    $data['filter_ebay_category_name'] = $filter_ebay_category_name;
    $data['filter_category_level'] = $filter_category_level;
    $data['filter_ebay_site_id'] = $filter_ebay_site_id;
    $data['row'] = $this->config->get('ebay_connector_category_row');
    $data['sort'] = $sort;
    $data['order'] = $order;

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('ebay_map/seller_category_list', $data));

  }

  public function fetchEbayCategories() {

    $this->load->language('ebay_map/seller_category');
    $json = array();

    if (isset($this->request->post['account_id'])) {

      $ebay_client = $this->Ebayconnector->_eBayAuthSession($this->request->post['account_id']);

      $account_info = $this->Ebayconnector->getEbayStoreDetails($this->request->post['account_id']);

      if ($ebay_client) {
        $request_credentails = array(
          'eBayAuthToken' => $account_info['ebay_connector_ebay_auth_token']
        );

        $category_param = array(
          'RequesterCredentials' => $request_credentails,
          'Version'              => 1009,
          'LevelLimit'           => 3
        );

        try {

          $count = 0;

          $result = $ebay_client->GetStore($category_param);

          if (isset($result->Store->CustomCategories->CustomCategory) && $result->Store->CustomCategories->CustomCategory) {

            $count = $this->countCategory($result->Store->CustomCategories->CustomCategory);

            if ($count > 0) {
              $json['success'] = sprintf($this->language->get('text_success_fetch'), $count);

              $json['total_category'] = $count;

              $this->load->model('ebay_map/seller_category');
              $data = $result->Store->CustomCategories->CustomCategory;

              $account_id = $this->request->post['account_id'];

              $this->model_ebay_map_seller_category->saveSellerCategories($data, $count, $account_id);

            } else {
              $json['error'] = $this->language->get('error_no_category_found');
            }
          } else {
            $json['error'] = $this->language->get('error_no_category_found');
          }
        } catch (\Exception $e) {
          $json['error'] = $e->getMessage();
        }
      } else {
        $json['error'] = $this->language->get('error_account');
      }
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  private function countCategory($categories) {
    // Level 1
    foreach ($categories as $category) {
      if (is_object($category)) {
        $this->count ++;
      }
      // Level 2
      if (isset($category->ChildCategory) && $category->ChildCategory && is_array($category->ChildCategory)) {
        foreach ($category->ChildCategory as $child_category) {
          $this->count ++;
          // Level 3
          if (isset($child_category->ChildCategory)) {
            foreach ($child_category->ChildCategory as $child) {
              $this->count ++;
            }
          }
        }
      }
    }
    return $this->count;
  }

  public function importCategories() {
    $json = array();
    $this->load->language('ebay_map/seller_category');
    if (isset($this->request->post['account_id']) && isset($this->request->post['start']) && $this->validate()) {

      $this->load->model('ebay_map/seller_category');
      $data = $this->model_ebay_map_seller_category->getSellerCategoryData($this->request->post['account_id']);
      if ($data && isset($data['data'])) {

        $row = (int)$this->config->get('ebay_connector_category_row');
        if (!$row) {
          $row = 20;
        }
        $start = (int)$this->request->post['start'];
        $account_id = $this->request->post['account_id'];
        $data = json_decode($data['data'], true);
        $site_id = $this->model_ebay_map_seller_category->getSiteId($account_id);
        $categories = array();
        $ebay_count = 0;
        // Level 1
        foreach ($data as $category) {
          if ($ebay_count <= ($row + $start)) {
            $categories[] = array(
              'ebay_category_id'         => $category['CategoryID'],
              'ebay_category_parent_id'  => $category['CategoryID'],
              'ebay_category_level'      => 1,
              'ebay_category_name'       => $category['Name'],
              'ebay_site_id'             => $site_id,
              'account_id'               => $account_id
            );
          }
          $ebay_count ++;
          // Level 2
          if (isset($category['ChildCategory'])) {
            foreach ($category['ChildCategory'] as $child_category) {
              if ($ebay_count <= ($row + $start)) {
                $categories[] = array(
                  'ebay_category_id'         => $child_category['CategoryID'],
                  'ebay_category_parent_id'  => $category['CategoryID'],
                  'ebay_category_level'      => 2,
                  'ebay_category_name'       => $child_category['Name'],
                  'ebay_site_id'             => $site_id,
                  'account_id'               => $account_id
                );
              }
              $ebay_count ++;
              // Level 3
              if (isset($child_category['ChildCategory'])) {
                foreach ($child_category['ChildCategory'] as $child) {
                  if ($ebay_count <= ($row + $start)) {
                    $categories[] = array(
                      'ebay_category_id'         => $child_category['CategoryID'],
                      'ebay_category_parent_id'  => $child['CategoryID'],
                      'ebay_category_level'      => 3,
                      'ebay_category_name'       => $child['Name'],
                      'ebay_site_id'             => $site_id,
                      'account_id'               => $account_id
                    );
                  }
                  $ebay_count ++;
                }
              }
            }
          }
        }
        $category_data = array_slice($categories, $start, $row);
        if ($category_data) {
          $this->model_ebay_map_seller_category->addEbaySellerCategory($category_data);
          $json['success'] = sprintf($this->language->get('text_success_import'), count($category_data));
        }
      } else {
        $json['error'] = $this->language->get('error_no_category_found');
      }
    }
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  public function eBayCategoryAutocomplete() {
    $json = array();

    if (isset($this->request->get['filter_ebay_category'])) {
      $this->load->model('ebay_map/seller_category');
      if (isset($this->request->get['child']) && $this->request->get['child'] == 1) {
        $categories = $this->model_ebay_map_seller_category->getEbayCategories(array('filter_ebay_category_name' => $this->request->get['filter_ebay_category'], 'child' => true));
      } else {
        $categories = $this->model_ebay_map_seller_category->getEbayCategories(array('filter_ebay_category_name' => $this->request->get['filter_ebay_category']));
      }

      foreach ($categories as $category) {
        $json[] = array(
          'ebay_category_id'    => $category['ebay_category_id'],
          'ebay_category_name'  => $category['ebay_category_name']
        );
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'ebay_map/seller_category')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
    return !$this->error;
  }

  protected function validateForm() {
    if (!$this->user->hasPermission('modify', 'ebay_map/seller_category')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!isset($this->request->post['top_parent'])) {
      if (!$this->request->post['destination_parent_category_id'] || !$this->request->post['destination_parent_category']) {
        $this->error['destination_parent_category'] = $this->language->get('error_destination_parent_category');
      }
    }

    if (!$this->request->post['item_destination_category_id']) {
      $this->error['item_destination_category'] = $this->language->get('error_item_destination_category');
    }

    if (strlen(trim($this->request->post['name'])) < 3) {
      $this->error['name'] = $this->language->get('error_name');
    }
    if ($this->request->post['sort_order'] == '') {
      $this->error['sort_order'] = $this->language->get('error_sort_order');
    }
    return !$this->error;
  }

}
