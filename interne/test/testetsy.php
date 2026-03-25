<?php

$curl = curl_init();
$data= array(
    'title' =>'Unfinished Business : Rental Exclusive (blu-ray) Canadian Cover Version Test',
    'description' =>'Title : Unfinished Business : Rental Exclusive (blu-ray) Canadian Cover Version Test Model : None Brand : 20th Century Fox Home Entertainment Color : N/A UPC : 024543968924 Package Dimension : 7x5x1 Inch Weight : 0.25 Lbs Conditions :- Pre Owned Very Good - Well-cared-for Item In Great Condition. - May Show Some Limited Signs Of Wear. - Undamaged. - Item Has Been Used Previously. - Fully Operational And Functions As Intended. Conditions: - Comes from a former rental store - could have a RFID sticker in the middle of the disk - no Digital Code included',
    'shipping_profile_id' =>'176860401619',
    'state' =>'active',
    'taxonomy_id' =>'355',
    'shop_section_id' =>'38789987',
    'who_made' =>'someone_else',
    'is_supply' =>'0',
    'when_made' =>'2020_2022',
    'should_auto_renew' =>'1',
    'item_weight' =>'0.25',
    'item_weight_unit' =>'lb',
    'item_length' =>'7',
    'item_width' =>'5',
    'item_height' =>'1',
    'item_dimensions_unit' =>'in',
    'language' =>'en',
    'tags' =>'Pre Owned Very Good,Unfinished,Business,,Rental,Exclusive,blu ray,Canadian,Cover,Version,Test,024543968924',
    'listing_id' =>'1307749892',
    'renew' =>'1'
);
//echo http_build_query($data);
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.etsy.com/v3/application/shops/35974697/listings/1307749892',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    //CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_POSTFIELDS =>'title=Unfinished+Business+%3A+Rental+Exclusive+%28blu-ray%29+Canadian+Cover+Version&item_weight=4
    &item_weight_unit=oz&item_length=7&item_width=5.00&item_height=1.00&item_dimensions_unit=in',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'x-api-key: 162qwlhzsikqvt56rcvpnakw',
        'Authorization: Bearer 4b159f98055325e74e39e3e6689681'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;


?>