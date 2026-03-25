<?php
// catalog/controller/module/recent_products.php
class ControllerModuleHelloWorld extends Controller {
    public function index($setting) {
        $this->load->language('module/helloworld');

 
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_cart'] = $this->language->get('button_cart');
        $data['button_wishlist'] = $this->language->get('button_wishlist');
        $data['button_compare'] = $this->language->get('button_compare');
 
        $this->load->model('module/helloworld');
 
        $this->load->model('tool/image');
 
        $data['products'] = array();
 		//$setting['limit'] = (isset($setting['limit'])) ? $setting['limit'] : '4';
        $results = $this->model_module_helloworld->getvalue('4');
 
        if ($results) {
            foreach ($results as $result) {
                if ($result['image']) {
                    $image = $this->model_tool_image->resize($result['image'], 100, 100);
                } else {
                    $image = $this->model_tool_image->resize('placeholder.png', 100, 100);
                }
 
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')),'INR');                   
 
                } else {
                    $price = false;
                }
 
                if ((float)$result['special']) {
                    $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')),'INR');
                } else {
                    $special = false;
                }
 
                $data['products'][] = array(
                    'product_id'  => $result['product_id'],
                    'thumb'       => $image,
                    'name'        => $result['name'],
                    'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
                    'price'       => $price,
                    'special'     => $special,
                    'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
                );
            }
 
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/helloworld.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/module/helloworld.tpl', $data);
            } else {
                return $this->load->view('module/helloworld.tpl', $data);
            }
        }
    }
}