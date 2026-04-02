<?php
namespace Opencart\Admin\Model\Shopmanager;

class Ebaytemplate extends \Opencart\System\Engine\Model {

	private $domain;

    public function __construct($registry) {
        parent::__construct($registry);
        // Chargez la valeur depuis la configuration
        $this->domain = HTTP_CATALOG;
    }
	   public function getEbayTemplate($product, $site_setting = [],$marketplace_account_id = null)
        {
            $this->load->model('shopmanager/catalog/product');
			$this->load->model('shopmanager/condition');
			$this->load->model('shopmanager/ebay');
            $product_id = $product['product_id'];
            $product_description_result = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
            $product_description= $site_setting['Language']=='fr_CA'?$product_description_result[2]:$product_description_result[1];
		//	//print("<pre>".print_r ($product,true )."</pre>");
		//print("<pre>" . print_r(value: '18:EBAYTEMPLATE.php') . "</pre>");

            $category_info= $this->model_shopmanager_condition->getConditionDetails($product['category_id'], $product['condition_id'] );
			$ConditionsByCategory=$this->model_shopmanager_ebay->getConditionsByCategory($product['category_id']);
			if(!isset($ConditionsByCategory[$category_info[1][$product['condition_id']]['ConditionID']])){
				//print("<pre>".print_r ( 'Product:'.$product_id,true )."</pre>");
				//print("<pre>".print_r ($category_info,true )."</pre>");
				//print("<pre>".print_r ($ConditionsByCategory,true )."</pre>");
			}
			
          //  $product['category_id'] = $category_info['category_id'];
            $product['ConditionID'] = $category_info[1][$product['condition_id']]['ConditionID']??'';
        //print("<pre>".print_r ($product,true )."</pre>");
          //print("<pre>".print_r ($product['ConditionID'],true )."</pre>");
            $template_parts = $this->getTemplateParts();
            $listing_description = $this->generateListingDescription($product['category_id'],$product_description['description'], $template_parts);
           if(isset($product_description['specifics'])){
			//print("<pre>".print_r (25,true )."</pre>");
            $item_specifics = $this->generateItemSpecifics($product_description['specifics'],$product);
			//print("<pre>".print_r ($item_specifics,true )."</pre>");
		   }else{
			$this->load->model('shopmanager/marketplace');
		//	//print("<pre>".print_r (28,true )."</pre>");
			$specifics = $this->model_shopmanager_marketplace->getProductSpecifics($product['product_id'], 1);
		//	//print("<pre>".print_r ($specifics,true )."</pre>");
			$item_specifics = $this->generateItemSpecifics($specifics,$product);

		   }
		//print("<pre>".print_r ($item_specifics,true )."</pre>");
			
            $product_images = $this->model_shopmanager_catalog_product->getImages($product_id);

            $pictures = $this->generatePictures($product_images,$product['image'] );//$product['image_princ']
            $shipping_details = $this->generateShippingDetails($product,$site_setting);
            
            $result = $this->generateEbayListing($product, $listing_description, $item_specifics, $pictures, $shipping_details,$site_setting);
          
            return $result;
        }
        


	
        private function getTemplateParts()
        {
            return [
                'list1' => '<style type="text/css">
.three-columns {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 colonnes */
    gap: 5px; /* Espacement entre les colonnes */
    list-style-type: none; /* Enlève les puces par défaut */
    padding-left: 0;
}

.three-columns li {
    margin-bottom: 5px; /* Espacement entre les éléments de la liste */
}


				.three-columns .secondary-list-item {
					margin-left: 10px; /* Décalage des sous-éléments */
				}

					#SuperWrapper {
					width: 1200px;
					margin-left: auto;
					margin-right: auto;
					font-family: arial, Helvetica, sans-serif;
					font-size: 12px;
					}
					#SuperWrapper p {
					margin: 0px;
					padding: 0px 0px 15px 0px;
					line-height: 20px;
					}
					#SuperWrapper h1 {
					padding: 5px 0px 15px 0px;
					margin: 0px;
					font-size: 26px;
					letter-spacing: -1px;
					color: #000000;
					}
					#SuperWrapper a {
					text-decoration: underline;
					color: #990000;
					}
					#SuperWrapper a:hover {
					text-decoration:none;
					}
					#SuperHeader {
					width:1200px;
					height: 239px;
					background-image:url(' . $this->domain . '/ebay/usHeader.jpg);
					}
					#SuperHeaderLogo {
					padding: 60px 0px 59px 50px;
					font-family: arial, Helvetica, sans-serif;
					font-size: 50px;
					letter-spacing: -3px;
					margin: 0px;
					color: #FFFFFF;
					text-shadow: 1px 1px 1px #000;
					}
					#SuperHeaderMenu {
					margin: 0px;
					}
					#SuperHeaderMenu ul.navi{
					padding: 0px;
					margin: 0px 0px 0px 0px;
					width: 1200px;
					text-align: center;
					position: relative;
					}
					#SuperHeaderMenu ul.navi li{
					height: 22px;
					padding: 0 10px 0 10px;
					margin: 0px;
					display: inline;
					}
					#SuperHeaderMenu ul.navi li a{
					padding: 0px 8px 0px 8px;
					font: 18px arial, Helvetica, sans-serif;
					color: #FFFFFF;
					text-decoration: none;
					text-indent: 0px;
					margin: 0;
					width: inherit;
					letter-spacing: -1px;
					line-height: 30px;
					}
					#SuperHeaderMenu ul.navi li a:hover{
					color: #DEE4ED;
					}
					#SuperContentsWrapper {
					width: 1200px;
					background-image: url(' . $this->domain . '/ebay/usContents.jpg);
					}
					#SuperContents {
					width: 1200px;
					background-image: url(' . $this->domain . '/ebay/usContentsTop.jpg);
					background-repeat: no-repeat;
					}
					#SuperContentsSub {
					padding: 20px 100px 20px 100px;
					}
					#SuperFooter {
					width: 1200px;
					height: 44px;
					background-image:url(' . $this->domain . '/ebay/usFooter.jpg);
					}
					#SuperFooterLink {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usBG.jpg);
					height: 100px;
					}
					#SuperBoxContents {
					padding: 0px 60px 0px 60px;
					margin: 0px;
					}
					#SuperBoxContents p {
					padding: 0px 60px 0px 60px;
					margin: 0px;
					line-height: 20px;
					}
					#SuperBoxContents ul {
					padding: 0px 60px 0px 60px;
					margin: 0px;
					list-style-type: disc;
					}
					#SuperBoxContents li {
					line-height: 20px;
					}
					#SuperPayment {
					width: 1200px;
					}
					#SuperPaymentTop {
					width: 1200px;
					height: 83px;
					background-image:url(' . $this->domain . '/ebay/usPaymentPolicyTop.jpg);
					}
					#SuperPaymentContents {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usPaymentPolicyContents.jpg);
					}
					#SuperPaymentBottom {
					width: 1200px;
					height: 53px;
					background-image:url(' . $this->domain . '/ebay/usPaymentPolicyBottom.jpg);
					}
					#SuperShipping {
					width: 1200px;
					}
					#SuperShippingTop {
					width: 1200px;
					height: 83px;
					background-image: url(' . $this->domain . '/ebay/usShippingPolicyTop.jpg);
					}
                    #SuperAboutContents {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usAboutContents.jpg);
					}
					#SuperAboutTop {
					width: 1200px;
					height: 83px;
					background-image: url(' . $this->domain . '/ebay/usAboutTop.jpg);
					}
                    #SuperAboutBottom {
					width: 1200px;
					height: 53px;
					background-image:url(' . $this->domain . '/ebay/usAboutBottom.jpg);
					}
					#SuperTermTop {
					width: 1200px;
					height: 83px;
					background-image: url(' . $this->domain . '/ebay/usTermPolicyTop.jpg);
					}
                    #SuperTermBottom {
					width: 1200px;
					height: 53px;
					background-image:url(' . $this->domain . '/ebay/usAboutBottom.jpg);
					}
                    #SuperTermContents {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usAboutContents.jpg);
					}
					#SuperShippingContents {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usShippingPolicyContents.jpg);
					}

					#SuperShippingBottom {
					width: 1200px;
					height: 53px;
					background-image:url(' . $this->domain . '/ebay/usShippingPolicyBottom.jpg);
					}
					#SuperContacts {
					width: 1200px;
					}
					#SuperContactsTop {
					width: 1200px;
					height: 83px;
					background-image:url(' . $this->domain . '/ebay/usContactsTop.jpg);
					}
					#SuperContactsContents {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usContactsContents.jpg);
					}
					#SuperContactsBottom {
					width: 1200px;
					height: 53px;
					background-image:url(' . $this->domain . '/ebay/usContactsBottom.jpg);
					}
					#SuperReturns {
					width: 1200px;
					}
					#SuperReturnsTop {
					width: 1200px;
					height: 83px;
					background-image:url(' . $this->domain . '/ebay/usReturnsTop.jpg);
					}
					#SuperReturnsContents {
					width: 1200px;
					background-image:url(' . $this->domain . '/ebay/usReturnsContents.jpg);
					}
					#SuperReturnsBottom {
					width: 1200px;
					height: 53px;
					background-image:url(' . $this->domain . '/ebay/usReturnsBottom.jpg);
					}
					/* HTML5 ELEMENTS */
					/* sub images > thumbnail list */
					ul#SuperThumbs, ul#SuperThumbs li {
					margin: 0;
					padding: 0;
					list-style: none;
					}
					ul#SuperThumbs li {
					float: left;
					background: #ffffff;
					border: 1px solid #cccccc;
					margin: 0px 0px 10px 10px;
					padding: 8px;
					-moz-border-radius: 10px;
					border-radius: 10px;
					}
					ul#SuperThumbs a {
					float: left;
					display: block;
					width: 150px;
					height: 150px;
					line-height: 100px;
					overflow: hidden;
					position: relative;
					z-index: 1;
					}
					ul#SuperThumbs a img {
					float: left;
					width: 100%;
					height: 100%;
					border: 0px;
					}
					/* sub images > mouse over */
					ul#SuperThumbs a:hover {
					overflow: visible;
					z-index: 1000;
					border: none;
					}
					ul#SuperThumbs a:hover img {
					background: #ffffff;
					border: 1px solid #cccccc;
					padding: 10px;
					-moz-border-radius: 10px;
					border-radius: 10px;
					position: absolute;
					top:-20px;
					left:-50px;
					width: auto;
					height: auto;
					}
					/* sub images > clearing floats */
					ul#SuperThumbs:after, li#SuperThumbs:after {
					content: ".";
					display: block;
					height: 0;
					clear: both;
					visibility: hidden;
					}
					ul#SuperThumbs, li#SuperThumbs {
					display: block;
					}
					ul#SuperThumbs, li#SuperThumbs {
					min-height: 1%;
					}
					* html ul#SuperThumbs, * html li#SuperThumbs {
					height: 1%;
					}
                    .secondary-list-item {list-style-type: none;padding-left: 3em; /* Adjust the padding as needed */text-indent: -1em; }


					</style>
					<div id="SuperWrapper">
					    <div id="SuperHeader">
					        <div id="SuperHeaderLogo"><br></div>
					        <div id="SuperHeaderMenu">
					            <ul class="navi">
					                <li><a href="https://www.ebay.ca/str/phoenixdepotcom">Other Items</a></li>
					                <li><a href="https://www.ebay.ca/str/phoenixdepotcom?_tab=feedback">Feedbacks</a></li>
					                <li><a href="https://www.ebay.ca/str/phoenixdepotcom?_tab=about">About Us</a></li>
					                <li><a href="https://www.ebay.ca/cnt/intermediatedFAQ?requested=phoenixliquidationcenter">Contact Us</a></li>
					                <li><a href="https://my.ebay.ca/ws/eBayISAPI.dll?AcceptSavedSeller&amp;ru=http%3A//cgi.ebay.com/ws/eBayISAPI.dll?ViewItemNext&amp;item=330478824623&amp;mode=0&amp;ssPageName=STRK:MEFS:ADDVI&amp;SellerId=phoenixdepotcom&amp;preference=0&amp;selectedMailingList_4487562=false">Add To Favorites</a></li>
					            </ul>
					        </div>
					    </div>
					    <div id="SuperContentsWrapper">
					        <div id="SuperContents">
					            <div id="SuperContentsSub">',
                'list2' => '    </div>
                                    <div id="SuperAbout">
                                        <div id="SuperAboutTop"></div>
                                        <div id="SuperAboutContents">
                                            <div id="SuperBoxContents">',
                'list3' => '                </div>
                                        </div>
                                         <div id="SuperAboutBottom"></div>
                                    </div>
                                    <div id="SuperShipping">
                                        <div id="SuperShippingTop"></div>
                                        <div id="SuperShippingContents">
                                            <div id="SuperBoxContents">',
                'list4' => '                </div>
                                        </div>
                                        <div id="SuperShippingBottom"></div>
                                    </div>
                                    <div id="SuperReturns">
                                        <div id="SuperReturnsTop"></div>
                                        <div id="SuperReturnsContents">
                                            <div id="SuperBoxContents">',
                'list5' => '                </div>
                                        </div>
                                        <div id="SuperReturnsBottom"></div>
                                    </div>
                                    <div id=""SuperTerm">
                                        <div id="SuperTermTop"></div>
                                        <div id="SuperTermContents">
                                            <div id="SuperBoxContents">',
                'list6' => '                </div>
                                        </div>
                                        <div id="SuperTermBottom"></div>
                                    </div>
                                    <div id="SuperPayment">
                                        <div id="SuperPaymentTop"></div>
                                        <div id="SuperPaymentContents">
                                            <div id="SuperBoxContents">',
                'list7' => '                </div>
                                        </div>
                                        <div id="SuperPaymentBottom"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="SuperFooter"></div>
                            <div id="SuperFooterLink"><p align="center"></p></div>
                        </div>',
                'desc1' => '<p><b>🍁 Proud Canadian Seller</b> - PhoenixDepot is a trusted Canadian business specializing in premium liquidation products. We proudly serve customers across North America with exceptional value and outstanding service!</p>
					<br>
				<p><b>OUR MISSION:</b> Deliver top-quality products at unbeatable prices while ensuring 100% customer satisfaction. Your happiness is our #1 priority!</p>
					<br><p><b>NEW Products (Factory Sealed):</b><br>
					Brand new items removed from retail stores for reasons such as:<br>
					• Discontinued or end-of-line models<br>
					• Updated packaging or new model releases<br>
					• Minor cosmetic damage to outer packaging<br>
					• Customer returns (changed mind, never opened)<br>
					The product inside is completely unused and in pristine condition!</p>
					<br><p><b>OPEN BOX Products (Like New):</b><br>
					Fully functional items that may have been returned for various reasons. Every OPEN BOX item is:<br>
					• Professionally tested for full functionality<br>
					• Thoroughly cleaned and sanitized<br>
					• Verified to work perfectly before shipping<br>
					<br><b>100% GUARANTEED TO WORK!</b></p>
					<br><p><b>Quality Guarantee:</b> If you receive a non-functional item, we will replace or refund it at our expense within 30 days under eBay\'s Money Back Guarantee. Your satisfaction is GUARANTEED!</p>
					',
                'desc2' => '<p><b>Trusted Carriers:</b> We ship via premium carriers including CanPar, Dicom, UPS, Purolator, and FedEx - all with full tracking for your peace of mind!</p>
					<p><b>Affordable Canadian Shipping:</b> We understand shipping costs in Canada can be high. That\'s why we subsidize shipping fees to keep prices low and deliver quality products to every Canadian customer!</p>
					<br><p><b>Processing Time:</b> Orders are processed within 1 business day. Please allow 1-2 business days for tracking information to become active in the carrier\'s system.</p>
					<br><p><b>Business Hours:</b><br>
					Monday - Friday: 9:00 AM - 5:00 PM EST (Eastern Standard Time)<br>
					Saturday - Sunday: CLOSED</p>
					<br><p><b>Note:</b> Messages received during weekends will be answered promptly on the next business day. Thank you for your patience!</p>
					',
                'desc3' => '<p><b>Canadian Customers:</b><br>
					We offer a full 30-Day eBay Money Back Guarantee on all domestic orders.</p>
					<br><p><b>Easy Return Process:</b><br>
					• Contact us directly before opening any case<br>
					• Our dedicated support team will assist you promptly<br>
					• We\'ll work with you to find the best solution<br>
					• Your satisfaction is our priority!</p>
					<br><p><b>International Customers:</b><br>
					We welcome international buyers! However, due to high international shipping costs and extended transit times, all items shipped outside the USA and Canada are sold AS-IS with no guarantees. All international sales are FINAL with no refunds or returns. Please ensure you understand the item details before purchasing. Thank you for your understanding!</p>
					',
                'desc4' => '<p><b>Product Descriptions:</b> All product information is provided accurately to the best of our knowledge. We assume buyers are familiar with the product type and its typical usage. If you have any questions, please don\'t hesitate to ask before purchasing!</p>
					<br><p><b>Condition Notes:</b><br>
					• All items are tested to ensure full functionality<br>
					• Any exceptions will be clearly noted in the listing</p>
					<br><p><b>Important:</b> Buyers are responsible for understanding the terms of sale and the nature of the product offered. Please read all descriptions carefully before making your purchase!</p>
					',
                'desc5' => '
					<p>All transactions are secure and protected by eBay\'s buyer protection program!</p>
					<br><p><b>For Canadian Customers:</b> Items ship directly from our Canadian warehouse for faster delivery! Please note that applicable Canadian taxes (GST/HST/PST) may apply at checkout based on your province.</p>
					',
            ];
        }

	private function getTemplatePartsCardListing()
        {
            $d = $this->domain . '/ebay/';
            $s = 'style="';

            $wrap   = $s . 'width:1200px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;font-size:13px;color:#333;"';
            $hdr    = $s . 'background-image:url(' . $d . 'usHeader.jpg);background-size:1200px 239px;background-repeat:no-repeat;width:1200px;height:239px;overflow:hidden;position:relative;"';
            $hdrL   = $s . 'display:block;padding:60px 0 0 50px;font-family:Arial,Helvetica,sans-serif;font-size:50px;letter-spacing:-3px;color:#fff;text-shadow:1px 1px 1px #000;"';
            $hdrNav = $s . 'position:absolute;bottom:0;left:0;width:1200px;background:#000;text-align:center;padding:6px 0;"';
            $hdrA   = $s . 'color:#fff;text-decoration:none;font:18px Arial,Helvetica,sans-serif;padding:0 12px;letter-spacing:-1px;line-height:30px;"';
            $contT  = $s . 'background-image:url(' . $d . 'usContentsTop.jpg);background-size:100% auto;background-repeat:no-repeat;"';
            $contW  = $s . 'background-image:url(' . $d . 'usContents.jpg);background-size:100% auto;background-repeat:repeat-y;"';
            $inner  = $s . 'padding:20px 8%;line-height:1.6;"';
            $sh     = $s . 'font-family:Arial,Helvetica,sans-serif;font-size:16px;font-weight:bold;color:#333;border-bottom:2px solid #ccc;padding-bottom:4px;margin:0 0 10px;"';
            $p      = $s . 'margin:0 0 8px;"';
            $ul     = $s . 'margin:4px 0 8px 18px;padding:0;"';
            $ftr    = $s . 'background-image:url(' . $d . 'usFooter.jpg);background-size:100% 100%;background-repeat:no-repeat;min-height:44px;"';
            $ftrBg  = $s . 'background-image:url(' . $d . 'usBG.jpg);background-size:100% auto;background-repeat:repeat-y;min-height:60px;"';

            return [
                'list1' => '<div ' . $wrap . '>'
                         . '<div ' . $hdr . '>'
                         . '<div ' . $hdrNav . '>'
                         . '<a href="https://www.ebay.ca/str/phoenixdepotcom" ' . $hdrA . '>Other Items</a>'
                         . '<a href="https://www.ebay.ca/str/phoenixdepotcom?_tab=feedback" ' . $hdrA . '>Feedbacks</a>'
                         . '<a href="https://www.ebay.ca/str/phoenixdepotcom?_tab=about" ' . $hdrA . '>About Us</a>'
                         . '<a href="https://www.ebay.ca/cnt/intermediatedFAQ?requested=phoenixliquidationcenter" ' . $hdrA . '>Contact Us</a>'
                         . '<a href="https://my.ebay.ca/ws/eBayISAPI.dll?AcceptSavedSeller&amp;ru=http%3A//cgi.ebay.com/ws/eBayISAPI.dll?ViewItemNext&amp;item=330478824623&amp;mode=0&amp;ssPageName=STRK:MEFS:ADDVI&amp;SellerId=phoenixdepotcom&amp;preference=0&amp;selectedMailingList_4487562=false" ' . $hdrA . '>Add To Favorites</a>'
                         . '</div></div>'
                         . '<div ' . $contW . '><div ' . $contT . '><div ' . $inner . '>',

                'list2' => '</div></div></div>'
                         . '<div ' . $contW . '><div ' . $inner . '><p ' . $sh . '>About Us</p>',

                'list3' => '</div></div>'
                         . '<div ' . $contW . '><div ' . $inner . '><p ' . $sh . '>Shipping</p>',

                'list4' => '</div></div>'
                         . '<div ' . $contW . '><div ' . $inner . '><p ' . $sh . '>Returns</p>',

                'list5' => '</div></div>'
                         . '<div ' . $contW . '><div ' . $inner . '><p ' . $sh . '>Terms</p>',

                'list6' => '</div></div>'
                         . '<div ' . $contW . '><div ' . $inner . '><p ' . $sh . '>Payment</p>',

                'list7' => '</div></div>'
                         . '<div ' . $ftr . '></div>'
                         . '<div ' . $ftrBg . '></div>'
                         . '</div>',

                'desc1' => '<p ' . $p . '><b>&#127809; Proud Canadian Seller</b> &mdash; PhoenixDepot is a trusted Canadian business specializing in premium liquidation products. We proudly serve customers across North America with exceptional value and outstanding service!</p>'
                         . '<p ' . $p . '><b>OUR MISSION:</b> Deliver top-quality products at unbeatable prices while ensuring 100% customer satisfaction. Your happiness is our #1 priority!</p>'
                         . '<p ' . $p . '><b>NEW Products (Factory Sealed):</b> Brand new items removed from retail stores for reasons such as discontinued or end-of-line models, updated packaging, minor cosmetic damage to outer packaging, or customer returns (changed mind, never opened). The product inside is completely unused and in pristine condition!</p>'
                         . '<p ' . $p . '><b>OPEN BOX Products (Like New):</b> Fully functional items that may have been returned. Every OPEN BOX item is professionally tested, thoroughly cleaned, and verified to work perfectly before shipping. <b>100% GUARANTEED TO WORK!</b></p>'
                         . '<p ' . $p . '><b>Quality Guarantee:</b> If you receive a non-functional item, we will replace or refund it at our expense within 30 days under eBay\'s Money Back Guarantee.</p>',

                'desc2' => '<p ' . $p . '><b>Trusted Carriers:</b> We ship via CanPar, Dicom, UPS, Purolator, and FedEx &mdash; all with full tracking.</p>'
                         . '<p ' . $p . '><b>Affordable Canadian Shipping:</b> We subsidize shipping fees to keep prices low for every Canadian customer!</p>'
                         . '<p ' . $p . '><b>Processing Time:</b> Orders processed within 1 business day. Allow 1&ndash;2 business days for tracking to activate.</p>'
                         . '<p ' . $p . '><b>Business Hours:</b> Monday&ndash;Friday 9:00 AM&ndash;5:00 PM EST. Closed weekends &mdash; messages answered next business day.</p>',

                'desc3' => '<p ' . $p . '><b>Canadian Customers:</b> Full 30-Day eBay Money Back Guarantee on all domestic orders.</p>'
                         . '<p ' . $p . '><b>Easy Return Process:</b></p><ul ' . $ul . '><li>Contact us before opening any case</li><li>Our support team will assist you promptly</li><li>We\'ll find the best solution &mdash; your satisfaction is our priority!</li></ul>'
                         . '<p ' . $p . '><b>International Customers:</b> Welcome! Due to high international shipping costs, all items shipped outside USA/Canada are sold AS-IS with no refunds or returns. All international sales are FINAL.</p>',

                'desc4' => '<p ' . $p . '><b>Product Descriptions:</b> All product information is provided accurately to the best of our knowledge. Questions? Ask before purchasing!</p>'
                         . '<p ' . $p . '><b>Condition Notes:</b></p><ul ' . $ul . '><li>All items tested for full functionality</li><li>Any exceptions clearly noted in the listing</li></ul>'
                         . '<p ' . $p . '><b>Important:</b> Buyers are responsible for understanding the terms of sale. Please read all descriptions carefully!</p>',

                'desc5' => '<p ' . $p . '>All transactions are secure and protected by eBay\'s buyer protection program.</p>'
                         . '<p ' . $p . '><b>Canadian Customers:</b> Ships from our Canadian warehouse. Applicable taxes (GST/HST/PST) may apply at checkout based on your province.</p>',
            ];
        }
        
    private function generateListingDescription($category_id,$description, $template_parts)
        {
			$this->load->model('shopmanager/tools');
            $desc1 = $this->getDescription1($category_id, $template_parts['desc1']);
            $desc2 = $template_parts['desc2'];
            $desc3 = $template_parts['desc3'];
            $desc4 = $template_parts['desc4'];
            $desc5 = $template_parts['desc5'];
        
            $list1 = $template_parts['list1'];
            $list2 = $template_parts['list2'];
            $list3 = $template_parts['list3'];
            $list4 = $template_parts['list4'];
            $list5 = $template_parts['list5'];
            $list6 = $template_parts['list6'];
            $list7 = $template_parts['list7'];
        
            $line = html_entity_decode ($description);
           // echo  $description;
        
            $listing_description = $list1 . $line . $list2 . $desc1 . $list3 . $desc2 . $list4 . $desc3 . $list5 . $desc4 . $list6 . $desc5 . $list7;
            $listing_description = str_replace(["\r", "\n"], "", $listing_description);
            $listing_description = urldecode($listing_description);
            $listing_description = "<![CDATA[" . $this->model_shopmanager_tools->convert_smart_quotes($listing_description) . "]]>";
      //  echo $listing_description;
	  //print("<pre>".print_r ($line,true )."</pre>");
	  //die(); 
            return $listing_description;
        }

	private function generateCardListingDescription($category_id,$description, $template_parts)
        {
			$this->load->model('shopmanager/tools');
            $desc1 = $this->getDescription1($category_id, $template_parts['desc1']);
            $desc2 = $template_parts['desc2'];
            $desc3 = $template_parts['desc3'];
            $desc4 = $template_parts['desc4'];
            $desc5 = $template_parts['desc5'];
        
            $list1 = $template_parts['list1'];
            $list2 = $template_parts['list2'];
            $list3 = $template_parts['list3'];
            $list4 = $template_parts['list4'];
            $list5 = $template_parts['list5'];
            $list6 = $template_parts['list6'];
            $list7 = $template_parts['list7'];
        
            $line = html_entity_decode($description);

            // Mobile snippet (eBay mobile = max 800 chars, plain text only)
            // Hidden on desktop, shown only on mobile via eBay's schema.org mechanism
            $mobile_text = strip_tags($line);
            $mobile_text = preg_replace('/\s+/', ' ', $mobile_text);
            $mobile_text = trim(mb_substr($mobile_text, 0, 750));
            $mobile_snippet = '<div vocab="https://schema.org/" typeof="Product" style="display:none">'
                            . '<span property="description">' . $mobile_text . '</span>'
                            . '</div>';

            $listing_description = $mobile_snippet . $list1 . $line . $list2 . $desc1 . $list3 . $desc2 . $list4 . $desc3 . $list5 . $desc4 . $list6 . $desc5 . $list7;
            $listing_description = str_replace(["\r", "\n"], "", $listing_description);
            $listing_description = urldecode($listing_description);
            $listing_description = "<![CDATA[" . $this->model_shopmanager_tools->convert_smart_quotes($listing_description) . "]]>";
            return $listing_description;
        }
        
	private function generateItemSpecifics($NameValueLists = null, $product = null)
        {
			
              if(isset($NameValueLists)){
			//	//print("<pre>".print_r ($NameValueLists,true )."</pre>");
                    $item_specifics = '<ItemSpecifics>';
                    foreach ($NameValueLists as $NameValueList) {
						
                        if (isset($NameValueList['Value']) && is_array($NameValueList['Value'])) {
						//	//print("<pre>".print_r ($NameValueList['Value'],true )."</pre>");
                            $item_specifics .= '
							<NameValueList>
							<Name><![CDATA[' . $NameValueList['Name'] . ']]></Name>
							';
                            foreach ($NameValueList['Value'] as $value) {
                                $item_specifics .= '
								<Value><![CDATA[' . (is_array($value) ? (isset($value['name']) ? $value['name'] : $value['Name']) : $value) . ']]></Value>
								';
                            }
                            $item_specifics .= '
							</NameValueList>';
                        } elseif (isset($NameValueList['Value'])) {
						//	//print("<pre>".print_r ($NameValueList['Value'],true )."</pre>");
                            if ($NameValueList['Value'] != "") {
                                $item_specifics .= '
								<NameValueList>
								<Name><![CDATA[' . $NameValueList['Name'] . ']]></Name>
								<Value><![CDATA[' . $NameValueList['Value'] . ']]></Value>
								</NameValueList>';
                            }
                        }
                    }
					// UPC
					if(isset($product['upc']) && $product['upc']!='' ){
						$item_specifics .= '
						<NameValueList>
						<Name>UPC</Name>
						<Value>' . $product['upc'] . '</Value>
						</NameValueList>';
					}else{
						$item_specifics .= '
						<NameValueList>
						<Name>UPC</Name>
						<Value><![CDATA[Does Not Apply]]></Value>
						</NameValueList>';
					}
                    $item_specifics .= '</ItemSpecifics>
					';
				//	//print("<pre>".print_r ($item_specifics,true )."</pre>");
                    return $item_specifics;
                }else{
                    return null;
                }
        }

