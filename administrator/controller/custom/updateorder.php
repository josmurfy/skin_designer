<?php
class ControllerCustomUpdateOrder extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('custom/updateorder');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/updateorder');

        $this->getForm();
    }

    public function update() {
        $this->load->language('custom/updateorder');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('custom/updateorder');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_custom_updateorder->updateOrder($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('custom/updateorder', 'user_token=' . $this->session->data['token'], true));
        }

        $this->getForm();
    }

    public function getEbayOrders() {
        $this->load->model('custom/updateorder');
        $orders = $this->model_custom_updateorder->getEbayOrders();

        // Format the orders to be returned as JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($orders));
    }

    protected function getForm() {


        $this->load->model('custom/updateorder');

        $data['token'] = $this->session->data['token'];
        date_default_timezone_set('America/New_York');
        //$dateheure=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]." ".$ebayinputnameline[1];
        $dateformat=explode (" ",gmdate('Y-m-d H:i:s',strtotime("-6 days")));
        //echo strtotime (date('Y-m-d',$dateformat[0].' - 1 days'));
        $date_transaction=$dateformat[0]."T00:00:01.000Z";//.$dateformat[1].
        $data['orders'] = $this->model_custom_updateorder->getEbayOrders($date_transaction); // Simulated data for orders
  //      $data['inventory'] = $this->model_custom_updateorder->getInventory(); // Simulated data for inventory

 //print("<pre>".print_r ($data['orders'],true )."</pre>");

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = $this->language->get('text_form');
        $data['entry_sku'] = $this->language->get('entry_sku');
        $data['entry_vendu'] = $this->language->get('entry_vendu');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('custom/updateorder', 'user_token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('custom/updateorder/update', 'user_token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('custom/updateorder', 'user_token=' . $this->session->data['token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('custom/updateorder_form.tpl', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'custom/updateorder')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
?>
