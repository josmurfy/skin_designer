<?php
// Original: warehouse/card/manufacturer.php
namespace Opencart\Admin\Controller\Warehouse\Card;

class Manufacturer extends \Opencart\System\Engine\Controller {

    private array $error = [];

    // ------------------------------------------------------------------
    // INDEX – list view
    // ------------------------------------------------------------------
    public function index(): void {
        $this->load->language('warehouse/card/manufacturer');
        $data = [];
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->load->model('warehouse/card/manufacturer');

        $this->getList();
    }

    // ------------------------------------------------------------------
    // ADD
    // ------------------------------------------------------------------
    public function add(): void {
        $json = [];

        $this->load->language('warehouse/card/manufacturer');
        $data = [];
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->load->model('warehouse/card/manufacturer');

        if (!$this->user->hasPermission('modify', 'warehouse/card/manufacturer')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $isAjax = isset($this->request->get['ajax']) && $this->request->get['ajax'] == 'true';

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
            $manufacturer_id = $this->model_warehouse_card_manufacturer->addManufacturer($this->request->post);

            $this->session->data['success'] = ($lang['text_success'] ?? '');
            $json['success']         = ($lang['text_success'] ?? '');
            $json['manufacturer_id'] = $manufacturer_id;

            if ($isAjax) {
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $this->response->redirect(
                $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'] . $this->buildUrlParams(), true)
            );
            return;
        }

        if ($isAjax) {
            $json['error'] = $this->error['warning'] ?? ($lang['error_form'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->getForm();
    }

    // ------------------------------------------------------------------
    // EDIT
    // ------------------------------------------------------------------
    public function edit(): void {
        $json = [];

        $this->load->language('warehouse/card/manufacturer');
        $data = [];
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->load->model('warehouse/card/manufacturer');

        if (!$this->user->hasPermission('modify', 'warehouse/card/manufacturer')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $isAjax = isset($this->request->get['ajax']) && $this->request->get['ajax'] == 'true';

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
            $this->model_warehouse_card_manufacturer->editManufacturer(
                (int)$this->request->get['manufacturer_id'],
                $this->request->post
            );

            $this->session->data['success'] = ($lang['text_success'] ?? '');
            $json['success'] = ($lang['text_success'] ?? '');

            if ($isAjax) {
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $this->response->redirect(
                $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'] . $this->buildUrlParams(), true)
            );
            return;
        }

        if ($isAjax) {
            $json['error'] = $this->error['warning'] ?? ($lang['error_form'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->getForm();
    }

    // ------------------------------------------------------------------
    // DELETE
    // ------------------------------------------------------------------
    public function delete(): void {
        $json = [];

        $this->load->language('warehouse/card/manufacturer');
        $data = [];
        
        $this->load->model('warehouse/card/manufacturer');

        if (!$this->user->hasPermission('modify', 'warehouse/card/manufacturer')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $isAjax = isset($this->request->get['ajax']) && $this->request->get['ajax'] == 'true';

        if (isset($this->request->post['selected'])) {
            foreach ($this->request->post['selected'] as $manufacturer_id) {
                $this->model_warehouse_card_manufacturer->deleteManufacturer((int)$manufacturer_id);
            }

            $this->session->data['success'] = ($lang['text_delete_success'] ?? '');
            $json['success'] = ($lang['text_delete_success'] ?? '');

            if ($isAjax) {
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $this->response->redirect(
                $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'] . $this->buildUrlParams(), true)
            );
            return;
        }

        $json['error'] = ($lang['error_delete'] ?? '');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ------------------------------------------------------------------
    // AJAX SEARCH (auto-complete / select2 usage)
    // ------------------------------------------------------------------
    public function search(): void {
        $this->load->model('warehouse/card/manufacturer');

        $filter_name = $this->request->get['filter_name'] ?? '';

        $manufacturers = $this->model_warehouse_card_manufacturer->getManufacturers([
            'filter_name'   => $filter_name,
            'filter_status' => 1,
            'start'         => 0,
            'limit'         => 50,
        ]);

        $json = [];
        foreach ($manufacturers as $m) {
            $json[] = [
                'manufacturer_id' => $m['manufacturer_id'],
                'name'            => $m['name'],
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ------------------------------------------------------------------
    // PRIVATE: build HTML list page
    // ------------------------------------------------------------------
    protected function getList(): void {
        $data = [];

        // Filters
        $filter_name      = $this->request->get['filter_name']      ?? '';
        $filter_card_type = $this->request->get['filter_card_type']  ?? '';
        $filter_status    = $this->request->get['filter_status']     ?? '';

        $sort  = $this->request->get['sort']  ?? 'name';
        $order = $this->request->get['order'] ?? 'ASC';
        $page  = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
        $limit = 25;

        $manufacturers = $this->model_warehouse_card_manufacturer->getManufacturers([
            'filter_name'      => $filter_name,
            'filter_card_type' => $filter_card_type,
            'filter_status'    => $filter_status,
            'sort'             => $sort,
            'order'            => $order,
            'start'            => ($page - 1) * $limit,
            'limit'            => $limit,
        ]);

        $total = $this->model_warehouse_card_manufacturer->getTotalManufacturers([
            'filter_name'      => $filter_name,
            'filter_card_type' => $filter_card_type,
            'filter_status'    => $filter_status,
        ]);

        // Rows
        $data['manufacturers'] = [];
        foreach ($manufacturers as $m) {
            $data['manufacturers'][] = [
                'manufacturer_id' => $m['manufacturer_id'],
                'name'            => $m['name'],
                'card_type'       => $m['card_type'] ?? '',
                'status'          => $m['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
                'edit'            => $this->url->link('warehouse/card/manufacturer.edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $m['manufacturer_id'], true),
            ];
        }

        // Card types for filter dropdown (from oc_card_type)
        $this->load->model('warehouse/card/type');
        $data['card_types'] = $this->model_warehouse_card_type->getCardTypes();

        // Pagination – OC4 pattern
        $url = '';
        if ($filter_name)                          $url .= '&filter_name='      . urlencode($filter_name);
        if ($filter_card_type !== '')              $url .= '&filter_card_type=' . urlencode($filter_card_type);
        if ($filter_status !== '')                 $url .= '&filter_status='    . $filter_status;
        if ($sort)                                 $url .= '&sort='             . $sort;
        if ($order)                                $url .= '&order='            . $order;

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true)
        ]);

        $data['results'] = sprintf(
            ($lang['text_pagination'] ?? ''),
            ($total) ? ($page - 1) * $limit + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : (($page - 1) * $limit + $limit),
            $total,
            ceil($total / $limit)
        );

        // Urls
        $data['add']    = $this->url->link('warehouse/card/manufacturer.add',    'user_token=' . $this->session->data['user_token'], true);
        $data['delete'] = $this->url->link('warehouse/card/manufacturer.delete', 'user_token=' . $this->session->data['user_token'], true);

        // Sorting links
        $data['sort']  = $sort;
        $data['order'] = $order;
        $url_base      = 'user_token=' . $this->session->data['user_token'] . '&filter_name=' . urlencode($filter_name) . '&filter_card_type=' . urlencode($filter_card_type) . '&filter_status=' . $filter_status;
        $data['sort_name']       = $this->url->link('warehouse/card/manufacturer', $url_base . '&sort=name&order='   . ($sort == 'name'   && $order == 'ASC' ? 'DESC' : 'ASC'), true);
        $data['sort_status']     = $this->url->link('warehouse/card/manufacturer', $url_base . '&sort=status&order=' . ($sort == 'status' && $order == 'ASC' ? 'DESC' : 'ASC'), true);

        // Filters pass-through
        $data['filter_name']      = $filter_name;
        $data['filter_card_type'] = $filter_card_type;
        $data['filter_status']    = $filter_status;

        // Flash messages
        $data['success'] = '';
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        $data['error_warning'] = $this->error['warning'] ?? '';

        // Language strings
        $data = array_merge($data, $this->buildLangData());

        // Breadcrumbs
        $data['breadcrumbs'] = [
            [
                'text' => ($lang['text_home'] ?? ''),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            ],
            [
                'text' => ($lang['heading_title'] ?? ''),
                'href' => $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'], true),
            ],
        ];

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

                $data['text_select_one_mfr']      = ($lang['text_select_one_mfr'] ?? '');
        $data['text_confirm_delete_mfr']  = ($lang['text_confirm_delete_mfr'] ?? '');
        $data['text_name_length_error']   = ($lang['text_name_length_error'] ?? '');

        $this->response->setOutput($this->load->view('warehouse/card/manufacturer_list', $data));
    }

    // ------------------------------------------------------------------
    // PRIVATE: build HTML add/edit form
    // ------------------------------------------------------------------
    protected function getForm(): void {
        $data = [];

        $manufacturer_id = isset($this->request->get['manufacturer_id']) ? (int)$this->request->get['manufacturer_id'] : 0;

        $manufacturer = [];
        if ($manufacturer_id) {
            $manufacturer = $this->model_warehouse_card_manufacturer->getManufacturer($manufacturer_id);
        }

        // Load POST values or DB values
        $data['manufacturer_id'] = $manufacturer_id;
        $data['name']            = $this->request->post['name']   ?? $manufacturer['name']   ?? '';
        $data['status']          = $this->request->post['status'] ?? $manufacturer['status'] ?? 1;

        // Selected card type IDs (from POST array or from pivot table)
        if (isset($this->request->post['card_type_ids'])) {
            $data['selected_card_type_ids'] = array_map('intval', (array)$this->request->post['card_type_ids']);
        } elseif ($manufacturer_id) {
            $data['selected_card_type_ids'] = $this->model_warehouse_card_manufacturer->getManufacturerCardTypeIds($manufacturer_id);
        } else {
            $data['selected_card_type_ids'] = [];
        }

        // All available card types for the multiselect
        $this->load->model('warehouse/card/type');
        $data['card_types'] = $this->model_warehouse_card_type->getCardTypes();

        // Errors
        $data['error_name']    = $this->error['name']    ?? '';
        $data['error_warning'] = $this->error['warning'] ?? '';

        // Success flash
        $data['success'] = '';
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        // Action URLs
        if ($manufacturer_id) {
            $data['action'] = $this->url->link('warehouse/card/manufacturer.edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $manufacturer_id, true);
        } else {
            $data['action'] = $this->url->link('warehouse/card/manufacturer.add', 'user_token=' . $this->session->data['user_token'], true);
        }
        $data['cancel'] = $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'], true);

        // Language strings
        $data = array_merge($data, $this->buildLangData());

        // Breadcrumbs
        $data['breadcrumbs'] = [
            [
                'text' => ($lang['text_home'] ?? ''),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            ],
            [
                'text' => ($lang['heading_title'] ?? ''),
                'href' => $this->url->link('warehouse/card/manufacturer', 'user_token=' . $this->session->data['user_token'], true),
            ],
            [
                'text' => $manufacturer_id ? ($lang['text_edit'] ?? '') : ($lang['text_add'] ?? ''),
                'href' => $data['action'],
            ],
        ];

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('warehouse/card/manufacturer_form', $data));
    }

    // ------------------------------------------------------------------
    // VALIDATION
    // ------------------------------------------------------------------
    protected function validateForm(): bool {
        if (!$this->user->hasPermission('modify', 'warehouse/card/manufacturer')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        $name = trim($this->request->post['name'] ?? '');
        if (empty($name) || strlen($name) > 100) {
            $this->error['name'] = ($lang['error_name'] ?? '');
        }

        return empty($this->error);
    }

    protected function validateDelete(): bool {
        if (!$this->user->hasPermission('modify', 'warehouse/card/manufacturer')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        return empty($this->error);
    }

    // ------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------
    private function buildUrlParams(): string {
        $url = '';
        if (isset($this->request->get['sort']))  $url .= '&sort='  . $this->request->get['sort'];
        if (isset($this->request->get['order'])) $url .= '&order=' . $this->request->get['order'];
        if (isset($this->request->get['page']))  $url .= '&page='  . $this->request->get['page'];
        return $url;
    }

    private function buildLangData(): array {
        $keys = [
            'heading_title', 'text_list', 'text_add', 'text_edit', 'text_no_results',
            'text_confirm_delete', 'text_enabled', 'text_disabled', 'text_filter', 'text_card_type_help',
            'column_name', 'column_card_type', 'column_status', 'column_action',
            'entry_name', 'entry_card_type', 'entry_status',
            'button_add', 'button_edit', 'button_delete', 'button_save', 'button_cancel', 'button_filter', 'button_reset',
        ];

        $data = [];
        foreach ($keys as $key) {
            $data[$key] = ($lang[$key] ?? '');
        }

        return $data;
    }
}
