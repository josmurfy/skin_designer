<?php 


$url_import=file_get_contents('https://www.walmart.com/ip/Napa-Gold-Fuel-Filter-3122/307758087?irgwc=1&sourceid=imp_Q9wWbwSy0xyJUmzwUx0Mo381UkE2BdWHNTRiW80&veh=aff&wmlspartner=imp_120157&clickid=Q9wWbwSy0xyJUmzwUx0Mo381UkE2BdWHNTRiW80&sharedid=');
//$url_import=file_get_contents('https://www.walmart.ca/en/ip/zuru-mayka-block-tape-2-stud-yellow/6000197203951');
$url_import_export=explode('<script id="item" class="tb-optimized" type="application/json">',$url_import);
echo $url_import;
//echo count($url_import_export)."PREMIER<br>";
if(count($url_import_export)>1){

	$url_import_export=explode('</script>',$url_import_export[1]);
	//echo $url_import_export[0];
	$json = json_decode($url_import_export[0],true);
				$brand= $json[item][product][buyBox][products][0][brandName];			
				$features="";
				$model= $json[item][product][buyBox][products][0][otherInfoValue] ;
				$name=$json[item][product][buyBox][products][0][productName];			
				$description=$json[item][product][buyBox][products][0][detailedDescription] ;
				$images= $json[item][product][buyBox][products][0][images];
				
				//var_dump(json_decode($json, true));
		//echo $url_import_export[0];
}else{
	$url_import_export=explode('<script>window.__PRELOADED_STATE__=',$url_import);
	//echo count($url_import_export)."Deuxieme<br>";
	if(count($url_import_export)>1){
		
		$url_import_export=explode(';</script><script>',$url_import_export[1]);
				
				$json = json_decode($url_import_export[0],true);	
				$id=$json [product][activeSkuId];
				$brand= $json  [entities][skus][$id][brand][name] ;			
				$features=$json  [entities][skus][$id][featuresSpecifications];
				$model= $json  [entities][skus][$id][modelNumber] ;
				$name=$json[entities][skus][$id][name];			
				$description= $json [entities][skus][$id][longDescription] ;
				$images=$json [entities][skus][$id][images];
				//echo "<br><br>ALLO<br><br>";
				//print("<pre>".print_r ($json [entities][skus][$id][images],true )."</pre>");//[labelContent][longDescription]['product']['item']); ['items']   ['variantNames'] [facets][labelContent]
				//var_dump($json);
				/* 
				echo "<br><br>ALLO<br><br>";
				echo '<br><br>$brand='.$brand;
				echo '<br><br>$features='.$features;
				echo '<br><br>$model='.$model;
				echo '<br>$name='.$name;
				echo '<br>$description='.$description; */
				//var_dump(json_decode($json, true));
		//echo $url_import_export[0];
	}else{
		//echo "rien";
	}
}

 ?>