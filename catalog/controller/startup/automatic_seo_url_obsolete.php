<?php

class ControllerStartupAutomaticSeoUrl extends Controller {

	// configuration attributes
	var $forceAutoUrl = true; //if set to true, default OC SEO keyword for urls will be ignored
	var $manufacturerNameInUrl = true;
	var $categoriesPathInProductUrl = true;
	var $languageSlugInUrl = false;
	var $preventProdDuplicates = true; //if a product is linked to more then one category, only use one category for product details page
	var $product_identifier = 'p';
	var $category_identifier = 'c';
	var $manufacturer_identifier = 'm';
	var $info_identifier = 'i';
	var $jblog_post_identifier = 'b';
	var $jblog_post_cat_identifier = 'bc';
	var $route_identifier = 'r';
	var $_sep = '-';
	var $_sep2 = '_';
	var $_url_ext = '.html'; // .html, .php etc.
	var $_languageindex='en-gb';
	
	// reserved attributes
	var $_cachefile;
	var $_products = array();
	var	$_categories = array();
	var $_tmp_cat_path = array();
	var	$_manufacturers = array();
	var	$_jblog_posts = array();
	var $_jblog_post_cats = array();
	var	$_infos = array();
	var $_infoString;
	var $_languages;
	
				//echo $languageindex;
	

	public function index() {

		if(!defined('DIR_CACHE')) define('DIR_CACHE', DIR_SYSTEM.'cache/');
		$cache_folder = DIR_CACHE.'autourl/';
		if (!is_dir($cache_folder)) {
		 	mkdir($cache_folder);
		}
		$this->_cachefile = $cache_folder.'cache.autourl.'.$this->session->data['language'].'.'.md5(urlencode($_SERVER['REQUEST_URI']));
		//echo $cache_folder.'xxx cache.autourlxxx'.$this->session->data['language'].'xxx'.md5(urlencode($_SERVER['REQUEST_URI']));
		if(file_exists($this->_cachefile)){
			$data = file_get_contents($this->_cachefile);
			//echo $data;
			$data = unserialize($data);
			//echo $data['categories'];
			$this->_products = $data['products'];
			$this->_categories = $data['categories'];
			$this->_manufacturers = $data['manufacturers'];
			$this->_infos = $data['infos'];
			$this->_jblog_posts = $data['jblog_posts'];
			$this->_jblog_post_cats = $data['jblog_post_cats'];
		}else{
			$cachefile = fopen($this->_cachefile, "w") or die("Unable to open file!");
			fclose($cachefile);
		}

		//change language id: i.e. for sitemap
		
		if(isset($this->request->get['language'])){
			$this->_getLanguages();
			if(isset($this->_languages[$this->request->get['language']]) && $this->request->get['language'] != $this->config->get('config_language')){
				
				$this->session->data['language'] = $this->request->get['language'];
				if(isset($this->request->get['_route_']) && $this->request->get['_route_'] == 'feed/google_sitemap')
					$this->config->set('config_language', $this->_languages[$this->request->get['language']]); //google webmaster tools deny redirects
				else 
				//echo allo;
				header('location: '.urldecode($_SERVER['REQUEST_URI']));
				//exit();
			}
		}
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
            $this->url->addRewrite($this);
        }
        //detect language change
        if( 
        	$this->languageSlugInUrl 
        	&& isset($this->request->get['route']) 
        	&& 
        		(
        			$this->request->get['route'] == 'common/language/language' //OC 2.0
        			||
        			( $this->request->get['route'] == 'module/language' && isset($this->request->post['language_code']) ) //OC up to 1.5.4
        		)
        ){
        	$this->session->data['language_is_changing'] = true;
        }
        //echo urldecode($_SERVER['REQUEST_URI']);
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			
			if(substr($this->request->get['_route_'], strlen($this->_url_ext)*-1) == $this->_url_ext)
				$this->request->get['_route_'] = substr($this->request->get['_route_'], 0, strlen($this->request->get['_route_']) - strlen($this->_url_ext));
			//decode url
			$segments = explode('/', $this->request->get['_route_']);
			//language slug
			//echo urldecode($_SERVER['REQUEST_URI']);
			
