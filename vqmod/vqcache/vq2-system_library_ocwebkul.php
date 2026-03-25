<?php
/**
 * @version [1.0.0.0] [Supported opencart version 3.x.x.x]
 * @category Webkul
 * @package Opencart-Webkul
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

class Ocwebkul {
	public function __construct($registry) {
		$this->config 	= $registry->get('config');
		$this->db 		  = $registry->get('db');
		$this->request 	= $registry->get('request');
		$this->session 	= $registry->get('session');
	}

  public function _setNullValueFilterVar($filter_var) {
  		return isset($this->request->get[$filter_var]) ? $this->request->get[$filter_var] : null;
  }

  public function _manageGetVariable($key,$defult_value) {
  		return isset($this->request->get[$key]) ? $this->request->get[$key] : $defult_value;
  }

  public function _manageGetVariableWithStatus($key,$defult_value,$status) {
      return isset($this->request->get[$key]) && (isset($this->request->get['status']) && $this->request->get['status'] == $status) ? $this->request->get[$key] : $defult_value;
  }

  public function _appendStringVarToUrl($filter_var) {
		return isset($this->request->get[$filter_var]) ? '&' . $filter_var . '=' . urlencode(html_entity_decode($this->request->get[$filter_var], ENT_QUOTES, 'UTF-8')): '';
	}

	public function _appendNumericVarToUrl($filter_var) {
		return isset($this->request->get[$filter_var]) ? '&' . $filter_var . '=' . $this->request->get[$filter_var]: '';
	}

  public function _appendNumericVarToUrlWithStatus($filter_var,$status) {
		return isset($this->request->get[$filter_var]) && (isset($this->request->get['status']) && $this->request->get['status'] == $status)? '&' . $filter_var . '=' . $this->request->get[$filter_var]: '';
	}

  public function _setUrlVars() {
		$url = '';
		$url.= $this->_appendNumericVarToUrl('sort');
		$url.= $this->_appendNumericVarToUrl('order');
		$url.= $this->_appendNumericVarToUrl('page');
  	return $url;
  }

	public function _setSession($key,$val) {
			$this->session->data[$key] = $val;
	}

	public function _getSession($key) {
		return  isset($this->session->data[$key]) ? $this->session->data[$key] : '';
	}

	public function _unsetSession($key) {
		unset($this->session->data[$key]);
	}

	public function _isSetSession($key) {
		return isset($this->session->data[$key]) ? TRUE : FALSE;
	}

	public function _isSessionHasValue($key) {
		return $this->isSetSession($key) && $this->session->data[$key] ? TRUE : FALSE;
	}

	public function _isSetPOST($key) {
		return isset($this->request->post[$key]) ? TRUE : FALSE;
	}

	public function _isSetGET($key) {
		return isset($this->request->post[$key]) ? TRUE : FALSE;
	}

	public function _isPOSTHasValue($key) {
		return isset($this->request->post[$key]) && $this->request->post[$key]? TRUE : FALSE;
	}

	public function _isHasValue($key) {
		return isset($this->request->post[$key]) && $this->request->post[$key]? TRUE : FALSE;
	}

	public function _GetPostValue($key) {
		return _isPOSTHasValue($key) ? $key : '';
	}

	public function _isGETHasValue($key) {
		return isset($this->request->get[$key]) ? TRUE : FALSE;
	}

	public function _setSessionVal($frst_key,$sec_key,$val = '') {
		 return isset($this->session->data[$frst_key][$sec_key]) ? $this->session->data[$frst_key][$sec_key] : $val;
	}

  public function _manageSessionVariable($key,$default) {
    if (isset($this->session->data[$key])) {
 		  $return = $this->session->data[$key];
 		  unset($this->session->data[$key]);
 	  } else {
 		  $return = $default;
 	  }
		return $return;
	}

}
