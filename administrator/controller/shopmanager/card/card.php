<?php
namespace Opencart\Admin\Controller\Shopmanager\Card;

/**
 * Class Card
 *
 * Main card controller in card/ subdirectory
 *
 * @package Opencart\Admin\Controller\Shopmanager\Card
 */
class Card extends \Opencart\System\Engine\Controller {
    private $error = array();

    /**
     * Index
     *
     * @return void
     */
    public function index(): void {
        $lang = $this->load->language('shopmanager/card/card');
        $data = $data ?? [];
        $data += $lang;

        // Filters
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_set_name'])) {
            $filter_set_name = $this->request->get['filter_set_name'];
        } else {
            $filter_set_name = '';
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $filter_card_type_id = (int)$this->request->get['filter_card_type_id'];
        } else {
            $filter_card_type_id = 0;
        }

        if (isset($this->request->get['filter_year'])) {
            $filter_year = $this->request->get['filter_year'];
        } else {
            $filter_year = '';
        }

        if (isset($this->request->get['filter_location'])) {
            $filter_location = $this->request->get['filter_location'];
        } else {
            $filter_location = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['filter_card_id'])) {
            $filter_card_id = $this->request->get['filter_card_id'];
        } else {
            $filter_card_id = '';
        }

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $url .= '&filter_card_type_id=' . (int)$this->request->get['filter_card_type_id'];
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_card_id'])) {
            $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/card', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['add'] = $this->url->link('shopmanager/card/card.form', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['copy'] = $this->url->link('shopmanager/card/card.copy', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('shopmanager/card/card.delete', 'user_token=' . $this->session->data['user_token']);
        $data['user_token'] = $this->session->data['user_token'];

        $data['list'] = $this->load->controller('shopmanager/card/card.getList');

        // Load card types for filter dropdown
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card_type');
        $data['card_types'] = $this->model_shopmanager_card_card_type->getCardTypes();

        $data['filter_name'] = $filter_name;
        $data['filter_set_name'] = $filter_set_name;
        $data['filter_card_type_id'] = $filter_card_type_id;
        $data['filter_year'] = $filter_year;
        $data['filter_location'] = $filter_location;
        $data['filter_status'] = $filter_status;
        $data['filter_card_id'] = $filter_card_id;

        $data['per_page_options'] = [20, 50, 100, 200, 1000];

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
        $data['alert_popup'] = $this->load->controller('shopmanager/alert_popup');
        $data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');

        $this->response->setOutput($this->load->view('shopmanager/card/card', $data));
    }

    /**
     * List
     *
     * @return void
     */
    public function list(): void {
        $lang = $this->load->language('shopmanager/card/card');
        $data = $data ?? [];
        $data += $lang;

        $this->response->setOutput($this->load->controller('shopmanager/card/card.getList'));
    }

    /**
     * @return string
     */
    public function getList(): string {
        $this->document->addScript('view/javascript/shopmanager/bootstrap_helper.js');
        $this->document->addScript('view/javascript/shopmanager/ai.js');
        $this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
        $this->document->addScript('view/javascript/shopmanager/alert_popup.js');
        $this->document->addScript('view/javascript/shopmanager/ebay.js');
        $this->document->addScript('view/javascript/shopmanager/card/card_list.js');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_set_name'])) {
            $filter_set_name = $this->request->get['filter_set_name'];
        } else {
            $filter_set_name = '';
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $filter_card_type_id = (int)$this->request->get['filter_card_type_id'];
        } else {
            $filter_card_type_id = 0;
        }

        if (isset($this->request->get['filter_year'])) {
            $filter_year = $this->request->get['filter_year'];
        } else {
            $filter_year = '';
        }

        if (isset($this->request->get['filter_location'])) {
            $filter_location = $this->request->get['filter_location'];
        } else {
            $filter_location = '';
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['filter_card_id'])) {
            $filter_card_id = $this->request->get['filter_card_id'];
        } else {
            $filter_card_id = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'c.card_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = (int)$this->request->get['limit'];
        } else {
            $limit = 20;
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_card_id'])) {
            $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['cards'] = array();

        $filter_data = array(
            'filter_name' => $filter_name,
            'filter_set_name' => $filter_set_name,
            'filter_card_type_id' => $filter_card_type_id,
            'filter_year' => $filter_year,
            'filter_location' => $filter_location,
            'filter_status' => $filter_status,
            'filter_card_id' => $filter_card_id,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        );

        $this->load->model('shopmanager/card/card');

        $results = $this->model_shopmanager_card_card->getCards($filter_data);

        foreach ($results as $result) {
            $data['cards'][] = array(
                'card_id' => $result['card_id'],
                'name' => $result['player_name'],
                'player_name' => $result['player_name'],
                'title' => $result['title'],
                'set_name' => $result['set_name'], // Now using set_name from card_listing instead of brand
                'card_type_name' => $result['card_type_name'] ?? '',
                'year' => $result['year'],
                'location' => $result['location'],
                'status' => $result['status'],
                'date_added' => $result['date_added'],
                'edit' => $this->url->link('shopmanager/card/card.form', 'user_token=' . $this->session->data['user_token'] . '&card_id=' . $result['card_id'] . $url, true)
            );
        }

        $card_total = $this->model_shopmanager_card_card->getTotalCards($filter_data);

        $data['user_token'] = $this->session->data['user_token'];

        if (!$this->config->get('config_limit_admin')) {
            $config_limit_admin = 20;
        } else {
            $config_limit_admin = (int)$this->config->get('config_limit_admin');
        }

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_card_id'])) {
            $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['sort_card_id'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.card_id' . $url, true);
        $data['sort_name'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.player_name' . $url, true);
        $data['sort_player_name'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.player_name' . $url, true);
        $data['sort_title'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.title' . $url, true);
        $data['sort_set_name'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=cl.set_name' . $url, true);
        $data['sort_card_type'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=ct.name' . $url, true);
        $data['sort_year'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.year' . $url, true);
        $data['sort_location'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.location' . $url, true);
        $data['sort_status'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
        $data['sort_date_added'] = $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_card_id'])) {
            $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $card_total,
            'page' => $page,
            'limit' => $limit,
            'url' => $this->url->link('shopmanager/card/card.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true),
            'text' => ($lang['text_pagination'] ?? '')
        ]);

        $data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($card_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($card_total - $limit)) ? $card_total : ((($page - 1) * $limit) + $limit), $card_total, ceil($card_total / $limit));

        // Load card types for filter dropdown
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card_type');
        $data['card_types'] = $this->model_shopmanager_card_card_type->getCardTypes();

        $data['filter_name'] = $filter_name;
        $data['filter_set_name'] = $filter_set_name;
        $data['filter_card_type_id'] = $filter_card_type_id;
        $data['filter_year'] = $filter_year;
        $data['filter_location'] = $filter_location;
        $data['filter_status'] = $filter_status;
        $data['filter_card_id'] = $filter_card_id;

        $data['sort'] = $sort;
        $data['order'] = $order;

        return $this->load->view('shopmanager/card/card_list', $data);
    }

    /**
     * Form
     *
     * @return void
     */
    public function form(): void {
        $lang = $this->load->language('shopmanager/card/card');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            if (isset($this->request->get['card_id'])) {
                $this->load->model('shopmanager/card/card');
                $this->model_shopmanager_card->editCard($this->request->get['card_id'], $this->request->post);
            } else {
                $this->load->model('shopmanager/card/card');
                $this->model_shopmanager_card->addCard($this->request->post);
            }

            $this->session->data['success'] = ($lang['text_success'] ?? '');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_set_name'])) {
                $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_year'])) {
                $url .= '&filter_year=' . $this->request->get['filter_year'];
            }

            if (isset($this->request->get['filter_location'])) {
                $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_card_id'])) {
                $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('shopmanager/card', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    /**
     * Get Form
     *
     * @return void
     */
    protected function getForm(): void {
        $data['text_form'] = !isset($this->request->get['card_id']) ? ($lang['text_add'] ?? '') : ($lang['text_edit'] ?? '');

        $url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_card_id'])) {
            $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/card/card', 'user_token=' . $this->session->data['user_token'] . $url, true)
        );

        $data['save'] = $this->url->link('shopmanager/card/card.form', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['back'] = $this->url->link('shopmanager/card/card', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['card_id'])) {
            $this->load->model('shopmanager/card/card');
            $card_info = $this->model_shopmanager_card_card->getCard($this->request->get['card_id']);
        }

        if (isset($this->request->get['card_id'])) {
            $data['card_id'] = (int)$this->request->get['card_id'];
        } else {
            $data['card_id'] = 0;
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($card_info)) {
            $data['name'] = $card_info['player_name'];
        } else {
            $data['name'] = '';
        }

        if (isset($this->request->post['set_name'])) {
            $data['set_name'] = $this->request->post['set_name'];
        } elseif (!empty($card_info)) {
            $data['set_name'] = $card_info['set_name'];
        } else {
            $data['set_name'] = '';
        }

        if (isset($this->request->post['year'])) {
            $data['year'] = $this->request->post['year'];
        } elseif (!empty($card_info)) {
            $data['year'] = $card_info['year'];
        } else {
            $data['year'] = '';
        }

        if (isset($this->request->post['location'])) {
            $data['location'] = $this->request->post['location'];
        } elseif (!empty($card_info)) {
            $data['location'] = $card_info['location'];
        } else {
            $data['location'] = '';
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($card_info)) {
            $data['status'] = $card_info['status'];
        } else {
            $data['status'] = true;
        }

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/card/card_form', $data));
    }

    /**
     * Validate Form
     *
     * @return bool
     */
    protected function validateForm(): bool {
        if (!$this->user->hasPermission('modify', 'shopmanager/card/card')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        if ((oc_strlen($this->request->post['name']) < 1) || (oc_strlen($this->request->post['name']) > 255)) {
            $this->error['name'] = ($lang['error_name'] ?? '');
        }

        return !$this->error;
    }

    /**
     * Delete
     *
     * @return void
     */
    public function delete(): void {
        $lang = $this->load->language('shopmanager/card/card');
        $data = $data ?? [];
        $data += $lang;

        if (isset($this->request->post['selected'])) {
            if (!$this->user->hasPermission('modify', 'shopmanager/card/card')) {
                $this->error['warning'] = ($lang['error_permission'] ?? '');
            }

            $this->load->model('shopmanager/card/card');

            foreach ($this->request->post['selected'] as $card_id) {
                $this->model_shopmanager_card_card->deleteCard($card_id);
            }

            $this->session->data['success'] = ($lang['text_success'] ?? '');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_set_name'])) {
                $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_year'])) {
                $url .= '&filter_year=' . $this->request->get['filter_year'];
            }

            if (isset($this->request->get['filter_location'])) {
                $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_card_id'])) {
                $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('shopmanager/card', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->index();
    }

    /**
     * Copy
     *
     * @return void
     */
    public function copy(): void {
        $lang = $this->load->language('shopmanager/card/card');
        $data = $data ?? [];
        $data += $lang;

        if (isset($this->request->post['selected'])) {
            if (!$this->user->hasPermission('modify', 'shopmanager/card/card')) {
                $this->error['warning'] = ($lang['error_permission'] ?? '');
            }

            $this->load->model('shopmanager/card/card');

            foreach ($this->request->post['selected'] as $card_id) {
                $card_info = $this->model_shopmanager_card_card->getCard($card_id);
                if ($card_info) {
                    $this->model_shopmanager_card_card->addCard($card_info);
                }
            }

            $this->session->data['success'] = ($lang['text_success'] ?? '');

            $url = '';

            if (isset($this->request->get['filter_name'])) {
                $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_set_name'])) {
                $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_year'])) {
                $url .= '&filter_year=' . $this->request->get['filter_year'];
            }

            if (isset($this->request->get['filter_location'])) {
                $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_status'])) {
                $url .= '&filter_status=' . $this->request->get['filter_status'];
            }

            if (isset($this->request->get['filter_card_id'])) {
                $url .= '&filter_card_id=' . $this->request->get['filter_card_id'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('shopmanager/card', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->index();
    }

    /**
     * Get Cards — AJAX endpoint, pattern OpenCart 4 (filter_*, sort, order, page, limit).
     * Utilisé par card_listing_tab_cards.js pour l'infinite scroll et le tri colonne.
     *
     * GET params :
     *   filter_listing_id, filter_name, filter_set_name, filter_card_type_id,
     *   filter_year, filter_location, filter_status, filter_card_id,
     *   sort, order, page, limit
     *
     * @return void
     */
    public function getCards(): void {
        $lang = $this->load->language('shopmanager/card/card');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/card/card');

        $json = [];

        if (!$this->user->hasPermission('access', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // ── Filters (pattern OC4 getProducts) ──────────────────────────────
        $filter_data = [
            'filter_listing_id'   => isset($this->request->get['filter_listing_id'])  ? (int)$this->request->get['filter_listing_id']  : 0,
            'filter_card_id'      => $this->request->get['filter_card_id']      ?? '',
            'filter_name'         => $this->request->get['filter_name']         ?? '',
            'filter_set_name'     => $this->request->get['filter_set_name']     ?? '',
            'filter_card_type_id' => isset($this->request->get['filter_card_type_id']) ? (int)$this->request->get['filter_card_type_id'] : 0,
            'filter_year'         => $this->request->get['filter_year']         ?? '',
            'filter_location'     => $this->request->get['filter_location']     ?? '',
            'filter_status'       => $this->request->get['filter_status']       ?? '',
            'sort'                => $this->request->get['sort']  ?? 'c.card_number',
            'order'               => in_array(strtoupper($this->request->get['order'] ?? ''), ['ASC', 'DESC'])
                                        ? strtoupper($this->request->get['order'])
                                        : 'ASC',
        ];

        // ── Pagination (page/limit → start/limit) ───────────────────────────
        $page  = max(1, (int)($this->request->get['page']  ?? 1));
        $limit = min(100, max(1, (int)($this->request->get['limit'] ?? 25)));

        $filter_data['start'] = ($page - 1) * $limit;
        $filter_data['limit'] = $limit;

        // ── Fetch cards + total ─────────────────────────────────────────────
        $cards_raw = $this->model_shopmanager_card_card->getCards($filter_data);
        $total     = $this->model_shopmanager_card_card->getTotalCards($filter_data);

        // ── Build response rows (with max 2 images each) ────────────────────
        $cards = [];
        foreach ($cards_raw as $row) {
            $images = $this->model_shopmanager_card_card->getCardImageUrls((int)$row['card_id']);
            $cards[] = [
                'card_id'     => (int)$row['card_id'],
                'sku'         => $row['sku']         ?? '',
                'player_name' => $row['player_name'] ?? '',
                'card_number' => $row['card_number'] ?? '',
                'set_name'    => $row['set_name']    ?? '',
                'brand'       => $row['brand']       ?? '',
                'year'        => $row['year']        ?? '',
                'price'       => number_format((float)($row['price'] ?? 0), 2, '.', ''),
                'raw_price'   => $row['raw_price'] !== null ? number_format((float)$row['raw_price'], 2, '.', '') : null,
                'raw_price_is_ref' => (bool)($row['raw_price_is_ref'] ?? false),
                'quantity'    => (int)($row['quantity'] ?? 0),
                'merge'       => (int)($row['merge']    ?? 0),
                'batch_id'    => (int)($row['batch_id']   ?? 0),
                'batch_name'  => (int)($row['batch_name'] ?? 0),
                'images'      => array_slice($images, 0, 2),
                'price_sold'  => $row['price_sold'] !== null ? number_format((float)$row['price_sold'], 2, '.', '') : null,
                'price_list'  => $row['price_list'] !== null ? number_format((float)$row['price_list'], 2, '.', '') : null,
                'date_price_check' => $row['date_price_check'] ?? null,
            ];
        }

        $json['success'] = true;
        $json['cards']   = $cards;
        $json['total']   = $total;
        $json['page']    = $page;
        $json['limit']   = $limit;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Update card price via AJAX
     * Used for inline editing in card listing form
     *
     * @return void
     */
    public function updatePrice(): void {
        $lang = $this->load->language('shopmanager/card/card_listing');
        $data = $data ?? [];
        $data += $lang;

        $json = [];

        // Check permission
        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['success'] = false;
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Validate input
        if (!isset($this->request->post['card_id']) || !isset($this->request->post['price'])) {
            $json['success'] = false;
            $json['error'] = 'Missing card_id or price parameter';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $card_id = (int)$this->request->post['card_id'];
        $price = (float)$this->request->post['price'];

        // Validate price
        if ($price < 0) {
            $json['success'] = false;
            $json['error'] = 'Price cannot be negative';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Update price in database
        try {
            $this->db->query("UPDATE `" . DB_PREFIX . "card` 
                SET `price` = '" . $this->db->escape($price) . "' 
                WHERE `card_id` = " . (int)$card_id);

            $json['success'] = true;
            $json['message'] = 'Price updated successfully';
            $json['card_id'] = $card_id;
            $json['price'] = number_format($price, 2);
            
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = 'Database error: ' . $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Update card quantity via AJAX (inline save in card listing tab)
     */
    public function updateQuantity(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card')) {
            $json['success'] = false;
            $json['error']   = 'Permission refusée';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->request->post['card_id']) || !isset($this->request->post['quantity'])) {
            $json['success'] = false;
            $json['error']   = 'Paramètres manquants : card_id ou quantity';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $card_id  = (int)$this->request->post['card_id'];
        $quantity = (int)$this->request->post['quantity'];

        if ($quantity < 0) {
            $json['success'] = false;
            $json['error']   = 'La quantité ne peut pas être négative';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card');

        // Met à jour la quantité dans oc_card
        $this->model_shopmanager_card_card->updateCardQuantity($card_id, $quantity);

        // Recalcule total_quantity dans oc_card_listing
        $card_query = $this->db->query("SELECT `listing_id` FROM `" . DB_PREFIX . "card` WHERE `card_id` = " . $card_id);
        if ($card_query->num_rows) {
            $listing_id   = (int)$card_query->row['listing_id'];
            $total_query  = $this->db->query("SELECT SUM(`quantity`) as total FROM `" . DB_PREFIX . "card` WHERE `listing_id` = " . $listing_id);
            $total_quantity = (int)($total_query->row['total'] ?? 0);
            $this->db->query("UPDATE `" . DB_PREFIX . "card_listing` SET `total_quantity` = " . $total_quantity . " WHERE `listing_id` = " . $listing_id);
        }

        $json['success']  = true;
        $json['card_id']  = $card_id;
        $json['quantity'] = $quantity;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Update card raw_price via AJAX (inline save in card listing tab — used for lot price calculation)
     */
    public function updateRawPrice(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card')) {
            $json['success'] = false;
            $json['error']   = 'Permission refusée';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->request->post['card_id'])) {
            $json['success'] = false;
            $json['error']   = 'Paramètre manquant : card_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $card_id   = (int)$this->request->post['card_id'];
        $raw_price = isset($this->request->post['raw_price']) && $this->request->post['raw_price'] !== ''
            ? (float)$this->request->post['raw_price']
            : null;

        if ($raw_price !== null && $raw_price < 0) {
            $json['success'] = false;
            $json['error']   = 'Le prix brut ne peut pas être négatif';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $value = ($raw_price === null) ? 'NULL' : "'" . $raw_price . "'";
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card` SET `raw_price` = " . $value . " WHERE `card_id` = " . $card_id
        );

        $json['success']   = true;
        $json['card_id']   = $card_id;
        $json['raw_price'] = $raw_price !== null ? number_format($raw_price, 2) : null;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Autocomplete for card filters
     */
    public function autocomplete(): void {
        $json = [];

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_set_name'])) {
            $filter_set_name = $this->request->get['filter_set_name'];
        } else {
            $filter_set_name = '';
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $filter_card_type_id = (int)$this->request->get['filter_card_type_id'];
        } else {
            $filter_card_type_id = 0;
        }

        if (isset($this->request->get['filter_location'])) {
            $filter_location = $this->request->get['filter_location'];
        } else {
            $filter_location = '';
        }

        if (isset($this->request->get['limit'])) {
            $limit = (int)$this->request->get['limit'];
        } else {
            $limit = 5;
        }

        // Get distinct values for autocomplete
        $this->load->model('shopmanager/card/card');

        if (!empty($filter_name)) {
            $results = $this->model_shopmanager_card_card->getDistinctValues('player_name', $filter_name, $limit);
            foreach ($results as $result) {
                $json[] = [
                    'name' => $result['value']
                ];
            }
        } elseif (!empty($filter_set_name)) {
            $results = $this->model_shopmanager_card_card->getDistinctValues('set_name', $filter_set_name, $limit);
            foreach ($results as $result) {
                $json[] = [
                    'name' => $result['value']
                ];
            }
        } elseif (!empty($filter_location)) {
            $results = $this->model_shopmanager_card_card->getDistinctValues('location', $filter_location, $limit);
            foreach ($results as $result) {
                $json[] = [
                    'name' => $result['value']
                ];
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Fetch lowest market prices for a single card and save to DB.
     * Called via AJAX from card_listing_form tab-cards "Market Price" button.
     * POST: card_id
     * Returns: {success, card_id, price_sold, price_list, date}
     */
    public function fetchCardMarketPrice(): void {
        $lang = $this->load->language('shopmanager/card/card_listing');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $card_id = (int)($this->request->post['card_id'] ?? 0);
        if (!$card_id) {
            $json['error'] = 'Missing card_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card');
        $this->load->model('shopmanager/card/card_market');

        // Buffer any PHP warnings so they don't corrupt the JSON output
        ob_start();

        try {
            // Load full card + listing data
            $card = $this->model_shopmanager_card_card->getCardById($card_id);
            if (!$card) {
                ob_end_clean();
                $json['error'] = 'Card not found: ' . $card_id;
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            // Fetch market prices via Finding API (completed + active)
            $prices = $this->model_shopmanager_card_card_market->fetchLowestMarketPrices([
                'player'                 => $card['player_name'] ?? '',
                'set'                    => $card['set_name']    ?? '',
                'year'                   => $card['year']        ?? ($card['listing_year'] ?? ''),
                'brand'                  => $card['brand']       ?? ($card['listing_brand'] ?? ''),
                'card_number'            => $card['card_number'] ?? '',
                'marketplace_account_id' => 1,
            ]);

            // Save to DB (even if null — clears stale values)
            $this->model_shopmanager_card_card->updateCardMarketPrices(
                $card_id,
                $prices['price_sold'],
                $prices['price_list']
            );

            $warnings = ob_get_clean();

            $json['success']    = true;
            $json['card_id']    = $card_id;
            $json['price_sold'] = $prices['price_sold'] !== null ? number_format((float)$prices['price_sold'], 2, '.', '') : null;
            $json['price_list'] = $prices['price_list'] !== null ? number_format((float)$prices['price_list'], 2, '.', '') : null;
            $json['keyword']    = $prices['keyword'] ?? '';
            $json['api_error']  = $prices['error']   ?? '';
            $json['date']       = date('Y-m-d H:i');
            if ($warnings) {
                $json['php_warning'] = trim($warnings);
            }

        } catch (\Throwable $e) {
            ob_end_clean();
            $json['error'] = 'Exception: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
