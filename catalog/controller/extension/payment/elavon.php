<?php
/*  This module was written by Kagonesti for Virtual Merchant Processing for Elavon for opencart
 *  This is not free software - Once purchased, you may use it on any website you own (up to five websites, unless you contact me first for approval)
 *  email:  pdressler@telus.net
 *  Thank you for your purchase.  Please do not distribute
 *  For Opencart 2.0 - Jan 2015
 */

function rmq ($str) {
       //removes single quotes '
       $rpl = array('\"', '\'');
       return str_replace($rpl, '', $str);
    }

function fixPostalCode ($postcode, $isocode) {
//fix canadian postal code
        if ($isocode == 'CA') {
            $postcode = strtoupper($postcode);
      	    $postcode = str_replace (" ", "", $postcode);
            $postcode = str_replace ("-", "", $postcode);
            $postcode = str_replace (":", "", $postcode);
            $postcode = str_replace ("=", "", $postcode);
            $postcode = str_replace ("_", "", $postcode);           
            $postcode = str_replace ("O", "0", $postcode);   //change all letter o's to zeros. 
        }
        //end fix canadian postal code
        //fix (truncate) US Postal code
        else if (($isocode == 'US')&&(strlen($postcode) > 5)) {
            $postcode = substr($postcode,0,5);  //first five digits
        }
        //end fix US postal code
        return $postcode;
}

//Key is the transaction id returned by VM which can be viewed by logging into VM
function encrypt_decrypt($action, $string, $key) {

   $output = false;

     if( $action == 'newencrypt' ) {
     $encryption_key = base64_decode($key);
     $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
     $encrypted = openssl_encrypt($string, 'aes-256-cbc', $encryption_key, 0, $iv);
     $output = base64_encode($encrypted . '::' . $iv);     
   } else if( $action == 'encrypt' ) {
       $iv = md5(md5($key));
       $output = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, $iv);
       $output = base64_encode($output);
   }
   
   return $output;
}
    
  class chargeit_cc_validation {
    var $cc_type, $cc_number, $cc_expires_month, $cc_expires_year;
    
    function validate($number, $expires_m, $expires_y, $cc_owner, $cvv) {
      $this->cc_number = preg_replace('/[^0-9]/', '', $number);

      if (preg_match('/^4[0-9]{12}([0-9]{3})?$/', $this->cc_number)) {
        $this->cc_type = 'Visa';
      } elseif (preg_match('/^5[1-5][0-9]{14}$/', $this->cc_number)) {
        $this->cc_type = 'Master Card';
      } elseif (preg_match('/^3[47][0-9]{13}$/', $this->cc_number)) {
        $this->cc_type = 'American Express';
//      } elseif (preg_match('/^3(0[0-5]|[68][0-9])[0-9]{11}$/', $this->cc_number)) {
//        $this->cc_type = 'Diners Club';
     } elseif (preg_match('/^6011[0-9]{12}$/', $this->cc_number)) {
        $this->cc_type = 'Discover';
//    } elseif (preg_match('/^(3[0-9]{4}|2131|1800)[0-9]{11}$/', $this->cc_number)) {
//       $this->cc_type = 'JCB';
 //     } elseif (preg_match('/^5610[0-9]{12}$/', $this->cc_number)) {
  //      $this->cc_type = 'Australian BankCard';
      } else {
        return -1;
     }

      if (is_numeric($expires_m) && ($expires_m > 0) && ($expires_m < 13)) {
        $this->cc_expires_month = $expires_m;
      } else {
        return -2;
      }

      $current_year = date('Y');
      $expires_y = substr($current_year, 0, 2) . $expires_y;
      if (is_numeric($expires_y) && ($expires_y >= $current_year) && ($expires_y <= ($current_year + 10))) {
        $this->cc_expires_year = $expires_y;
      } else {
        return -3;
      }

      if ($expires_y == $current_year) {
        if ($expires_m < date('n')) {
          return -4;
        }
      }
      
      if (strlen($cc_owner) <= 3) {
        return -5;
      }
        
      if (!(is_numeric($cvv))) {
        return -6;
      }
      
      if ($this->cc_type == 'American Express') {
         if (strlen($cvv) != 4) { return -7; }
      }  else {
         if (strlen($cvv) != 3) { return -8; }
      }

      return $this->is_valid();
    }

    function is_valid() {
      $cardNumber = strrev($this->cc_number);
      $numSum = 0;

      for ($i=0; $i<strlen($cardNumber); $i++) {
        $currentNum = substr($cardNumber, $i, 1);

// Double every second digit
        if ($i % 2 == 1) {
          $currentNum *= 2;
        }

// Add digits of 2-digit numbers together
        if ($currentNum > 9) {
          $firstNum = $currentNum % 10;
          $secondNum = ($currentNum - $firstNum) / 10;
          $currentNum = $firstNum + $secondNum;
        }

        $numSum += $currentNum;
      }

// If the total has no remainder it's OK
      return ($numSum % 10 == 0);
    }
  }

