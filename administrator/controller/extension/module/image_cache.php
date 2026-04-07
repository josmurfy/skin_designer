<?php
/**
 * Image Cache v1.1
 * Last update 19.07.2018(d.m.y)
 *
 * Support
 * Site: digital-elephant.com.ua
 * mail: digital.elephant.studio@gmail.com
 *
 * Created by Digital Elephant
 */

class ControllerExtensionModuleImageCache extends Controller
{
    const MODULE_KEY = 'de_image_cache';

    private static $keySessionCacheImageCount = 'cache_image_count';

    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/image_cache');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->addStyle(HTTP_SERVER . 'view/stylesheet/digital-elephant-image-cache.css');
        $this->document->addStyle('https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css');
        $this->document->addScript('https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js');


        $this->load->model('extension/module');
        $this->load->model('setting/setting');
        $this->load->model('tool/image');

        $this->load->model('localisation/language');
        $languages = $this->model_localisation_language->getLanguages();
        $data['languages'] = $languages;

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting(self::MODULE_KEY, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_settings'] = $this->language->get('text_settings');
        $data['text_message_info_header'] = $this->language->get('text_message_info_header');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_width'] = $this->language->get('entry_width');
        $data['entry_height'] = $this->language->get('entry_height');
        $data['entry_image_sizes'] = $this->language->get('entry_image_sizes');

        $data['tab_general'] = $this->language->get('tab_general');
        $data['tab_advanced'] = $this->language->get('tab_advanced');

        $data['notice_advanced'] = $this->language->get('notice_advanced');

        $data['text_js_size_table_error']     = $this->language->get('text_js_size_table_error');
        $data['text_js_confirm']        = $this->language->get('text_js_confirm');
        $data['text_js_server_error']   = $this->language->get('text_js_server_error');
        $data['text_js_completed']   = $this->language->get('text_js_completed');

        $data['button_run_cache'] = $this->language->get('button_run_cache');
        $data['button_stop_cache'] = $this->language->get('button_stop_cache');
        $data['button_reset_cache'] = $this->language->get('button_reset_cache');


        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['empty_table_sizes'])) {
            $data['error_empty_table_sizes'] = $this->error['empty_table_sizes'];
        } else {
            $data['error_empty_table_sizes'] = '';
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['token'], true)
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/image_cache', 'user_token=' . $this->session->data['token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/image_cache', 'user_token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        $data['image_sizes_of_current_template'] = $this->getImageSizesOfCurrentTemplate();
        $data['input_fields'] = $this->createAndGetFormInputs();

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/image_cache', 'user_token=' . $this->session->data['token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/image_cache', 'user_token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['token'], true);

        $data['action_cache_start'] = $this->urlToAjax($this->url->link('extension/module/image_cache/ajaxCacheImageStart', 'user_token=' . $this->session->data['token'], true));
        $data['action_save_last_state'] = $this->urlToAjax($this->url->link('extension/module/image_cache/ajaxSaveLastState', 'user_token=' . $this->session->data['token'], true));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/image_cache', $data));
    }


    private function createAndGetFormInputs()
    {
        $post = $this->request->post;
        $key = self::MODULE_KEY;

        $defaultValues = $this->getDefaultValues();

        if (isset($post[$key . '_sizes'])) {
            $value_sizes = $post[$key . '_sizes'];
        } elseif ($this->config->has($key . '_sizes')) {
            $value_sizes = $this->config->get($key . '_sizes');
        } else {
            $value_sizes = $defaultValues['sizes'];
        }

        if (isset($post[$key . '_is_cache_after_save_or_edit'])) {
            $value_is_cache_after_save_or_edit = $post[$key . '_is_cache_after_save_or_edit'];
        } elseif ($this->config->has($key . '_is_cache_after_save_or_edit')) {
            $value_is_cache_after_save_or_edit = $this->config->get($key . '_is_cache_after_save_or_edit');
        } elseif (!$this->model_setting_setting->getSetting(self::MODULE_KEY)) {
            $value_is_cache_after_save_or_edit = $defaultValues['sizes'];
        } else {
            $value_is_cache_after_save_or_edit = false;
        }

        if (isset($post[$key . '_quantity_of_images'])) {
            $value_quantity_of_images = $post[$key . '_quantity_of_images'];
        } elseif ($this->config->has($key . '_quantity_of_images')) {
            $value_quantity_of_images = $this->config->get($key . '_quantity_of_images');
        } else {
            $value_quantity_of_images = $defaultValues['quantity_of_images'];
        }

        if (isset($post[$key . '_cached_image_count'])) {
            $value_cached_image_count = $post[$key . '_cached_image_count'];
        } elseif ($this->config->has($key . '_cached_image_count')) {
            $value_cached_image_count = $this->config->get($key . '_cached_image_count');
        } else {
            $value_cached_image_count = $defaultValues['cached_image_count'];
        }

        if (isset($post[$key . '_delay_between_requests'])) {
            $value_delay_between_requests = $post[$key . '_delay_between_requests'];
        } elseif ($this->config->has($key . '_delay_between_requests')) {
            $value_delay_between_requests = $this->config->get($key . '_delay_between_requests');
        } else {
            $value_delay_between_requests = $defaultValues['delay_between_requests'];
        }

        $output['is_cache_after_save_or_edit'] = [
            'label' => $this->language->get('entry_is_cache_after_save_or_edit'),
            'name' => $key . '_is_cache_after_save_or_edit',
            'is_checked' => ($value_is_cache_after_save_or_edit) ? 'checked' : '',
        ];

        $output['sizes'] = [
            'label' => $this->language->get('entry_table_sizes'),
            'base_name' => $key . '_sizes',
            'values' => $value_sizes,
        ];

        $output['quantity_of_images'] = [
            'label' => $this->language->get('entry_quantity_of_images'),
            'name' => $key . '_quantity_of_images',
            'value' => $value_quantity_of_images
        ];

        $output['delay_between_requests'] = [
            'label' => $this->language->get('entry_delay_between_requests'),
            'name' => $key . '_delay_between_requests',
            'value' => $value_delay_between_requests
        ];

        $output['cached_image_count'] = [
            'name' => $key . '_cached_image_count',
            'value' => $value_cached_image_count
        ];

        return $output;
    }

    private function getImageSizesOfCurrentTemplate()
    {
        $imageTypes = [
            'thumb',
            'popup',
            'product',
            'additional',
            'related',
            'compare',
            'wishlist',
            'cart'
        ];

        $output = [];

        foreach ($imageTypes as $imageType) {
            if ($this->config->has($this->config->get('config_theme') . '_image_' . $imageType . '_width') && $this->config->has($this->config->get('config_theme') . '_image_' . $imageType . '_height')) {
                $output[] = [
                    'name' => $this->language->get('text_image_' . $imageType . ''),
                    'size' => [
                        'width' => $this->config->get($this->config->get('config_theme') . '_image_' . $imageType . '_width'),
                        'height' => $this->config->get($this->config->get('config_theme') . '_image_' . $imageType . '_height')
                    ]
                ];
            }
        }

       return $output;
    }

    public function ajaxCacheImageStart() {

        $json['error'] = '';
        if (!empty($this->request->post[self::MODULE_KEY . '_sizes'])) {

            $cachedImageCount = $this->request->post[self::MODULE_KEY . '_cached_image_count'];

            $this->load->model('catalog/product');
            $this->load->model('tool/image');

            $total = $this->model_catalog_product->getTotalProducts();

            $limit = $this->request->post[self::MODULE_KEY . '_quantity_of_images'];

            $modelProducts = $this->model_catalog_product->getProducts([
                'start' => $cachedImageCount,
                'limit' => $limit,
                'sort'  => 'p.product_id',
                'order' => 'ASC'
            ]);

            $json['isCompleted'] = false;

            if ($modelProducts) {
                foreach ($modelProducts as $modelProduct) {
                    $images = [];

                    if ($modelProduct['image']) {
                        $images[] = $modelProduct['image'];
                    }

                    $modelImages = $this->model_catalog_product->getProductImages($modelProduct['product_id']);

                    if ($modelImages) {
                        foreach ($modelImages as $modelImage) {
                            if ($modelImage['image']) {
                                $images[] = $modelImage['image'];
                            }
                        }
                    }

                    foreach ($images as $image) {
                        if (file_exists(DIR_IMAGE . $image)) {
                            foreach ($this->request->post[self::MODULE_KEY . '_sizes'] as $size) {
                                $this->model_tool_image->resize($image, $size['width']['value'], $size['height']['value']);
                            }
                        }
                    }
                }

                $json['cachedImageCount'] = $cachedImageCount + count($modelProducts);

                usleep($this->request->post[self::MODULE_KEY . '_delay_between_requests'] * 1000);
            } else {
                $json['isCompleted'] = true;
                $json['cachedImageCount'] = $cachedImageCount;
            }

            $json['percentComplete'] = round(($cachedImageCount + $this->request->post[self::MODULE_KEY . '_quantity_of_images'])/$total * 100) . '%';
        } else {
            $this->load->language('extension/module/image_cache');
            $json['error'] = $this->language->get('text_js_input_error');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function ajaxSaveLastState() {
        $this->saveLastState();
    }

    public function saveLastState() {
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting(self::MODULE_KEY, $this->request->post);
    }

    private function getDefaultValues() {

        $value_sizes = [];
        foreach ($this->getImageSizesOfCurrentTemplate() as $item) {
            $value_sizes[] = [
                'width' => [
                    'value' => $item['size']['width']
                ],
                'height' => [
                    'value' => $item['size']['height']
                ],
            ];
        }

        return [
            'sizes' => $value_sizes,
            'is_cache_after_save_or_edit' => true,
            'quantity_of_images' => 10,
            'delay_between_requests' => 100,
            'cached_image_count' => 0
        ];
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/image_cache')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!empty($this->request->post[self::MODULE_KEY . '_sizes'])) {
            foreach ($this->request->post[self::MODULE_KEY . '_sizes'] as &$row) {
                if (empty(trim($row['width']['value'])) || !ctype_digit($row['width']['value'])) {
                    $row['width']['error'] = $this->language->get('error_sizes_width');
                    $this->error[] = true;
                }
                if (empty(trim($row['height']['value'])) || !ctype_digit($row['height']['value'])) {
                    $row['height']['error'] = $this->language->get('error_sizes_height');
                    $this->error[] = true;
                }
            }
            unset($row);
        } else {
            $this->error['empty_table_sizes'] = $this->language->get('error_empty_table_sizes');
        }

        return !$this->error;
    }

    private function urlToAjax($url)
    {
        return str_replace('&amp;', '&', $url);
    }
}
