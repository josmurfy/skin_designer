		<?
		//echo " ebay<br>";
				$homepage = file_get_contents('https://www.ebay.com/itm/144120759446');
				//$result=get_ebay_product($connectionapi,$ebay_id_a_cloner);
				//$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
				//echo $homepage;
unlink($GLOBALS['SITE_ROOT'].'interne/testimage.txt');
link($GLOBALS['SITE_ROOT'].'interne/testimage.txt', $GLOBALS['SITE_ROOT'].'interne/testimage.txt');
$fp = fopen($GLOBALS['SITE_ROOT'].'interne/testimage.txt', 'w');
fwrite($fp, $homepage); 
unlink($GLOBALS['SITE_ROOT'].'interne/testimage2.txt');
link($GLOBALS['SITE_ROOT'].'interne/testimage2.txt',$GLOBALS['SITE_ROOT'].'interne/testimage2.txt');
$fp = fopen($GLOBALS['SITE_ROOT'].'interne/testimage2.txt', 'w');
				$tmp=explode('maxImageUrl":"',$homepage);
				//echo count($tmp);
				$j=((count($tmp)-1)/3)+1;
				for($i=1;$i<$j;$i++){
					
					$tmp2=explode('","maxImageHeight"',$tmp[$i]);
					$tmp3=str_replace('u002F','',$tmp2[0]); 
					fwrite($fp, $tmp3);
					fputs($fp, "\n");					
				}
				
				//print("<pre>".print_r ($imagetmp,true )."</pre>");



				/* if(count($imagetmp)==1){ */
/* 					$imageprincipal=upload_from_ebay($product_id,$imagetmp[0],1,$db);
					//echo '<br>'.$imageprincipal;
					$i=0;
					if(is_array ( $json["Item"]["PictureDetails"]["PictureURL"] )){
						$j=count($json["Item"]["PictureDetails"]["PictureURL"]);
					}else{
						$j=0;
					}
					if($j>1){
						unset ($json["Item"]["PictureDetails"]["PictureURL"][0]);
						foreach  ($json["Item"]["PictureDetails"]["PictureURL"] as $image){
							$imagetmp=explode("?",$image);
							$imagesecondaire[$i]=upload_from_ebay($product_id,$imagetmp[0],0,$db);
							//echo '<br>'.$imagesecondaire;
							$i++;
						}
					} */
					
					
					
				/* }else{
					upload_from_link($product_id,$ebay_id_a_cloner,1,$db);	
					echo "<br>1 photo";
				} */
				
				?>