private function generateCardListingSpecifics($NameValueLists = null): string|null {
    if (isset($NameValueLists)) {
        $item_specifics = '<ItemSpecifics>';
        
        foreach ($NameValueLists as $Name => $value) {
            // Décoder JSON si c'est une chaîne JSON
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $value = $decoded;
                }
            }
            
            if (isset($value) && is_array($value)) {
                // C'est un tableau - plusieurs valeurs
                $item_specifics .= '
                <NameValueList>
                <Name><![CDATA[' . $Name . ']]></Name>';
                
                foreach ($value as $val) {
                    $item_specifics .= '
                    <Value><![CDATA[' . $val . ']]></Value>';
                }
                
                $item_specifics .= '
                </NameValueList>';
                
            } elseif (isset($value) && $value != "") {
                // Valeur simple
                $item_specifics .= '
                <NameValueList>
                <Name><![CDATA[' . $Name . ']]></Name>
                <Value><![CDATA[' . $value . ']]></Value>
                </NameValueList>';
            }
        }
        
        // UPC
        $item_specifics .= '
        <NameValueList>
        <Name>UPC</Name>
        <Value><![CDATA[Does Not Apply]]></Value>
        </NameValueList>';
        
        $item_specifics .= '</ItemSpecifics>';
        
        return $item_specifics;
    } else {
        return null;
    }
}
        
    private function generatePictures($images, $image_princ)
        {
                    
            $Image_1 =  $this->domain  . '/image/' . $image_princ;
            $pictures = '<PictureDetails><GalleryType>Gallery</GalleryType><PictureURL>' . addslashes($Image_1) . '</PictureURL>';
        
            $i = 1;
            foreach ($images as $image) {
                if ($i < 13) {
                    $pictures .= '<PictureURL>' . addslashes( $this->domain  . '/image/' . $image['image']) . '</PictureURL>';
                    $i++;
                }
            }
            $pictures .= '</PictureDetails>';
        
            return $pictures;
        }


        
    private function generateShippingDetails($product,$site_setting =[])
        {
            //print("<pre>".print_r ($product,true )."</pre>");
            $Weight = floatval($product['weight']);
            $WeightTot = explode('.', $Weight);
            $WeightOZ = $Weight < .25 ? 4 : intval(($Weight - $WeightTot[0]) * 16);

			// Base already stores dimensions in inches and weight in pounds
			$lenIn = round(floatval($product['length']), 2);
			$wdIn  = round(floatval($product['width']), 2);
			$htIn  = round(floatval($product['height']), 2);

			$poids_total = $this->poidsVolumiqueNucleaire($lenIn, $wdIn, $htIn, $Weight);
        
            $shipping_details = '<ShippingPackageDetails>
                                  <MeasurementUnit>English</MeasurementUnit>
                                   <PackageLength>' . $lenIn . '</PackageLength>
                                  <PackageDepth>' . $htIn . '</PackageDepth>
                                  <PackageWidth>' . $wdIn . '</PackageWidth>
                                  <WeightMajor>' . $WeightTot[0] . '</WeightMajor>
                                  <WeightMinor>' . $WeightOZ . '</WeightMinor> 
                                </ShippingPackageDetails>';

			// Pass normalized inch dimensions for profile matching
            $product['length'] = $lenIn;
            $product['width']  = $wdIn;
            $product['height'] = $htIn;
        
           
				$shippingProfile = $this->getShippingProfile($product, $site_setting);
				//$this->log->write('ShippingPackageDetails sent (in/lb): L=' . $lenIn . ', W=' . $wdIn . ', H=' . $htIn . ', Weight=' . $Weight . ', profile=' . json_encode($shippingProfile));

				
				
                $returnProfile = $this->getReturnProfile($product, $poids_total, $product['condition_id'],$site_setting);
        
                          $shipping_details .= '<SellerProfiles>
                                        <SellerShippingProfile>
                                            <ShippingProfileID>' . $shippingProfile['id'] . '</ShippingProfileID>
                                            <ShippingProfileName>' . $shippingProfile['name'] . '</ShippingProfileName>
                                        </SellerShippingProfile>
                                        <SellerReturnProfile>
                                            <ReturnProfileID>' . ($returnProfile['id']??''). '</ReturnProfileID>
                                            <ReturnProfileName>' . ($returnProfile['name']??'') . '</ReturnProfileName>
                                        </SellerReturnProfile>';
						

						$shipping_details .= $this->getPaymentProfile($product['made_in_country_id'],$site_setting);
                                    
						
					$shipping_details .='</SellerProfiles>';
					$shipping_details .='<Location>Longueuil</Location>;
										<PostalCode>J4G1R3</PostalCode>';
            	
        
            return $shipping_details;
        }

	private function generateCardListingShippingDetails()
        {
            //print("<pre>".print_r ($product,true )."</pre>");
         
            $shipping_details = '<ShippingPackageDetails>
                                  <MeasurementUnit>English</MeasurementUnit>
                                   <PackageLength>3.5</PackageLength>
                                  <PackageDepth>1</PackageDepth>
                                  <PackageWidth>2.5</PackageWidth>
                                  <WeightMajor>0</WeightMajor>
                                  <WeightMinor>1</WeightMinor> 
                                </ShippingPackageDetails>';
        
        
			
					$shipping_details .= '<SellerProfiles>
									<SellerShippingProfile>
										<ShippingProfileID>270305103019</ShippingProfileID>
										<ShippingProfileName>Canada_Shipping_Cards</ShippingProfileName>
									</SellerShippingProfile>
									<SellerReturnProfile>
										<ReturnProfileID>261009811019</ReturnProfileID>
										<ReturnProfileName>Canada_No_Return</ReturnProfileName>
									</SellerReturnProfile>';
						

						$shipping_details .=  '<SellerPaymentProfile>
													<PaymentProfileID>66471759019</PaymentProfileID>
													<PaymentProfileName>Canada_PayPal</PaymentProfileName>
												</SellerPaymentProfile>';
                                    
						
					$shipping_details .='</SellerProfiles>';
					$shipping_details .='<Location>Longueuil</Location>
										<PostalCode>J4G1R3</PostalCode>';
            	
        
            return $shipping_details;
        }

		private function getLocation($made_in_country_id = null,$site_setting = []) {

		//	if($made_in_country_id != 44 && $made_in_country_id!==null){
				$location = '<Location>'.$site_setting['Location']['Location'].'</Location>
				<PostalCode>'.$site_setting['Location']['PostalCode'].'</PostalCode>';
		/*	}else{
				$location = '<Location>Longueuil, Quebec</Location>
				<PostalCode>J4G1R3</PostalCode>';
			}*/
			return $location;
		}

		private function getShippingProfile($data , $site_setting = []) {
		//	if($data['made_in_country_id'] !=44 && $data['made_in_country_id']!== null){
			//print("<pre>".print_r ($site_setting,true )."</pre>");
				$shippingProfiles = $site_setting['shippingProfile'];
				

			
		
		//	echo "<br>length: ".$data['length'];
		//	echo "<br>height: ".$data['height'];
		//	echo "<br>width: ".$data['width'];
		//	echo "<br>weight: ".$data['weight'];
		//	echo "<br>category_id: ".$data['category_id']. "<br>";
		
			foreach ($shippingProfiles as $name => $profile) {
				$categoryMatch = true;
				$weightMatch = true;
				$dimensionMatch = true;
		
				// Vérifier si la catégorie correspond
				if (isset($profile['categories']) && !empty($profile['categories'])) {
					$categoryMatch = in_array($data['category_id'], $profile['categories']);
			//		echo "<br>catégorie correspond: ".$categoryMatch. "<br>";
				}
		
				// Vérifier si le poids correspond
				if (isset($profile['weight'])) {
					$weightMatch = ($data['weight'] >= $profile['weight'][0] && $data['weight'] <= $profile['weight'][1]);
		//			echo "<br>poids correspond: ".$weightMatch. "<br>";
				}
				//print("<pre>".print_r ($profile['dimension'],true )."</pre>");
				// Vérifier les dimensions si nécessaire
				if (isset($profile['dimension'])) {
					
						if (isset($data['length'], $data['height'], $data['width'])) {
							$dimensionMatch = $this->validateDimension($data['length'], $data['height'], $data['width'], $profile['dimension']);
		//					echo "<br>dimensions correspondent: ".$dimensionMatch. "<br>";
						} else {
							$dimensionMatch = false;
		//					echo "<br>dimensions non fournies<br>";
						}
				
				}else{
					$dimensionMatch = true;
				}
			// Debugging des résultats avant le retour
		//	echo "<br>Profil: $name<br>";
		//	echo "Catégorie: " . ($categoryMatch ? 'Oui' : 'Non') . "<br>";
		//	echo "Poids: " . ($weightMatch ? 'Oui' : 'Non') . "<br>";
		//	echo "Dimensions: " . ($dimensionMatch ? 'Oui' : 'Non') . "<br>";
				// Si toutes les conditions sont remplies, retourner ce profil
				if ($categoryMatch && $weightMatch && $dimensionMatch) {
	//				echo "<br>Correspondance trouvée: Profil $name<br>";
					return ['name' => $name, 'id' => $profile['id']];
				}
			}
		
			// Retour par défaut si aucune condition ne correspond
			return null;
		}
		
	   
        private function getReturnProfile($data, $poids_total, $condition_id, $site_setting = []) {
            
			//if($data['made_in_country_id']!=44 && $data['made_in_country_id']!== null){
				$returnProfiles = $site_setting['returnProfile'];
				/*[
					'No_Return' => ['categories' => [212, 261332], 'conditions' => [1], 'id' => '246806570019'],
					'Return_Buyer_Pay' => ['categories' => [117414], 'weight' => [70, PHP_INT_MAX], 'id' => '233511458019'],
					'Return' => ['weight' => [0, 25], 'id' => '244801165019'],
				];*/
		/*	}else{
				$returnProfiles = [
					'Canada_No_Return' => ['categories' => [212, 261332], 'conditions' => [1], 'id' => '261009811019'],
					'Canada_Return_Buyer_Pay' => ['categories' => [117414], 'weight' => [70, PHP_INT_MAX], 'id' => '261009829019'],
					'Canada_Return' => ['weight' => [0, 25], 'id' => '66471757019'],
				];
			}*/
        
            foreach ($returnProfiles as $name => $profile) {
                if ((isset($profile['categories']) && in_array($data['category_id'], $profile['categories'])) ||
                    (isset($profile['conditions']) && in_array($condition_id, $profile['conditions'])) ||
                    (isset($profile['weight']) && $poids_total >= $profile['weight'][0] && $poids_total <= $profile['weight'][1])) {
                    return ['name' => $name, 'id' => $profile['id']];
                }
            }
        
            return null;
        }

		private function getPaymentProfile($made_in_country_id=null, $site_setting = []) {
            
		//	if($made_in_country_id!=44 && $made_in_country_id!== null){
				return '<SellerPaymentProfile>
						<PaymentProfileID>'.$site_setting['SellerPaymentProfile']['PaymentProfileID'].'</PaymentProfileID>
						<PaymentProfileName>'.$site_setting['SellerPaymentProfile']['PaymentProfileName'].'</PaymentProfileName>
					</SellerPaymentProfile>';
			/*}else{
				return '<SellerPaymentProfile>
						<PaymentProfileID>66471759019</PaymentProfileID>
						<PaymentProfileName>Canada_PayPal</PaymentProfileName>
					</SellerPaymentProfile>';
			}*/
        
    
        
         //   return null;
        }
        private 
        function poidsVolumiqueNucleaire($longueur, $largeur, $hauteur, $poids) {
            // Vérification que les dimensions ne sont pas nulles dans l'une des trois mesures
            if ($longueur == 0) {
                return "Erreur : La longueur ne peut pas être nulle.";
            }
            if ($largeur == 0) {
                return "Erreur : La largeur ne peut pas être nulle.";
            }
            if ($hauteur == 0) {
                return "Erreur : La hauteur ne peut pas être nulle.";
            }
            // Conversion des dimensions en pouces en pieds cubes
            $volume_pieds_cubes = ($longueur * $largeur * $hauteur) /1728;
            // Poids volumique nucléaire en livres par pied cube (ex. valeur arbitraire pour l'exemple)
            $poids_volumique_lbs_pied_cube = 12.4; 
            // Calcul du poids volumique nucléaire total en livres
            $poids_total_lbs = $volume_pieds_cubes * $poids_volumique_lbs_pied_cube;
            // Arrondi à deux décimales
            $poids_total_lbs_arrondi = round($poids_total_lbs, 2);
            // Retourne la plus haute valeur entre le poids calculé et le poids donné
            return max($poids_total_lbs_arrondi, $poids);
        }

        private function validateDimension($length, $height, $width, $dimension_cond = []) {
            // Calculer la somme totale des dimensions 
		//	//print("<pre>".print_r ($dimension_cond,true )."</pre>");
		//	//print("<pre>".print_r ($length,true )."</pre>");
		//	//print("<pre>".print_r ($height,true )."</pre>");
		//	//print("<pre>".print_r (($length * $height * $width),true )."</pre>");
            $total = $length + $height + $width;
            // Vérifier si la somme totale dépasse 36 pouces
            if ($total > $dimension_cond['total']) {
			//	//print("<pre>".print_r ($length,true )."</pre>");
                return false;
            }
            // Vérifier si au moins l'une des dimensions dépasse 24 pouces
            if ($length > (double) $dimension_cond['max'] || $height > (double)$dimension_cond['max'] || $width > (double)$dimension_cond['max']) {
			//	//print("<pre>".print_r ($length,true )."</pre>");
                return false;
            }

			if (($length * $height * $width) > (double)$dimension_cond['cubic']) {
			//	//print("<pre>".print_r (($length * $height * $width),true )."</pre>");
                return false;
            }
            // Si les conditions précédentes ne sont pas remplies, alors les dimensions sont acceptables
            return true;
        }
       
        

