<?php
use Fogg\Google\CustomSearch\CustomSearch;
require 'CustomSearch.php';

//Initialize the search class
$cs = new CustomSearch();

//Perform a simple search
$response = $cs->simpleSearch('883929207848');

//Perform a search with extra parameters
$response2 = $cs->search('883929207848', ['searchType'=>'image']);//'searchType'=>'image',
$result =  (array) json_encode($response, true);
for($i = 0; $i < count($result); $i++) {
    if( $result[$i] == $myweb ) {
     print $i;
     break;
    }
   }

   echo "allo";
   //print("<pre>".print_r ($response2,true )."</pre>");