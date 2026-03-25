<?php
class ControllerShopManagerMarketplaceErrorPopup extends Controller {

    public function index() {
        // Charger le JS pour gérer l'affichage de la popup
        $this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
    
        // Charger les traductions
        $this->load->language('shopmanager/marketplace_error_popup');
    
        // Charger le modèle
        $this->load->model('shopmanager/marketplace');
    
        // Récupérer les paramètres GET
        $product_id = $this->request->get['product_id'] ?? null;
    
        // Tu pourrais aussi charger l'erreur directement ici si besoin
        $data['error'] = $this->model_shopmanager_marketplace->getMarketplaceERROR($product_id);
        //print("<pre>".print_r ('18:error',true )."</pre>");
        //print("<pre>".print_r ($data,true )."</pre>");

        // Charger les textes traduits dans $data
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_close'] = $this->language->get('text_close');
        $data['text_retry'] = $this->language->get('text_retry');
        $data['text_fix_issue'] = $this->language->get('text_fix_issue');
    
        // ID pour savoir quel item est en erreur (utile pour JS)
        //$data['marketplace_item_id'] = $marketplace_item_id;
        $data['rows_error'] = $this->generateTableRows($data['error']);
        // Afficher le template
        $this->response->setOutput($this->load->view('shopmanager/marketplace_error_popup', $data));
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