private function generateEbayCardListing($listing_data, $listing_description, $item_specifics, $shipping_details)
{
    $this->load->model('shopmanager/tools');
    $title = $this->model_shopmanager_tools->convert_smart_quotes(
        $this->escape_special_chars($listing_data['title'])
    );
    
    // Échapper les caractères spéciaux
    $title = htmlspecialchars($title, ENT_XML1, 'UTF-8');

    $result = "<Title><![CDATA[" . html_entity_decode($listing_data['title'], ENT_QUOTES, 'UTF-8') . "]]></Title>";

    // ConditionID hardcodé à 400012 (Near Mint or Better)
    $result .= '<ConditionID>400012</ConditionID>';

    // ProductListingDetails
    $result .= '<ProductListingDetails>';
    
    // Brand and MPN - Only include if brand has a value
    if (!empty($listing_data['brand'])) {
        $result .= '<BrandMPN>';
        $result .= '<Brand><![CDATA[' . $this->escape_special_chars($listing_data['brand']) . ']]></Brand>';
        $result .= '<MPN><![CDATA[Does Not Apply]]></MPN>';
        $result .= '</BrandMPN>';
    }
    
    // PAS de UPC ici - requis au niveau des variations pour multi-variation listings
    
    // ReturnSearchResultOnDuplicates
    $result .= '<ReturnSearchResultOnDuplicates>true</ReturnSearchResultOnDuplicates>';
    
    // ProductReferenceID
    if (isset($listing_data['product_reference_id']) && $listing_data['product_reference_id'] != '') {
        $result .= '<ProductReferenceID>' . $this->escape_special_chars($listing_data['product_reference_id']) . '</ProductReferenceID>';
        $result .= '<UseFirstProduct>true</UseFirstProduct>';
        $result .= '<UseStockPhotoURLAsGallery>true</UseStockPhotoURLAsGallery>';
    }
    $result .= '</ProductListingDetails>';
    
    // Item specifics
    $result .= $item_specifics;
    
    // ===== PICTURES - SANS GalleryURL (déprécié) =====
    $result .= '<PictureDetails>';
    
    $all_images = [];
    
    if (!empty($listing_data['variations'])) {
        foreach ($listing_data['variations'] as $variation) {
            if (!empty($variation['images'])) {
                // Ajouter front et back
                if (!empty($variation['images']['front'])) {
                    $all_images[] = $variation['images']['front'];
                }
                if (!empty($variation['images']['back'])) {
                    $all_images[] = $variation['images']['back'];
                }
            }
        }
    }
    
    // Supprimer les doublons et limiter à 12 images (limite eBay)
    $all_images = array_unique($all_images);
    $all_images = array_slice($all_images, 0, 12);
    
    // Ajouter toutes les images (PAS de GalleryURL - déprécié)
    foreach ($all_images as $image_url) {
        $result .= '<PictureURL>' . htmlspecialchars($image_url, ENT_XML1, 'UTF-8') . '</PictureURL>';
    }
    
    $result .= '</PictureDetails>';
    
    // Description
    $result .= '<Description>' . $listing_description . '</Description>';
    
    // Shipping details
    $result .= $shipping_details;
    
    // SKU principal
    $com = 'CARD_LIST';
    $result .= '<SKU>' . $com . $listing_data['listing_id'] . '</SKU>';
    
    // ===== VARIATIONS =====
    if (!empty($listing_data['variations'])) {
        $result .= '<Variations>';
        
        // Get all unique players for VariationSpecificsSet
        $players = [];
        foreach ($listing_data['variations'] as $variation) {
            if (!empty($variation['player_name'])) {
                $players[] = $variation['player_name'];
            }
        }
        $players = array_unique($players);
        
        $result .= '<VariationSpecificsSet>
            <NameValueList>
                <Name>Player/Athlete</Name>';
        
        foreach ($players as $player) {
            $player_escaped = htmlspecialchars($player, ENT_XML1, 'UTF-8');
            $result .= '<Value>' . $player_escaped . '</Value>';
        }
        
        $result .= '    </NameValueList>
        </VariationSpecificsSet>';
        
        // Add each variation
        foreach ($listing_data['variations'] as $variation) {
            $player_name = htmlspecialchars($variation['player_name'] ?? 'Unknown Player', ENT_XML1, 'UTF-8');
            $card_number = htmlspecialchars($variation['card_number'] ?? '', ENT_XML1, 'UTF-8');
            $team_name = htmlspecialchars($variation['team_name'] ?? '', ENT_XML1, 'UTF-8');
            $price = number_format((float)($variation['price'] ?? 0), 2, '.', '');
            $quantity = (int)($variation['quantity'] ?? 1);
            $sku = 'CARD_'.$variation['card_id'];
            
            $result .= '<Variation>
                <SKU>' . $sku . '</SKU>
                <StartPrice>' . $price . '</StartPrice>
                <Quantity>' . $quantity . '</Quantity>
                <VariationSpecifics>
                    <NameValueList>
                        <Name>Player/Athlete</Name>
                        <Value>' . $player_name . '</Value>
                    </NameValueList>
                </VariationSpecifics>';
            
            // Add variation title
            $variation_title = $player_name;
            if (!empty($card_number)) {
                $variation_title .= ' #' . $card_number;
            }
            if (!empty($team_name)) {
                $variation_title .= ' - ' . $team_name;
            }
            $result .= '<VariationTitle>' . htmlspecialchars($variation_title, ENT_XML1, 'UTF-8') . '</VariationTitle>';
            
            $result .= '</Variation>';
        }
        
        $result .= '</Variations>';
    }
    
    return $result;
}


	private function generateEbayListing($product, $listing_description, $item_specifics, $pictures, $shipping_details, $site_setting = [])
		{
			$this->load->model('shopmanager/tools');
			$title = $this->model_shopmanager_tools->convert_smart_quotes(
				$this->escape_special_chars($product['name'])
			);
			
			// Échapper les caractères spéciaux
			$title = htmlspecialchars($title, ENT_XML1, 'UTF-8');
		
			$result = "<Title><![CDATA[" . html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8') . "]]></Title>";
	
		$result .= isset($product['ConditionID'])?'<ConditionID>' . $product['ConditionID'] . '</ConditionID>':'';

			// ProductListingDetails
			$result .= '<ProductListingDetails>';
			
			// Brand and MPN - Only include if brand has a value
			if (!empty($product['brand'])) {
				$result .= '
							<BrandMPN>';
				$result .= '<Brand><![CDATA[' . $this->escape_special_chars($product['brand']) . ']]></Brand>';
				$result .= '<MPN><![CDATA[' . (!empty($product['mpn']) ? $this->escape_special_chars($product['mpn']) : 'Does Not Apply') . ']]></MPN>';
				$result .= '</BrandMPN>
			';
			}
			
			// EAN
			if(isset($product['ean']) && $product['ean']!='' )
			$result .= '
		<EAN>' . (!empty($product['ean']) ? $this->escape_special_chars($product['ean']) : '') . '</EAN>';

			// Include eBay product details
			// UPC
			if(isset($product['upc']) && $product['upc']!='' ){
				$result .= '
				<UPC>' . (!empty($product['upc']) ? $this->escape_special_chars($product['upc']) : '') . '</UPC>';
			}else{
				$result .= '
				<UPC>Does Not Apply</UPC>';
			}
			
			// ISBN
			if(isset($product['isbn']) && $product['isbn']!='' )
			$result .= '
		<ISBN>' . (!empty($product['isbn']) ? $this->escape_special_chars($product['isbn']) : '') . '</ISBN>';
			
			// ProductReferenceID
				
			// ReturnSearchResultOnDuplicates
			$result .= '
			<ReturnSearchResultOnDuplicates>true</ReturnSearchResultOnDuplicates>';
			
		
			
			// UseFirstProduct and UseStockPhotoURLAsGallery
			if(isset($product['product_reference_id']) && $product['product_reference_id']!='' ){
				$result .= '
				<ProductReferenceID>' . (!empty($product['product_reference_id']) ? $this->escape_special_chars($product['product_reference_id']) : '') . '</ProductReferenceID>';
			
				$result .= '
				<UseFirstProduct>true</UseFirstProduct>';
				$result .= '
				<UseStockPhotoURLAsGallery>true</UseStockPhotoURLAsGallery>';
			} else {
				// No explicit catalog match: prevent eBay from overriding our category
				// via EAN/UPC/ISBN catalog lookup (fixes ErrorCode 21917164)
				$result .= '
				<UseFirstProduct>false</UseFirstProduct>';
			}
			$result .= '</ProductListingDetails>
			';
			
			// Item specifics, pictures, description, and shipping details
			$result .= $item_specifics;
			$result .= $pictures;
			$result .= '
			<Description>' . $listing_description . '</Description>';
			$result .= $shipping_details;

			$com = "";
			$host = $_SERVER['HTTP_HOST'] ?? '';
            $is_phoenixsupplies = strpos($host, 'phoenixsupplies') !== false;
            $is_phoenixliquidation = strpos($host, 'phoenixliquidation') !== false;
            $com = ((!isset($product['upc']) || $product['upc']=='') && $is_phoenixsupplies)?'COM_':'';
			
			$result .= '
			<SKU>' . $com . $product['product_id'] . '</SKU>';
			//print("<pre>".print_r ($result,true )."</pre>"); 
			return $result;
		}

        function escape_special_chars($xml_string) {
            $replacements = array(
                '&amp;' => '&',
            
            );
        
            return str_replace(array_keys($replacements), array_values($replacements), $xml_string);
        }
        private function getDescription1($category_id, $default_desc)
        {
            if (in_array($category_id, [73836,20349, 178893, 182066, 123417, 112529, 58540, 33602, 146496, 48619, 20357, 80077, 123422, 96991, 35190, 48677, 182068, 42425])) {
                return "<p><b>PhoenixDepotDotCom </b>is a business based in CANADA that resells products acquired from liquidation center, primarily to Canadian buyers. </p>
                    <p><b>OUR GOAL: </b><br>Offer very good products, sold at the BEST PRICE and thus make you happy!</p>";
            }
        
            return $default_desc;
        }
        
		// Dans ebaytemplate.php

	public function getEbayTemplateCardListing($listing_data, $site_setting = [], $marketplace_account_id = null)
	{
		$this->load->model('shopmanager/card/card_listing');
		$this->load->model('shopmanager/condition');
		$this->load->model('shopmanager/ebay');
	
		// Description brutte : priorité à ce qui est passé dans listing_data,
		// sinon charger le batch 1 depuis la DB.
		$listing_id = (int)($listing_data['listing_id'] ?? 0);
		$merged_description = '';

		if (!empty($listing_data['description'])) {
			// Déjà fourni par l'appellant (multi-batch: description spécifique au batch)
			$merged_description = html_entity_decode($listing_data['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
		} elseif ($listing_id > 0) {
			// Lit la description du batch 1 depuis getDescriptions() (indexé par batch_name).
			$batchDescriptions = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id);

			if (!empty($batchDescriptions[1]['description'])) {
				$merged_description = html_entity_decode($batchDescriptions[1]['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
			}
		}

		// Fallback if no descriptions found
		if (empty($merged_description)) {
			$merged_description = $listing_data['description'] ?? '<p>Trading cards in excellent condition.</p>';
		}

		if (empty($listing_data['ebay_category_id'])) {
			$listing_data['ebay_category_id'] = '183050';
		}
		if (empty($listing_data['title'])) {
			$listing_data['title'] = ($listing_data['set_name'] ?? 'Trading Cards ') . ' - Collectible Cards';
		}
		
		// Générer la description HTML avec le contenu multilingue
		$template_parts = $this->getTemplatePartsCardListing();
		$listing_description = $this->generateCardListingDescriptionHTML($listing_data['ebay_category_id'], $merged_description, $template_parts);

		// Préparer les aspects (specifics) au format Inventory API
		$aspects = [];
		if (isset($listing_data['specifics'])) {
			foreach ($listing_data['specifics'] as $name => $value) {
				// Décoder JSON si nécessaire
				if (is_string($value)) {
					$decoded = json_decode($value, true);
					if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
						$value = $decoded;
					}
				}
				
				// L'API Inventory attend toujours un tableau de valeurs
				if (!is_array($value)) {
					$value = [$value];
				}
				
				$aspects[$name] = $value;
			}
		}
		
		// Ajouter Card Condition
		$aspects['Card Condition'] = ['Near Mint or Better'];
    	$aspects['Country/Region of Manufacture'] = ['United States'];
    	$aspects['Country of Origin'] = ['United States'];
    
		
		// Construire le résultat au format attendu par addCardListingInventory
		$result = [
			'title' => $listing_data['title'],
			'description' => $listing_description,
			'raw_description' => $merged_description,  // HTML sans CSS wrapper — pour Inventory REST API
			'aspects' => $aspects,
			'brand' => $listing_data['brand'] ?? 'Topps',
			'set_name' => $listing_data['set_name'] ?? '',
			'year' => $listing_data['year'] ?? '',
			'ebay_category_id' => $listing_data['ebay_category_id'],
			'listing_id' => $listing_data['listing_id'],
			'variations' => $this->formatVariationsForInventory($listing_data['variations'], $aspects),
			'condition_id' =>  400012,
			'location' => [
				'city' => 'Longueuil',
				'stateOrProvince' => 'QC',
				'postalCode' => 'J4G1R3',
				'country' => 'CA'
			],
			'policies' => [
				'fulfillmentPolicyId' => $site_setting['CardShippingProfile'] ?? '270305103019',
				'paymentPolicyId' => $site_setting['CardPaymentProfile'] ?? '66471759019',
				'returnPolicyId' => $site_setting['CardReturnProfile'] ?? '261009811019'
			]
		];
	
		return $result;
	}

	/**
	 * Build a fully HTML-wrapped eBay description for a specific batch.
	 * Generates the card list from the batch variations and wraps it in the
	 * standard eBay listing template.
	 *
	 * @param string $ebay_category_id  eBay category ID for the listing.
	 * @param array  $batchVariations   Variations (cards) for this batch only.
	 * @return string  Ready-to-send HTML description.
	 */
	public function buildBatchDescription(string $ebay_category_id, array $batchVariations): string {
		$this->load->model('shopmanager/card/card_listing');
		$rawDesc       = $this->model_shopmanager_card_card_listing->generateBatchDescription($batchVariations);
		$template_parts = $this->getTemplatePartsCardListing();
		return $this->generateCardListingDescriptionHTML($ebay_category_id, $rawDesc, $template_parts);
	}

private function formatVariationsForInventory($variations, $base_aspects = []) {
    $formatted = [];
    
    // ⚠️ RETIRER les aspects qui VARIENT des base_aspects
    unset($base_aspects['Player/Athlete']);
    unset($base_aspects['Card Number']);
    unset($base_aspects['Team']);
    
    foreach ($variations as $variation) {
        $variation_aspects = $base_aspects;

        // Ajouter Country/Region of Manufacture si pas déjà présent
        if (!isset($variation_aspects['Country/Region of Manufacture'])) {
            $variation_aspects['Country/Region of Manufacture'] = ['United States'];
        }

        if (!isset($variation_aspects['Country of Origin'])) {
            $variation_aspects['Country of Origin'] = ['United States'];
        }

        if (!isset($variation_aspects['Country'])) {
            $variation_aspects['Country'] = ['United States'];
        }
        
        // ✅ Utiliser Card Number comme variation (accepté par eBay CA)
        if (!empty($variation['card_number'])) {
            $variation_aspects['Card Number'] = [$variation['card_number']];
        }
        
        // Ajouter Player comme aspect normal (pas variation)
        $variation_aspects['Player/Athlete'] = [$variation['player_name']];
        
        if (!empty($variation['team_name'])) {
            $variation_aspects['Team'] = [$variation['team_name']];
        }
        
        $formatted[] = [
            'card_id' => $variation['card_id'],
            'sku' => $variation['sku'],
            'title' => $variation['title'],
            'description' => $variation['description'],
            'player_name' => $variation['player_name'],
            'card_number' => $variation['card_number'] ?? '',
            'team_name' => $variation['team_name'] ?? '',
            'year' => $variation['year'] ?? '',
            'brand' => $variation['brand'] ?? '',
            'condition_name' => $variation['condition_name'] ?? 'Near Mint or Better',
            'price' => $variation['price'],
            'quantity' => $variation['quantity'],
            'images' => $variation['images'],
            'aspects' => $variation_aspects
        ];
		//print("<pre>".print_r ($formatted,true )."</pre>");
    }
    
    return $formatted;
}

	private function generateCardListingDescriptionHTML($category_id, $description, $template_parts)
	{
		$this->load->model('shopmanager/tools');
		
		$desc1 = $this->getDescription1($category_id, $template_parts['desc1']);
		$desc2 = $template_parts['desc2'];
		$desc3 = $template_parts['desc3'];
		$desc4 = $template_parts['desc4'];
		$desc5 = $template_parts['desc5'];

		$list1 = $template_parts['list1'];
		$list2 = $template_parts['list2'];
		$list3 = $template_parts['list3'];
		$list4 = $template_parts['list4'];
		$list5 = $template_parts['list5'];
		$list6 = $template_parts['list6'];
		$list7 = $template_parts['list7'];

		$line = html_entity_decode($description);

		// Mobile snippet (eBay mobile = max 800 chars) — caché desktop, visible mobile via schema.org
		$mobile_text = strip_tags($line);
		$mobile_text = preg_replace('/\s+/', ' ', $mobile_text);
		$mobile_text = trim(mb_substr($mobile_text, 0, 750));
		$mobile_snippet = '<div vocab="https://schema.org/" typeof="Product" style="display:none">'
		                . '<span property="description">' . $mobile_text . '</span>'
		                . '</div>';

		$listing_description = $mobile_snippet . $list1 . $line . $list2 . $desc1 . $list3 . $desc2 . $list4 . $desc3 . $list5 . $desc4 . $list6 . $desc5 . $list7;
		
		$listing_description = str_replace(["\r", "\n"], "", $listing_description);
		$listing_description = urldecode($listing_description);
		$listing_description = $this->model_shopmanager_tools->convert_smart_quotes($listing_description);

		return $listing_description;
	}
 }