class ControllerExtensionPaymentElavon extends Controller {
	public function index() {
		$this->load->language('extension/payment/elavon');

		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');
		
		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['entry_cc_owner_change'] = $this->language->get('entry_cc_owner_change');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['months'] = array();

		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
				'value' => sprintf('%02d', $i)
			);
		}

		$today = getdate();

		$data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
			);
		}
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		
                //fill in display name
                $data['cc_owner'] = rmq(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8')) . ' ' . rmq(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'));

		return $this->load->view('extension/payment/elavon', $data);
	}
	
	function validEmail($email) {
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex) {
			$isValid = false;
		} else if (strlen($email) > $this->maxFieldLenAry['ssl_email']) {
			$isValid = false;
		} else {
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64) {
				// local part length exceeded
				$isValid = false;
			} else if ($domainLen < 1 || $domainLen > 255) {
				// domain part length exceeded
				$isValid = false;
			} else if ($local[0] == '.' || $local[$localLen-1] == '.') {
				// local part starts or ends with '.'
				$isValid = false;
			} else if (preg_match('/\\.\\./', $local)) {
				// local part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
				// character not valid in domain part
				$isValid = false;
			} else if (preg_match('/\\.\\./', $domain)) {
				// domain part has two consecutive dots
				$isValid = false;
			} else if (!preg_match('/^(\\\\.|[A-Za-z0-9`_=\\/\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
				// character not valid in local part unless
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
					$isValid = false;
				}
			}

			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))) {
				// domain not found in DNS
				$isValid = false;
			}
		}

		return $isValid;
	}

//This function checks cards prior to sending to VM
public function pre_confirmation_check($cc_number, $cc_expires_month,$cc_expires_year,$cc_owner,$cvv_number) {
                $error = '';
                
                $chargeit_cc_validation = new chargeit_cc_validation();
		
		$result = $chargeit_cc_validation->validate($cc_number, $cc_expires_month, $cc_expires_year, $cc_owner, $cvv_number);

		switch ($result) {
		        case 1:
		               $error ='';  //no error
		               break;

			case -1:
				$error = $this->language->get('text_invalid_cc_type');
				break;
			case -2:
			case -3:
			case -4:
				$error = $this->language->get('text_invalid_expiry');
				break;
			case -5:
			        $error = $this->language->get('text_invalid_name');	
				break;
			case -6:
			       $error = $this->language->get('text_invalid_cc_number');
			       break;
			case -7:
			       $error = $this->language->get('text_invalid_cvv_number');
			       break;
			case -8:
			       $error = $this->language->get('text_invalid_cvv_number');
			       break;
			case false:
				$error = $this->language->get('text_invalid_cc_number');
				break;
		}
		return $error;
}

