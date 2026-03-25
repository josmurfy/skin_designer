<?php
 // include_once(DIR_APPLICATION.'/../eventtest.php');
ini_set("soap.wsdl_cache_enabled", "0");
class ControllerEbayMapListner extends Controller {
    protected $ebaySession;

    protected $ebayDetails ;

    protected $NotificationSignature;

    private   $Items = array();

    private  $ebayItemId = 0;

    protected $method;

    protected $xmlResponse;

    protected $args;

    public function __call($method, $args) {
      $this->method = $method;

			$method = substr($method, 0, -8);

			if (method_exists($this, $method)) {
				return call_user_func_array(array($this, $method), $args);
			}
   }

   public function realTimeSync() {

     if(!$this->config->get('ebay_connector_realtime_sync')) {
       return;
     }

     $xdoc = new DOMDocument();

     $post = file_get_contents('php://input');

     $xdoc->loadXML($post);

     $all = $xdoc->getElementsByTagName('GetItemResponse')->item(0);

     $item = $all->getElementsByTagName('Item')->item(0);

     $this->xmlResponse = $xdoc;

     $Ack = $xdoc->getElementsByTagNameNS('urn:ebay:apis:eBLBaseComponents', 'Ack')->item(0);

     if($Ack->nodeValue == 'Success'){
         $this->log->write('*****************Valid Response***************');
         $this->__ProcessResponse($xdoc,$Ack->nodeValue);
     } else{
        $this->log->write('********************Not a Valid a Response***************');
     }
  }

   public function __ProcessResponse($xmlResponse,$status) {
        /**
         * [$NotificationEventName Get the Name of the Platform Notification]
         * @var [string]
         */
        $NotificationEventName = $this->_GetValueByTagName('NotificationEventName')->nodeValue;
        //$this->log->write('Call __ProcessResponse function 1');
        /**
         * [$Ack Status of the Event]
         * @var [string]
         */
        $Ack                   = $this->_GetValueByTagName('Ack')->nodeValue;

        /**
         * [$Timestamp Time of the event occured]
         * @var [string]
         */
        $Timestamp             = $this->_GetValueByTagName('Timestamp')->nodeValue;
        /**
         * [$Version build version of the WSDL]
         * @var [numeric]
         */
        $Version               = $this->_GetValueByTagName('Version')->nodeValue;

        /**
         * [$Build build name of the WSDL ]
         * @var [string]
         */
        $Build                 = $this->_GetValueByTagName('Build')->nodeValue;
        /**
         * [$CorrelationID CorrelationID of Ebay event is unique for each action response for header varification]
         * @var [string]
         */
        $CorrelationID         = $this->_GetValueByTagName('CorrelationID')->nodeValue;

        /**
         * [$EIASToken encrypted Token DEVId+CRTid +USerID +Timestamp]
         * @var [varchar]
         */
        $EIASToken             = $this->_GetValueByTagName('EIASToken')->nodeValue;

        $RecipientUserID       = $this->_GetValueByTagName('RecipientUserID')->nodeValue;
        /**
         * [$Item Array of the item]
         * @var [array]
         */
         $Item = $this->_GetValueByTagName('Item');

        $this->GetItem($Timestamp, $Ack, $CorrelationID, $Version, $Build, $NotificationEventName, $RecipientUserID, $EIASToken, $Item);
   }

   public function _GetValueByTagName($tagName) {
      return  $this->xmlResponse->getElementsByTagNameNS('urn:ebay:apis:eBLBaseComponents', $tagName)->item(0);
   }

  /**
   * [GetItem this function wil call from Soap Server with call_user_func_array ]
   * @param [int] $Timestamp             [time of event happens]
   * @param [boolean] $Ack                   [acknowledge status ]
   * @param [int] $CorrelationID         [Unique $CorrelationID for each event]
   * @param [int] $Version               [WSDL version used for event]
   * @param [varchar] $Build                 [Evnt biuld version]
   * @param [string] $NotificationEventName [Name of the event]
   * @param [int] $RecipientUserID       [account holder user id]
   * @param [Int] $EIASToken             [description]
   * @param [object array] $Item                  [Details of the product]
   */

   public function GetItem($Timestamp, $Ack, $CorrelationID,
                   $Version, $Build, $NotificationEventName, $RecipientUserID, $EIASToken, $Item)
   {
     $allItem = array();
      $this->load->model('ebay_map/ebay_event');
     if($Ack == 'Success') {
       foreach ($Item->childNodes as $key => $node) {
            $this->Items[$node->nodeName] = $node->nodeValue;
       }

      $allItem = $this->Items;

      $allItem['ItemID']   = $this->Items['ItemID'];

      $userDetails = $this->model_ebay_map_ebay_event->_getUserAccount($RecipientUserID);

      if ($NotificationEventName == 'ItemRevised' || $NotificationEventName == 'ItemListed' || $NotificationEventName == 'ItemSold') {
          $response = $this->model_ebay_map_ebay_event->__realTimeProductManagement($allItem,$userDetails['id']);
      } else if($NotificationEventName == 'ItemClosed') {
         $response = $this->model_ebay_map_ebay_event->__ItemClosed($allItem,$userDetails['id']);
      }
    }
  }

}



?>
