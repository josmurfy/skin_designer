<?php
class ControllerExtensionFeedGoogleBase extends Controller {
	public function index() {
		if ($this->config->get('google_base_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8" ?>';
			$output .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">';
			$output .= '  <channel>';
			$output .= '  <title>' . $this->config->get('config_name') . '</title>';
			$output .= '  <description>' . $this->config->get('config_meta_description') . '</description>';
			$output .= '  <link>' . $this->config->get('config_url') . '</link>';

			$this->load->model('extension/feed/google_base');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			$this->load->model('tool/image');

			$product_data = array();

			$google_base_categories = $this->model_extension_feed_google_base->getCategories();

			foreach ($google_base_categories as $google_base_category) {
				$filter_data = array(
					'filter_category_id' => $google_base_category['category_id'],
					'filter_filter'      => false
				);

				$products = $this->model_catalog_product->getProducts($filter_data);
				//print("<pre>".print_r ($products,true )."</pre>");
				foreach ($products as $product) {
					if (!in_array($product['product_id'], $product_data) && $product['description']&& $product['quantity']>0) {
						$output .= '<item>';
					$output .= '<title><![CDATA[' . preg_replace('/\(|\)\[|\]/','',$product['name']) . ']]></title>';
						$output .= '<link><![CDATA[https://phoenixliquidation.ca/index.php?route=product/product&product_id=' . $product['product_id'].'&google=CAD]]></link>';
						$str_toremove = array("<br>");
						$description = str_replace($str_toremove, ", ",$product['description']);
						$description = str_replace("?","'", html_entity_decode (strip_tags($description), ENT_QUOTES, 'UTF-8'));
						$output .= '<description><![CDATA[' . $description . ']]></description>';
						$output .= '<g:brand><![CDATA[' .$product['manufacturer'] . ']]></g:brand>';
						$output .= '<g:condition>new</g:condition>';
						$output .= '<g:id>' . $product['product_id'] . '</g:id>';

						if ($product['image']) {
							$output .= '  <g:image_link>' . $this->model_tool_image->resize($product['image'], 500, 500) . '</g:image_link>';
						} else {
							$output .= '  <g:image_link></g:image_link>';
						}

						$output .= '  <g:model_number>' . preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;',$product['model']) . '</g:model_number>';

						if ($product['mpn']&& $product['mpn']!="") {
							$output .= '  <g:mpn><![CDATA[' . $product['upc'] . ']]></g:mpn>' ;
						} else {
							$output .= '  <g:mpn><![CDATA[' . $product['upc'] . ']]></g:mpn>';
						}

						if ($product['upc']) {
							$output .= '  <g:upc>' . $product['upc'] . '</g:upc>';
						}

						if ($product['ean']) {
							$output .= '  <g:ean>' . $product['ean'] . '</g:ean>';
						}

						$currencies = array(
							'USD',
							'EUR',
							'GBP'
						);

/* 						if (in_array($this->session->data['currency'], $currencies)) {
							$currency_code = $this->session->data['currency'];
							$currency_value = $this->currency->getValue($this->session->data['currency']);
						} else { */
							$currency_code = 'CAD';
							$currency_value = $this->currency->getValue('CAD');
/* 						} */

						if ((float)$product['special']) {
							$output .= '  <g:price>' .  /* $this->currency->format($this->tax->calculate( */$product['special']/* , $product['tax_class_id']), $currency_code, $currency_value, false)  */. '</g:price>';
						} else {
							$output .= '  <g:price>' . /* $this->currency->format($this->tax->calculate( */$product['price']/* , $product['tax_class_id']), $currency_code, $currency_value, false)  */. '</g:price>';
						}

						$output .= '  <g:google_product_category>' . $google_base_category['google_base_category_id'] . '</g:google_product_category>';

/* 						$categories = $this->model_catalog_product->getCategories($product['product_id']);

						foreach ($categories as $category) {
							$path = $this->getPath($category['category_id']);

							if ($path) {
								$string = '';

								foreach (explode('_', $path) as $path_id) {
									$category_info = $this->model_catalog_category->getCategory($path_id);

									if ($category_info) {
										if (!$string) {
											$string = $category_info['name'];
										} else {
											$string .= ' &gt; ' . $category_info['name'];
										}
									}
								}

								$output .= '<g:product_type><![CDATA[' . $string . ']]></g:product_type>';
							}
						} */

						$output .= '  <g:quantity>' . $product['quantity'] . '</g:quantity>';
						$output .= '  <g:shipping_length>' . (int) $product['length'] . ' in</g:shipping_length>';
						$output .= '  <g:shipping_width>' .(int) $product['width'] . ' in</g:shipping_width>';
						$output .= '  <g:shipping_height>' .(int) $product['height'] . ' in</g:shipping_height>';
						if ($product['weight']<1){
							$product['weight']=$product['weight']*16;
							$output .= '  <g:shipping_weight>' . (int)$product['weight'].' oz</g:shipping_weight>';
							$output .= '  <g:weight>' . (int)$product['weight'].' oz</g:weight>';
						
						}else{
							$output .= '  <g:shipping_weight>' . ceil($product['weight']).' lb</g:shipping_weight>';
							$output .= '  <g:weight>' . ceil($product['weight']).' lb</g:weight>';
						}
						
						$output .= '  <g:shipping><g:country>US</g:country></g:shipping>';
						$output .= '  <g:availability><![CDATA[' . ($product['quantity'] ? 'in stock' : 'out of stock') . ']]></g:availability>';
						$output .= '</item>';
					}
				}
			}

			$output .= '  </channel>';
			$output .= '</rss>';

			$this->response->addHeader('Content-Type: application/rss+xml');
			$this->response->setOutput($output);
		}
	}

	protected function getPath($parent_id, $current_path = '') {
		$category_info = $this->model_catalog_category->getCategory($parent_id); 

		if ($category_info) {
			if (!$current_path) {
				$new_path = $category_info['category_id'];
			} else {
				$new_path = $category_info['category_id'] . '_' . $current_path;
			}

			$path = $this->getPath($category_info['parent_id'], $new_path);

			if ($path) {
				return $path;
			} else {
				return $new_path;
			}
		}
	}
}
