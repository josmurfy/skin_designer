<?php
// Original: shopmanager/card/card_listing.php
namespace Opencart\Admin\Controller\Shopmanager\Card;

/**
 * Class CardListing
 *
 * Card Listing controller for managing eBay card listings
 *
 * @package Opencart\Admin\Controller\Shopmanager\Card
 */
class CardListing extends \Opencart\System\Engine\Controller {
    private $error = array();

    /**
     * Index
     *
     * @return void
     */
    public function index(): void {
        $data = $this->load->language('shopmanager/card/card_listing');

        // Filters
        if (isset($this->request->get['filter_listing_id'])) {
            $filter_listing_id = $this->request->get['filter_listing_id'];
        } else {
            $filter_listing_id = '';
        }

        if (isset($this->request->get['filter_set_name'])) {
            $filter_set_name = $this->request->get['filter_set_name'];
        } else {
            $filter_set_name = '';
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $filter_card_type_id = $this->request->get['filter_card_type_id'];
        } else {
            $filter_card_type_id = '';
        }

        if (isset($this->request->get['filter_year'])) {
            $filter_year = $this->request->get['filter_year'];
        } else {
            $filter_year = '';
        }

        if (isset($this->request->get['filter_manufacturer'])) {
            $filter_manufacturer = $this->request->get['filter_manufacturer'];
        } else {
            $filter_manufacturer = '';
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

        if (isset($this->request->get['filter_language'])) {
            $filter_language = $this->request->get['filter_language'];
        } else {
            $filter_language = '';
        }

        if (isset($this->request->get['filter_ebay_item_id'])) {
            $filter_ebay_item_id = $this->request->get['filter_ebay_item_id'];
        } else {
            $filter_ebay_item_id = '';
        }

        if (isset($this->request->get['filter_publish_status'])) {
            $filter_publish_status = $this->request->get['filter_publish_status'];
        } else {
            $filter_publish_status = '';
        }

        if (isset($this->request->get['filter_sync'])) {
            $filter_sync = $this->request->get['filter_sync'];
        } else {
            $filter_sync = '';
        }

        if (isset($this->request->get['limit'])) {
            $filter_limit = (int)$this->request->get['limit'];
        } else {
            $filter_limit = 150;
        }

        $this->document->setTitle(($data['heading_title'] ?? ''));

        $url = '';

        if (isset($this->request->get['filter_listing_id'])) {
            $url .= '&filter_listing_id=' . $this->request->get['filter_listing_id'];
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $url .= '&filter_card_type_id=' . urlencode(html_entity_decode($this->request->get['filter_card_type_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

        if (isset($this->request->get['filter_manufacturer'])) {
            $url .= '&filter_manufacturer=' . urlencode(html_entity_decode($this->request->get['filter_manufacturer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_language'])) {
            $url .= '&filter_language=' . $this->request->get['filter_language'];
        }

        if (isset($this->request->get['filter_ebay_item_id'])) {
            $url .= '&filter_ebay_item_id=' . urlencode(html_entity_decode($this->request->get['filter_ebay_item_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_publish_status'])) {
            $url .= '&filter_publish_status=' . $this->request->get['filter_publish_status'];
        }

        if (isset($this->request->get['filter_sync'])) {
            $url .= '&filter_sync=' . $this->request->get['filter_sync'];
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

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => ($data['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => ($data['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/card/card_listing', 'user_token=' . $this->session->data['user_token'] . $url)
        ];

        $data['add'] = $this->url->link('shopmanager/card/card_listing.form', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['delete'] = $this->url->link('shopmanager/card/card_listing.delete', 'user_token=' . $this->session->data['user_token']);
        $data['url_update_location'] = html_entity_decode($this->url->link('shopmanager/card/card_listing.updateLocation', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_check_ebay_health'] = html_entity_decode($this->url->link('shopmanager/card/card_listing.checkEbayHealth', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['user_token'] = $this->session->data['user_token'];

        $data['list'] = $this->load->controller('shopmanager/card/card_listing.getList');

        // Load card types for filter dropdown
        $this->load->model('shopmanager/card/card_type');
        $data['card_types'] = $this->model_shopmanager_card_card_type->getCardTypes();

        $data['filter_listing_id'] = $filter_listing_id;
        $data['filter_set_name'] = $filter_set_name;
        $data['filter_card_type_id'] = $filter_card_type_id;
        $data['filter_year'] = $filter_year;
        $data['filter_manufacturer'] = $filter_manufacturer;
        $data['filter_location'] = $filter_location;
        $data['filter_status'] = $filter_status;
        $data['filter_language'] = $filter_language;
        $data['filter_ebay_item_id'] = $filter_ebay_item_id;
        $data['filter_publish_status'] = $filter_publish_status;
        $data['filter_sync'] = $filter_sync;
        $data['per_page_options'] = [20, 50, 100, 150, 200];
        $data['limit'] = $filter_limit;

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/card/card_listing', $data));
    }

    /**
     * List
     *
     * @return void
     */
    public function list(): void {
        $this->response->setOutput($this->load->controller('shopmanager/card/card_listing.getList'));
    }

    /**
     * Get List
     *
     * @return string
     */
    public function getList(): string {
        $data = $this->load->language('shopmanager/card/card_listing');

        $this->document->addScript('view/javascript/shopmanager/card_listing_list.js');

        // Filters
        if (isset($this->request->get['filter_listing_id'])) {
            $filter_listing_id = $this->request->get['filter_listing_id'];
        } else {
            $filter_listing_id = '';
        }

        if (isset($this->request->get['filter_set_name'])) {
            $filter_set_name = $this->request->get['filter_set_name'];
        } else {
            $filter_set_name = '';
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $filter_card_type_id = $this->request->get['filter_card_type_id'];
        } else {
            $filter_card_type_id = '';
        }

        if (isset($this->request->get['filter_year'])) {
            $filter_year = $this->request->get['filter_year'];
        } else {
            $filter_year = '';
        }

         if (isset($this->request->get['filter_manufacturer'])) {
             $filter_manufacturer = $this->request->get['filter_manufacturer'];
         } else {
             $filter_manufacturer = '';
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

        if (isset($this->request->get['filter_language'])) {
            $filter_language = $this->request->get['filter_language'];
        } else {
            $filter_language = '';
        }

        if (isset($this->request->get['filter_ebay_item_id'])) {
            $filter_ebay_item_id = $this->request->get['filter_ebay_item_id'];
        } else {
            $filter_ebay_item_id = '';
        }

        if (isset($this->request->get['filter_publish_status'])) {
            $filter_publish_status = $this->request->get['filter_publish_status'];
        } else {
            $filter_publish_status = '';
        }

        if (isset($this->request->get['filter_sync'])) {
            $filter_sync = $this->request->get['filter_sync'];
        } else {
            $filter_sync = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'l.listing_id';
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
            $limit = 150;
        }

        $url = '';

        if (isset($this->request->get['filter_listing_id'])) {
            $url .= '&filter_listing_id=' . $this->request->get['filter_listing_id'];
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $url .= '&filter_card_type_id=' . urlencode(html_entity_decode($this->request->get['filter_card_type_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . $this->request->get['filter_year'];
        }

         if (isset($this->request->get['filter_manufacturer'])) {
             $url .= '&filter_manufacturer=' . urlencode(html_entity_decode($this->request->get['filter_manufacturer'], ENT_QUOTES, 'UTF-8'));
         }

        if (isset($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_language'])) {
            $url .= '&filter_language=' . $this->request->get['filter_language'];
        }

        if (isset($this->request->get['filter_ebay_item_id'])) {
            $url .= '&filter_ebay_item_id=' . urlencode(html_entity_decode($this->request->get['filter_ebay_item_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_publish_status'])) {
            $url .= '&filter_publish_status=' . $this->request->get['filter_publish_status'];
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

        $data['action'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . $url);

        $data['url_update_location'] = html_entity_decode($this->url->link('shopmanager/card/card_listing.updateLocation', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_check_ebay_health'] = html_entity_decode($this->url->link('shopmanager/card/card_listing.checkEbayHealth', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');

        $data['listings'] = [];

        $filter_data = [
            'filter_listing_id' => $filter_listing_id,
            'filter_set_name' => $filter_set_name,
            'filter_card_type_id' => $filter_card_type_id,
            'filter_year' => $filter_year,
            'filter_manufacturer' => $filter_manufacturer,
            'filter_location' => $filter_location,
            'filter_status' => $filter_status,
            'filter_language' => $filter_language,
            'filter_ebay_item_id' => $filter_ebay_item_id,
            'filter_publish_status' => $filter_publish_status,
            'filter_sync' => $filter_sync,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ];

        $this->load->model('shopmanager/card/card_listing');

        $listing_total = $this->model_shopmanager_card_card_listing->getTotalListings($filter_data);

        $results = $this->model_shopmanager_card_card_listing->getListings($filter_data);

        foreach ($results as $result) {
            // eBay status and thumb
            $ebay_status = 'grey'; // Default: not published
            $ebay_url = '';
            
            if (!empty($result['is_published'])) {
                $ebay_status = 'green'; // Published
                // Use the first available ebay_item_id for the URL
                $ebay_item_id = $result['ebay_item_id'];
                if (empty($ebay_item_id)) {
                    // If language 1 doesn't have it, get from descriptions
                    $descriptions = $this->model_shopmanager_card_card_listing->getDescriptions($result['listing_id']);
                    foreach ($descriptions as $desc) {
                        if (!empty($desc['ebay_item_id'])) {
                            $ebay_item_id = $desc['ebay_item_id'];
                            break;
                        }
                    }
                }
                if (!empty($ebay_item_id)) {
                    $ebay_url = 'https://www.ebay.ca/itm/' . $ebay_item_id;
                }
            }
            // TODO: Add red status if error tracking is implemented
            
            $ebay_thumb = 'catalog/marketplace/ebay_ca_' . $ebay_status . '.png';

            $data['listings'][] = [
                'listing_id' => $result['listing_id'],
                'set_name' => $result['set_name'],
                'card_type' => $result['card_type_name'] ?? $result['sport'] ?? 'N/A',
                'year' => $result['year'],
                'manufacturer' => $result['brand'] ?? '',
                'variation_count' => $result['variation_count'],
                'total_quantity' => $result['total_quantity'] ?? 0,
                'total_value' => number_format((float)($result['total_value'] ?? 0), 2),
                'total_batches' => (int)($result['total_batches'] ?? 0),
                'published_batches' => (int)($result['published_batches'] ?? 0),
                'status' => $result['status'],
                'status_text' => $result['status'] ? ($data['text_enabled'] ?? '') : ($data['text_disabled'] ?? ''),
                'date_added' => date(($data['date_format_short'] ?? ''), strtotime($result['date_added'])),
                'ebay_item_id' => $result['ebay_item_id'] ?? '',
                'is_published' => $result['is_published'],
                'ebay_status' => $ebay_status,
                'ebay_thumb' => $ebay_thumb,
                'ebay_url' => $ebay_url,
                'location' => $result['location'] ?? '',
                'first_image' => $result['first_image'] ?? '',
                'to_sync' => (int)($result['cards_to_sync'] ?? 0),
                'edit' => $this->url->link('shopmanager/card/card_listing.form', 'user_token=' . $this->session->data['user_token'] . '&listing_id=' . $result['listing_id'] . $url),
                'count_no_offer'      => $this->model_shopmanager_card_card_listing->getCardsWithoutOfferCount($result['listing_id']),
                'count_unpublished'   => $this->model_shopmanager_card_card_listing->getCardsUnpublishedWithOffer($result['listing_id']),
                'count_google_images' => $this->model_shopmanager_card_card_listing->getGoogleImageCount($result['listing_id']),
                'health_status'       => (int)($result['health_status'] ?? 0),
                'health_error'        => $result['health_error'] ?? '',
            ];
        }

        // Sorting URLs
        $url_sort = '';

        if (isset($this->request->get['filter_listing_id'])) {
            $url_sort .= '&filter_listing_id=' . $this->request->get['filter_listing_id'];
        }

        if (isset($this->request->get['filter_set_name'])) {
            $url_sort .= '&filter_set_name=' . urlencode(html_entity_decode($this->request->get['filter_set_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_card_type_id'])) {
            $url_sort .= '&filter_card_type_id=' . urlencode(html_entity_decode($this->request->get['filter_card_type_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url_sort .= '&filter_year=' . $this->request->get['filter_year'];
        }

         if (isset($this->request->get['filter_manufacturer'])) {
             $url_sort .= '&filter_manufacturer=' . urlencode(html_entity_decode($this->request->get['filter_manufacturer'], ENT_QUOTES, 'UTF-8'));
         }

        if (isset($this->request->get['filter_location'])) {
            $url_sort .= '&filter_location=' . urlencode(html_entity_decode($this->request->get['filter_location'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_status'])) {
            $url_sort .= '&filter_status=' . $this->request->get['filter_status'];
        }

        if (isset($this->request->get['filter_language'])) {
            $url_sort .= '&filter_language=' . $this->request->get['filter_language'];
        }

        if (isset($this->request->get['filter_ebay_item_id'])) {
            $url_sort .= '&filter_ebay_item_id=' . urlencode(html_entity_decode($this->request->get['filter_ebay_item_id'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_publish_status'])) {
            $url_sort .= '&filter_publish_status=' . $this->request->get['filter_publish_status'];
        }

        if (isset($this->request->get['page'])) {
            $url_sort .= '&page=' . $this->request->get['page'];
        }

        if (isset($this->request->get['limit'])) {
            $url_sort .= '&limit=' . $this->request->get['limit'];
        }

        $data['sort_listing_id'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.listing_id' . '&order=' . ($sort == 'l.listing_id' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_set_name'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.set_name' . '&order=' . ($sort == 'l.set_name' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_card_type'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=ct.name' . '&order=' . ($sort == 'ct.name' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_year'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.year' . '&order=' . ($sort == 'l.year' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_manufacturer'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.brand' . '&order=' . ($sort == 'l.brand' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_variation'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=variation_count' . '&order=' . ($sort == 'variation_count' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_quantity'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=total_quantity' . '&order=' . ($sort == 'total_quantity' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_value']    = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=total_value'    . '&order=' . ($sort == 'total_value'    && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_location'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.location' . '&order=' . ($sort == 'l.location' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_status'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.status' . '&order=' . ($sort == 'l.status' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);
        $data['sort_date_added'] = $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . '&sort=l.date_added' . '&order=' . ($sort == 'l.date_added' && $order == 'ASC' ? 'DESC' : 'ASC') . $url_sort);

        $data['sort'] = $sort;
        $data['order'] = $order;

        // Pagination
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $listing_total,
            'page' => $page,
            'limit' => $limit,
            'url' => $this->url->link('shopmanager/card/card_listing.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
        ]);

        $data['results'] = sprintf(($data['text_pagination'] ?? ''), ($listing_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($listing_total - $limit)) ? $listing_total : ((($page - 1) * $limit) + $limit), $listing_total, ceil($listing_total / $limit));

        $data['selected'] = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : [];

        // Pass user_token to template for AJAX calls
        $data['user_token'] = $this->session->data['user_token'];

        return $this->load->view('shopmanager/card/card_listing_list', $data);
    }

    /**
     * Form
     *
     * @return void
     */
    public function form(): void {
        $data = $this->load->language('shopmanager/card/card_listing');

        $this->document->setTitle(($data['heading_title'] ?? ''));

        $data['text_form'] = !isset($this->request->get['listing_id']) ? ($data['text_add'] ?? '') : ($data['text_edit'] ?? '');
        $data['text_select'] = ($data['text_select'] ?? '');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => ($data['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => ($data['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/card/card_listing', 'user_token=' . $this->session->data['user_token'] . $url)
        ];

        $data['save'] = $this->url->link('shopmanager/card/card_listing.save', 'user_token=' . $this->session->data['user_token']);
        $data['back'] = $this->url->link('shopmanager/card/card_listing', 'user_token=' . $this->session->data['user_token'] . $url);

        $listing_info = [];
        
        if (isset($this->request->get['listing_id'])) {
            $this->load->model('shopmanager/card/card_listing');

            $listing_info = $this->model_shopmanager_card_card_listing->getListing((int)$this->request->get['listing_id']);
        }

        $this->load->model('shopmanager/card/card_manufacturer');

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['listing_id'])) {
            $data['listing_id'] = (int)$this->request->get['listing_id'];
        } else {
            $data['listing_id'] = 0;
        }

        if (!empty($listing_info)) {
            $data['set_name'] = $listing_info['set_name'];
            $data['subset'] = $listing_info['subset'] ?? '';
            $data['sport'] = $listing_info['sport'] ?? '';
            $data['card_type_id'] = $listing_info['card_type_id'] ?? 0;
            $data['year'] = $listing_info['year'];
            $data['manufacturer'] = $listing_info['brand'] ?? '';
            $data['location'] = $listing_info['location'] ?? '';
            $data['variation_count'] = $listing_info['variation_count'] ?? 0;
            $data['total_quantity'] = $listing_info['total_quantity'] ?? 0;
            $data['status'] = $listing_info['status'];
            $data['ebay_category_id'] = $listing_info['ebay_category_id'] ?? '';
            
            // Load first 25 cards via model (paginated) — AJAX fetches the rest
            $this->load->model('shopmanager/card/card');
            $cards_raw = $this->model_shopmanager_card_card->getCards([
                'filter_listing_id' => $data['listing_id'],
                'sort'              => 'c.card_number',
                'order'             => 'ASC',
                'start'             => 0,
                'limit'             => 25,
            ]);
            $data['cards_total'] = $data['variation_count']; // already computed by getListing()
            $data['cards'] = [];
            foreach ($cards_raw as $card) {
                $images = $this->model_shopmanager_card_card->getCardImageUrls((int)$card['card_id']);
                $data['cards'][] = [
                    'card_id'     => (int)$card['card_id'],
                    'sku'         => $card['sku']         ?? '',
                    'title'       => $card['title']       ?? '',
                    'description' => $card['description'] ?? '',
                    'player_name' => $card['player_name'] ?? '',
                    'card_number' => $card['card_number'] ?? '',
                    'team_name'   => $card['team_name']   ?? '',
                    'year'        => $card['year']        ?? '',
                    'brand'       => $card['brand']       ?? '',
                    'condition'   => $card['condition']   ?? '',
                    'price'       => $card['price']       ?? '0.00',
                    'raw_price'        => isset($card['raw_price']) && $card['raw_price'] !== null ? (float)$card['raw_price'] : null,
                    'raw_price_is_ref' => (bool)($card['raw_price_is_ref'] ?? false),
                    'quantity'    => (int)($card['quantity']   ?? 1),
                    'merge'       => (int)($card['merge']      ?? 0),
                    'sort_order'  => (int)($card['sort_order'] ?? 0),
                    'batch_id'    => (int)($card['batch_id'] ?? 0),
                    'batch_name'  => (int)($card['batch_name'] ?? 0),
                    'images'      => array_slice($images, 0, 2),
                ];
            }
            
            // Load descriptions — batch-based (batch_number=0 global, 1+ per eBay batch)
            $data['card_listing_description'] = [];
            $data['batch_descriptions'] = [];
            if ($this->model_shopmanager_card_card_listing) {
                $descriptions = $this->model_shopmanager_card_card_listing->getDescriptions($data['listing_id']);
                foreach ($descriptions as $lang_id => $desc) {
                    $data['card_listing_description'][$lang_id] = $desc;
                }
                // All batches (keyed by batch_number: 0=global, 1+=per batch)
                $data['batch_descriptions'] = $this->model_shopmanager_card_card_listing->getDescriptions($data['listing_id']);
            }
            
            // Load specifics
            $data['specifics'] = $listing_info['specifics'] ?? [];
            
        } else {
            $data['set_name'] = '';
            $data['subset'] = '';
            $data['sport'] = '';
            $data['card_type_id'] = 0;
            $data['year'] = '';
            $data['manufacturer'] = '';
            $data['location'] = '';
            $data['variation_count'] = 0;
            $data['total_quantity'] = 0;
            $data['status'] = 1;
            $data['ebay_category_id'] = '';
            $data['cards'] = [];
            $data['cards_total'] = 0;
            $data['card_listing_description'] = [];
            $data['batch_descriptions'] = [];
            $data['specifics'] = [];
            $data['lot_info'] = [
                'lot_ebay_item_id'    => '',
                'lot_status'          => 0,
                'lot_price'           => null,
                'lot_date_published'  => null,
                'lot_weight'          => '0',
                'lot_weight_class_id' => 5,
                'lot_length'          => '0',
                'lot_width'           => '0',
                'lot_height'          => '0',
                'lot_length_class_id' => 3,
            ];
            $data['lot_calculated'] = [
                'card_count'       => 0,
                'total_qty'        => 0,
                'calculated_price' => 0,
                'floored_count'    => 0,
            ];
        }
        
        // Load manufacturers for dropdown
        $data['manufacturers'] = array_column($this->model_shopmanager_card_card_manufacturer->getManufacturers(['filter_status' => 1]), 'name');
        
        // Load card types for dropdown
        $this->load->model('shopmanager/card/card_type');
        $data['card_types'] = $this->model_shopmanager_card_card_type->getCardTypes();

        // Weight & length class options for lot shipping dropdowns
        $data['weight_classes'] = [
            ['weight_class_id' => 5, 'title' => 'lb'],
            ['weight_class_id' => 6, 'title' => 'oz'],
            ['weight_class_id' => 1, 'title' => 'kg'],
            ['weight_class_id' => 2, 'title' => 'g'],
        ];
        $data['length_classes'] = [
            ['length_class_id' => 3, 'title' => 'in'],
            ['length_class_id' => 1, 'title' => 'cm'],
            ['length_class_id' => 2, 'title' => 'mm'],
        ];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        // URLs (must be set BEFORE rendering partial tab views)
        $data['url_import_csv']      = html_entity_decode($this->url->link('shopmanager/card/card_listing.importCsv',      'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_confirm_import']  = html_entity_decode($this->url->link('shopmanager/card/card_listing.confirmImport',  'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_regenerate_cards']= html_entity_decode($this->url->link('shopmanager/card/card_listing.regenerateCards','user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_merge_variants']   = html_entity_decode($this->url->link('shopmanager/card/card_listing.mergeVariants',  'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_process_merge_groups'] = html_entity_decode($this->url->link('shopmanager/card/card_listing.processMergeGroups', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_regen_preview']        = html_entity_decode($this->url->link('shopmanager/card/card_listing.getRegenPreview',  'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_migrate_images']         = html_entity_decode($this->url->link('shopmanager/card/card_listing.migrateImages',       'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_sync_offers']             = html_entity_decode($this->url->link('shopmanager/card/card_listing.syncOffers',          'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_republish_offers']        = html_entity_decode($this->url->link('shopmanager/card/card_listing.republishOffers',     'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_save_batch_specifics']    = html_entity_decode($this->url->link('shopmanager/card/card_listing.saveBatchSpecifics',  'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_get_cards']               = html_entity_decode($this->url->link('shopmanager/card/card.getCards',                   'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_fetch_market_price']      = html_entity_decode($this->url->link('shopmanager/ebay.getMarketPrices', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_add_batch']               = html_entity_decode($this->url->link('shopmanager/card/card_listing.addBatch',            'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_end_batch']               = html_entity_decode($this->url->link('shopmanager/card/card_listing.endBatch',            'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        // Lot eBay URLs
        $data['url_publish_lot']             = html_entity_decode($this->url->link('shopmanager/card/card_listing.publishLot',          'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_end_lot']                 = html_entity_decode($this->url->link('shopmanager/card/card_listing.endLot',              'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_lot_preview']             = html_entity_decode($this->url->link('shopmanager/card/card_listing.getLotPreview',       'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_save_lot_description']    = html_entity_decode($this->url->link('shopmanager/card/card_listing.saveLotDescription',  'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_regen_lot_description']   = html_entity_decode($this->url->link('shopmanager/card/card_listing.regenLotDescription', 'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_generate_lot_images']     = html_entity_decode($this->url->link('shopmanager/card/card_listing.generateLotImages',   'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['lot_image_base_url']          = HTTP_CATALOG . 'image/';

       
        // Tab cards rendered before ebay_batches (doesn't need it)
        $data['tab_cards_html'] = $this->load->view('shopmanager/card/card_listing_tab_cards', $data);

        // Count Google images still to migrate
        $data['google_image_count'] = 0;
        $data['cards_without_offer_count']    = 0;
        $data['cards_unpublished_count']      = 0;
        $data['ebay_batches']                 = [];
        if ($data['listing_id'] > 0) {
            $this->load->model('shopmanager/card/card_listing');
            $data['google_image_count']        = $this->model_shopmanager_card_card_listing->getGoogleImageCount($data['listing_id']);
            $data['cards_without_offer_count'] = $this->model_shopmanager_card_card_listing->getCardsWithoutOfferCount($data['listing_id']);
            $data['cards_unpublished_count']   = $this->model_shopmanager_card_card_listing->getCardsUnpublishedWithOffer($data['listing_id']);
            $data['ebay_batches']              = $this->model_shopmanager_card_card_listing->getBatches($data['listing_id']);
            $data['batch_totals']              = $this->model_shopmanager_card_card_listing->getBatchTotals($data['listing_id']);

            // Lot eBay info — backfill raw_price avant le calcul du total
            $this->model_shopmanager_card_card->backfillRawPrices($data['listing_id']);
            $data['lot_info']       = $this->model_shopmanager_card_card_listing->getLotInfo($data['listing_id']);
            $data['lot_calculated'] = $this->model_shopmanager_card_card_listing->getLotPriceSummary($data['listing_id']);

            // Images mosaiques -- si vides, le popup front-end se charge de les generer
            $data['lot_images']       = $this->model_shopmanager_card_card_listing->getLotImages($data['listing_id']);
            $data['lot_images_empty'] = empty($data['lot_images']);

            // Auto-génération + sauvegarde systématique du titre et de la description du lot
            $regenLot = $this->model_shopmanager_card_card_listing->regenAndSaveLotDescription($data['listing_id']);
            $data['lot_title']       = $regenLot['title'];
            $data['lot_description'] = $regenLot['description'];
            // Auto-regenerate descriptions each time the form is opened
            if (!empty($data['ebay_batches'])) {
              
                
                $data['batch_descriptions'] = $this->model_shopmanager_card_card_listing->getDescriptions($data['listing_id']);
            }
        }

        // Tab descriptions rendered AFTER ebay_batches is populated
        $data['tab_descriptions_html'] = $this->load->view('shopmanager/card/card_listing_tab_descriptions', $data);
        // Warning: listing has >250 cards but batches not assigned yet
        $data['needs_batch_warning'] = (
            $data['listing_id'] > 0 &&
            ($data['variation_count'] ?? 0) > 250 &&
            empty($data['ebay_batches'])
        );
        $data['url_assign_batches']    = html_entity_decode($this->url->link('shopmanager/card/card_listing.assignBatches',    'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');
        $data['url_update_location']   = html_entity_decode($this->url->link('shopmanager/card/card_listing.updateLocation',   'user_token=' . $this->session->data['user_token']), ENT_QUOTES, 'UTF-8');

        // Batch i18n — computed key for warning title
        $data['text_needs_batch_warning_title']   = sprintf(($data['text_needs_batch_warning_title'] ?? ''), $data['variation_count'] ?? 0);

        // Batch i18n JSON — injected into twig as JS globals for card_listing_form.js
        $data['batch_i18n'] = json_encode([
            'calculating'       => ($data['button_calculating'] ?? ''),
            'ajax_error'        => ($data['text_batch_ajax_error'] ?? ''),
            'no_batches'        => ($data['text_no_batches_empty'] ?? ''),
            'status_draft'      => ($data['text_batch_status_draft'] ?? ''),
            'status_published'  => ($data['text_batch_status_published'] ?? ''),
            'status_ended'      => ($data['text_batch_status_ended'] ?? ''),
            'col_published'     => ($data['text_batch_status_published'] ?? ''),
            'recalculate'       => ($data['button_recalculate_batches'] ?? ''),
            'column_batch'      => ($data['column_batch'] ?? ''),
            'column_variations' => ($data['column_variations'] ?? ''),
            'grand_total'       => ($data['text_grand_total'] ?? ''),
        ], JSON_UNESCAPED_UNICODE);

        $this->response->setOutput($this->load->view('shopmanager/card/card_listing_form', $data));
    }

    /**
     * Save (update) a card listing from the admin form.
     */
    public function save(): void {
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $this->session->data['error'] = ($lang['error_permission'] ?? '');
            $this->response->redirect($this->url->link('shopmanager/card/card_listing', 'user_token=' . $this->session->data['user_token']));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);

        if ($listing_id) {
            $this->load->model('shopmanager/card/card_listing');

            $this->model_shopmanager_card_card_listing->updateListing($listing_id, [
                'set_name'         => $this->request->post['set_name']         ?? '',
                'subset'           => $this->request->post['subset']          ?? '',
                'card_type_id'     => (int)($this->request->post['card_type_id']   ?? 0),
                'year'             => (int)($this->request->post['year']           ?? 0),
                'brand'            => $this->request->post['manufacturer']    ?? '',
                'location'         => $this->request->post['location']         ?? '',
                'status'           => (int)($this->request->post['status']         ?? 0),
                'ebay_category_id' => $this->request->post['ebay_category_id'] ?? '',
            ]);

            $this->session->data['success'] = ($lang['text_success'] ?? '');
        }

        $this->response->redirect($this->url->link(
            'shopmanager/card/card_listing.form',
            'user_token=' . $this->session->data['user_token'] . '&listing_id=' . $listing_id
        ));
    }

    /**
     * Autocomplete
     *
     * @return void
     */
    public function autocomplete(): void {
        $json = [];

            if (!$this->user->hasPermission('access', 'shopmanager/card/card_listing')) {
                $json = [];
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

        $this->load->model('shopmanager/card/card_listing');

        // Determine which field to autocomplete
        if (isset($this->request->post['filter_set_name'])) {
            $filter_value = $this->request->post['filter_set_name'];
            $results = $this->model_shopmanager_card_card_listing->getDistinctSetNames($filter_value);
            
            foreach ($results as $result) {
                if (!empty($result['set_name'])) {
                    $json[] = [
                        'label' => $result['set_name'],
                        'value' => $result['set_name']
                    ];
                }
            }
        } elseif (isset($this->request->post['filter_card_type_id'])) {
            $filter_value = $this->request->post['filter_card_type_id'];
            $results = $this->model_shopmanager_card_card_listing->getDistinctSports($filter_value);
            
            foreach ($results as $result) {
                if (!empty($result['sport'])) {
                    $json[] = [
                        'label' => $result['sport'],
                        'value' => $result['sport']
                    ];
                }
            }
        } elseif (isset($this->request->post['filter_manufacturer'])) {
            $filter_value = $this->request->post['filter_manufacturer'];
            $results = $this->model_shopmanager_card_card_listing->getDistinctBrands($filter_value);
            
            foreach ($results as $result) {
                if (!empty($result['brand'])) {
                    $json[] = [
                        'label' => $result['brand'],
                        'value' => $result['brand']
                    ];
                }
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Delete
     *
     * @return void
     */
    public function delete(): void {
        $this->load->language('shopmanager/card/card_listing');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (isset($this->request->post['selected'])) {
            $this->load->model('shopmanager/card/card_listing');

            foreach ($this->request->post['selected'] as $listing_id) {
                $result = $this->model_shopmanager_card_card_listing->deleteListing((int)$listing_id);
                if (!$result['ok']) {
                    $json['error'] = $result['error'] ?? 'Delete failed for listing ' . (int)$listing_id;
                }
            }

            if (empty($json['error'])) {
                $json['success'] = ($lang['text_success'] ?? '');
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function publishToEbay(): void {
        $json = [];

          $this->load->language('shopmanager/card/card_listing');


        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/marketplace');

        if (isset($this->request->post['listing_id'])) {
            $listing_id = (int)$this->request->post['listing_id'];

            // Récupérer le compte marketplace EN (language_id=1)
            $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                'customer_id'        => 10,
                'filter_language_id' => 1
            ]);

            if (empty($marketplace_account) || !isset($marketplace_account['site_setting']) || !isset($marketplace_account['marketplace_account_id'])) {
                $json['error'] = ($lang['error_marketplace_account'] ?? '') ?: 'Compte marketplace introuvable (lang 1).';
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $result = $this->model_shopmanager_ebay->addCardListing(
                $listing_id,
                $marketplace_account['site_setting'],
                $marketplace_account['marketplace_account_id'],
                true
            );

            $errors       = $result['errors'] ?? [];
            $publishedItems = [];

            if (!empty($result['batches'])) {
                foreach ($result['batches'] as $batchNum => $batchResult) {
                    if (!empty($batchResult['ebay_item_id'])) {
                        $publishedItems[] = [
                            'batch_name'     => $batchNum,
                            'ebay_item_id' => $batchResult['ebay_item_id'],
                            'status'       => !empty($batchResult['skipped']) ? 'already_published' : 'published',
                        ];
                    }
                }
            }

            $newlyPublished = array_filter($publishedItems, fn($i) => $i['status'] === 'published');

            // Construire la réponse
            if (!empty($publishedItems)) {
                $json['success']           = true;
                $json['message']           = count($newlyPublished) . ' batch(es) publié(s) sur eBay avec succès!';
                $json['published_items']   = $publishedItems;
                $json['marketplace_item_id'] = $publishedItems[0]['ebay_item_id'] ?? '';
            } else {
                $json['error'] = 'Aucun listing n\'a pu être publié. ' . implode(' | ', $errors);
            }

            if (!empty($errors)) {
                $json['warnings'] = $errors;
            }
        } else {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Publish multiple card listings to eBay at once
     */
    public function publishMultiple(): void {
        $json = [];

          $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->request->post['listing_ids']) || !is_array($this->request->post['listing_ids'])) {
            $json['error'] = ($lang['error_no_listings_selected'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/marketplace');

        $listing_ids = array_map('intval', $this->request->post['listing_ids']);
        $results = [];
        $success_count = 0;
        $error_count = 0;

        foreach ($listing_ids as $listing_id) {
            $listing_result = ['listing_id' => $listing_id];

            // Récupérer les données du listing
            // Récupérer le compte marketplace EN (language_id=1)
            $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                'customer_id'        => 10,
                'filter_language_id' => 1
            ]);

            if (empty($marketplace_account)) {
                $listing_result['success'] = false;
                $listing_result['error']   = 'Compte marketplace introuvable (lang 1).';
                $results[] = $listing_result;
                $error_count++;
                continue;
            }

            $published_items = [];
            $listing_errors  = [];

            // Publish — model gère le skip des batches déjà publiés en interne
            $site_setting           = $marketplace_account['site_setting'];
            $marketplace_account_id = $marketplace_account['marketplace_account_id'];

            $result = $this->model_shopmanager_ebay->addCardListing($listing_id, $site_setting, $marketplace_account_id, true);

            $listing_errors = $result['errors'] ?? [];

            if (!empty($result['batches'])) {
                foreach ($result['batches'] as $batchNum => $batchResult) {
                    if (!empty($batchResult['ebay_item_id'])) {
                        $published_items[] = [
                            'batch_name'     => $batchNum,
                            'ebay_item_id' => $batchResult['ebay_item_id'],
                            'status'       => !empty($batchResult['skipped']) ? 'already_published' : 'published',
                        ];
                    }
                }
            }

            if (count($published_items) > 0) {
                $listing_result['success'] = true;
                $listing_result['published_items'] = $published_items;
                $success_count++;
            } else {
                $listing_result['success'] = false;
                $listing_result['error'] = implode(', ', array_map(
                    fn($e) => is_array($e) ? ($e['message'] ?? json_encode($e)) : (string)$e,
                    $listing_errors
                ));
                $error_count++;
            }

            $results[] = $listing_result;
        }

        $json['success'] = true;
        $json['success_count'] = $success_count;
        $json['error_count'] = $error_count;
        $json['results'] = $results;

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * End (terminate) multiple eBay listings at once
     */
    /**
     * AJAX: (Re)assign eBay batches for a listing (by card-number range).
     * Returns the batch summary so the UI can refresh.
     */
    public function assignBatches(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');

        // ── Actually assign batch_name to cards and rebuild oc_card_listing_batch ──
        $this->model_shopmanager_card_card_listing->recalculateBatches($listing_id);

        // ── Regenerate descriptions (titles, descriptions, specifics per batch) ──
        $this->model_shopmanager_card_card_listing->regenerateDescriptions($listing_id);

        $batches = $this->model_shopmanager_card_card_listing->getBatchesWithCardIds($listing_id);
        $savedBatches = $this->model_shopmanager_card_card_listing->getBatches($listing_id);

  

        // ────────────────────────────────────────────────────────────────────────

        // Build warnings
        $warnings    = [];
        $totalCards  = array_sum(array_column($batches, 'count'));
        $totalBatches = count($batches);


        if ($totalBatches > 1) {
            $warnings[] = $totalBatches . ' listings eBay seront utilisés. Les cartes sont réparties par plage de numéro (B1 = #1-250, B2 = #251-500…, B99 = spéciaux).';
        }

        $batchDescriptions = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id);
        $batchTotals       = $this->model_shopmanager_card_card_listing->getBatchTotals($listing_id);

        $json['success']           = true;
        $json['batches']           = $savedBatches;
        $json['batch_descriptions'] = $batchDescriptions;
        $json['batch_totals']       = $batchTotals;
        $json['summary']           = $batches;
      
        $json['warnings']          = $warnings;
        $json['total_cards']       = $totalCards;

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Save per-batch specifics into oc_card_listing_description.specifics
     * POST: listing_id, batch_name, specifics[] = [{name, value}, ...]
     */
    public function saveBatchSpecifics(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        $batch_name   = (int)($this->request->post['batch_name']   ?? 0);
        $rows       = $this->request->post['specifics'] ?? [];

        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $specifics = [];
        foreach ($rows as $row) {
            $name  = trim($row['name']  ?? '');
            $value = trim($row['value'] ?? '');
            if ($name === '') continue;
            // Comma-separated → array of values
            if (strpos($value, ',') !== false) {
                $parts = array_values(array_filter(array_map('trim', explode(',', $value))));
                $specifics[$name] = $parts;
            } else {
                $specifics[$name] = $value;
            }
        }

        $this->load->model('shopmanager/card/card_listing');

        // Resolve batch_name (logical number from POST) → batch_id FK
        $batchRow = $this->model_shopmanager_card_card_listing->getEbayBatch($listing_id, $batch_name);
        $batch_id = (int)($batchRow['batch_id'] ?? 0);

        $this->model_shopmanager_card_card_listing->updateBatchSpecifics($listing_id, $batch_id, $specifics);

        $json['success'] = true;
        $json['count']   = count($specifics);
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    public function endMultiple(): void {
      

        $json = [];

        $this->load->language('shopmanager/card/card_listing');



        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->request->post['listing_ids']) || !is_array($this->request->post['listing_ids'])) {
            $json['error'] = ($lang['error_no_listings_selected'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/marketplace');

        $listing_ids = array_map('intval', $this->request->post['listing_ids']);
        $results = [];
        $success_count = 0;
        $error_count = 0;

        foreach ($listing_ids as $listing_id) {
            $listing_result = ['listing_id' => $listing_id];

            // Récupérer uniquement les descriptions publiées (avec ebay_item_id)
            $descriptions = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id, 1, true);

            if (empty($descriptions)) {
                $listing_result['success'] = false;
                $listing_result['error'] = ($lang['error_no_published_listings'] ?? '');
                $results[] = $listing_result;
                $error_count++;
                continue;
            }

            $ended_count = 0;
            $end_errors = [];

            // Boucle sur les descriptions avec ebay_item_id (déjà filtrées)
            foreach ($descriptions as $description) {

                // Récupérer le marketplace account pour cette langue
                try {
                    $mkt = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                        'customer_id' => 10,
                        'filter_language_id' => $description['language_id']
                    ]);
                } catch (\Exception $e) {
                    $end_errors[] = sprintf(($lang['error_marketplace_not_found'] ?? ''), $description['language_id']) . ' (Exception: ' . $e->getMessage() . ')';
                    continue;
                }

                if (empty($mkt)) {
                    $end_errors[] = sprintf(($lang['error_marketplace_not_found'] ?? ''), 1);
                    continue;
                }

                $site_setting = $mkt['site_setting'];

                // Terminer le listing eBay
                try {
                    $result = $this->model_shopmanager_ebay->endCardListing($description['ebay_item_id'], $mkt['marketplace_account_id'], $site_setting, $listing_id, $description['language_id']);
                } catch (\Exception $e) {
                    $result = ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
                }

                $alreadyGone = !$result['success'] && preg_match('/not found|already ended|invalid item|no longer|doesn.t exist|Item.*ended|ended or/i', $result['error'] ?? '');
                if ($result['success'] || $alreadyGone) {
                    // Supprimer l'ebay_item_id de la base de données
                    try {
                        $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, null, $description['language_id'], (int)$description['batch_id']);
                        $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, (int)$description['batch_id'], 2, null, $description['language_id']);
                        $ended_count++;
                        if ($alreadyGone) {
                            $end_errors[] = sprintf(($lang['error_end_failed'] ?? ''), $description['language_id'], 'already gone on eBay — DB cleared.');
                        }
                    } catch (\Exception $e) {
                        $end_errors[] = sprintf(($lang['error_end_failed'] ?? ''), $description['language_id'], 'Database error: ' . $e->getMessage());
                    }
                } else {
                    $end_errors[] = sprintf(($lang['error_end_failed'] ?? ''), $description['language_id'], ($result['error'] ?? 'Unknown'));
                }
            }

            if ($ended_count > 0) {
                $listing_result['success'] = true;
                $listing_result['ended_count'] = $ended_count;
                $success_count++;
            } else {
                $listing_result['success'] = false;
                $listing_result['error'] = implode(', ', $end_errors);
                $error_count++;
            }

            $results[] = $listing_result;
        }

        $json['success'] = true;
        $json['success_count'] = $success_count;
        $json['error_count'] = $error_count;
        $json['results'] = $results;

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

  

    /**
     * Update card price via AJAX
     * Used for inline editing in card listing form
     *
     * @return void
     */
    public function updatePrice(): void {
        $this->load->language('shopmanager/card/card_listing');

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
     * Update listing location via AJAX (inline save in form)
     */
    public function updateLocation(): void {
        $this->load->language('shopmanager/card/card_listing');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['success'] = false;
            $json['error']   = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        $location   = strtoupper(trim($this->request->post['location'] ?? ''));

        if (!$listing_id) {
            $json['success'] = false;
            $json['error']   = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');
        $this->model_shopmanager_card_card_listing->updateLocation($listing_id, $location);

        $json['success']  = true;
        $json['location'] = $location;
        $json['message']  = 'Location updated successfully';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Update card sort order via AJAX
     * Used for drag & drop reordering in card listing form
     *
     * @return void
     */
    public function updateSortOrder(): void {
        $this->load->language('shopmanager/card/card_listing');

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
        if (!isset($this->request->post['card_ids']) || !is_array($this->request->post['card_ids'])) {
            $json['success'] = false;
            $json['error'] = 'Missing or invalid card_ids parameter';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $card_ids = $this->request->post['card_ids'];

        // Update sort_order for each card
        try {
            $sort_order = 0;
            foreach ($card_ids as $card_id) {
                $this->db->query("UPDATE `" . DB_PREFIX . "card` 
                    SET `sort_order` = " . (int)$sort_order . " 
                    WHERE `card_id` = " . (int)$card_id);
                $sort_order++;
            }

            $json['success'] = true;
            $json['message'] = 'Sort order updated successfully';
            $json['count'] = count($card_ids);
            
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = 'Database error: ' . $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Import CSV - add cards to existing listing (like variant_listing_creator)
     */
    public function importCsv(): void {
        $this->load->language('shopmanager/card/card_listing');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        if (!isset($this->request->files['file']['tmp_name']) || empty($this->request->files['file']['tmp_name'])) {
            $json['error'] = 'No file uploaded';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $this->load->model('shopmanager/card/card_listing');

        // Parse CSV
        $parse_result = $this->model_shopmanager_card_card_listing->parseCSV($this->request->files['file']['tmp_name']);
        if (!empty($parse_result['error'])) {
            $json['error'] = $parse_result['error'];
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $cards = $parse_result['data'];

        // Convert USD -> CAD
        foreach ($cards as &$card) {
            $sale_price = floatval($card['sale_price'] ?? 0);
            if ($sale_price > 0.99) {
                $cad = $this->currency->convert($sale_price, 'USD', 'CAD');
                $card['sale_price'] = number_format($cad, 2, '.', '');
            } elseif ($sale_price <= 0) {
                $card['sale_price'] = '0.99';
            }
        }
        unset($card);

        // Group cards (reuse smartGroupCards logic)
        $groups = $this->model_shopmanager_card_card_listing->smartGroupCards($cards);

        // Return preview HTML for JS to display
        $preview_html = $this->buildImportPreviewHtml($groups);

        $json['success'] = true;
        $json['total_cards'] = count($cards);
        $json['total_groups'] = count($groups);
        $json['groups'] = $groups;
        $json['html'] = $preview_html;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Build a preview HTML table from groups (simplified version)
     */
    private function buildImportPreviewHtml(array $groups): string {
        $html = '';
        foreach ($groups as $g_idx => $group) {
            $color_map = ['gray' => '#6c757d', 'blue' => '#0d6efd', 'orange' => '#fd7e14', 'gold' => '#ffc107'];
            $cat = $group['price_category'] ?? 'gray';
            $color = $color_map[$cat] ?? '#6c757d';
            $card_count = count($group['cards']);
            $total_qty = $group['total_quantity'] ?? $card_count;
            $html .= '<div class="card mb-2" style="border-left: 4px solid ' . $color . ';">';
            $html .= '<div class="card-header py-1" style="background: #f8f9fa;">';
            $html .= '<strong>' . htmlspecialchars($group['set'] ?? 'No SET') . '</strong>';
            $html .= ' &mdash; <span class="badge" style="background:' . $color . '">' . $card_count . ' cards / qty ' . $total_qty . '</span>';
            $html .= ' <small class="text-muted">$' . number_format($group['min_price'] ?? 0, 2) . ' - $' . number_format($group['max_price'] ?? 0, 2) . '</small>';
            $html .= '</div>';
            $html .= '<div class="card-body py-1">';
            $html .= '<ul style="column-count:3; column-gap:10px; font-size:0.85em; margin:0;">';
            foreach ($group['cards'] as $card) {
                $info = '';
                if (!empty($card['card_number'])) $info .= '#' . $card['card_number'] . ' ';
                if (!empty($card['player'])) $info .= $card['player'];
                else $info .= $card['title'] ?? 'Card';
                $qty = (int)($card['quantity'] ?? 1);
                $price = number_format(floatval($card['sale_price'] ?? 0), 2);
                $html .= '<li>' . htmlspecialchars($info) . ' <small class="text-muted">$' . $price . ($qty > 1 ? ' x' . $qty : '') . '</small></li>';
            }
            $html .= '</ul>';
            $html .= '</div></div>';
        }
        return $html;
    }

    /**
     * Confirm import - save CSV cards to existing listing
     */
    public function confirmImport(): void {
        $this->load->language('shopmanager/card/card_listing');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $listing_id = (int)($input['listing_id'] ?? 0);
        $groups = $input['groups'] ?? [];

        if (!$listing_id || empty($groups)) {
            $json['error'] = 'Missing listing_id or groups';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $this->load->model('shopmanager/card/card_listing');

        $added = 0;
        foreach ($groups as $group) {
            foreach ($group['cards'] as $card) {
                // Normalize fields for saveVariation
                $variation = [
                    'title'       => $card['title'] ?? ($card['player'] ?? 'Card'),
                    'card_number' => $card['card_number'] ?? '',
                    'player'      => $card['player'] ?? '',
                    'team'        => $card['team'] ?? '',
                    'year'        => $card['year'] ?? '',
                    'brand'       => $card['brand'] ?? '',
                    'condition'   => $card['condition'] ?? 'Near Mint or Better',
                    'price'       => floatval($card['sale_price'] ?? 0),
                    'quantity'    => (int)($card['quantity'] ?? 1),
                    'merge'       => (floatval($card['sale_price'] ?? 0) < 10) ? 1 : 0,
                    'front_image' => $card['front_image'] ?? '',
                    'back_image'  => $card['back_image'] ?? '',
                    'all_images'  => $card['all_images'] ?? [],
                ];
                $this->model_shopmanager_card_card_listing->addVariationToListing($listing_id, $variation);
                $added++;
            }
        }

       
        $json['success'] = true;
        $json['added'] = $added;
        $json['message'] = $added . ' cards added to listing';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Regenerate - merge duplicates, consolidate images, rebuild descriptions
     */
    public function regenerateCards(): void {
        $this->load->language('shopmanager/card/card_listing');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $listing_id = (int)($input['listing_id'] ?? $this->request->post['listing_id'] ?? 0);

        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $this->load->model('shopmanager/card/card_listing');

        // Step 1: Merge duplicates (passe 1 exact + passe 2 variantes lettres)
        $result = $this->model_shopmanager_card_card_listing->mergeAndDeduplicateCards($listing_id);

        // Step 2: Recalculate batch totals / stats
        $this->model_shopmanager_card_card_listing->recalculateBatches($listing_id);

        // Step 3: Regenerate eBay titles + descriptions + marquer to_sync=1
        $this->model_shopmanager_card_card_listing->regenerateDescriptions($listing_id);

        $json['success']              = true;
        $json['merged']               = $result['merged'];
        $json['deleted']              = $result['deleted'];
        $json['images_consolidated']  = $result['images_consolidated'];
        $json['letter_merged']        = $result['letter_merged']        ?? 0;
        $json['letter_warned_price']  = $result['letter_warned_price']  ?? 0;
        $json['letter_warned_format'] = $result['letter_warned_format'] ?? 0;
        $json['ebay_pending_deletes'] = $result['ebay_pending_deletes'] ?? 0;
        $json['ebay_live_warnings']   = $result['ebay_live_warnings']   ?? [];
        $json['message'] = 'Regenerated: '
            . $result['merged']              . ' merged, '
            . $result['deleted']             . ' deleted, '
            . $result['images_consolidated'] . ' images, '
            . ($result['letter_merged'] ?? 0) . ' letter-variants merged';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX : fusion manuelle d'un groupe de variantes (sans seuils de prix).
     * POST body : { listing_id: int, card_ids: int[] }
     * Retourne  : { success, merged_card_id, message }
     */
    public function mergeVariants(): void {
        $this->load->language('shopmanager/card/card_listing');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input      = json_decode(file_get_contents('php://input'), true);
        $listing_id = (int)($input['listing_id'] ?? 0);
        $card_ids   = array_map('intval', $input['card_ids'] ?? []);

        if (!$listing_id || count($card_ids) < 2) {
            $json['error'] = 'listing_id and at least 2 card_ids required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');

        $survivor_id = $this->model_shopmanager_card_card_listing->mergeCardGroup($listing_id, $card_ids);

        $this->model_shopmanager_card_card_listing->recalculateBatches($listing_id);
        $this->model_shopmanager_card_card_listing->regenerateDescriptions($listing_id);

        $json['success']        = true;
        $json['merged_card_id'] = $survivor_id;
        $json['message']        = 'Merged ' . count($card_ids) . ' variants into card_id ' . $survivor_id;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX: process a selected list of merge groups.
     * Receives JSON { listing_id: int, groups: [[card_id, ...], ...] }
     * Calls mergeCardGroup() for each group, then recalculateBatches + regenerateDescriptions.
     */
    public function processMergeGroups(): void {
        $this->load->language('shopmanager/card/card_listing');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $input      = json_decode(file_get_contents('php://input'), true);
        $listing_id = (int)($input['listing_id'] ?? 0);
        $groups     = $input['groups']      ?? [];
        $orphan_ids = $input['orphan_ids']  ?? [];
        $p4_groups  = $input['p4_groups']   ?? [];

        if (!$listing_id || (empty($groups) && empty($orphan_ids) && empty($p4_groups))) {
            $json['error'] = 'listing_id and at least groups, orphan_ids or p4_groups required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');

        $merged_count  = 0;
        $deleted_count = 0;
        $renamed_count = 0;

        foreach ($groups as $card_ids) {
            $card_ids = array_map('intval', (array)$card_ids);
            if (count($card_ids) < 2) {
                continue;
            }
            $this->model_shopmanager_card_card_listing->mergeCardGroup($listing_id, $card_ids);
            $merged_count++;
            $deleted_count += count($card_ids) - 1;
        }

        foreach ($orphan_ids as $orphan_id) {
            $orphan_id = (int)$orphan_id;
            if (!$orphan_id) continue;
            if ($this->model_shopmanager_card_card_listing->cleanupOrphanVariant($listing_id, $orphan_id)) {
                $renamed_count++;
            }
        }

        foreach ($p4_groups as $pg) {
            $card_ids    = array_map('intval', (array)($pg['card_ids'] ?? []));
            $survivor_id = (int)($pg['survivor_id'] ?? 0);
            if (count($card_ids) < 2) continue;
            $this->model_shopmanager_card_card_listing->mergeCardGroup($listing_id, $card_ids, $survivor_id);
            $merged_count++;
            $deleted_count += count($card_ids) - 1;
        }

        $this->model_shopmanager_card_card_listing->recalculateBatches($listing_id);
        $this->model_shopmanager_card_card_listing->regenerateDescriptions($listing_id);

        $parts = [];
        if ($merged_count)  $parts[] = $merged_count  . ' group(s) merged, ' . $deleted_count . ' card(s) removed';
        if ($renamed_count) $parts[] = $renamed_count . ' card(s) renamed (letter stripped)';

        $json['success'] = true;
        $json['merged']  = $merged_count;
        $json['deleted'] = $deleted_count;
        $json['renamed'] = $renamed_count;
        $json['message'] = implode(' — ', $parts) ?: 'No changes';

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX: Publish a single batch to eBay.
     * POST listing_id + batch_name
     */
    public function addBatch(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        $batch_name = (int)($this->request->post['batch_name'] ?? 0);

        if (!$listing_id || !$batch_name) {
            $json['error'] = 'listing_id et batch_name requis';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');

        $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
            'customer_id'        => 10,
            'filter_language_id' => 1
        ]);

        if (empty($marketplace_account)) {
            $json['error'] = 'Compte marketplace introuvable';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $result = $this->model_shopmanager_ebay->addCardListing(
            $listing_id,
            $marketplace_account['site_setting'],
            $marketplace_account['marketplace_account_id'],
            false,   // no full cleanup for single-batch add
            false,
            $batch_name
        );

        $errors = $result['errors'] ?? [];
        $batchResult = $result['batches'][$batch_name] ?? [];

        if (!empty($batchResult['ebay_item_id'])) {
            $json['success']      = true;
            $json['ebay_item_id'] = $batchResult['ebay_item_id'];
            $json['message']      = 'Batch ' . $batch_name . ' publié: ' . $batchResult['ebay_item_id'];
        } else {
            $json['error'] = 'Publication échouée. ' . implode(' | ', array_map(fn($e) => is_array($e) ? ($e['message'] ?? '') : $e, $errors));
        }

        if (!empty($errors)) $json['warnings'] = $errors;

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX: End (deactivate) a single batch eBay listing.
     * POST listing_id + batch_name
     */
    public function endBatch(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        $batch_name = (int)($this->request->post['batch_name'] ?? 0);

        if (!$listing_id || !$batch_name) {
            $json['error'] = 'listing_id et batch_name requis';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/marketplace');

        $batchDescriptions = $this->model_shopmanager_card_card_listing->getDescriptions($listing_id);
        $desc = $batchDescriptions[$batch_name] ?? null;

        if (empty($desc) || empty($desc['ebay_item_id'])) {
            $json['error'] = 'Aucun ebay_item_id trouvé pour le batch ' . $batch_name;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $mkt = $this->model_shopmanager_marketplace->getMarketplaceAccount([
            'customer_id'        => 10,
            'filter_language_id' => $desc['language_id'] ?? 1
        ]);

        if (empty($mkt)) {
            $json['error'] = 'Compte marketplace introuvable';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $result = $this->model_shopmanager_ebay->endCardListing(
            $desc['ebay_item_id'],
            $mkt['marketplace_account_id'],
            $mkt['site_setting'],
            $listing_id,
            $desc['language_id'] ?? 1
        );

        $alreadyGone = !($result['success'] ?? false) && preg_match('/not found|already ended|invalid item|no longer|doesn.t exist|Item.*ended|ended or/i', $result['error'] ?? '');

        if (($result['success'] ?? false) || $alreadyGone) {
            $this->model_shopmanager_card_card_listing->updateEbayListingId($listing_id, null, $desc['language_id'] ?? 1, (int)$desc['batch_id']);
            $this->model_shopmanager_card_card_listing->updateBatchPublishedStatus($listing_id, (int)$desc['batch_id'], 2, null, $desc['language_id'] ?? 1);
            $json['success'] = true;
            $json['message'] = 'Batch ' . $batch_name . ' terminé.';
            if ($alreadyGone) $json['message'] .= ' (déjà absent sur eBay — DB nettoyée)';
        } else {
            $json['error'] = $result['error'] ?? 'Erreur inconnue';
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX: Return all regen groups (Pass 1/2/3) for a listing — server-side,
     * queries ALL cards regardless of pagination limit.
     * GET  ?route=shopmanager/card/card_listing.getRegenPreview&listing_id=X
     */
    public function getRegenPreview(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->get['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = 'listing_id required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');
        $groups = $this->model_shopmanager_card_card_listing->getRegenGroups($listing_id);

        $json['success'] = true;
        $json['p1']      = $groups['p1'];
        $json['p2']      = $groups['p2'];
        $json['p3']      = $groups['p3'];
        $json['p4']      = $groups['p4'];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX: re-publish cards that have an offer_id but are not yet published.
     */
    public function republishOffers(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->get['listing_id'] ?? $this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(300);

        try {
            $this->load->model('shopmanager/marketplace');
            $this->load->model('shopmanager/ebay');

            $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                'customer_id'        => 10,
                'filter_language_id' => 1
            ]);

            if (empty($marketplace_account['marketplace_account_id'])) {
                throw new \Exception('No marketplace account found (customer_id=10, language_id=1)');
            }
            $marketplace_account_id = $marketplace_account['marketplace_account_id'];

            $result = $this->model_shopmanager_ebay->republishCardOffers($listing_id, $marketplace_account_id);

            $json['success'] = true;
            $json['result']  = $result;

        } catch (\Throwable $t) {
            $json['error'] = $t->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * AJAX: retrieve eBay offer_id + published status for all cards in a listing
     * that are currently missing an offer_id, then persist results to oc_card.
     */
    public function syncOffers(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->get['listing_id'] ?? $this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(300);

        try {
            $this->load->model('shopmanager/marketplace');
            $this->load->model('shopmanager/ebay');

            $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                'customer_id'        => 10,
                'filter_language_id' => 1
            ]);

            if (empty($marketplace_account['marketplace_account_id'])) {
                throw new \Exception('No marketplace account found (customer_id=10, language_id=1)');
            }

            $marketplace_account_id = $marketplace_account['marketplace_account_id'];

            // Mettre à jour inventory_items + offers existants sur eBay avant de créer/syncer de nouveaux offers.
            $refreshResult = $this->model_shopmanager_ebay->editCardListing($listing_id, $marketplace_account['site_setting'], $marketplace_account_id);

            $result = $this->model_shopmanager_ebay->syncCardOffers($listing_id, $marketplace_account_id);

            $json['success'] = true;
            $json['refresh'] = $refreshResult;
            $json['result']  = $result;

        } catch (\Throwable $t) {
            $json['error'] = $t->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Migrate Google images to eBay for a given listing.
     * Only processes images with googleapis.com URLs, updates oc_card_image.
     */
    public function migrateImages(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->get['listing_id'] ?? $this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = 'Missing listing_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(300);
        ini_set('memory_limit', '512M');

        try {
            $this->load->model('shopmanager/marketplace');
            $this->load->model('shopmanager/ebay');

            // Use customer_id=10, language_id=1 (English CA account)
            $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                'customer_id' => 10,
                'filter_language_id' => 1
            ]);

            if (empty($marketplace_account['marketplace_account_id'])) {
                throw new \Exception('No marketplace account found (customer_id=10, language_id=1)');
            }

            $marketplace_account_id = $marketplace_account['marketplace_account_id'];

            $result = $this->model_shopmanager_ebay->migrateImagesToEbay($listing_id, $marketplace_account_id);

            $json['success'] = true;
            $json['result']  = $result;

        } catch (\Throwable $t) {
            $json['error'] = $t->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * AJAX batch: pour chaque listing_id sélectionné, exécute les 3 opérations
     * dans l'ordre : migrateImages → syncOffers → republishOffers.
     * Retourne un résumé par listing.
     */
    public function batchEbaySync(): void {
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_ids = $this->request->post['listing_ids'] ?? [];
        if (empty($listing_ids) || !is_array($listing_ids)) {
            $json['error'] = 'No listings selected';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(600);
        ini_set('memory_limit', '512M');

        try {
            $this->load->model('shopmanager/marketplace');
            $this->load->model('shopmanager/ebay');

            $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
                'customer_id'        => 10,
                'filter_language_id' => 1
            ]);

            if (empty($marketplace_account['marketplace_account_id'])) {
                throw new \Exception('No marketplace account found (customer_id=10, language_id=1)');
            }

            $marketplace_account_id = $marketplace_account['marketplace_account_id'];

            // sync_mode: 'missing' (only cards without offer_id), 'all', 'none'
            $sync_mode = $this->request->post['sync_mode'] ?? 'missing';
            if (!in_array($sync_mode, ['missing', 'all', 'none'])) {
                $sync_mode = 'missing';
            }

            $this->load->model('shopmanager/card/card_listing');

            $results      = [];
            $total_ok     = 0;
            $total_errors = 0;

            foreach ($listing_ids as $listing_id) {
                $listing_id = (int)$listing_id;

                if (!$listing_id) continue;

                $row = ['listing_id' => $listing_id];

                // 0. Mettre à jour inventory_items + inventory_item_group + offers existants sur eBay
                try {
                    $row['refresh'] = $this->model_shopmanager_ebay->editCardListing($listing_id, $marketplace_account['site_setting'], $marketplace_account_id);
                } catch (\Throwable $t) {
                    $row['refresh'] = ['error' => $t->getMessage()];
                }

                // 1. Migrate Google images → eBay EPS
                try {
                    $row['migrate'] = $this->model_shopmanager_ebay->migrateImagesToEbay($listing_id, $marketplace_account_id);
                } catch (\Throwable $t) {
                    $row['migrate'] = ['error' => $t->getMessage()];
                }

                // 2. Sync offer_ids from eBay (mode: missing/all/none)
                try {
                    $row['sync'] = $this->model_shopmanager_ebay->syncCardOffers($listing_id, $marketplace_account_id, $sync_mode);
                } catch (\Throwable $t) {
                    $row['sync'] = ['error' => $t->getMessage()];
                }

                // 3. Re-publish cards that have offer_id but are not published
                try {
                    $row['republish'] = $this->model_shopmanager_ebay->republishCardOffers($listing_id, $marketplace_account_id);
                } catch (\Throwable $t) {
                    $row['republish'] = ['error' => $t->getMessage()];
                }

                $hasError = isset($row['refresh']['error']) || isset($row['migrate']['error']) || isset($row['sync']['error']) || isset($row['republish']['error']);
                $row['status'] = $hasError ? 'error' : 'ok';
                $hasError ? $total_errors++ : $total_ok++;

                $results[] = $row;
            }

            $json['success']      = true;
            $json['total']        = count($listing_ids);
            $json['total_ok']     = $total_ok;
            $json['total_errors'] = $total_errors;
            $json['results']      = $results;

        } catch (\Throwable $t) {
            $json['error'] = $t->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * AJAX: vérifie la santé eBay (group_key + ebay_item_id) pour les listing_ids sélectionnés.
     * Pour chaque listing, itère sur ses batches et appelle checkBatchHealth().
     */
    public function checkEbayHealth(): void {
        $this->load->language('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card_listing');

        $json = [
            'success'  => false,
            'results'  => [],
            'summary'  => ['checked' => 0, 'ok' => 0, 'errors' => 0],
        ];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $selected = $this->request->post['selected'] ?? [];
        if (empty($selected) || !is_array($selected)) {
            $json['error'] = ($lang['error_no_listings_selected'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $marketplace_account_id = (int)($this->request->post['marketplace_account_id'] ?? 1);
        set_time_limit(300);

        foreach ($selected as $listing_id) {
            $listing_id = (int)$listing_id;
            if (!$listing_id) continue;

            $batches = $this->model_shopmanager_card_card_listing->getBatches($listing_id);
            $listing_result = ['listing_id' => $listing_id, 'batches' => []];

            foreach ($batches as $batch) {
                $batch_id = (int)$batch['batch_id'];
                $check    = $this->model_shopmanager_card_card_listing->checkBatchHealth($listing_id, $batch_id, $marketplace_account_id);
                $skipped  = !empty($check['skipped']);
                $listing_result['batches'][] = [
                    'batch_id'   => $batch_id,
                    'batch_name' => $check['batch_name'] ?? ($batch['batch_name'] ?? ''),
                    'status'     => $check['status'],
                    'error'      => $check['error'],
                    'skipped'    => $skipped,
                ];
                if ($skipped) continue; // pas de ebay_item_id — ne pas comptabiliser
                $json['summary']['checked']++;
                if ($check['status'] === 1) {
                    $json['summary']['ok']++;
                } else {
                    $json['summary']['errors']++;
                }
            }

            $listing_result['health_status'] = $this->model_shopmanager_card_card_listing->getAggregatedHealthStatus($listing_id);
            $json['results'][] = $listing_result;
        }

        $json['success'] = true;
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LOT LISTING AJAX ACTIONS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * AJAX GET — retourne le prix calculé, titre et stats du lot.
     */
    public function getLotPreview(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        $listing_id = (int)($this->request->get['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');
        $lot        = $this->model_shopmanager_card_card_listing;
        $summary    = $lot->getLotPriceSummary($listing_id);
        $lotInfo    = $lot->getLotInfo($listing_id);
        $title      = $lot->generateLotTitle($listing_id);

        $json['success']           = true;
        $json['lot_info']          = $lotInfo;
        $json['calculated_price']  = $summary['calculated_price'];
        $json['card_count']        = $summary['card_count'];
        $json['total_qty']         = $summary['total_qty'];
        $json['floored_count']     = $summary['floored_count'];
        $json['title_preview']     = $title;

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX POST — publie le lot sur eBay.
     */
    public function publishLot(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/marketplace');

        // Override de prix si fourni
        $price_override = isset($this->request->post['lot_price']) ? (float)$this->request->post['lot_price'] : null;
        if ($price_override > 0) {
            $this->model_shopmanager_card_card_listing->saveLotPriceOverride($listing_id, $price_override);
        }

        // Weight & dimensions si fournis
        $lot_weight = isset($this->request->post['lot_weight']) ? (float)$this->request->post['lot_weight'] : null;
        if ($lot_weight !== null) {
            $this->model_shopmanager_card_card_listing->saveLotShipping(
                $listing_id,
                $lot_weight,
                (int)($this->request->post['lot_weight_class_id'] ?? 5),
                (float)($this->request->post['lot_length'] ?? 0),
                (float)($this->request->post['lot_width']  ?? 0),
                (float)($this->request->post['lot_height'] ?? 0),
                (int)($this->request->post['lot_length_class_id'] ?? 3)
            );
        }

        $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
            'customer_id'        => 10,
            'filter_language_id' => 1
        ]);

        if (empty($marketplace_account['site_setting'])) {
            $json['error'] = ($lang['error_marketplace_account'] ?? '') ?: 'Compte marketplace introuvable.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $result = $this->model_shopmanager_ebay->publishLotListing(
            $listing_id,
            $marketplace_account['site_setting'],
            $marketplace_account['marketplace_account_id']
        );

        if (!empty($result['success'])) {
            $json['success']      = true;
            $json['ebay_item_id'] = $result['ebay_item_id'];
            $json['message']      = ($lang['text_lot_published_ok'] ?? '') ?: 'Lot publié sur eBay : ' . $result['ebay_item_id'];
            $json['ebay_url']     = 'https://www.ebay.com/itm/' . $result['ebay_item_id'];
        } else {
            $json['error'] = $result['error'] ?? ($lang['text_lot_publish_failed'] ?? '');
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * AJAX POST — termine l'annonce lot sur eBay.
     */
    public function endLot(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/marketplace');

        $marketplace_account = $this->model_shopmanager_marketplace->getMarketplaceAccount([
            'customer_id'        => 10,
            'filter_language_id' => 1
        ]);

        if (empty($marketplace_account['site_setting'])) {
            $json['error'] = ($lang['error_marketplace_account'] ?? '') ?: 'Compte marketplace introuvable.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $result = $this->model_shopmanager_ebay->endLotListing(
            $listing_id,
            $marketplace_account['site_setting'],
            $marketplace_account['marketplace_account_id']
        );

        if (!empty($result['success'])) {
            $json['success'] = true;
            $json['message'] = ($lang['text_lot_ended_ok'] ?? '') ?: 'Lot eBay terminé avec succès.';
        } else {
            $json['error'] = $result['error'] ?? ($lang['text_lot_end_failed'] ?? '');
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Lot description & images
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * POST listing_id, title, description
     * Sauvegarde le titre + description édités dans oc_card_listing_description.
     */
    public function saveLotDescription(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id  = (int)($this->request->post['listing_id']  ?? 0);
        $title       = (string)($this->request->post['title']       ?? '');
        $description = (string)($this->request->post['description'] ?? '');

        if (!$listing_id) {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');
        $this->model_shopmanager_card_card_listing->saveLotDescription($listing_id, $title, $description);

        $json['success'] = true;
        $json['message'] = ($lang['text_lot_desc_saved_ok'] ?? '') ?: 'Description saved';

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * POST listing_id
     * Régénère titre + description depuis les données live, sauvegarde et retourne.
     */
    public function regenLotDescription(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');
        $result = $this->model_shopmanager_card_card_listing->regenAndSaveLotDescription($listing_id);

        $json['success']     = true;
        $json['title']       = $result['title'];
        $json['description'] = $result['description'];

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /**
     * POST listing_id
     * Génère les mosaïques Imagick, sauvegarde dans oc_card_listing_image, retourne les URLs.
     */
    public function generateLotImages(): void {
        $json = [];
        $this->load->language('shopmanager/card/card_listing');

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_listing')) {
            $json['error'] = ($lang['error_permission_denied'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $listing_id = (int)($this->request->post['listing_id'] ?? 0);
        if (!$listing_id) {
            $json['error'] = ($lang['error_invalid_parameters'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $this->load->model('shopmanager/card/card_listing');

        try {
            $this->model_shopmanager_card_card_listing->generateMosaicImages($listing_id);
        } catch (\Exception $e) {
            $json['error'] = $e->getMessage();
            $this->response->addHeader('Content-Type: application/json; charset=utf-8');
            $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
            return;
        }

        $images  = $this->model_shopmanager_card_card_listing->getLotImages($listing_id);
        $baseUrl = HTTP_CATALOG . 'image/';
        $urls    = array_map(fn($img) => ['url' => $baseUrl . $img['image_path'], 'path' => $img['image_path']], $images);

        $json['success'] = true;
        $json['count']   = count($urls);
        $json['images']  = $urls;
        $json['message'] = ($lang['text_lot_images_generated'] ?? '') ?: 'Mosaic images generated';

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
}

