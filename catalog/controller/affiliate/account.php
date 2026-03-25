<?php
namespace Opencart\Catalog\Controller\Affiliate;
class Account extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->response->redirect($this->url->link('common/home', '', true));
    }
}