function cURLDataStream($transaction_data) {		
		// concatenate the submission data and put into variable $data
                $data = '';
		$responseAry = array();

		while(list($key, $value) = each($transaction_data)) {
			$data .= $key . '=' . urlencode(preg_replace('/,/', '', $value)) . '&'; 
		}
		
		// Remove the last "&" from the string
		$data = substr($data, 0, -1);

		unset($response);
		
//remove actual lookup from VM for debuging - only send in production mode
if ($this->config->get('elavon_mode') != 'test') {
		// Post order info data to Virtual Merchant
		// Requires cURL must be compiled into PHP
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->virtual_merchant_url); // url set in constructor
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_REFERER, $this->curl_referer);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		if ($this->config->get('elavon_compatability') == 'true') {
                       //pretty sure this is only needed on localhost//		
                       //these two options are frequently necessary to avoid SSL errors with PHP
                       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                       //
                }
		$authorize = curl_exec($ch);
		if (curl_error($ch)) {
			$this->errMsg = $this->text_declined_system_error;
			$this->log->write('Elavon CURL ERROR: ' . curl_errno($ch) . '::' . curl_error($ch));
			return;
		}
		
} else {		
   //Assign a message for testing purposes:  (note use ssl_result=1 for declines)
   $authorize = "ssl_card_number=45**********1234\nssl_exp_date=0115\nssl_amount=" . $this->submit_data['ssl_amount'] . "\nssl_invoice_number=1234\nssl_description=1234\nssl_result=0\nssl_result_message=APPROVAL\nssl_txn_id=AA4A39-76B8973F-806B-4039-83AD-AAF10938E0DC\nssl_approval_code=Y\nssl_cvv2_response=M\nssl_avs_response=X\nssl_account_balance=0.00\nssl_txn_time=04/12/2014 09:28:56 PM";
}

       if (strlen ($authorize) > 5) {
		  $response = explode("\n", $authorize); // explode each line of response into an array
		
    		  foreach ($response as $line) {
			$codes = explode('=', $line);
 			list($key, $value) = $codes;
 			if (strlen($value) == 0) {
 			  $value = '';  //for empty values so they arent set to NULL, such as a declined approval code
 			}         
 			$responseAry[$key] = $value;             
	           }
              
		if ($responseAry['ssl_result'] != '0') {
		       // Catch NON APPROVED transactions here.
			if ($responseAry['ssl_result_message'] != '') { // Catch non system errors e.g. declined or declined cvv2
				//$this->errMsg = 'Credit Card Error: ' . $responseAry['ssl_result_message'];
				if ($responseAry['ssl_result_message'] == 'SERV NOT ALLOWED') {
				  //visa debit or some other card we can't accept
				  $this->errMsg = $this->text_serv_not_allowed;
				} else {
				  //declined (could be cvv, expiry, or some other issue, which we shouldn't display to the user for security sake.
				  $this->errMsg = $this->text_declined;
				}
			} else {
			    $this->errMsg = $this->text_declined_system_error;
			}
/*
//  Although this works, we dont really need to display this info
			if ((isset($responseAry['errorMessage'])) && ($responseAry['errorMessage'] != '')) { // Catch system error messages e.g. Invalid Merchant Number. Add system errors to error msg.
				$this->errMsg .= ' ' . $responseAry['errorCode'] . '. ' . $responseAry['errorName'] . '. ' . $responseAry['errorMessage'];
			}
*/
		}  else {
		   //APPROVED TRANSACTION HERE
		        $this->successMsg = '';
		   
			//this will need to be commented out as we are using it as a key to retreive cc info
			//if (isset($responseAry['ssl_txn_id'])) {
			//	$this->successMsg .= 'Transaction: ' . $responseAry['ssl_txn_id'] . "\n";
			//}
			
			//Redundant
			//if (isset($responseAry['ssl_approval_code'])) {
			//	$this->successMsg = 'Approval Code: ' . $responseAry['ssl_approval_code'] . "\n";
			//}

			
			if (isset($responseAry['ssl_amount'])) {
				$this->successMsg .= 'Amount: ' . $responseAry['ssl_amount'] . ", ";
			}
			

			if (isset($responseAry['ssl_avs_response'])) {
			        $xxx = $responseAry['ssl_avs_response'];
			        if (isset($this->avsResponseAry[$xxx])) {
				  $this->successMsg .= 'AVS: ' . $this->avsResponseAry[$xxx] . ", ";
			        }
			}

			if (isset($responseAry['ssl_cvv2_response'])) {
			           $xxx = $responseAry['ssl_cvv2_response'];
			           if (isset($this->cvvResponseAry[$xxx])) {
				        $this->successMsg .= 'CVV: ' . $this->cvvResponseAry[$xxx] . "\n";
				}
			}
			
			if ($this->config->get('elavon_store_number') == 'true') {
			   //This is to store the CC Number - Possibily not a good idea for PCI Compliance
			   //As it does make life easier, it is here unless you want to remove it - can be turned off in admin
			   //CC info is only conmplete when using special tool, the transaction id, and the missing digits in VM
			   $ccnum = str_replace(' ', '', $this->request->post['cc_number']);
			   $cvv = $this->request->post['cc_cvv2'];
			   $expire = $this->request->post['cc_expire_date_month'] . ($this->request->post['cc_expire_date_year'] - 2000);
			
			   $to_encrypt = substr($ccnum, 2, -4) . '/' . $cvv;
			   $encrypt_key = $responseAry['ssl_txn_id'];
			   $encrypted = encrypt_decrypt('newencrypt',$to_encrypt,$encrypt_key);
			   $this->successMsg .= $encrypted;
		           //end storing CC info//
		        }
		}


//end testing results//

         } else {
            $this->errMsg = $this->text_declined_system_error;
	    $this->log->write('Elavon CURL ERROR: Empty Gateway Response');
         }  
    }

