<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Walmart extends \Opencart\System\Engine\Controller {
    public function authorize(): void {
        $this->load->model('shopmanager/walmart');
        $connectionapi = $this->model_shopmanager_walmart->getApiCredentials();
        $this->model_shopmanager_walmart->redirectToAuthorizationEndpoint($connectionapi);
    }

    public function handleAuthorizationRedirect(): void {
        $this->load->model('shopmanager/walmart');
        $connectionapi = $this->model_shopmanager_walmart->getApiCredentials();
        $this->model_shopmanager_walmart->handleAuthorizationRedirect($connectionapi);
    }
}