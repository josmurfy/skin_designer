<?
//use Google\Cloud\Translate\V2\TranslateClient;
require_once '../vendor/autoload.php';
//use Google\Cloud\Translate\V3\TranslateClient;
use Google\Cloud\Translate\TranslateClient;


/** Uncomment and populate these variables in your code */
//$text = 'Va chier.';
//$_POST['targetLanguage'] = 'fr';  // Language to translate to 


$translate = new TranslateClient();
$json = array();
	$json['success']="";
		if ($_POST['text_field']!=""){
			//echo $data4['accessory'];
				$result = $translate->translate(($_POST['text_field']), [
			'target' => $_POST['targetLanguage'],
				]);	
		
			$json['success']=addslashes($result['text']);
		}
	
		/* $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	 */	
		echo json_encode($json);	 
exit;		


?>