<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayEvents extends Controller {

    const _WSDL_VERSION_ = 1045;
    private $ApplicationURL = '';
    public function index($data = array()) {

    $data = array_merge($data, $this->load->language('ebay_map/events'));

    $sellerId = 0;
    $eBayEvents = array();
    // $eBayEvents = ['ItemSold','ItemListed','ItemRevised','ItemClosed'];

    foreach ($data['events'] as $key => $value) {
        array_push($eBayEvents,$value);
    }

      try{
          $client = $this->getEbayAPI($data['id']);

          $pageUrl = $this->__GetEventListnerUrl();;

          $params = array('Version' => self::_WSDL_VERSION_,
             'ApplicationDeliveryPreferences' => array(
             'ApplicationURL' => $pageUrl,));

          $results = $client->SetNotificationPreferences($params);

          if ($results->Ack == 'Success') {
              $setRealEvent = $this->_setRealTimeUpdateNotificationEvent($client, $pageUrl,  $sellerId, $eBayEvents);
              if ($setRealEvent->Ack =='Success') {
                  $responce = [
                              'notification' => __('Successfully Set Notification Events')
                  ];
              } else {
                  $responce = [
                              'error_msg' => $setRealEvent->Errors->LongMessage
                  ];
              }
          } else {
              $responce = [
                          'error_msg' => $results->Errors->LongMessage
              ];
          }
      } catch(\Exception $e) {
          $this->log->write('data enableEventNotification : '.$e->getMessage());
      }
    }

    public function __GetEventListnerUrl() {

      $pattern = '/\/admin/i';

      $replacement = '';

      $pageUrl = HTTPS_SERVER.'index.php?route=ebay_map/listner/realTimeSync';

      return preg_replace($pattern, $replacement, $pageUrl);

    }

    private function _setRealTimeUpdateNotificationEvent($client, $applicationUrl, $sellerId, $eBayEvents) {

        $customerEmail = $this->config->get['config_email'];

        $subEbayEvents = [];

        foreach($eBayEvents as $event) {
            $subEbayEvents[] = [
                                    'EventType' => $event,
                                    'EventEnable' => 'Enable'
                                ];
        }

        $params = array('Version' => self::_WSDL_VERSION_, 'WarningLevel' => 'High',
            'ApplicationDeliveryPreferences' => array('ApplicationEnable' => 'Enable',
                                                      'ApplicationURL' =>$applicationUrl,
                                                      'DeviceType' => 'Platform',
                                                      'AlertEmail'=>'mailto://'.$customerEmail,
                                                      'AlertEnable'=> 'Enable',
                                                      'PayloadVersion'=> self::_WSDL_VERSION_),
            'UserDeliveryPreferenceArray' => array(
                'NotificationEnable' => $subEbayEvents
            )
        );

      $result = $client->SetNotificationPreferences($params);

      return $result;
    }

    public function getEbayAPI($ebayAccountId = false) {
        $client = null;
        $eBayConfig 	= $this->Ebayconnector->_getModuleConfiguration($ebayAccountId);
        if ($eBayConfig) {
            $session = new Ebay\eBaySession(
                $eBayConfig['ebayDevId'],
                $eBayConfig['ebayAppId'],
                $eBayConfig['ebayCertId']
            );
            $session->token = $eBayConfig['ebayToken'];
            $session->site = $eBayConfig['ebaySites'];
            $session->location = $eBayConfig['location'];
            $client = new Ebay\eBaySOAP($session);
        }
        return $client;
    }
  }
