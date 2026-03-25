<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerPriceRulesPriceRules extends Controller {
	private $error = array();

	private $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

  private $sorting = array(
		'price_to',
		'ASC',
		 1
	);

  private $post_required_fields = array('price_from','price_to','price_value');

  private $post_optional_fields = array('price_type','price_opration','price_status');

	private $sop_keys = array(
		'sort',
		'order',
		'page'
	);

  private $so_keys = array(
		'sort',
		'order',
	);

  static $rule_rgs;

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('price_rule/price_rule');
		$this->_ebayPriceRule = $this->model_price_rule_price_rule;

  }

  public function index() {

    $this->getList();
	}

  public function getList(){

    $data = array();

		$data = array_merge($data, $this->load->language('ebay_map/rules'));

    $this->document->setTitle($this->language->get('heading_title'));

    $fields = array_merge($this->post_required_fields, $this->post_optional_fields);

    foreach ($fields as $key => $field) {
      if (isset($this->request->get['filter_'.$field])) {
        ${'filter_' . $field} = $this->request->get['filter_'.$field];
      } else {
        ${'filter_' . $field} = null;
      }
    }

    foreach ($this->sop_keys as $key => $sop_key) {
  			if (isset($this->request->get[$sop_key])) {
  				$$sop_key = $this->request->get[$sop_key];
  			} else {
  				$$sop_key = $this->sorting[$key];
  			}
    }

    $url = '';

    foreach ($fields as $key => $field) {
      if (isset($this->request->get['filter_'.$field])) {
        $url .= '&filter_'.$field.'=' . $this->request->get['filter_'.$field];
      }
    }

    foreach ($this->sop_keys as $key => $sop_key) {
      if (isset($this->request->get[$sop_key])) {
        $url .= '&' .$sop_key. '=' . $this->request->get[$sop_key];
      }
    }

    $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true)
		);

    if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

    $filter_data = array(
			'filter_price_to'	      => $filter_price_to,
			'filter_price_from'	    => $filter_price_from,
			'filter_price_value'	  => $filter_price_value,
      'filter_price_type'	    => $filter_price_type,
			'filter_price_opration' => $filter_price_opration,
			'filter_price_status'	  => $filter_price_status,
			'sort'                  => $sort,
			'order'                 => $order,
			'start'                 => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                 => $this->config->get('config_limit_admin')
		);

    $rules_total = $this->_ebayPriceRule->getTotalPriceRules($filter_data);

		$results = $this->_ebayPriceRule->getPriceRules($filter_data);

    $data['rule_list'] = $this->_buildRuleListFormat($url,$results);

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

    $url = '';

    foreach ($fields as $key => $field) {
      if (isset($this->request->get['filter_'.$field])) {
        $url .= '&filter_'.$field.'=' . $this->request->get['filter_'.$field];
      }
    }

    foreach ($this->sop_keys as $key => $sop_key) {
      if (isset($this->request->get[$sop_key])) {
        $url .= '&' .$sop_key. '=' . $this->request->get[$sop_key];
      }
    }

    if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['add_rules'] = $this->url->link('price_rules/price_rules/add', 'token=' . $this->session->data['token'] . $url, true);

		$data['add_csv'] = $this->url->link('price_rules/price_rules/csv', 'token=' . $this->session->data['token'] . $url, true);

    $data['clear'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'], true);

    $data['delete'] = $this->url->link('price_rules/price_rules/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data = array_merge($data, $this->_buildSortingLink($url));

    $url = '';

		foreach ($fields as $key => $field) {
      if (isset($this->request->get['filter_'.$field])) {
        $url .= '&filter_'.$field.'=' . $this->request->get['filter_'.$field];
      }
    }

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

    $pagination = new Pagination();
		$pagination->total = $rules_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($rules_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($rules_total - $this->config->get('config_limit_admin'))) ? $rules_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $rules_total, ceil($rules_total / $this->config->get('config_limit_admin')));

		$data['filter_price_from']     = $filter_price_from;
		$data['filter_price_to']       = $filter_price_to;
		$data['filter_price_value']    = $filter_price_value;
		$data['filter_price_type']     = $filter_price_type;
		$data['filter_price_status']   = $filter_price_status;
		$data['filter_price_opration'] = $filter_price_opration;

		$data['sort'] = $sort;
		$data['order'] = $order;

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    $this->response->setOutput($this->load->view('price_rules/rule_list', $data));
 }

 public function _buildSortingLink($url) {

	  $data = array();

		$data['sort_price_from'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=price_from' . $url, true);

	 	$data['sort_price_to'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=price_to' . $url, true);

	 	$data['sort_price_value'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=price_value' . $url, true);

	 	$data['sort_price_type'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=price_type' . $url, true);

	 	$data['sort_price_opration'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=price_opration' . $url, true);

	 	$data['sort_price_status'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=price_status' . $url, true);

	 	$data['sort_order'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, true);

		return $data;

 }

 public function csv(){
	 $data = array();

	 $data = array_merge($data, $this->load->language('ebay_map/rules'));

   $this->document->setTitle($this->language->get('heading_title_csv'));
   $url = '';

	 if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateCsv()) {

			 $files = $this->request->files;

			 $bulkData = $this->_buildPriceRuleDataFromCsv($files);

			 foreach ($bulkData['csvValues'] as $key => $value) {

					 $this->_ebayPriceRule->addPriceRule($value);

					 $this->session->data['success'] = $this->language->get('text_success_add');

					 $url = '';

					 foreach ($this->sop_keys as $key => $sop_key) {
							 if (isset($this->request->get[$sop_key])) {
								  $url .= '&' .$sop_key. '=' . $this->request->get[$sop_key];
							 }
					 }
			 }

			 $this->response->redirect($this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true));
	 }

	 $data['breadcrumbs'] = array();

	 $data['breadcrumbs'][] = array(
		 'text' => $this->language->get('text_home'),
		 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
	 );

	 $data['breadcrumbs'][] = array(
		 'text' => $this->language->get('heading_title'),
		 'href' => $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true)
	 );

	 $data['breadcrumbs'][] = array(
		 'text' => $this->language->get('heading_title_csv'),
		 'href' => $this->url->link('price_rules/price_rules/csv', 'token=' . $this->session->data['token'] . $url, true)
	 );

   if(isset($this->error['error_csv_file']) && $this->error['error_csv_file']) {
		 $data['error_csv_file'] = $this->error['error_csv_file'];
	 }

	 if(isset($this->error['warning']) && $this->error['warning']) {
		 $data['warning'] = $this->error['warning'];
	 }

	 $data['action'] = $this->url->link('price_rules/price_rules/csv', 'token=' . $this->session->data['token'] . $url, true);

	 $data['cancel'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true);

	 $data['header'] = $this->load->controller('common/header');
	 $data['column_left'] = $this->load->controller('common/column_left');
	 $data['footer'] = $this->load->controller('common/footer');
   $this->response->setOutput($this->load->view('price_rules/rule_csv', $data));
}

public function _buildPriceRuleDataFromCsv($files) {

	 $row = 1;
 	 $csvData = array();
	 $csvKeys = array();
	 $csvValues = array();

 	 if (($csvFile = fopen($files['ebay_rule_csv']['tmp_name'], "r")) !== FALSE) {

 			while (($data = fgetcsv($csvFile, 1000, ",")) !== FALSE) {

 					$num = count($data);

 					$csvData[] = $data;

 					++$row;

 			}
 			fclose($csvFile);
  }

 	foreach ($csvData as $key => $value) {
 		if($key == 0) {
 			$csvKeys = $value;
 		} else {
 			foreach ($value as $keys => $value1) {
 				$csvValues[$key-1][$csvKeys[$keys]] =  $value1;
 			}
 		}
 	}

  $dataValues['csvkeys'] = $csvKeys;
	$dataValues['csvValues'] = $csvValues;

 	return $dataValues;
}

public function _validatePriceRuleColumnNames($csvKeys) {

   $status = false;

	 $columns = array();

   $allCols = $this->_ebayPriceRule->getColumnNames();

   foreach ($allCols as $key => $value) {
     $columns[] = $value['COLUMN_NAME'];
   }

	 if($columns === $csvKeys) {
		 $status = true;
	 }

	 return $status;

}

public function _buildRuleListFormat($url,$results = array()){

    $rules = array();
    foreach ($results as $result) {

      $price_type = '- Decrement'; // Default value
			$price_opration = 'Fixed'; // Default value

      if($result['price_type']) {
        $price_type = '+ Increment';
      }
      if($result['price_opration']) {
        $price_opration = 'Percentage';
      }

      $rules[] = array(
        'id'              => $result['id'],
        'price_from'      => $result['price_from'],
        'price_to'        => $result['price_to'],
        'price_value'     => $result['price_value'],
        'price_type'      => $price_type,
        'price_opration'  => $price_opration,
        'price_status'     => $result['price_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
        'edit'       => $this->url->link('price_rules/price_rules/edit', 'token=' . $this->session->data['token'] . '&id=' . $result['id'] . $url, true)
      );
    }
    return $rules;
  }

  public function add(){

   $data = array();

   $data = array_merge($data, $this->load->language('ebay_map/rules'));

   $url = '';

   $this->document->setTitle($this->language->get('heading_title_add'));

   if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateRuleForm()) {

     $this->_ebayPriceRule->addPriceRule($this->request->post);

     $this->session->data['success'] = $this->language->get('text_success_add');

     $url = '';

     foreach ($this->sop_keys as $key => $sop_key) {
			 if (isset($this->request->get[$sop_key])) {
				 $url .= '&' .$sop_key. '=' . $this->request->get[$sop_key];
			 }
     }

     $this->response->redirect($this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true));
   }
    $this->ruleForm();
  }

	public function edit(){

	 $data = array();

	 $data = array_merge($data, $this->load->language('ebay_map/rules'));

	 $url = '';

	 $this->document->setTitle($this->language->get('heading_title_edit'));

	 if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateRuleForm()) {

		 $this->_ebayPriceRule->editPriceRule($this->request->post,$this->request->get['id']);

		 $this->session->data['success'] = $this->language->get('text_success_edit');

		 $url = '';

		 foreach ($this->sop_keys as $key => $sop_key) {
			 if (isset($this->request->get[$sop_key])) {
				 $url .= '&' .$sop_key. '=' . $this->request->get[$sop_key];
			 }
		 }

		 $this->response->redirect($this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true));
	 }
		$this->ruleForm();
	}

  public function ruleForm(){
     $data = array();

     $data = array_merge($data, $this->load->language('ebay_map/rules'));

     $url = '';

		 if(isset($this->request->get['id']) && $this->request->get['id']) {

			  $data['heading_title'] = $this->language->get('heading_title_edit');
			  $this->document->setTitle($this->language->get('heading_title_edit'));
		 } else {

			 $data['heading_title'] = $this->language->get('heading_title_add');
			 $this->document->setTitle($this->language->get('heading_title_add'));
		 }

     $data['breadcrumbs'] = array();

     $data['breadcrumbs'][] = array(
       'text' => $this->language->get('text_home'),
       'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
     );

     $data['breadcrumbs'][] = array(
       'text' => $this->language->get('heading_title'),
       'href' => $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true)
     );

		 if(isset($this->request->get['id']) && $this->request->get['id']) {
				$data['breadcrumbs'][] = array(
	         'text' => $data['heading_title'],
	         'href' =>  $this->url->link('price_rules/price_rules/edit', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'] . $url, true)
	      );
		 } else {

			 $data['breadcrumbs'][] = array(
	        'text' => $data['heading_title'],
	        'href' => $this->url->link('price_rules/price_rules/add', 'token=' .  $this->session->data['token'] . $url, true)
	     );
		 }

     if (isset($this->error['warning'])) {
 			 $data['error_warning'] = $this->error['warning'];
 		 } else {
 			 $data['error_warning'] = '';
 		 }

     foreach ($this->post_required_fields as $key => $err_value) {
   		 if (isset($this->error['err_'.$err_value])) {

   			 $data['err_'.$err_value] = $this->error['err_'.$err_value];
   		 } else {

   			 $data['err_'.$err_value] = '';
   		 }
     }

		 if(isset($this->error['err_wide_range']) && $this->error['err_wide_range']) {
			 $data['err_price_to'] = $data['err_price_from'] = $this->error['err_wide_range'] ;
		 }

     $price_rule = array();

     if(isset($this->request->get['id']) && $this->request->get['id']) {
			 $price_rule =  $this->_ebayPriceRule->getPriceRule($this->request->get['id']);
		 }

     foreach ($this->post_required_fields as $key => $post_field) {
       if(isset($this->request->post[$post_field]) && $this->request->post[$post_field]) {
         $data[$post_field] = $this->request->post[$post_field];
       } else if(isset($price_rule[$post_field]) && $price_rule[$post_field]) {
         $data[$post_field] = $price_rule[$post_field];
       } else {
         $data[$post_field] = '';
       }
     }

     foreach ($this->post_optional_fields as $key => $post_field) {
       if( isset($this->request->post[$post_field]) &&  $this->request->post[$post_field]) {
         $data[$post_field] = $this->request->post[$post_field];
       } else if(isset($price_rule[$post_field]) && $price_rule[$post_field]) {
         $data[$post_field] = $price_rule[$post_field];
       } else{
         $data[$post_field] = 0;
       }
     }

		 if(isset($this->request->get['id']) && $this->request->get['id']) {
			   $data['action'] = $this->url->link('price_rules/price_rules/edit', 'token=' . $this->session->data['token'] . '&id=' . $this->request->get['id'] . $url, true);
		 } else {
			  $data['action'] = $this->url->link('price_rules/price_rules/add', 'token=' . $this->session->data['token'] . $url, true);
		 }

     $data['cancel'] = $this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true);

     $data['header'] = $this->load->controller('common/header');
     $data['column_left'] = $this->load->controller('common/column_left');
     $data['footer'] = $this->load->controller('common/footer');

     $this->response->setOutput($this->load->view('price_rules/rules_form', $data));
   }

	 public function delete() {
		 $data = array();

 		 $data = array_merge($data, $this->load->language('ebay_map/rules'));

 		if (isset($this->request->post['selected']) && $this->validate()) {
 			foreach ($this->request->post['selected'] as $rule_id) {
 				$this->_ebayPriceRule->deleteRule($rule_id);
 			}

 			$this->session->data['success'] = $this->language->get('text_success_del');

			$url = '';

			foreach ($this->sop_keys as $key => $sop_key) {
				if (isset($this->request->get[$sop_key])) {
					$url .= '&' .$sop_key. '=' . $this->request->get[$sop_key];
				}
			}

			$this->response->redirect($this->url->link('price_rules/price_rules', 'token=' . $this->session->data['token'] . $url, true));

 		}
 		$this->getList();
 	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'price_rules/price_rules')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCsv() {
		if (!$this->user->hasPermission('modify', 'price_rules/price_rules')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

    $rule_rgs = $this->_ebayPriceRule->getPriceRulesRanges();

		$files = $this->request->files;

    if(isset($files['ebay_rule_csv']['name']) && !$files['ebay_rule_csv']['name']) {
			$this->error['error_csv_file']	=	$this->language->get('error_empty_file');
			$this->error['warning']	=	$this->language->get('error_empty_file');
	  } else if(!in_array($files['ebay_rule_csv']['type'],$this->csvMimes)){
			$this->error['error_csv_file']	=	$this->language->get('error_file_type');
			$this->error['warning']	=	$this->language->get('error_file_type');
		} else {

			 $bulkData = $this->_buildPriceRuleDataFromCsv($files);

			 if($this->_validatePriceRuleColumnNames($bulkData['csvkeys'])){
          foreach ($bulkData['csvValues'] as $key => $value) {
							if(!$this->error) {
								 if(isset($value['price_from']) && $value['price_from'] && isset($value['price_to']) && $value['price_to'] && ($value['price_to'] == $value['price_from']) && ($value['price_to'] > $value['price_from'])) {
										  $this->error['error_csv_file']	=	$this->language->get('error_same_value');
										  $this->error['warning']	=	$this->language->get('error_same_value');
									 } else {
											foreach ($value as $key => $post_value) {
												if(in_array($key,$this->post_required_fields)) {
														if(isset($post_value) && !$post_value){
															$this->error['error_csv_file']	=	$this->language->get('error_non_zero');
															$this->error['warning']	=	$this->language->get('error_non_zero');
														}
												}
												
												if($this->_validateNegativeNumber($post_value)){
													$this->error['error_csv_file']		=	$this->language->get('error_negative_number');
									        $this->error['warning']	=	$this->language->get('error_negative_number');
												}

												if(in_array($key,$this->post_required_fields)) {
														if(isset($post_value) && !$post_value){
															$this->error['error_csv_file']	=	$this->language->get('error_non_zero');
															$this->error['warning']	=	$this->language->get('error_non_zero');
														}
												}

												if(!isset($post_value) && !$this->error){
													$this->error['error_csv_file']	=	$this->language->get('error_field_required');
									        $this->error['warning']	=	$this->language->get('error_field_required');
												} else if(!empty($post_value) && !is_numeric($post_value) && !$this->error) {
													$this->error['error_csv_file']	=	$this->language->get('error_numeric');
									        $this->error['warning']	=	$this->language->get('error_field_required');
												}
											}
								    }
								 if(!$this->error) {
									 $rule_rg = array();
	                 $check_ranges['min'] = $value['price_from'];
									 $check_ranges['max'] = $value['price_to'];
									 if(!empty($rule_rgs)) {
										 $this->validatePriceValuesRanges($check_ranges,$rule_rgs);
									 }
	                 array_push($rule_rgs,$check_ranges);
								 }
							}
					}
			 } else {
				 $this->error['error_csv_file']	=	$this->language->get('error_csv_keys');
	 			 $this->error['warning']	=	$this->language->get('error_csv_keys');
			 }
		}
    if (isset($this->error['err_price_from']) && !isset($this->error['error_csv_file'])) {

			$this->error['error_csv_file']	=	$this->error['err_price_from'];

		}

		if (isset($this->error['err_price_to']) && !isset($this->error['error_csv_file'])) {

			$this->error['error_csv_file']	=	$this->error['err_price_to'];

		}

		if (isset($this->error['err_wide_range']) && !isset($this->error['error_csv_file'])) {

			$this->error['error_csv_file']	=	$this->error['err_wide_range'];

		}
		return !$this->error;
	}

	public function validatePriceValuesRanges($value,$rule_ranges) {

    foreach ($rule_ranges as $key => $rule_range) {

			if (isset($rule_range['min']) && isset($rule_range['max'])) {

				if (isset($value['min']) && $value['min']) {

           $this->_validateRuleRange('price_from',$value['min'], $rule_range['min'], $rule_range['max']);
				}
				if (isset($value['max']) && $value['max']){

           $this->_validateRuleRange('price_to',$value['max'], $rule_range['min'], $rule_range['max']);
				}

				if (isset($value['max']) && $value['max'] && isset($value['min']) && $value['min']) {

					$this->_validateRuleRange('wide_range',$rule_range['min'], $value['min'], $value['max']);

					$this->_validateRuleRange('wide_range',$rule_range['max'], $value['min'], $value['max']);
				}
			}
    }
	}

  public function validateRuleForm(){
    if (!$this->user->hasPermission('modify', 'price_rules/price_rules')) {

			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->post_required_fields as $key => $post_value) {

			if(empty($this->request->post[$post_value])){

				$this->error['err_'.$post_value]	=	$this->language->get('error_'.$post_value);

        $this->error['warning']	=	$this->language->get('error_field_required');

			} else if(!empty($this->request->post[$post_value]) && !is_numeric($this->request->post[$post_value])) {

				$this->error['err_'.$post_value]	=	$this->language->get('error_numeric');

        $this->error['warning']	=	$this->language->get('error_field_required');

			} else if(isset($this->request->post[$post_value]) && !$this->request->post[$post_value]) {

				$this->error['err_'.$post_value]	=	$this->language->get('error_zero');

        $this->error['warning']	=	$this->language->get('error_field_required');
			} else if(isset($this->request->post[$post_value]) && $this->request->post[$post_value] && $this->_validateNegativeNumber($this->request->post[$post_value])) {

				$this->error['err_'.$post_value]	=	$this->language->get('error_negative_number');

        $this->error['warning']	=	$this->language->get('error_field_required');
			}

		}

    if (!$this->error) {

      if(!($this->request->post['price_from'] < $this->request->post['price_to'])) {

				$this->error['err_price_from']	=	$this->language->get('error_equal');

				$this->error['err_price_to']	=	$this->language->get('error_equal');

        $this->error['warning']	=	$this->language->get('error_field_required');
			}
		}

    if(!$this->error){

     if(isset($this->request->get['id']) && $this->request->get['id']){
			 $rule_ranges = $this->_ebayPriceRule->getExcludedPriceRulesRanges($this->request->get['id']);
		 } else {
			 $rule_ranges = $this->_ebayPriceRule->getPriceRulesRanges();
		 }

	    foreach ($rule_ranges as $key => $rule_range) {

				if(isset($rule_range['min']) && isset($rule_range['max'])){

					if(isset($this->request->post['price_from']) && $this->request->post['price_from']){

	           $this->_validateRuleRange('price_from',$this->request->post['price_from'], $rule_range['min'], $rule_range['max']);

					}
					if(isset($this->request->post['price_to']) && $this->request->post['price_to']){

	           $this->_validateRuleRange('price_to',$this->request->post['price_to'], $rule_range['min'], $rule_range['max']);

					}
					if(isset($this->request->post['price_to']) && $this->request->post['price_to'] && isset($this->request->post['price_from']) && $this->request->post['price_from']){

						$this->_validateRuleRange('wide_range',$rule_range['min'], $this->request->post['price_from'], $this->request->post['price_to']);

						$this->_validateRuleRange('wide_range',$rule_range['max'], $this->request->post['price_from'], $this->request->post['price_to']);

					}
				}
	    }
	  }
    return !$this->error;
  }

	protected function _validateNegativeNumber($value) {
        return (substr(strval($value), 0, 1) == "-");
  }

	public function _validateRuleRange($for ,$price, $min, $max){

		if (filter_var($price, FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max)))) {

			$this->error['err_'.$for]	=	$this->language->get('error_range_'.$for);

			$this->error['warning']	=	$this->language->get('error_field_required');
		}

	}

}
