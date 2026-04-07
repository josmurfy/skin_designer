<?php
//==============================================================================
// Smart Search v303.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

class ControllerExtensionModuleSmartsearch extends Controller {
	private $type = 'module';
	private $name = 'smartsearch';
	
	public function index() {
		$data = array(
			'type'			=> $this->type,
			'name'			=> $this->name,
			'autobackup'	=> false,
			'save_type'		=> 'keepediting',
			'permission'	=> $this->hasPermission('modify'),
		);
		
		// extension-specific
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . $this->name . "` (
			`" . $this->name . "_id` int(11) NOT NULL AUTO_INCREMENT,
			`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`search` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
			`phase` int(1) NOT NULL,
			`results` int(11) NOT NULL,
			`customer_id` int(11) NOT NULL,
			`ip` varchar(15) COLLATE utf8_bin NOT NULL DEFAULT '0',
			PRIMARY KEY (`" . $this->name . "_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin
		");
		if (isset($this->request->get['table']) && $this->request->get['table'] == 'reset') {
			$this->db->query("TRUNCATE TABLE " . DB_PREFIX . $this->name);
			if (version_compare(VERSION, '3.0', '<')) {
				$reload = $this->url->link('extension/' . $this->type . '/' . $this->name . '&token=' . $this->session->data['token'], '', 'SSL');
			} else {
				$reload = $this->url->link('extension/' . $this->type . '/' . $this->name . '&user_token=' . $this->session->data['user_token'], '', 'SSL');
			}
			$this->response->redirect($reload);
		}
		// end
		
		$this->loadSettings($data);
		
		//------------------------------------------------------------------------------
		// Data Arrays
		//------------------------------------------------------------------------------
		$data['language_array'] = array($this->config->get('config_language') => '');
		$data['language_flags'] = array();
		$this->load->model('localisation/language');
		foreach ($this->model_localisation_language->getLanguages() as $language) {
			$data['language_array'][$language['code']] = $language['name'];
			$data['language_flags'][$language['code']] = (version_compare(VERSION, '2.2', '<')) ? 'view/image/flags/' . $language['image'] : 'language/' . $language['code'] . '/' . $language['code'] . '.png';
		}
		
		//------------------------------------------------------------------------------
		// Search History
		//------------------------------------------------------------------------------
		$data['settings'] = array();
		if (empty($data['saved'])) {
			$data['settings'][] = array(
				'type'		=> 'tabs',
				'tabs'		=> array('search_settings', 'fields_searched', 'misspelling_settings', 'live_search', 'pre_search_replacements', 'testing_mode'),
			);
		} else {
			$data['settings'][] = array(
				'type'		=> 'tabs',
				'tabs'		=> array('search_history', 'search_settings', 'fields_searched', 'misspelling_settings', 'live_search', 'pre_search_replacements', 'testing_mode'),
			);
			$data['settings'][] = array(
				'type'		=> 'html',
				'content'	=> '<div class="text-info text-center" style="padding-bottom: 5px">' . $data['help_search_history'] . '</div>',
			);
			$data['settings'][] = array(
				'key'		=> 'search_history',
				'type'		=> 'heading',
				'buttons'	=> '<a class="btn btn-danger" onclick="if (confirm(\'' . $data['standard_confirm'] . '\')) go(\'extension/' . $this->type . '/' . $this->name . '&table=reset\')">' . $data['button_reset_search_history'] . '</a>',
			);
			
			$filters = array(
				'date_start'		=> date('Y-m-d', strtotime('-1 month')),
				'date_end'			=> date('Y-m-d', time()),
				'combine_searches'	=> 0,
				'page'				=> 1,
				'limit'				=> (version_compare(VERSION, '2.0', '<')) ? $this->config->get('config_admin_limit') : $this->config->get('config_limit_admin'),
			);
			$url = '';
			foreach ($filters as $key => $value) {
				if (isset($this->request->get[$key])) {
					if ($key != 'page') $url .= '&' . $key . '=' . $this->request->get[$key];
					//if ($this->request->get[$key] != '{page}') $filters[$key] = $this->request->get[$key];
					$filters[$key] = $this->request->get[$key];
				}
			}
			
			$data['settings'][] = array(
				'type'		=> 'html',
				'content'	=> '
					<style type="text/css">
						.search_history {
							-webkit-user-select: text;
							-moz-user-select: text;
							-ms-user-select: text;
							user-select: text;
						}
					</style>
					<div class="alert alert-info">
						' . $data['entry_date_start'] . ' <input type="date" id="date_start" class="nosave form-control" placeholder="' . $data['placeholder_date_format'] . '" value="' . $filters['date_start'] . '" /> &nbsp; &nbsp;
						' . $data['entry_date_end'] . ' <input type="date" id="date_end" class="nosave form-control" placeholder="' . $data['placeholder_date_format'] . '" value="' . $filters['date_end'] . '" /> &nbsp; &nbsp;
						' . $data['entry_combine_same_searches'] . ' 
						<select id="combine_searches" class="nosave form-control">
							<option value="0" ' . (!$filters['combine_searches'] ? 'selected="selected"' : '') . '>' . $data['text_no'] . '</option>
							<option value="1" ' . ($filters['combine_searches'] ? 'selected="selected"' : '') . '>' . $data['text_yes'] . '</option>
						</select> &nbsp; &nbsp;
						<a class="btn btn-primary" onclick="go(\'extension/' . $this->type . '/' . $this->name . '\')">' . $data['button_filter'] . '</a> &nbsp; &nbsp;
						<a class="btn btn-info" onclick="go(\'extension/' . $this->type . '/' . $this->name . '/exportCSV\')">' . $data['button_export_csv'] . '</a>
					</div>
				',
			);
			
			if (empty($filters['combine_searches'])) {
				$sql = "SELECT * FROM " . DB_PREFIX . $this->name . " WHERE TRUE";
			} else {
				$sql = "SELECT MIN(date_added) AS first_time, MAX(date_added) AS last_time, LCASE(search) AS search, ROUND(AVG(results),1) AS average_results, COUNT(*) AS times_searched FROM " . DB_PREFIX . $this->name . " WHERE TRUE";
			}
			$sql .= (!empty($filters['date_start'])) ? " AND DATE(date_added) >= '" . $this->db->escape($filters['date_start']) . "'" : "";
			$sql .= (!empty($filters['date_end'])) ? " AND DATE(date_added) <= '" . $this->db->escape($filters['date_end']) . "'" : "";
			$sql .= (!empty($filters['combine_searches'])) ? " GROUP BY search ORDER BY times_searched DESC" : " ORDER BY date_added DESC";
			
			$searches = $this->db->query($sql . " LIMIT " . (int)(($filters['page'] - 1) * $filters['limit']) . "," . (int)$filters['limit'])->rows;
			$searches_total = $this->db->query($sql)->num_rows;
			
			$pagination = new Pagination();
			$pagination->total = $searches_total;
			$pagination->page = $filters['page'];
			$pagination->limit = $filters['limit'];
			$pagination->text = $data['text_pagination'];
			$pagination->url = $this->url->link('extension/' . $this->type . '/' . $this->name, (version_compare(VERSION, '3.0', '<') ? 'user_token=' : 'user_token=') . $data['token'] . '&page={page}', 'SSL');
			
			$data['settings'][] = array(
				'type'		=> 'html',
				'content'	=> '<div class="pagination" style="border: none; margin-top: -10px;">' . $pagination->render() . '</div>',
			);
			
			if ($filters['combine_searches']) {
				$data['settings'][] = array(
					'key'		=> 'search_history',
					'type'		=> 'table_start',
					'columns'	=> array('action', 'first_time', 'last_time', 'search_terms', 'average_results', 'times_searched'),
				);
				foreach ($searches as $search) {
					$data['settings'][] = array(
						'type'		=> 'row_start',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> '<a class="btn btn-danger" data-key="' . $search['search'] . '" onclick="deleteRecord($(this))" data-help="' . $data['button_delete'] . '"><i class="fa fa-trash-o fa-lg fa-fw"></i></a>',
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['first_time'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['last_time'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['search'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['average_results'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['times_searched'],
					);
					$data['settings'][] = array(
						'type'		=> 'row_end',
					);
				}
				$data['settings'][] = array(
					'type'		=> 'table_end',
					'buttons'	=> '',
				);					
			} else {
				$data['settings'][] = array(
					'key'		=> 'search_history',
					'type'		=> 'table_start',
					'columns'	=> array('action', 'time', 'search_terms', 'phase_reached', 'product_results', 'customer', 'ip_address'),
				);
				
				$this->load->model((version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '/customer');
				
				foreach ($searches as $search) {
					if (empty($search['customer_id'])) {
						$customer = $data['text_guest'];
					} else {
						$customer_data = $this->{'model_' . (version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '_customer'}->getCustomer($search['customer_id']);
						$customer = '<a href="' . HTTPS_SERVER . 'index.php?route=' . (version_compare(VERSION, '2.1', '<') ? 'sale' : 'customer') . '/customer/edit&token=' . $data['token'] . '&customer_id=' . $search['customer_id'] . '" title="' . $data['text_view_customer'] . '">' . $customer_data['firstname'] . ' ' . $customer_data['lastname'] . '</a>';
					}
					$data['settings'][] = array(
						'type'		=> 'row_start',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> '<a class="btn btn-danger" data-key="' . $search['smartsearch_id'] . '" onclick="deleteRecord($(this))" data-help="' . $data['button_delete'] . '"><i class="fa fa-trash-o fa-lg fa-fw"></i></a>',
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['date_added'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['search'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['phase'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['results'],
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $customer,
					);
					$data['settings'][] = array(
						'type'		=> 'column',
					);
					$data['settings'][] = array(
						'type'		=> 'html',
						'content'	=> $search['ip'],
					);
					$data['settings'][] = array(
						'type'		=> 'row_end',
					);
				}
				$data['settings'][] = array(
					'type'		=> 'table_end',
					'buttons'	=> '',
				);					
			}

			$data['settings'][] = array(
				'type'		=> 'html',
				'content'	=> '<div class="pagination" style="border: none; margin-top: -10px;">' . $pagination->render() . '</div>',
			);
			
			$data['settings'][] = array(
				'key'		=> 'search_settings',
				'type'		=> 'tab',
			);
		}
		
		//------------------------------------------------------------------------------
		// Search Settings
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info">' . $data['help_search_settings'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'search_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'status',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_enabled'], 0 => $data['text_disabled']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'phase_behavior',
			'type'		=> 'select',
			'options'	=> array('normal' => $data['text_run_normally'], 'skip' => $data['text_skip_phase_1'], 'proceed' => $data['text_perform_phase_1']),
		);
		$data['settings'][] = array(
			'key'		=> 'default_sort',
			'type'		=> 'select',
			'options'	=> array(
				'date_added'		=> $data['text_date_added'],
				'date_available'	=> $data['text_date_available'],
				'date_modified'		=> $data['text_date_modified'],
				'model'				=> $data['text_model'],
				'name'				=> $data['text_name'],
				'price'				=> $data['text_price'],
				'quantity'			=> $data['text_quantity'],
				'rating'			=> $data['text_rating'],
				'sort_order'		=> $data['text_sort_order'],
				'times_purchased'	=> $data['text_times_purchased'],
				'times_viewed'		=> $data['text_times_viewed'],
			),
			'default'	=> 'sort_order',
		);
		$data['settings'][] = array(
			'key'		=> 'default_order',
			'type'		=> 'select',
			'options'	=> array(
				'ASC'	=> $data['text_ascending'],
				'DESC'	=> $data['text_descending'],
			),
		);
		$data['settings'][] = array(
			'key'		=> 'cache_keywords',
			'type'		=> 'select',
			'options'	=> array(
				'3600'		=> $data['text_hourly'],
				'86400'		=> $data['text_daily'],
				'604800'	=> $data['text_weekly'],
				'2592000'	=> $data['text_monthly'],
				'31536000'	=> $data['text_yearly'],
				'0'			=> $data['text_dont_use_cache'],
			),
			'default'	=> '86400',
			'after'		=> '<a id="keywords" class="btn btn-primary" onclick="clearCache($(this))">' . $data['button_clear_cache'] . '</a>',
		);
		$data['settings'][] = array(
			'key'		=> 'plurals',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'partials',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'min_results',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'single_redirect',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'subcategories',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'use_html_encoding',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 1,
		);
		$data['settings'][] = array(
			'key'		=> 'hide_out_of_stock',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_yes'], 0 => $data['text_no']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'hidden_products',
			'type'		=> 'textarea',
		);
		$data['settings'][] = array(
			'key'		=> 'excluded_ips',
			'type'		=> 'textarea',
		);
		
		//------------------------------------------------------------------------------
		// Fields Searched
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'fields_searched',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info">' . $data['help_fields_searched'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'fields_searched',
			'type'		=> 'heading',
		);
		
		$search_fields_1 = array(
			'product_id',
			'name',
			'description',
			'description_misspelled',
			'meta_title',
			'meta_description',
			'meta_keyword',
			'tag',
			'model',
			'sku',
			'ean',
			'jan',
		);
		$search_fields_2 = array(
			'isbn',
			'mpn',
			'upc',
			'location',
			'category',
			'manufacturer',
			'attribute_group',
			'attribute_name',
			'attribute_value',
			'option_name',
			'option_value',
		);
		
		$data['settings'][] = array(
			'key'		=> 'search_fields',
			'type'		=> 'table_start',
			'columns'	=> array(),
			'attributes'=> array('style' => 'max-width: 600px'),
		);
		for ($i = 0; $i < count($search_fields_1); $i++) {
			$data['settings'][] = array(
				'type'		=> 'row_start',
			);
			$data['settings'][] = array(
				'type'		=> 'html',
				'content'	=> '<div class="text-right" style="padding-top: 7px">' . $data['text_search_' . $search_fields_1[$i]] . '</div>',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> 'search_' . $search_fields_1[$i],
				'type'		=> 'text',
				'attributes'=> array('style' => 'width: 50px !important'),
				'default'	=> ($search_fields_1[$i] == 'name') ? 1 : '',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			if (isset($search_fields_2[$i])) {
				$data['settings'][] = array(
					'type'		=> 'html',
					'content'	=> '<div class="text-right" style="padding-top: 7px">' . $data['text_search_' . $search_fields_2[$i]] . '</div>',
				);
			}
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			if (isset($search_fields_2[$i])) {
				$data['settings'][] = array(
					'key'		=> 'search_' . $search_fields_2[$i],
					'type'		=> 'text',
					'attributes'=> array('style' => 'width: 50px !important'),
				);
			}
			$data['settings'][] = array(
				'type'		=> 'row_end',
			);
		}
		$data['settings'][] = array(
			'type'		=> 'table_end',
			'buttons'	=> '',
		);
		
		if (version_compare(VERSION, '2.0', '<')) {
			$data['settings'][] = array(
				'type'		=> 'html',
				'content'	=> "<script>$('#input-search_meta_title').attr('disabled', 'disabled');</script>",
			);
		}
		
		//------------------------------------------------------------------------------
		// Misspelling Settings
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'misspelling_settings',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'key'		=> 'misspelling_settings',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'tolerance',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '75',
			'after'		=> '%',
		);
		$data['settings'][] = array(
			'key'		=> 'cache_misspelling',
			'type'		=> 'select',
			'options'	=> array(
				'3600'		=> $data['text_hourly'],
				'86400'		=> $data['text_daily'],
				'604800'	=> $data['text_weekly'],
				'2592000'	=> $data['text_monthly'],
				'31536000'	=> $data['text_yearly'],
				'0'			=> $data['text_dont_use_cache'],
			),
			'default'	=> '86400',
			'after'		=> '<a id="misspelling" class="btn btn-primary" onclick="clearCache($(this))">' . $data['button_clear_cache'] . '</a>',
		);
		$data['settings'][] = array(
			'key'		=> 'word_length',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '3',
		);
		
		//------------------------------------------------------------------------------
		// Live Search
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'live_search',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'key'		=> 'live_search',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'live_search',
			'type'		=> 'select',
			'options'	=> array('0' => $data['text_disabled'], 1 => $data['text_enabled']),
		);
		$data['settings'][] = array(
			'key'		=> 'live_selector',
			'type'		=> 'text',
			'default'	=> '#search input',
		);
		$data['settings'][] = array(
			'key'		=> 'live_css',
			'type'		=> 'textarea',
		);
		
		// Colors
		$data['settings'][] = array(
			'key'		=> 'colors',
			'type'		=> 'heading',
		);
		$color_class = "jscolor {required: false, hash: true, insetColor:'#333', backgroundColor: '#515151'}";
		$data['settings'][] = array(
			'key'		=> 'live_background_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#FFFFFF',
		);
		$data['settings'][] = array(
			'key'		=> 'live_borders_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#EEEEEE',
		);
		$data['settings'][] = array(
			'key'		=> 'live_font_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#000000',
		);
		$data['settings'][] = array(
			'key'		=> 'live_highlight_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#FF0000',
		);
		$data['settings'][] = array(
			'key'		=> 'live_hover_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#EEFFFF',
		);
		$data['settings'][] = array(
			'key'		=> 'live_price_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#000000',
		);
		$data['settings'][] = array(
			'key'		=> 'live_special_color',
			'type'		=> 'text',
			'class'		=> $color_class,
			'default'	=> '#FF0000',
		);
		
		// Display
		$data['settings'][] = array(
			'key'		=> 'display',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'live_delay',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '500',
		);
		$data['settings'][] = array(
			'key'		=> 'live_limit',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '5',
		);
		$data['settings'][] = array(
			'key'		=> 'live_price',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_show'], '0' => $data['text_hide']),
		);
		$data['settings'][] = array(
			'key'		=> 'live_model',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_show'], '0' => $data['text_hide']),
			'default'	=> '0',
		);
		$data['settings'][] = array(
			'key'		=> 'live_addtocart_button',
			'type'		=> 'select',
			'options'	=> array('0' => $data['text_hide'], 'quantity' => $data['text_show_with_quantity_field'], 'no_quantity' => $data['text_show_without_quantity_field']),
			'default'	=> '0',
		);
		$data['settings'][] = array(
			'key'		=> 'live_addtocart_class',
			'type'		=> 'text',
			'default'	=> 'btn btn-primary',
		);
		$data['settings'][] = array(
			'key'		=> 'live_description',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '100',
		);
		
		// Positioning
		$data['settings'][] = array(
			'key'		=> 'positioning',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'live_top',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '42',
		);
		$data['settings'][] = array(
			'key'		=> 'live_left',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
		);
		$data['settings'][] = array(
			'key'		=> 'live_right',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
		);
		
		// Sizes
		$data['settings'][] = array(
			'key'		=> 'sizes',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'live_width',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '98%',
		);
		$data['settings'][] = array(
			'key'		=> 'live_image_width',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '30',
		);
		$data['settings'][] = array(
			'key'		=> 'live_image_height',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '30',
		);
		$data['settings'][] = array(
			'key'		=> 'live_product_font',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '13',
		);
		$data['settings'][] = array(
			'key'		=> 'live_description_font',
			'type'		=> 'text',
			'attributes'=> array('style' => 'width: 50px !important'),
			'default'	=> '11',
		);
		
		// Text
		$data['settings'][] = array(
			'key'		=> 'text',
			'type'		=> 'heading',
		);
		$data['settings'][] = array(
			'key'		=> 'live_viewall',
			'type'		=> 'multilingual_text',
			'default'	=> 'View All Results',
		);
		$data['settings'][] = array(
			'key'		=> 'live_noresults',
			'type'		=> 'multilingual_text',
			'default'	=> 'No Results',
		);
		$data['settings'][] = array(
			'key'		=> 'live_addtocart_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Add to Cart',
		);
		$data['settings'][] = array(
			'key'		=> 'live_quantity_text',
			'type'		=> 'multilingual_text',
			'default'	=> 'Qty:',
		);
		
		//------------------------------------------------------------------------------
		// Pre-Search Replacements
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'pre_search_replacements',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center" style="padding-bottom: 5px">' . $data['help_pre_search_replacements'] . '</div>',
		);
		$data['settings'][] = array(
			'key'		=> 'pre_search_replacements',
			'type'		=> 'heading',
		);
		
		$table = 'replacement';
		$sortby = 'replace';
		$data['settings'][] = array(
			'key'		=> $table,
			'type'		=> 'table_start',
			'columns'	=> array('action', 'replace', 'with'),
		);
		foreach ($this->getTableRowNumbers($data, $table, $sortby) as $num => $rules) {
			$prefix = $table . '_' . $num . '_';
			$data['settings'][] = array(
				'type'		=> 'row_start',
			);
			$data['settings'][] = array(
				'key'		=> 'delete',
				'type'		=> 'button',
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'replace',
				'type'		=> 'text',
				'attributes'=> array('style' => 'width: 200px !important'),
			);
			$data['settings'][] = array(
				'type'		=> 'column',
			);
			$data['settings'][] = array(
				'key'		=> $prefix . 'with',
				'type'		=> 'text',
				'attributes'=> array('style' => 'width: 200px !important'),
			);
			$data['settings'][] = array(
				'type'		=> 'row_end',
			);
		}
		$data['settings'][] = array(
			'type'		=> 'table_end',
			'buttons'	=> 'add_row',
			'text'		=> 'button_add_replacement',
		);
		
		//------------------------------------------------------------------------------
		// Testing Mode
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'tab',
		);
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '<div class="text-info text-center pad-bottom">' . $data['testing_mode_help'] . '</div>',
		);
		
		$filepath = DIR_LOGS . $this->name . '.messages';
		$testing_mode_log = '';
		$refresh_or_download_button = '<a class="btn btn-info" onclick="refreshLog()"><i class="fa fa-refresh pad-right-sm"></i> ' . $data['button_refresh_log'] . '</a>';
		
		if (file_exists($filepath)) {
			$filesize = filesize($filepath);
			
			if ($filesize > 50000000) {
				file_put_contents($filepath, '');
				$filesize = 0;
			}
			
			if ($filesize > 999999) {
				$testing_mode_log = $data['standard_testing_mode'];
				$refresh_or_download_button = '<a class="btn btn-info" href="index.php?route=extension/' . $this->type . '/' . $this->name . '/downloadLog&token=' . $data['token'] . '"><i class="fa fa-download pad-right-sm"></i> ' . $data['button_download_log'] . ' (' . round($filesize / 1000000, 1) . ' MB)</a>';
			} else {
				$testing_mode_log = html_entity_decode(file_get_contents($filepath), ENT_QUOTES, 'UTF-8');
			}
		}
		
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'heading',
			'buttons'	=> $refresh_or_download_button . ' <a class="btn btn-danger" onclick="clearLog()"><i class="fa fa-trash-o pad-right-sm"></i> ' . $data['button_clear_log'] . '</a>',
		);
		$data['settings'][] = array(
			'key'		=> 'testing_mode',
			'type'		=> 'select',
			'options'	=> array(1 => $data['text_enabled'], 0 => $data['text_disabled']),
			'default'	=> 0,
		);
		$data['settings'][] = array(
			'key'		=> 'testing_messages',
			'type'		=> 'textarea',
			'class'		=> 'nosave',
			'attributes'=> array('style' => 'width: 100% !important; height: 400px; font-size: 12px !important'),
			'default'	=> htmlentities($testing_mode_log),
		);
		
		//------------------------------------------------------------------------------
		// Custom javascript functions
		//------------------------------------------------------------------------------
		$data['settings'][] = array(
			'type'		=> 'html',
			'content'	=> '
				<script>
					$("textarea").tabby();
					
					function clearCache(element) {
						if (!confirm("' . $data['standard_confirm'] . '")) return;
						element.attr("disabled", "disabled").html("' . $data['standard_please_wait'] . '");
						
						$.get("index.php?route=extension/' . $this->type . '/' . $this->name . '/clearCache&type=" + element.attr("id") + "&token=' . $data['token'] . '",
							function(data) {
								alert(data);
								element.removeAttr("disabled").html("' . $data['button_clear_cache'] . '");
							}
						);
					}
					
					function go(route) {
						url = "index.php?route=" + route + "&token=' . $data['token'] . '";
						url += ($("#date_start").val()) ? "&date_start=" + encodeURIComponent($("#date_start").val()) : "";
						url += ($("#date_end").val()) ? "&date_end=" + encodeURIComponent($("#date_end").val()) : "";
						url += ($("#combine_searches").val()) ? "&combine_searches=" + encodeURIComponent($("#combine_searches").val()) : "";
						location = url;
					}
					
					function deleteRecord(element) {
						if (!confirm("' . $data['standard_confirm'] . '")) return;
						$.ajax({
							type: "POST",
							url: "index.php?route=extension/' . $this->type . '/' . $this->name . '/deleteRecord&token=' . $data['token'] . '",
							data: {key: element.attr("data-key"), combined: $("#combine_searches").val()},
							success: function(data) {
								if (data) {
									alert(data);
								} else {
									element.parent().parent().parent().remove();
								}
							}
						});
					}
				</script>
			',
		);
		
		$this->document->addScript('view/javascript/jscolor.min.js');
		$this->document->addScript('view/javascript/jquery/jquery.tabby.min.js');
		
		//------------------------------------------------------------------------------
		// end settings
		//------------------------------------------------------------------------------
		
		$this->document->setTitle($data['heading_title']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$template_file = DIR_TEMPLATE . 'extension/' . $this->type . '/' . $this->name . '.twig';
		
		if (is_file($template_file)) {
			extract($data);
			
			ob_start();
			require(class_exists('VQMod') ? VQMod::modCheck(modification($template_file)) : modification($template_file));
			$output = ob_get_clean();
			
			if (version_compare(VERSION, '3.0', '>=')) {
				$output = str_replace(array('&token=', '&amp;token='), '&user_token=', $output);
			}
			
			echo $output;
		} else {
			echo 'Error loading template file';
		}
	}
	
	//==============================================================================
	// Helper functions
	//==============================================================================
	private function hasPermission($permission) {
		return ($this->user->hasPermission($permission, $this->type . '/' . $this->name) || $this->user->hasPermission($permission, 'extension/' . $this->type . '/' . $this->name));
	}
	
	private function loadLanguage($path) {
		$_ = array();
		$language = array();
		$admin_language = (version_compare(VERSION, '2.2', '<')) ? $this->db->query("SELECT * FROM " . DB_PREFIX . "language WHERE `code` = '" . $this->db->escape($this->config->get('config_admin_language')) . "'")->row['directory'] : $this->config->get('config_admin_language');
		foreach (array('english', 'en-gb', $admin_language) as $directory) {
			$file = DIR_LANGUAGE . $directory . '/' . $directory . '.php';
			if (file_exists($file)) require($file);
			$file = DIR_LANGUAGE . $directory . '/default.php';
			if (file_exists($file)) require($file);
			$file = DIR_LANGUAGE . $directory . '/' . $path . '.php';
			if (file_exists($file)) require($file);
			$file = DIR_LANGUAGE . $directory . '/extension/' . $path . '.php';
			if (file_exists($file)) require($file);
			$language = array_merge($language, $_);
		}
		return $language;
	}
	
	private function getTableRowNumbers(&$data, $table, $sorting) {
		$groups = array();
		$rules = array();
		
		foreach ($data['saved'] as $key => $setting) {
			if (preg_match('/' . $table . '_(\d+)_' . $sorting . '/', $key, $matches)) {
				$groups[$setting][] = $matches[1];
			}
			if (preg_match('/' . $table . '_(\d+)_rule_(\d+)_type/', $key, $matches)) {
				$rules[$matches[1]][] = $matches[2];
			}
		}
		
		if (empty($groups)) $groups = array('' => array('1'));
		ksort($groups, defined('SORT_NATURAL') ? SORT_NATURAL : SORT_REGULAR);
		
		foreach ($rules as $key => $rule) {
			ksort($rules[$key], defined('SORT_NATURAL') ? SORT_NATURAL : SORT_REGULAR);
		}
		
		$data['used_rows'][$table] = array();
		$rows = array();
		foreach ($groups as $group) {
			foreach ($group as $num) {
				$data['used_rows'][preg_replace('/module_(\d+)_/', '', $table)][] = $num;
				$rows[$num] = (empty($rules[$num])) ? array() : $rules[$num];
			}
		}
		sort($data['used_rows'][$table]);
		
		return $rows;
	}
	
	//==============================================================================
	// Setting functions
	//==============================================================================
	private $encryption_key = '';
	
	public function loadSettings(&$data) {
		$backup_type = (empty($data)) ? 'manual' : 'auto';
		if ($backup_type == 'manual' && !$this->hasPermission('modify')) {
			return;
		}
		
		$this->cache->delete($this->name);
		unset($this->session->data[$this->name]);
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		// Set exit URL
		$data['token'] = $this->session->data[version_compare(VERSION, '3.0', '<') ? 'token' : 'user_token'];
		$data['exit'] = $this->url->link((version_compare(VERSION, '3.0', '<') ? 'extension' : 'marketplace') . '/' . (version_compare(VERSION, '2.3', '<') ? '' : 'extension&type=') . $this->type . '&token=' . $data['token'], '', 'SSL');
		
		// Load saved settings
		$data['saved'] = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$key = str_replace($code . '_', '', $setting['key']);
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			
			$data['saved'][$key] = $value;
			
			if (is_array($value)) {
				foreach ($value as $num => $value_array) {
					foreach ($value_array as $k => $v) {
						$data['saved'][$key . '_' . $num . '_' . $k] = $v;
					}
				}
			}
		}
		
		// Load language and run standard checks
		$data = array_merge($data, $this->loadLanguage($this->type . '/' . $this->name));
		
		if (ini_get('max_input_vars') && ((ini_get('max_input_vars') - count($data['saved'])) < 50)) {
			$data['warning'] = $data['standard_max_input_vars'];
		}
		
		// Modify files according to OpenCart version
		if ($this->type == 'total' && version_compare(VERSION, '2.2', '<')) {
			file_put_contents(DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php', str_replace('public function getTotal($total) {', 'public function getTotal(&$total_data, &$order_total, &$taxes) {' . "\n\t\t" . '$total = array("totals" => &$total_data, "total" => &$order_total, "taxes" => &$taxes);', file_get_contents(DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php')));
		}
		
		if (version_compare(VERSION, '2.3', '>=')) {
			$filepaths = array(
				DIR_APPLICATION . 'controller/' . $this->type . '/' . $this->name . '.php',
				DIR_CATALOG . 'controller/' . $this->type . '/' . $this->name . '.php',
				DIR_CATALOG . 'model/' . $this->type . '/' . $this->name . '.php',
			);
			foreach ($filepaths as $filepath) {
				if (file_exists($filepath)) {
					rename($filepath, str_replace('.php', '.php-OLD', $filepath));
				}
			}
		}
		
		// Set save type and skip auto-backup if not needed
		if (!empty($data['saved']['autosave'])) {
			$data['save_type'] = 'auto';
		}
		
		if ($backup_type == 'auto' && empty($data['autobackup'])) {
			return;
		}
		
		// Create settings auto-backup file
		$manual_filepath = DIR_LOGS . $this->name . $this->encryption_key . '.backup';
		$auto_filepath = DIR_LOGS . $this->name . $this->encryption_key . '.autobackup';
		$filepath = ($backup_type == 'auto') ? $auto_filepath : $manual_filepath;
		if (file_exists($filepath)) unlink($filepath);
		
		file_put_contents($filepath, 'SETTING	NUMBER	SUB-SETTING	SUB-NUMBER	SUB-SUB-SETTING	VALUE' . "\n", FILE_APPEND|LOCK_EX);
		
		foreach ($data['saved'] as $key => $value) {
			if (is_array($value)) continue;
			
			$parts = explode('|', preg_replace(array('/_(\d+)_/', '/_(\d+)/'), array('|$1|', '|$1'), $key));
			
			$line = '';
			for ($i = 0; $i < 5; $i++) {
				$line .= (isset($parts[$i]) ? $parts[$i] : '') . "\t";
			}
			$line .= str_replace(array("\t", "\n"), array('    ', '\n'), $value) . "\n";
			
			file_put_contents($filepath, $line, FILE_APPEND|LOCK_EX);
		}
		
		$data['autobackup_time'] = date('Y-M-d @ g:i a');
		$data['backup_time'] = (file_exists($manual_filepath)) ? date('Y-M-d @ g:i a', filemtime($manual_filepath)) : '';
		
		if ($backup_type == 'manual') {
			echo $data['autobackup_time'];
		}
	}
	
	public function saveSettings() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$this->cache->delete($this->name);
		unset($this->session->data[$this->name]);
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		if ($this->request->get['saving'] == 'manual') {
			$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' AND `key` != '" . $this->db->escape($this->name . '_module') . "'");
		}
		
		$module_id = 0;
		$modules = array();
		$module_instance = false;
		
		foreach ($this->request->post as $key => $value) {
			if (strpos($key, 'module_') === 0) {
				$parts = explode('_', $key, 3);
				$module_id = $parts[1];
				$modules[$parts[1]][$parts[2]] = $value;
				if ($parts[2] == 'module_id') $module_instance = true;
			} else {
				$key = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name . '_' . $key;
				
				if ($this->request->get['saving'] == 'auto') {
					$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "'");
				}
				
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "setting SET
					`store_id` = 0,
					`code` = '" . $this->db->escape($code) . "',
					`key` = '" . $this->db->escape($key) . "',
					`value` = '" . $this->db->escape(stripslashes(is_array($value) ? implode(';', $value) : $value)) . "',
					`serialized` = 0
				");
			}
		}
		
		foreach ($modules as $module_id => $module) {
			if (!$module_id) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "module SET
					`name` = '" . $this->db->escape($module['name']) . "',
					`code` = '" . $this->db->escape($this->name) . "',
					`setting` = ''
				");
				$module_id = $this->db->getLastId();
				$module['module_id'] = $module_id;
			}
			$module_settings = (version_compare(VERSION, '2.1', '<')) ? serialize($module) : json_encode($module);
			$this->db->query("
				UPDATE " . DB_PREFIX . "module SET
				`name` = '" . $this->db->escape($module['name']) . "',
				`code` = '" . $this->db->escape($this->name) . "',
				`setting` = '" . $this->db->escape($module_settings) . "'
				WHERE module_id = " . (int)$module_id . "
			");
		}
	}
	
