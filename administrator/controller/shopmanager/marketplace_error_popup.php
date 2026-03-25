<?php
namespace Opencart\Admin\Controller\Shopmanager;

class MarketplaceErrorPopup extends \Opencart\System\Engine\Controller {
    public function index(): string {
        //error_log('🔥 marketplace_error_popup controller CALLED');
        //error_log('🔥 GET params: ' . print_r($this->request->get, true));
        
        $this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
        $lang = $this->load->language('shopmanager/marketplace_error_popup');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/marketplace');

        $product_id = $this->request->get['product_id'] ?? null;
        //error_log('🔥 product_id: ' . $product_id);
        
        $marketplace_account_id = $this->request->get['marketplace_account_id'] ?? null;
        $marketplace_item_id = $this->request->get['marketplace_item_id'] ?? null;
        
        $data['error'] = $this->model_shopmanager_marketplace->getMarketplaceERROR($product_id);
        //error_log('🔥 error data: ' . print_r($data['error'], true));
        
        $data['product_id'] = $product_id;
        $data['marketplace_account_id'] = $marketplace_account_id;
        $data['marketplace_item_id'] = $marketplace_item_id;

        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_close'] = ($lang['text_close'] ?? '');
        $data['text_retry'] = ($lang['text_retry'] ?? '');
        $data['text_fix_issue'] = ($lang['text_fix_issue'] ?? '');

        $data['rows_error'] = $this->generateTableRows($data['error']);

        $view = $this->load->view('shopmanager/marketplace_error_popup', $data);
        
        // Si appelé directement via AJAX (route dans l'URL), setOutput est nécessaire
        // Si appelé comme enfant via load->controller(), return suffit
        $route = $this->request->get['route'] ?? '';
        if (str_contains($route, 'marketplace_error_popup')) {
            $this->response->setOutput($view);
        }
        
        return $view;
    }

    private function generateTableRows($data = null, $prefix = '') {
        $rows = '';

        if (empty($data)) {
            return '<tr><td colspan="2"><em>Aucune donnée</em></td></tr>';
        }

        foreach ($data as $key => $value) {
            $displayKey = $prefix !== '' ? $prefix . '[' . $key . ']' : '[' . $key . ']';
            $displayKey = str_replace('.', ' ', $displayKey);

            if (is_array($value) || is_object($value)) {
                $nested = $this->generateTableRows((array)$value, $displayKey);
                $rows .= '<tr><td>' . htmlspecialchars($displayKey) . '</td><td>';
                $rows .= '<div class="table-responsive"><table class="table table-bordered table-hover" style="border-collapse: separate; border-color: transparent;">';
                $rows .= '<thead><tr><th>Key</th><th>Value</th></tr></thead><tbody>';
                $rows .= $nested;
                $rows .= '</tbody></table></div>';
                $rows .= '</td></tr>';
            } else {
                $rows .= '<tr><td>' . htmlspecialchars($displayKey) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
            }
        }

        return $rows;
    }
}
