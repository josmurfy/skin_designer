<?
//use Google\Cloud\Translate\V2\TranslateClient;
	require_once '../vendor/autoload.php';
//use Google\Cloud\Translate\V3\TranslateClient;
	use Google\Cloud\Translate\TranslateClient;

function translate_field($post){
//print("<pre>".print_r ($post,true )."</pre>");
	$_GET['targetLanguage'] = 'fr';  // Language to translate to 

	$translate = new TranslateClient();

	 		if (($post['accessoryfr']==""|| !isset($post['accessoryfr'])) && $post['accessoryen']!=""){
				//echo $post['accessoryen'];
					$result = $translate->translate(($post['accessoryen']), [
				'target' => $_GET['targetLanguage'],
					]);	
				$accessoryfr=html_entity_decode($result['text']);
				unset($post['accessoryfr']);
			}else{
				$accessoryfr=$post['accessoryfr'];
			}
			if (($post['condition_suppfr']==""|| !isset($post['condition_suppfr'])) && $post['condition_suppen']!=""){
					$result = $translate->translate(($post['condition_suppen']), [
				'target' => $_GET['targetLanguage'],
					]);
				$condition_suppfr=html_entity_decode($result['text']);
				unset($post['condition_suppfr']);
			}else{
				$condition_suppfr=$post['condition_suppfr'];
			}
			if (($post['testfr']==""|| !isset($post['testfr'])) && $post['testen']!=""){
					$result = $translate->translate(($post['testen']), [
				'target' => $_GET['targetLanguage'],
					]);
				$testfr=html_entity_decode($result['text']);
				unset($post['testfr']);
			}else{
				$testfr=$post['testfr'];
			}
			if (($post['description_suppfr']==""|| !isset($post['description_suppfr'])) && $post['description_suppen']!=""){
					$result = $translate->translate(($post['description_suppen']), [
				'target' => $_GET['targetLanguage'],
					]);
				$description_suppfr=html_entity_decode($result['text']);
				unset($post['description_suppfr']);
			}else{
				$description_suppfr=$post['description_suppfr'];
			} 
			if (($post['colorfr']==""|| !isset($post['colorfr'])) && $post['coloren']!=""){
					$result = $translate->translate(($post['coloren']), [
				'target' => $_GET['targetLanguage'],
					]);
				$colorfr=html_entity_decode(strtoupper($result['text']));
				unset($post['colorfr']);
			}else{
				$colorfr=$post['colorfr'];
			}
			if ($post['namefr']==""|| !isset($post['namefr'])){
					$result = $translate->translate(($post['nameen']), [
				'target' => $_GET['targetLanguage'],
					]);		
					//echo html_entity_decode($result['text']);
					$namefr=html_entity_decode($result['text']);  
					unset($post['namefr']);
				
				//$name='`name`="'.$post['name_en'].'",';
			}else{
				$namefr=$post['namefr'];
			}
		//	print_r($result)."<br>";

		 

		$response= array(
		
			'description_suppfr'	=>$description_suppfr,
			'namefr'				=>$namefr,
			'colorfr'				=>$colorfr,
			'condition_suppfr'		=>$condition_suppfr,
			'accessoryfr'			=>$accessoryfr,
			'testfr'				=>$testfr
		);
		$post=array_merge($response,$post);
		//print("<pre>".print_r ($post,true )."</pre>");
		return $post;

}


?>