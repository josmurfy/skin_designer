<?php
/**
 * @version [Supported opencart version 3.x.x.x.]
 * @category Webkul
 * @package Opencart Multi Account Ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
 class ControllerEbayMapPriceQtyRule extends Controller {

   private $error = array();

   public function index() {
     $this->load->language('ebay_map/price_qty_rule');

     $this->document->setTitle($this->language->get('heading_title'));

     $this->load->model('ebay_map/price_qty_rule');

     $this->getList();
   }

   public function addRule() {
     $this->load->language('ebay_map/price_qty_rule');

     $this->document->setTitle($this->language->get('text_add'));

     $this->load->model('ebay_map/price_qty_rule');

     if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
       $this->model_ebay_map_price_qty_rule->addRule($this->request->post);

       $this->session->data['success'] = $this->language->get('text_success');

       $url = '';

 			if (isset($this->request->get['filter_value'])) {
 				$url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
 			}

 			if (isset($this->request->get['filter_rule_for'])) {
 				$url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
 			}

 			if (isset($this->request->get['filter_min'])) {
 				$url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
 			}

 			if (isset($this->request->get['filter_max'])) {
 				$url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
 			}

 			if (isset($this->request->get['filter_portation'])) {
 				$url .= '&filter_portation=' . $this->request->get['filter_portation'];
 			}
      if (isset($this->request->get['filter_operation_type'])) {
 				$url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
 			}
      if (isset($this->request->get['filter_operation'])) {
 				$url .= '&filter_operation=' . $this->request->get['filter_operation'];
 			}

      if (isset($this->request->get['filter_sort_order'])) {
         $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
      }

      if (isset($this->request->get['filter_status'])) {
 				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

 			$this->response->redirect($this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url, true));
     }

     $this->getForm();
   }

   public function editRule() {
     $this->load->language('ebay_map/price_qty_rule');

     $this->document->setTitle($this->language->get('text_edit'));

     $this->load->model('ebay_map/price_qty_rule');

     if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

       $this->model_ebay_map_price_qty_rule->updateRule($this->request->get['rule_id'], $this->request->post);

       $this->session->data['success'] = $this->language->get('text_success_edit');

       $url = '';

       if (isset($this->request->get['filter_value'])) {
         $url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
       }

       if (isset($this->request->get['filter_rule_for'])) {
         $url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
       }

       if (isset($this->request->get['filter_min'])) {
         $url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
       }

       if (isset($this->request->get['filter_max'])) {
         $url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
       }

       if (isset($this->request->get['filter_portation'])) {
         $url .= '&filter_portation=' . $this->request->get['filter_portation'];
       }
      if (isset($this->request->get['filter_operation_type'])) {
        $url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
      }
      if (isset($this->request->get['filter_operation'])) {
        $url .= '&filter_operation=' . $this->request->get['filter_operation'];
      }
      if (isset($this->request->get['filter_sort_order'])) {
        $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
      }
      if (isset($this->request->get['filter_status'])) {
        $url .= '&filter_status=' . $this->request->get['filter_status'];
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

      $this->response->redirect($this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url, true));
     }

     $this->getForm();
   }

   public function deleteRule() {
 		$this->load->language('ebay_map/price_qty_rule');

 		$this->document->setTitle($this->language->get('heading_title'));

 		$this->load->model('ebay_map/price_qty_rule');

 		if (isset($this->request->post['selected']) && $this->validateDelete()) {
 			foreach ($this->request->post['selected'] as $rule_id) {
 				$this->model_ebay_map_price_qty_rule->deleteRule($rule_id);
 			}

 			$this->session->data['success'] = $this->language->get('text_success_delete');

      $url = '';

      if (isset($this->request->get['filter_value'])) {
        $url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
      }

      if (isset($this->request->get['filter_rule_for'])) {
        $url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
      }

      if (isset($this->request->get['filter_min'])) {
        $url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
      }

      if (isset($this->request->get['filter_max'])) {
        $url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
      }

     if (isset($this->request->get['filter_portation'])) {
        $url .= '&filter_portation=' . $this->request->get['filter_portation'];
     }
     if (isset($this->request->get['filter_operation_type'])) {
        $url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
     }
     if (isset($this->request->get['filter_operation'])) {
       $url .= '&filter_operation=' . $this->request->get['filter_operation'];
     }
     if (isset($this->request->get['filter_sort_order'])) {
       $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
     }

     if (isset($this->request->get['filter_status'])) {
       $url .= '&filter_status=' . $this->request->get['filter_status'];
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

	   $this->response->redirect($this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url, true));
 		}

 		$this->getList();
 	}

  public function getList() {
    $data = $this->load->language('ebay_map/price_qty_rule');

    $url = '';

    if (isset($this->request->get['filter_value'])) {
      $filter_value = $this->request->get['filter_value'];
    } else {
      $filter_value = '';
    }

    if (isset($this->request->get['filter_rule_for'])) {
      $filter_rule_for = $this->request->get['filter_rule_for'];
    } else {
      $filter_rule_for = '';
    }

    if (isset($this->request->get['filter_min'])) {
      $filter_min = $this->request->get['filter_min'];
    } else {
      $filter_min = '';
    }

    if (isset($this->request->get['filter_max'])) {
      $filter_max = $this->request->get['filter_max'];
    } else {
      $filter_max = '';
    }

    if (isset($this->request->get['filter_portation'])) {
      $filter_portation = $this->request->get['filter_portation'];
    } else {
      $filter_portation = '';
    }

    if (isset($this->request->get['filter_operation_type'])) {
      $filter_operation_type = $this->request->get['filter_operation_type'];
    } else {
      $filter_operation_type = '';
    }

    if (isset($this->request->get['filter_operation'])) {
      $filter_operation = $this->request->get['filter_operation'];
    } else {
      $filter_operation = '';
    }

    if (isset($this->request->get['filter_sort_order'])) {
      $filter_sort_order = $this->request->get['filter_sort_order'];
    } else {
      $filter_sort_order = '';
    }

    if (isset($this->request->get['filter_status'])) {
      $filter_status = $this->request->get['filter_status'];
    } else {
      $filter_status = '';
    }

    if (isset($this->request->get['sort'])) {
      $sort = $this->request->get['sort'];
    } else {
      $sort = 'min';
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

    $url = '';

    if (isset($this->request->get['filter_value'])) {
      $url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_rule_for'])) {
      $url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_min'])) {
      $url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_max'])) {
      $url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_portation'])) {
      $url .= '&filter_portation=' . $this->request->get['filter_portation'];
    }
    if (isset($this->request->get['filter_operation_type'])) {
      $url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
    }
    if (isset($this->request->get['filter_operation'])) {
      $url .= '&filter_operation=' . $this->request->get['filter_operation'];
    }

    if (isset($this->request->get['filter_sort_order'])) {
      $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
    }

    if (isset($this->request->get['filter_status'])) {
      $url .= '&filter_status=' . $this->request->get['filter_status'];
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
      'href' => $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url, true)
    );

    $data['add'] = $this->url->link('ebay_map/price_qty_rule/addRule', 'user_token=' . $this->session->data['token'] . $url, true);

    $data['delete'] = $this->url->link('ebay_map/price_qty_rule/deleteRule', 'user_token=' . $this->session->data['token'] . $url, true);

    $filter_data = array(
      'filter_value'	        => $filter_value,
      'filter_min'	          => $filter_min,
      'filter_max'	          => $filter_max,
      'filter_rule_for'	      => $filter_rule_for,
      'filter_portation'      => $filter_portation,
      'filter_operation'      => $filter_operation,
      'filter_operation_type' => $filter_operation_type,
      'filter_sort_order'     => $filter_sort_order,
      'filter_status'         => $filter_status,
      'sort'                  => $sort,
      'order'                 => $order,
      'start'                 => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit'                 => $this->config->get('config_limit_admin')
    );

    $rule_total = $this->model_ebay_map_price_qty_rule->getTotalRule($filter_data);
    $results = $this->model_ebay_map_price_qty_rule->getRules($filter_data);

    $data['rules'] = array();

    foreach ($results as $result) {

      if ($result['rule_for'] == 'qty') {
        $result['value'] = (int)$result['value'];
        $rule_for = $this->language->get('text_quantity');
      } else {
        $rule_for = $this->language->get('text_price');
      }
      if ($result['portation'] == 'import') {
        $portation = $this->language->get('text_import');
      } else {
        $portation = $this->language->get('text_export');
      }
      if ($result['operation'] == 'fixed') {
        $operation = $this->language->get('text_fixed');
      } else {
        $operation = $this->language->get('text_percentage');
      }
      if ($result['status'] == 1) {
        $status = $this->language->get('text_enabled');
      } else {
        $status = $this->language->get('text_disabled');
      }
      if ($result['operation_type'] == '+') {
        $operation_type = $this->language->get('text_increment');
      } else {
        $operation_type = $this->language->get('text_decrement');
      }
      $data['rules'][] = array(
        'rule_id'        => $result['id'],
        'value'          => $result['value'],
        'portation'      => $portation,
        'min'            => $result['rule_for'] == 'qty' ? (int)$result['min'] : $result['min'],
        'max'            => $result['rule_for'] == 'qty' ? (int)$result['max'] : $result['max'],
        'operation'      => $operation,
        'operation_type' => $operation_type,
        'rule_for'       => $rule_for,
        'status'         => $status,
        'edit'           => $this->url->link('ebay_map/price_qty_rule/editRule&rule_id=' . $result['id'], 'user_token=' . $this->session->data['token'], true)
      );
    }

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

    if (isset($this->request->get['filter_value'])) {
      $url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_rule_for'])) {
      $url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_min'])) {
      $url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_max'])) {
      $url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_portation'])) {
      $url .= '&filter_portation=' . $this->request->get['filter_portation'];
    }
    if (isset($this->request->get['filter_operation_type'])) {
      $url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
    }
    if (isset($this->request->get['filter_operation'])) {
      $url .= '&filter_operation=' . $this->request->get['filter_operation'];
    }

    if (isset($this->request->get['filter_sort_order'])) {
      $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
    }

    if (isset($this->request->get['filter_status'])) {
      $url .= '&filter_status=' . $this->request->get['filter_status'];
    }

    if ($order == 'ASC') {
      $url .= '&order=DESC';
    } else {
      $url .= '&order=ASC';
    }

    if (isset($this->request->get['page'])) {
      $url .= '&page=' . $this->request->get['page'];
    }

    $data['sort_rule_for'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=rule_for' . $url, true);
    $data['sort_min'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=min' . $url, true);
    $data['sort_max'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=max' . $url, true);
    $data['sort_portation'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=portation' . $url, true);
    $data['sort_operation'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=operation' . $url, true);
    $data['sort_operation_type'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=operation_type' . $url, true);
    $data['sort_value'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=value' . $url, true);
    $data['sort_status'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . '&sort=status' . $url, true);

    $url = '';

    if (isset($this->request->get['filter_value'])) {
      $url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_rule_for'])) {
      $url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_min'])) {
      $url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_max'])) {
      $url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_portation'])) {
      $url .= '&filter_portation=' . $this->request->get['filter_portation'];
    }
    if (isset($this->request->get['filter_operation_type'])) {
      $url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
    }
    if (isset($this->request->get['filter_operation'])) {
      $url .= '&filter_operation=' . $this->request->get['filter_operation'];
    }

    if (isset($this->request->get['filter_sort_order'])) {
      $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
    }

    if (isset($this->request->get['filter_status'])) {
      $url .= '&filter_status=' . $this->request->get['filter_status'];
    }

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $rule_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($rule_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($rule_total - $this->config->get('config_limit_admin'))) ? $rule_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $rule_total, ceil($rule_total / $this->config->get('config_limit_admin')));

    $data['filter_value'] = $filter_value;
    $data['filter_min'] = $filter_min;
    $data['filter_max'] = $filter_max;
    $data['filter_rule_for'] = $filter_rule_for;
    $data['filter_portation'] = $filter_portation;
    $data['filter_operation_type'] = $filter_operation_type;
    $data['filter_status'] = $filter_status;
    $data['sort'] = $sort;
    $data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ebay_map/price_qty_rule', $data));
  }

  public function getForm() {
    $data = $this->load->language('ebay_map/price_qty_rule');
    $data['text_form'] = !isset($this->request->get['rule_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
    $error_arr = array(
      'warning',
      'value',
      'min',
      'max',
      'sort_order'
    );

    foreach ($error_arr as $error) {
      if (isset($this->error[$error])) {
        $data['error_' . $error] = $this->error[$error];
      } else {
        $data['error_' . $error] = '';
      }
    }

    $url = '';

    if (isset($this->request->get['filter_value'])) {
      $url .= '&filter_value=' . urlencode(html_entity_decode($this->request->get['filter_value'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_rule_for'])) {
      $url .= '&filter_rule_for=' . urlencode(html_entity_decode($this->request->get['filter_rule_for'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_min'])) {
      $url .= '&filter_min=' . urlencode(html_entity_decode($this->request->get['filter_min'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_max'])) {
      $url .= '&filter_max=' . urlencode(html_entity_decode($this->request->get['filter_max'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_portation'])) {
      $url .= '&filter_portation=' . $this->request->get['filter_portation'];
    }
    if (isset($this->request->get['filter_operation_type'])) {
      $url .= '&filter_operation_type=' . $this->request->get['filter_operation_type'];
    }
    if (isset($this->request->get['filter_operation'])) {
      $url .= '&filter_operation=' . $this->request->get['filter_operation'];
    }

    if (isset($this->request->get['filter_sort_order'])) {
      $url .= '&filter_sort_order=' . $this->request->get['filter_sort_order'];
    }

    if (isset($this->request->get['filter_status'])) {
      $url .= '&filter_status=' . $this->request->get['filter_status'];
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
      'href' => $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url, true)
    );

    if (!isset($this->request->get['rule_id'])) {
      $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_add'),
        'href' => $this->url->link('ebay_map/price_qty_rule/addRule', 'user_token=' . $this->session->data['token'] . $url, true)
      );
    } else {
      $data['breadcrumbs'][] = array(
        'text' => $this->language->get('text_edit'),
        'href' => $this->url->link('ebay_map/price_qty_rule/editRule&rule_id=' . $this->request->get['rule_id'], 'user_token=' . $this->session->data['token'] . $url, true)
      );
    }

    if (!isset($this->request->get['rule_id'])) {
			$data['action'] = $this->url->link('ebay_map/price_qty_rule/addRule', 'user_token=' . $this->session->data['token'] . $url, true);

		} else {

    	$data['action'] = $this->url->link('ebay_map/price_qty_rule/editRule', 'user_token=' . $this->session->data['token'] . '&rule_id=' . $this->request->get['rule_id'] . $url, true);

		}

    $data['cancel'] = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'], true);

    if (isset($this->request->get['rule_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      $this->load->model('ebay_map/price_qty_rule');
			$rule_info = $this->model_ebay_map_price_qty_rule->getRule($this->request->get['rule_id']);
		}

		$data['token'] = $this->session->data['token'];

    $post_data = array(
      'rule_for',
      'portation',
      'min',
      'max',
      'operation_type',
      'operation',
      'value',
      'sort_order',
      'status'
    );
    foreach ($post_data as $post) {
      if (isset($this->request->post[$post])) {
        $data[$post] = $this->request->post[$post];
      } else if (isset($rule_info) && isset($rule_info[$post])) {
        $data[$post] = $rule_info[$post];
      } else {
        $data[$post] = '';
      }
    }
    if ($data['rule_for'] == 'qty') {
      $data['min'] = (int)$data['min'];
      $data['max'] = (int)$data['max'];
      $data['value'] = (int)$data['value'];
    }

    $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('ebay_map/price_qty_rule_form', $data));
  }
  protected function validateDelete() {
    if (!$this->user->hasPermission('modify', 'ebay_map/price_qty_rule')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
    return !$this->error;
  }
  protected function validateForm() {

    if (!$this->user->hasPermission('modify', 'ebay_map/price_qty_rule')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
    if ($this->request->post['value'] == '' || $this->request->post['value'] == 0) {
      $this->error['value'] = $this->language->get('error_value');
    }
    if ($this->request->post['min'] == '') {
      $this->error['min'] = sprintf($this->language->get('error_min'), $this->request->post['rule_for']);
    }
    if ($this->request->post['sort_order'] == '') {
      $this->error['sort_order'] = $this->language->get('error_sort_order');
    }
    if ($this->request->post['max'] == '' || ($this->request->post['max'] <= $this->request->post['min'])) {
      $this->error['max'] = sprintf($this->language->get('error_max'), $this->request->post['rule_for'], $this->request->post['rule_for']);
    }

    return !$this->error;
  }
 }