			if($this->languageSlugInUrl){
				$this->_getLanguages();
				if(!isset($this->request->post['language_code']) && isset($this->_languages[$segments[0]]) && $segments[0] != $this->config->get('config_language')){
					$languageindex = $this->session->data['language'];
				//echo $languageindex;
					$this->session->data['language'] = $segments[0];
					//echo allo2;
					header('location: '.urldecode($_SERVER['REQUEST_URI']));
					//exit();
				}
				if(isset($this->_languages[$segments[0]])){
					unset($segments[0]);
					$segments = array_values($segments);
				}
			}
			if(!empty($segments)){
				$_segments = array_reverse($segments);
				if (strpos($_SERVER['REQUEST_URI'], "_") !== false){
					$segment = array_reverse(explode($this->_sep2, $_segments[0]));
				}else{
					$segment = array_reverse(explode($this->_sep, $_segments[0]));
				}
					//echo "allo";//$_sep="_";
				//$segment = array_reverse(explode($this->_sep, $_segments[0]));
				//echo $_SERVER['REQUEST_URI'];
				
				$string = $segment[0];
				$string = preg_replace('/[^A-Za-z0-9]/','',$string);
				$parts = preg_split("/(,?\s+)|((?<=[a-z])(?=\d))|((?<=\d)(?=[a-z]))/i", $string);
				//echo $parts[0];
				if(!empty($parts)){

					// adjust path and manufacturer id
					$path = array();
					foreach($parts as $k=>$p){
						$next = $k+1;
						if( ($k % 2) == 0 && $p == $this->category_identifier){
							$path[] = $parts[$next];
						}else if( ($k % 2) == 0 && $p == $this->manufacturer_identifier){
							$this->request->get['manufacturer_id'] = (int) $parts[$next];
						}
					}
					$path = implode('_',$path);
					//echo $path;
					// end adjust path and manufacturer id

					switch($parts[0]){
						case $this->product_identifier: 
							// product page
							$this->request->get['route'] = 'product/product';
							$this->request->get['product_id'] = (int) $parts[1];
							if(isset($parts[2]) && $parts[2] == $this->category_identifier){
								$this->request->get['path'] = $path; //$parts[3];
							}
							break;
						case $this->category_identifier:
							// category page
							$this->request->get['route'] = 'product/category';
							$this->request->get['path'] = $path; //$parts[1];
							break;
						case $this->manufacturer_identifier:
							// manufacturer page
							$mroute = ( version_compare(VERSION, '1.5.4.0') >= 0 ) ? 'product/manufacturer/info' : 'product/manufacturer/product';
							$this->request->get['route'] = $mroute;
							$this->request->get['manufacturer_id'] = (int) $parts[1];
							break;
						case $this->info_identifier:
							// information page
							$this->request->get['route'] = 'information/information';
							$this->request->get['information_id'] = (int) $parts[1];
							break;
						case $this->jblog_post_identifier: 
							$this->request->get['route'] = 'journal2/blog/post';
							$this->request->get['journal_blog_post_id'] = (int) $parts[1];
							if(isset($parts[2]) && $parts[2] == $this->jblog_post_cat_identifier){
								$this->request->get['journal_blog_category_id'] = $parts[3];
							}
							break;
						case $this->jblog_post_cat_identifier:
							// category page
							$this->request->get['route'] = 'journal2/blog';
							$this->request->get['journal_blog_category_id'] = $parts[1];
							break;
						case $this->route_identifier:
							// other route pages
							$this->request->get['route'] = implode('/',$segments);
							break;
						default:
							if($this->forceAutoUrl) 
								$this->request->get['route'] = implode('/',$segments);
							else{
								$this->request->get['_route_'] = implode('/',$segments);
								return $this->index_standard();
							}
								
							break;
					}
				}else{
					if($this->forceAutoUrl) 
						$this->request->get['route'] = implode('/',$segments);
					else{
						$this->request->get['_route_'] = implode('/',$segments);
						return $this->index_standard();
					}
						
				}
			}
			if( $this->languageSlugInUrl && isset($this->session->data['language_is_changing']) && $this->session->data['language_is_changing']){
				unset($this->session->data['language_is_changing']);
				$route = '';
				if(isset($this->request->get['route'])){
					$route = $this->request->get['route'];
					unset($this->request->get['route']);
				}
				unset($this->request->get['_route_']);
				$args = http_build_query($this->request->get);
				$redirect = $this->url->link($route, $args);
				if( version_compare(VERSION, '1.5.4') >= 0 ){
					$this->response->redirect($redirect);
				}else{
					$this->redirect($redirect);
				}
			}
			if (isset($this->request->get['route'])) {
				if (defined('VERSION') && version_compare(VERSION, '2.0.0.0') >= 0) {
					return new Action($this->request->get['route']);
				}
				else{
					return $this->forward($this->request->get['route']);
				}
			}
		}
		
	}

	public function rewrite($link){
	
		if(!$this->forceAutoUrl){
			$_link = $this->rewrite_standard($link);
			if($_link != $link) return $_link;
		}
		
		if($this->config->get('config_seo_url')){
			$this->_infoString = '';
			$url_data = parse_url(str_replace('&amp;', '&', $link));
			if(!isset($url_data['query']))return $link;
			$url = $urlpre = $urlpost = ''; 
			$data = array();
			parse_str($url_data['query'], $data);
			if(isset($data['route'])){
				//echo $link;
				switch($data['route']){
					case 'account/account':
						return $link;			
						break;
					case 'feed/google_base':
						return $link;			
						break;
					case 'feed/google_sitemap':
						return $link;			
						break;		
					case 'account/wishlist':
						return $link;			
						break;
					case 'account/logout':
						return $link;			
						break;
					case 'account/login':
						return $link;			
						break;
					case 'checkout/cart':
						return $link;			
						break;
					case 'checkout/checkout':
						return $link;			
						break;
					case 'account/register':
						return $link;			
						break;
					case 'account/return/add':
						return $link;			
						break;
					case 'account/edit':
						return $link;			
						break;
					case 'account/password':
						return $link;			
						break;
					case 'account/address':
						return $link;			
						break;
					case 'account/order':
						return $link;			
						break;
					case 'account/download':
						return $link;			
						break;
					case 'account/recurring':
						return $link;			
						break;
					case 'account/reward':
						return $link;			
						break;
					case 'information/contact':
						return $link;			
						break;
					case 'common/currency/currency':
						return $link;
						break;
					case 'common/language/language':
						return $link;
						break;
					case 'module/language':
						return $link;
						break;
					case 'module/currency':
						return $link;
						break;
					case 'product/product':
						$pid = (isset($data['product_id'])) ? $data['product_id'] : 0;
						$this->_get_products($pid);
						unset($data['product_id']);
						//$url .= '/'.$this->product_identifier.'/'.$pid;
						$this->_infoString .= $this->product_identifier.$pid;
						if(isset($this->_products[$pid])){
							if(isset($data['path']) && !$this->preventProdDuplicates){
								$cats = array_reverse(explode('_', $data['path']));
								$catid = $cats[0];
							}else{
								$catid = $this->_products[$pid]['category_id'];
							}
							$url .= $this->_traverse_categories($catid, $this->categoriesPathInProductUrl);
							if($this->manufacturerNameInUrl){
								$this->_get_manufacturers($this->_products[$pid]['manufacturer_id']);
								if(isset($this->_manufacturers[$this->_products[$pid]['manufacturer_id']]))
									$url .= $this->_manufacturers[$this->_products[$pid]['manufacturer_id']].'/';
							}
							$url .= $this->_products[$pid]['name'];
						}
						if(isset($data['path'])){
							unset($data['path']);
						}
						if(isset($data['manufacturer_id'])){
							$this->_infoString .= $this->manufacturer_identifier.$data['manufacturer_id'];
							unset($data['manufacturer_id']);
						}
							
						break;
					case 'product/category':
						$data['path'] = isset($data['path']) ? $data['path'] : 0;
						$ppath = array_reverse(explode('_',$data['path']));
						$url .= $this->_traverse_categories($ppath[0]);
						unset($data['path']);
						break;
					case 'product/manufacturer/info':
						//echo 'allo2';
						//return $link;
						//break;
					case 'product/manufacturer/product':
						$data['manufacturer_id'] = isset($data['manufacturer_id']) ? $data['manufacturer_id'] : 0;
						//$url .= '/'.$this->manufacturer_identifier.'/'.$data['manufacturer_id'];
						$this->_infoString .= $this->manufacturer_identifier.$data['manufacturer_id'];
						$this->_get_manufacturers($data['manufacturer_id']);
						if(isset($this->_manufacturers[$data['manufacturer_id']]))
							$url .= '/'.$this->_manufacturers[$data['manufacturer_id']];
						unset($data['manufacturer_id']);
						//echo 'allo';
						break;
					case 'information/information':
						$data['information_id'] = isset($data['information_id']) ? $data['information_id'] : 0;
						//$url .= '/'.$this->info_identifier.'/'.$data['information_id'];
						$this->_infoString .= $this->info_identifier.$data['information_id'];
						$this->_get_infos($data['information_id']);
						if(isset($this->_infos[$data['information_id']]))
							$url .= '/'.$this->_infos[$data['information_id']];
						unset($data['information_id']);
						break;
					/*journal 2 blog */
					case 'journal2/blog/post':
						$pid = isset($data['journal_blog_post_id']) ? $data['journal_blog_post_id'] : 0;
						$this->_get_jblog_posts($pid);
						unset($data['journal_blog_post_id']);
						$this->_infoString .= $this->jblog_post_identifier.$pid;
						$url .= $this->_get_jblog_key().'/';
						if(isset($this->_jblog_posts[$pid])){
							if(isset($data['journal_blog_category_id'])){
								$catid = (int)$data['journal_blog_category_id'];
								unset($data['journal_blog_category_id']);
							}else{
								$catid = $this->_jblog_posts[$pid]['category_id'];
							}
							$this->_get_jblog_post_cats($catid);
							$this->_infoString .= $this->jblog_post_cat_identifier.$catid;
							$url .= $this->_jblog_post_cats[$catid].'/';
						}
						$url .= $this->_jblog_posts[$pid]['name'];
						break;
					case 'journal2/blog':
						$data['journal_blog_category_id'] = isset($data['journal_blog_category_id']) ? $data['journal_blog_category_id'] : '0';
						$url .= '/'.$this->_get_jblog_key();
						if(isset($data['journal_blog_category_id'])){
							$catid = (int)$data['journal_blog_category_id'];
							unset($data['journal_blog_category_id']);
							$this->_get_jblog_post_cats($catid);
							$this->_infoString .= $this->jblog_post_cat_identifier.$catid;
							if($catid && $this->_jblog_post_cats[$catid])
								$url .= '/'.$this->_jblog_post_cats[$catid];
						}else{
							$url .= '/'.$data['route'];
						}
						break;
					/*end journal 2 blog */
					case 'common/home':
						break;
					default:
						$url .= '/'.$data['route'];
						//$url .= '/'.$this->route_identifier.'/'.$data['route'];
						//return $link;
						break;
				}
				unset($data['route']);
				$query = '';
				if ($data) {
					foreach ($data as $key => $value)
						$query .= '&' . $key . '=' . $value;
					
					if ($query)
						$query = '?' . trim($query, '&');
				}
				//$url .= $this->_sep.$this->_infoString;

				if(!empty($this->_infoString))
					$url = '/'.trim($url,'/').$this->_sep.$this->_infoString;
				return 	$url_data['scheme'].'://'.$url_data['host'].(isset($url_data['port']) ? ':'.$url_data['port'] : '').str_replace('/index.php', '', $url_data['path']).( $this->languageSlugInUrl ? '/'.$this->config->get('config_language') : '').rtrim($url,'/').(rtrim($url,'/') == '' ? '' : $this->_url_ext).$query;
			}else{
				return $link;
			}
		}else{
			return $link;
		}
	}

	private function _get_products($id = 0){
		if($id && !isset($this->_products[$id])){
			$query = $this->db->query("
				SELECT p.product_id, pd.name, pc.category_id, p.manufacturer_id FROM " . DB_PREFIX . "product AS p 
				LEFT JOIN " . DB_PREFIX . "product_description AS pd ON pd.product_id = p.product_id 
				LEFT JOIN " . DB_PREFIX . "product_to_category AS pc ON pc.product_id = p.product_id
				WHERE p.product_id = ".$id." AND pd.language_id = ".(int)$this->config->get('config_language_id')." AND p.status = 1");
			foreach($query->rows as $prod)
				$this->_products[$prod['product_id']] = array(
															'name'=>$this->clean_name($prod['name']),
															'category_id'=>$prod['category_id'],
															'manufacturer_id'=>$prod['manufacturer_id'],
															);
			$this->_cache_rebuild();
		}
	}
	private function _get_categories($id = 0){
	
		if($id && !isset($this->_categories[$id])){
			$query = $this->db->query("
				SELECT c.category_id, c.parent_id, cd.name FROM " . DB_PREFIX . "category AS c 
				LEFT JOIN " . DB_PREFIX . "category_description AS cd ON cd.category_id = c.category_id 
				WHERE c.category_id = ".$id." AND cd.category_id = c.category_id AND cd.language_id = ".(int)$this->config->get('config_language_id')." AND c.status = 1");
			foreach($query->rows as $cat){
				$this->_categories[$cat['category_id']] = array(
															'name'=>$this->clean_name($cat['name']),
															'parent'=>$cat['parent_id']
															);
			}
			$this->_cache_rebuild();
		}
	}
	private function _traverse_categories($catId, $appendCatName = true){
		if(!in_array($catId,$this->_tmp_cat_path))
			$this->_tmp_cat_path[] = $catId;
		$this->_get_categories($catId);
		if(isset($this->_categories[$catId])){
			if($this->_categories[$catId]['parent'] == 0){
				$this->_infoString .= $this->category_identifier.implode($this->category_identifier,array_reverse($this->_tmp_cat_path));
				$this->_infoString = rtrim($this->_infoString, $this->category_identifier);
				$return = $appendCatName ? $this->_categories[$catId]['name'].'/' : '';
				$this->_tmp_cat_path = array();
			}else{
				$return = $this->_traverse_categories($this->_categories[$catId]['parent'], $appendCatName).($appendCatName ? $this->_categories[$catId]['name'].'/' : '');
			}
		}else{
			$return = '';
		}
		return $return;
	}
	private function _get_manufacturers($id = 0){
		if($id && !isset($this->_manufacturers[$id])){
			$query = $this->db->query("SELECT manufacturer_id, name FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = ".$id);
			foreach($query->rows as $man)
				$this->_manufacturers[$man['manufacturer_id']] = $this->clean_name($man['name']);
			$this->_cache_rebuild();
		}
	}
	private function _get_infos($id = 0){
		if($id && !isset($this->_infos[$id])){
			$query = $this->db->query("SELECT i.information_id, id.title FROM " . DB_PREFIX . "information AS i LEFT JOIN " . DB_PREFIX . "information_description AS id ON id.information_id = i.information_id WHERE i.information_id = ".$id." AND id.information_id = i.information_id AND id.language_id = ".(int)$this->config->get('config_language_id')." AND i.status = 1");
			foreach($query->rows as $infos)
				$this->_infos[$infos['information_id']] = $this->clean_name($infos['title']);
			$this->_cache_rebuild();
		}
	}

	private function _get_jblog_key(){
		if(!isset($this->_jblog_key)){
			$query = $this->db->query("SELECT `value` FROM " . DB_PREFIX . "journal2_config WHERE `key`='blog_settings' AND `store_id` = ".$this->config->get('config_store_id')." AND `serialized` = 1");
			$data = json_decode($query->row['value']);
			$lang = (int)$this->config->get('config_language_id');
			$this->_jblog_key = empty($data->keyword->value->{$lang}) ? $this->clean_name($data->name->value->{$lang}) : $data->keyword->value->{$lang};
		}

		return $this->_jblog_key;
	}

	private function _get_jblog_posts($id = 0){
		if($id && !isset($this->_jblog_posts[$id])){
			$query = $this->db->query("
				SELECT p.post_id, pd.name, c.category_id 
				FROM " . DB_PREFIX . "journal2_blog_post AS p 
				LEFT JOIN " . DB_PREFIX . "journal2_blog_post_description AS pd ON p.post_id = pd.post_id 
				LEFT JOIN " . DB_PREFIX . "journal2_blog_post_to_category AS c ON p.post_id = c.post_id 
				WHERE p.post_id = ".$id." AND pd.language_id = ".(int)$this->config->get('config_language_id')." AND p.status = 1");
			foreach($query->rows as $post){
				$this->_jblog_posts[$post['post_id']] = array(
															'name'=>$this->clean_name($post['name']),
															'category_id'=>$post['category_id'],
															);
			}
			$this->_cache_rebuild();
		}
	}

	private function _get_jblog_post_cats($id = 0){
		if($id && !isset($this->_jblog_post_cats[$id])){
			$query = $this->db->query("
				SELECT c.category_id, cd.name 
				FROM " . DB_PREFIX . "journal2_blog_category c
				LEFT JOIN " . DB_PREFIX . "journal2_blog_category_description cd 
					ON c.category_id = cd.category_id 
				WHERE c.category_id = ".$id.' AND cd.language_id='.(int)$this->config->get('config_language_id')." AND c.status = 1");
			foreach($query->rows as $jcat)
				$this->_jblog_post_cats[$jcat['category_id']] = $this->clean_name($jcat['name']);
			$this->_cache_rebuild();
		}
	}

	private function _getLanguages(){
		if(empty($this->_languages)){
			$this->_languages = array();
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE status = '1'"); 
			foreach ($query->rows as $result) {
				$this->_languages[$result['code']] = $result;
			}
		}
	}

	private function _cache_rebuild(){
		$data = array(
			'products' => $this->_products,
			'categories' => $this->_categories,
			'manufacturers' => $this->_manufacturers,
			'infos' => $this->_infos,
			'jblog_posts' => $this->_jblog_posts,
			'jblog_post_cats' => $this->_jblog_post_cats
		);
		$json = serialize($data);
		file_put_contents($this->_cachefile, $json);
	}

	private function clean_name($name){
		$name = htmlspecialchars_decode($name);
		$bad_array = array("сква",
					   "À","à","Á","á","Â","â","Ã","ã","Ä","ä","Å","å","Ā","ā","Ă","ă","Ą","ą","Ǟ","ǟ","Ǻ","ǻ","Α","α",
					   "Æ", "æ", "Ǽ", "ǽ", 
					   "Ḃ","ḃ","Б","б",
					   "Ć","ć","Ç","ç","Č","č","Ĉ","ĉ","Ċ","ċ","Ч","ч","Χ","χ",
					   "Ḑ","ḑ","Ď","ď","Ḋ","ḋ","Đ","đ","Ð","ð","Д","д","Δ","δ",
					   "Ǳ",  "ǲ","ǳ", "Ǆ", "ǅ", "ǆ", 
					   "È","è","É","é","Ě","ě","Ê","ê","Ë","ë","Ē","ē","Ĕ","ĕ","Ę","ę","Ė","ė","Ʒ","ʒ","Ǯ","ǯ","Е","е","Э","э","Ε","ε",
					   "Ḟ","ḟ","ƒ","Ф","ф","Φ","φ",
					   "ﬁ", "ﬂ", 
					   "Ǵ","ǵ","Ģ","ģ","Ǧ","ǧ","Ĝ","ĝ","Ğ","ğ","Ġ","ġ","Ǥ","ǥ","Г","г","Γ","γ",
					   "Ĥ","ĥ","Ħ","ħ","Ж","ж","Х","х",
					   "Ì","ì","Í","í","Î","î","Ĩ","ĩ","Ï","ï","Ī","ī","Ĭ","ĭ","Į","į","İ","ı","И","и","Η","η","Ι","ι",
					   "Ĳ", "ĳ", 
					   "Ĵ","ĵ",
					   "Ḱ","ḱ","Ķ","ķ","Ǩ","ǩ","К","к","Κ","κ",
					   "Ĺ","ĺ","Ļ","ļ","Ľ","ľ","Ŀ","ŀ","Ł","ł","Л","л","Λ","λ",
					   "Ǉ", "ǈ", "ǉ", 
					   "Ṁ","ṁ","М","м","Μ","μ",
					   "Ń","ń","Ņ","ņ","Ň","ň","Ñ","ñ","ŉ","Ŋ","ŋ","Н","н","Ν","ν",
					   "Ǌ", "ǋ", "ǌ", 
					   "Ò","ò","Ó","ó","Ô","ô","Õ","õ","Ö","ö","Ō","ō","Ŏ","ŏ","Ø","ø","Ő","ő","Ǿ","ǿ","О","о","Ο","ο","Ω","ω",
					   "Œ", "œ", 
					   "Ṗ","ṗ","П","п","Π","π",
					   "Ŕ","ŕ","Ŗ","ŗ","Ř","ř","Р","р","Ρ","ρ","Ψ","ψ",
					   "Ś","ś","Ş","ş","Š","š","Ŝ","ŝ","Ṡ","ṡ","ſ","ß","С","с","Ш","ш","Щ","щ","Σ","σ","ς",
					   "Ţ","ţ","Ť","ť","Ṫ","ṫ","Ŧ","ŧ","Þ","þ","Т","т","Ц","ц","Θ","θ","Τ","τ",
					   "Ù","ù","Ú","ú","Û","û","Ũ","ũ","Ü","ü","Ů","ů","Ū","ū","Ŭ","ŭ","Ų","ų","Ű","ű","У","у",
					   "В","в","Β","β",
					   "Ẁ","ẁ","Ẃ","ẃ","Ŵ","ŵ","Ẅ","ẅ",
					   "Ξ","ξ",
					   "Ỳ","ỳ","Ý","ý","Ŷ","ŷ","Ÿ","ÿ","Й","й","Ы","ы","Ю","ю","Я","я","Υ","υ",
					   "Ź","ź","Ž","ž","Ż","ż","З","з","Ζ","ζ",
					   "Ь","ь",'Ъ',"ъ","^","&");
    $good_array= array("scow",
					   "A","a","A","a","A","a","A","a","A","a","A","a","A","a","A","a","A","a","A","a","A","a","A","a",
					   "AE","ae","AE","ae",
					   "B","b","B","b",
					   "C","c","C","c","C","c","C","c","C","c","CH","ch","CH","ch",
					   "D","d","D","d","D","d","D","d","D","d","D","d","D","d",
					   "DZ","Dz","dz","DZ","Dz","dz",
					   "E","e","E","e","E","e","E","e","E","e","E","e","E","e","E","e","E","e","E","e","E","e","Ye","ye","E","e","E","e",
					   "F","f","f","F","f","F","f",
					   "fi","fl",
					   "G","g","G","g","G","g","G","g","G","g","G","g","G","g","G","g","G","g",
					   "H","h","H","h","ZH","zh","H","h",
					   "I","i","I","i","I","i","I","i","I","i","I","i","I","i","I","i","I","i","I","i","I","i","I","i",
					   "IJ","ij",
					   "J","j",
					   "K","k","K","k","K","k","K","k","K","k",
					   "L","l","L","l","L","l","L","l","L","l","L","l","L","l",
					   "LJ","Lj","lj",
					   "M","m","M","m","M","m",
					   "N","n","N","n","N","n","N","n","n","N","n","N","n","N","n",
					   "NJ","Nj","nj",
					   "O","o","O","o","O","o","O","o","O","o","O","o","O","o","O","o","O","o","O","o","O","o","O","o","O","o",
					   "OE","oe",
					   "P","p","P","p","P","p","PS","ps",
					   "R","r","R","r","R","r","R","r","R","r",
					   "S","s","S","s","S","s","S","s","S","s","s","ss","S","s","SH","sh","SHCH","shch","S","s","s",
					   "T","t","T","t","T","t","T","t","T","t","T","t","TS","ts","TH","th","T","t",
					   "U","u","U","u","U","u","U","u","U","u","U","u","U","u","U","u","U","u","U","u","U","u",
					   "V","v","V","v",
					   "W","w","W","w","W","w","W","w",
					   "X","x",
					   "Y","y","Y","y","Y","y","Y","y","Y","y","Y","y","YU","yu","YA","ya","Y","y",
					   "Z","z","Z","z","Z","z","Z","z","Z","z",
					   "'","'",'"','"',""," and ");
	
		$name = str_replace($bad_array,$good_array,$name);
		unset($bad_array);
		unset($good_array);
		
		$name = preg_replace("/[^a-zA-Z0-9]/",$this->_sep,$name);
		
		while(strpos($name, str_repeat($this->_sep,2))){
			$name = str_replace(str_repeat($this->_sep,2),$this->_sep,$name);
		}
		return trim($name,$this->_sep);
	}
	
	
	
	public function index_standard() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}
		
		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);
			
			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");
				
				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);
					
					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}
					
					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}	
					
					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}
					
					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}	
				} else {
					$this->request->get['route'] = implode('/',$parts);
					if (defined('VERSION') && version_compare(VERSION, '2.0.0.0') >= 0) {
						return new Action($this->request->get['route']);
					}
					else{
						return $this->forward($this->request->get['route']);
					}  
					$this->request->get['route'] = 'error/not_found';	
				}
			}
			
			if (isset($this->request->get['product_id'])) {
				$this->request->get['route'] = 'product/product';
			} elseif (isset($this->request->get['path'])) {
				$this->request->get['route'] = 'product/category';
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$this->request->get['route'] = 'product/manufacturer/product';
			} elseif (isset($this->request->get['information_id'])) {
				$this->request->get['route'] = 'information/information';
			}
			
			if (isset($this->request->get['route'])) {
				if (defined('VERSION') && version_compare(VERSION, '2.0.0.0') >= 0) {
					return new Action($this->request->get['route']);
				}
				else{
					return $this->forward($this->request->get['route']);
				}
			}
		}
	}

	public function rewrite_standard($link) {
		if ($this->config->get('config_seo_url')) {
			$url_data = parse_url(str_replace('&amp;', '&', $link));
			if(!isset($url_data['query']))return $link;
		
			$url = ''; 
			
			$data = array();
						
			parse_str($url_data['query'], $data);
			
			foreach ($data as $key => $value) {
				if (isset($data['route'])) {
					if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/product' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");
					
						if ($query->num_rows) {
							$url .= '/' . $query->row['keyword'];
							
							unset($data[$key]);
						}					
					} elseif ($key == 'path') {
						$categories = explode('_', $value);
						
						foreach ($categories as $category) {
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");
					
							if ($query->num_rows) {
								$url .= '/' . $query->row['keyword'];
							}							
						}
						
						unset($data[$key]);
					}
				}
			}
		
			if ($url) {
				unset($data['route']);
			
				$query = '';
			
				if ($data) {
					foreach ($data as $key => $value) {
						$query .= '&' . $key . '=' . $value;
					}
					
					if ($query) {
						$query = '?' . trim($query, '&');
					}
				}
				return $url_data['scheme'] . '://' . $url_data['host'] . (isset($url_data['port']) ? ':' . $url_data['port'] : '') . str_replace('/index.php', '', $url_data['path']) . $url . $query;
			} else {
				return $link;
			}
		} else {
			return $link;
		}	
	
	}	
	//

}

?>