	public function deleteSetting() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		$prefix = (version_compare(VERSION, '3.0', '<')) ? '' : $this->type . '_';
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($prefix . $this->name) . "' AND `key` = '" . $this->db->escape($prefix . $this->name . '_' . str_replace('[]', '', $this->request->get['setting'])) . "'");
	}
	
	//==============================================================================
	// Testing Mode functions
	//==============================================================================
	public function refreshLog() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		if (!$this->hasPermission('modify')) {
			echo $data['standard_error'];
			return;
		}
		
		$filepath = DIR_LOGS . $this->name . '.messages';
		
		if (file_exists($filepath)) {
			if (filesize($filepath) > 999999) {
				echo $data['standard_testing_mode'];
			} else {
				echo html_entity_decode(file_get_contents($filepath), ENT_QUOTES, 'UTF-8');
			}
		}
	}
	
	public function downloadLog() {
		$file = DIR_LOGS . $this->name . '.messages';
		if (!file_exists($file) || !$this->hasPermission('access')) {
			return;
		}
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . $this->name . '.' . date('Y-n-d') . '.log');
		header('Content-Length: ' . filesize($file));
		header('Content-Transfer-Encoding: binary');
		header('Content-Type: text/plain');
		header('Expires: 0');
		header('Pragma: public');
		readfile($file);
	}
	
	public function clearLog() {
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		
		if (!$this->hasPermission('modify')) {
			echo $data['standard_error'];
			return;
		}
		
		file_put_contents(DIR_LOGS . $this->name . '.messages', '');
	}
	
	//==============================================================================
	// Custom functions
	//==============================================================================
	public function clearCache() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$cache = (isset($this->request->get['type']) && $this->request->get['type'] == 'misspelling') ? $this->name : $this->name . '_hash';
		$files = glob(DIR_CACHE . $cache . '.*');
		if (!empty($files)) {
			foreach ($files as $file) {
				if (file_exists($file)) unlink($file);
			}
		}
		
		$data = $this->loadLanguage($this->type . '/' . $this->name);
		echo $data['standard_success'];
	}
	
	public function createFulltextIndexes() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		$tables = array(
			'product',
			'product_description',
			'category_description',
			'manufacturer',
			'attribute_group_description',
			'attribute_description',
			'product_attribute',
			'option_description',
			'option_value_description',
		);
		
		foreach ($tables as $table) {
			${$table . '_columns'} = array();
		}
		
		foreach ($this->request->post as $key => $value) {
			if ($value == '') continue;
			
			if ($key == 'search_product_id')	$product_columns[] = 'product_id';
			if ($key == 'search_model')			$product_columns[] = 'model';
			if ($key == 'search_sku')			$product_columns[] = 'sku';
			if ($key == 'search_ean')			$product_columns[] = 'ean';
			if ($key == 'search_jan')			$product_columns[] = 'jan';
			if ($key == 'search_isbn')			$product_columns[] = 'isbn';
			if ($key == 'search_mpn')			$product_columns[] = 'mpn';
			if ($key == 'search_upc')			$product_columns[] = 'upc';
			if ($key == 'search_location')		$product_columns[] = 'location';
			
			if ($key == 'search_name')				$product_description_columns[] = 'name';
			if ($key == 'search_description')		$product_description_columns[] = 'description';
			if ($key == 'search_meta_description')	$product_description_columns[] = 'meta_description';
			if ($key == 'search_meta_keyword')		$product_description_columns[] = 'meta_keyword';
			
			if ($key == 'search_category')		$category_description_columns[] = 'name';
			if ($key == 'search_manufacturer')	$manufacturer_columns[] = 'name';
			
			if ($key == 'search_attribute_group')	$product_attribute_columns[] = 'name';
			if ($key == 'search_attribute_name')	$attribute_description_columns[] = 'name';
			if ($key == 'search_attribute_value')	$product_attribute_columns[] = 'text';
			
			if ($key == 'search_option_name')	$option_description_columns[] = 'name';
			if ($key == 'search_option_value')	$option_value_description_columns[] = 'name';
		}
		
		foreach ($tables as $table) {
			if (empty(${$table . '_columns'})) continue;
			
			$columns = ${$table . '_columns'};
			$table = DB_PREFIX . $table;
			
			$query = $this->db->query("SHOW INDEX FROM " . $table . " WHERE Key_name = '" . $this->name . "'");
			if ($query->num_rows) {
				$this->db->query("ALTER TABLE " . $table . " DROP INDEX " . $this->name);
			}
			$this->db->query("CREATE FULLTEXT INDEX " . $this->name . " ON " . $table . "(`" . implode("`,`", $columns) . "`)");
		}
		
		echo 'Complete!';
	}
	
	public function exportCSV() {
		if (!$this->hasPermission('access')) {
			return;
		}
		
		header('Pragma: public');
		header('Expires: 0');
		header('Content-Description: File Transfer');
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=' . $this->name . '_history.csv');
		header('Content-Transfer-Encoding: binary');
		
		$filters = array(
			'date_start'		=> (!empty($this->request->get['date_start'])) ? $this->request->get['date_start'] : '',
			'date_end'			=> (!empty($this->request->get['date_end'])) ? $this->request->get['date_end'] : '',
			'combine_searches'	=> (!empty($this->request->get['combine_searches'])) ? $this->request->get['combine_searches'] : '',
		);
		
		if (empty($filters['combine_searches'])) {
			$sql = "SELECT * FROM " . DB_PREFIX . $this->name . " WHERE TRUE";
			$columns = array('smart_search_id', 'date_added', 'search', 'phase', 'results', 'customer_id', 'ip');
		} else {
			$sql = "SELECT MIN(date_added) AS first_time, MAX(date_added) AS last_time, LCASE(search) AS search, ROUND(AVG(results),1) AS average_results, COUNT(*) AS times_searched FROM " . DB_PREFIX . $this->name . " WHERE TRUE";
			$columns = array('first_time', 'last_time', 'search', 'average_results', 'times_searched');
		}
		$sql .= (!empty($filters['date_start'])) ? " AND DATE(date_added) >= '" . $this->db->escape($filters['date_start']) . "'" : "";
		$sql .= (!empty($filters['date_end'])) ? " AND DATE(date_added) <= '" . $this->db->escape($filters['date_end']) . "'" : "";
		$sql .= (!empty($filters['combine_searches'])) ? " GROUP BY search ORDER BY times_searched DESC" : " ORDER BY date_added DESC";
		
		$searches = $this->db->query($sql)->rows;
		
		echo strtoupper('" ' . implode('"," ', $columns) . '"') . "\n";
		foreach ($searches as $search) {
			$search['search'] = str_replace(array('<br>[', '<br>'), array(' [', ''), $search['search']);
			echo '"' . implode('","', str_replace('"', "''", $search)) . '"' . "\n";
		}
		
		exit();
	}
	
	public function deleteRecord() {
		if (!$this->hasPermission('modify')) {
			echo 'PermissionError';
			return;
		}
		
		if (!$this->request->post['combined']) {
			$this->db->query("DELETE FROM " . DB_PREFIX . $this->name . " WHERE smartsearch_id = " . (int)$this->request->post['key']);
		} else {
			$this->db->query("DELETE FROM " . DB_PREFIX . $this->name . " WHERE search = '" . $this->db->escape($this->request->post['key']) . "'");
		}
	}
}
?>