function ChargeIt() {
                $this->virtual_merchant_url = 'https://www.myvirtualmerchant.com/VirtualMerchant/process.do';
		$this->curl_referer = $this->config->get('elavon_refererurl');
		$this->submit_data = array(); // Array type used to prep transaction params for cURL post to virtual merchant
		$this->resubmitted = false; // Boolean used to test if transaction has been resubmitted.
		$this->internationalOrder = 0; // Used to tag international orders
		
		$this->avsResponseAry = array(
										'A' => 'Address matches - Zip Code does not match.',
										'B' => 'Street address match, Postal code in wrong format. (International issuer)',
										'C' => 'Street address and postal code in wrong formats',
										'D' => 'Street address and postal code match (international issuer)',
										'E' => 'AVS Error',
										'G' => 'Service not supported by non-US issuer',
										'I' => 'Address information not verified by international issuer.',
										'M' => 'Street Address and Postal code match (international issuer)',
										'N' => 'No Match on Address (Street) or Zip',
										'O' => 'No Response sent',
										'P' => 'Postal codes match, Street address not verified due to incompatible formats.',
										'R' => 'Retry, System unavailable or Timed out',
										'S' => 'Service not supported by issuer',
										'U' => 'Address information is unavailable',
										'W' => '9 digit Zip matches, Address (Street) does not match.',
										'X' => 'Exact AVS Match',
										'Y' => 'Address (Street) and 5-digit Zip match.',
										'Z' => '5 digit Zip matches, Address (Street) does not match.'
									 );

		$this->cvvResponseAry = array(
										'M' => 'CVV2 Match',
										'N' => 'CVV2 No match',
										'P' => 'Not Processed',
										'S' => 'Issuer indicates that CVV2 data should be present on the card, but the merchant has indicated that the CVV2 data is not resent on the card',
										'U' => 'Issuer has not certified for CVV2 or Issuer has not provided Visa with the CVV2 encryption Keys'
									 );
		$this->maxFieldLenAry = array(
										// Billing Info Max Lengths
										'ssl_first_name' => '20',
										'ssl_last_name' => '30',
										'ssl_company' => '50',
										'ssl_avs_address' => '20',
										'ssl_address2' => '20',
										'ssl_city' => '30',
										'ssl_state' => '30',
										'ssl_avs_zip' => '9',
										'ssl_country' => '50',

										// Contact Info
										'ssl_phone' => '20',
										'ssl_email' => '100',

										// Shipping Info
										'ssl_ship_to_company' => '50',
										'ssl_ship_to_first_name' => '20',
										'ssl_ship_to_last_name' => '30',
										'ssl_ship_to_avs_address' => '20',
										'ssl_ship_to_avs_address2' => '20',
										'ssl_ship_to_city' => '30',
										'ssl_ship_to_state' => '30',
										'ssl_ship_to_avs_zip' => '9',
										'ssl_ship_to_country' => '50'
									 );
    }

	public function send() {
		$this->load->model('checkout/order');
		$this->load->language('extension/payment/elavon');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

                $this->text_declined = $this->language->get('text_declined');
		$this->text_serv_not_allowed = $this->language->get('text_serv_not_allowed');
		$this->text_declined_system_error = $this->language->get('text_declined_system_error');

		// DATA PREPARATION SECTION
		unset($this->submit_data);  // Cleans out any previous data stored in the variable
		
		$this->ChargeIt();  //set some defaults

		// Create an array containing products ordered for the description field
		$description = '';

		// Get the next expected order id
		$order_id = $this->session->data['order_id'];
		
		$description = $order_id;

		// Prep some data for transmission according to Virtual merchant requirements and variables.

		$testEmail = $this->validEmail($order_info['email']); // Strict test for valid email. Virtual merchant will fail transaction if email is not valid.
		if ($testEmail) {
			$sslEmail = $order_info['email'];
		} else {
			$sslEmail = '';
		}
		
		$pay_cnty = (strlen($order_info['payment_iso_code_2']) == 2 ? $order_info['payment_iso_code_2'] : $order_info['payment_country']);
		$ship_cnty = (strlen($order_info['shipping_iso_code_2']) == 2 ? $order_info['shipping_iso_code_2'] : $order_info['shipping_country']);		

                $pay_state = (strlen($order_info['payment_zone_code']) == 2 ? $order_info['payment_zone_code'] : $order_info['payment_zone']);
		$ship_state = (strlen($order_info['shipping_zone_code']) == 2 ? $order_info['shipping_zone_code'] : $order_info['shipping_zone']);
				
		if ($this->config->get('elavon_mode') == 'test') {
			$testMode = 'TRUE';
		} else {
			$testMode = 'FALSE';
		}
		
		//check before sending to Elavon, remove spaces.  Also use strip_tags to remove chance of malicious attacks.
		$cc_number = str_replace(' ', '', strip_tags($this->request->post['cc_number']));
		$cc_expires_month = strip_tags($this->request->post['cc_expire_date_month']);
		$cc_expires_year = strip_tags($this->request->post['cc_expire_date_year']) - 2000;
		$cc_owner = rmq(substr(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_first_name'])) . ' ' . rmq(substr(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_last_name']));
		$cvv_number = $this->request->post['cc_cvv2'];
				
		$pre_check = $this->pre_confirmation_check($cc_number, $cc_expires_month,$cc_expires_year,$cc_owner,$cvv_number);

                if (strlen($pre_check) < 2) {                
                  //passed our quick sanity test - pass onto VM
                
                  // Populate an array that contains all of the data to be submitted
		  $this->submit_data = array (
								// Transaction settings
								'ssl_merchant_id' => $this->config->get('elavon_vmid'), // The login name as assigned to you by Virtual Merchant
								'ssl_user_id' => $this->config->get('elavon_userid'), // The login name you setup for your automated web transaction user
								'ssl_pin' => $this->config->get('elavon_pin'), // The pin that was auto assigned to this new user
								'ssl_transaction_type' => $this->config->get('elavon_method'),  //'CCSALE', or 'CCAUTHONLY'
								'ssl_show_form' => 'FALSE', // Process transaction directly
								'ssl_result_format' => 'ASCII', // DO NOT CHANGE. The formatting type for result messages from Virtual Merchant
								// Transaction Info
								'ssl_amount' =>  $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false),
								'ssl_card_number' => str_replace(' ', '', $this->request->post['cc_number']),
								'ssl_exp_date' => $this->request->post['cc_expire_date_month'] . ($this->request->post['cc_expire_date_year'] - 2000),
				//				'ssl_customer_code' => $this->session->data['customer_id'],
								'ssl_invoice_number' => $this->session->data['order_id'],

								// Billing Info
								'ssl_first_name' => rmq(substr(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_first_name'])),
								'ssl_last_name' => rmq(substr(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_last_name'])),
								'ssl_company' => rmq(substr(html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_company'])),
								'ssl_avs_address' => rmq(substr(html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_avs_address'])), // Virtual merchant only accepts addresses up to 20 characters
								'ssl_address2' => rmq(substr(html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_address2'])), // Virtual merchant only accepts addresses up to 20 characters
								'ssl_city' => rmq(substr(html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_city'])),
								'ssl_state' => rmq(substr(html_entity_decode($pay_state, ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_state'])),
								'ssl_avs_zip' => rmq(substr(fixPostalCode($order_info['payment_postcode'],$order_info['payment_iso_code_2']), 0, $this->maxFieldLenAry['ssl_avs_zip'])),
								'ssl_country' => rmq(substr(html_entity_decode($pay_cnty, ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_country'])),


								// Contact Info
								'ssl_phone' => rmq(substr($order_info['telephone'], 0, $this->maxFieldLenAry['ssl_phone'])),
								'ssl_email' => rmq($sslEmail),

								// Shipping Info
								'ssl_ship_to_company' => rmq(html_entity_decode($order_info['shipping_company'])),
								'ssl_ship_to_first_name' => rmq(substr(html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_first_name'])),
								'ssl_ship_to_last_name' => rmq(substr(html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_last_name'])),
								'ssl_ship_to_avs_address' => rmq(substr(html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_avs_address'])),
								'ssl_ship_to_address2' => rmq(substr(html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_avs_address2'])),
								'ssl_ship_to_city' => rmq(substr(html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_city'])),
								'ssl_ship_to_state' => rmq(substr(html_entity_decode($ship_state, ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_state'])),
								'ssl_ship_to_avs_zip' => rmq(substr(html_entity_decode(fixPostalCode($order_info['shipping_postcode'],$order_info['shipping_iso_code_2']), ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_avs_zip'])),
								'ssl_ship_to_country' => rmq(substr(html_entity_decode($ship_cnty, ENT_QUOTES, 'UTF-8'), 0, $this->maxFieldLenAry['ssl_ship_to_country'])),


								// Products purchased summary
								'ssl_description' => rmq($description),
								'ssl_cvv2cvc2_indicator' => '1',
								'ssl_cvv2cvc2' => $this->request->post['cc_cvv2'],
								'ssl_test_mode' => $testMode
								
							 );

		$this->cURLDataStream($this->submit_data); // Submit sale transaction request to Virtual Merchant.      
           } else {
              //failed our quick sanity test
              $this->errMsg = $pre_check;
           }          
//
		$json = array();
		
                if (strlen ($this->errMsg) > 0) {
                   //we have an error
                   $json['error'] = $this->errMsg; 
                } elseif (strlen ($this->successMsg) > 0) {
                   // successfuly approved
       		   $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('elavon_order_status_id'), $this->successMsg, false);
       		   		
		   $json['redirect'] = $this->url->link('checkout/success', '', 'SSL');
               
                } else {
                   //Uncaught error - This code should never run.
                   $json['error'] = $this->text_declined_system_error;
                }
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		
	}
}