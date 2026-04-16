<?php
// Original: warehouse/marketplace/listing.php
namespace Opencart\Admin\Controller\Warehouse\Marketplace;

class Listing extends \Opencart\System\Engine\Controller {
    public function addToMarketplace(): void {
        $json = [];

		if (!$this->user->hasPermission('modify', 'warehouse/marketplace/listing')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
        $this->load->model('warehouse/marketplace/ebay/api');
        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/marketplace/listing');

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
                //print("<pre>".print_r ($this->request->post,true )."</pre>");
                $product_id = $this->request->post['product_id'] ?? '';
                $quantity = $this->request->post['quantity'] ?? '';
                $marketplace_account_id = $this->request->post['marketplace_account_id'] ?? '';
                $marketplace_id = $this->request->post['marketplace_id'] ?? 9;

                if ($product_id && $marketplace_account_id) {
                    // Mise à jour des tarifs dans la base de données
                    $result = $this->model_warehouse_marketplace_listing->addToMarketplace($product_id, $marketplace_account_id);
                    //print("<pre>".print_r ($result,true )."</pre>");
                    if (isset($result['ErrorLanguage'])) {
                        $json['error'] = false;
                        $json['message'] = json_encode($result);
                    } elseif (isset($result['Ack']) && $result['Ack'] != 'Failure') {
                        $data = array(
                            'customer_id' => 10,
                            'product_id' => $product_id,
                            'marketplace_id' => $marketplace_id,
                            'marketplace_account_id' => $marketplace_account_id,
                            'marketplace_item_id' => $result['ItemID'],
                            'quantity_listed' => $result['quantity_listed'],
                            'quantity_sold' => $result['quantity_sold'],
                            'currency' => '',
                            'price' => 0,
                            'category_id' => 0,
                            'specifics' => '',
                            'error' => '',
                            'status' => 1,
                            'to_update' => 0,
                            'ebay_image_count' => 0,
                        );

                        $this->model_warehouse_marketplace_listing->addProductMarketplace($data);
                        $this->model_warehouse_marketplace_listing->syncMarketplaceProduct($result['ItemID']);
                       
                        $json['marketplace_item_id'] = $result['ItemID'];
                        $json['success'] = $result;
                    } elseif (isset($result['error']) ) {
                        $json['error'] = json_encode($result['error']);
                    } else {
                       $json['error'] = json_encode($result);
                    }
                } else {
                    $$json['error'] = 'Paramètres invalides.';
                }
            } else {
                $json['error'] = 'Méthode de requête non autorisée.';
            }
        } else {
            $json['error'] = 'Méthode de requête non autorisée.';
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function editQuantity(): void {

        $json = [];

		if (!$this->user->hasPermission('modify', 'warehouse/marketplace/listing')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
        $this->load->model('warehouse/marketplace/ebay/api');
        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/marketplace/listing');

       

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
                //print("<pre>".print_r ($this->request->post,true )."</pre>");
                $product_id = $this->request->post['product_id'] ?? '';
                $quantity = $this->request->post['quantity'] ?? '';
                $marketplace_account_id = $this->request->post['marketplace_account_id'] ?? '';
                $marketplace_id = $this->request->post['marketplace_id'] ?? 9;

                if ($product_id && $marketplace_account_id) {
                    // Mise à jour des tarifs dans la base de données
                    $result = $this->model_warehouse_marketplace_listing->editQuantity($product_id, $marketplace_account_id);
                    //print("<pre>".print_r ($result,true )."</pre>");
                    if (isset($result['ErrorLanguage'])) {
                        $json['error'] = json_encode($result);
                    } elseif (isset($result['Ack']) && $result['Ack'] != 'Failure') {
                       
                        $json['marketplace_item_id'] = $result['ItemID'];
                         $json['success'] = $result;
                    } else {
                       $json['error'] = json_encode($result['Errors']);
                    }
                } else {
                   $json['error'] = 'Paramètres invalides.';
                }
            } else {
               $json['error'] = 'Méthode de requête non autorisée.';
            }
        } else {
            $json['error'] = 'Méthode de requête non autorisée.';
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function editMarketplaceBulk(): void {
        $json = [];

		if (!$this->user->hasPermission('modify', 'warehouse/marketplace/listing')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
        $this->load->model('warehouse/marketplace/listing');

        $marketplace_accounts_id = $this->model_warehouse_marketplace_listing->getProducts();
        //print("<pre>".print_r ($marketplace_accounts_id,true )."</pre>");

        foreach ($marketplace_accounts_id as $marketplace_account) {
            if (isset($marketplace_account['marketplace_item_id'])) {
                $this->model_warehouse_marketplace_listing->editToMarketplace($marketplace_account['product_id'], $marketplace_account['marketplace_account_id']);
                //print("<pre>".print_r ($marketplace_accounts_id,true )."</pre>");
            }
        }
    }

    public function updateListedProduct(): void {
        $json = ['results' => []];

		if (!$this->user->hasPermission('modify', 'warehouse/marketplace/listing')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if (!$product_id) {
            $json['error'] = 'Paramètres invalides.';
            $this->response->addHeader('Content-Type: application/json; charset=utf-8');
            $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return;
        }

        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/marketplace/listing');

        $product = $this->model_warehouse_product_product->getProduct($product_id);

        if (!$product) {
            $json['error'] = 'Produit introuvable.';
            $this->response->addHeader('Content-Type: application/json; charset=utf-8');
            $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return;
        }

        if ((int)($product['quantity'] ?? 0) <= 0) {
            $json['skipped'] = true;
            $json['reason'] = 'quantity_zero';
            $this->response->addHeader('Content-Type: application/json; charset=utf-8');
            $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            return;
        }

        // Check image count: DB images must be >= eBay images
        $this->load->model('warehouse/marketplace/ebay/api');
        $this->load->language('warehouse/product/product');
        $marketplace_accounts = $this->model_warehouse_marketplace_listing->getMarketplace(['product_id' => $product_id]);
        
        // Get eBay listing info if available
        $ebay_item_id = null;
        foreach ($marketplace_accounts as $acc) {
            if ((int)($acc['marketplace_id'] ?? 0) === 1 && !empty($acc['marketplace_item_id'])) {
                $ebay_item_id = $acc['marketplace_item_id'];
                break;
            }
        }

        if ($ebay_item_id) {
            $db_count = 0;
            $ebay_count = 0;

            // Count DB images (main + secondary)
            if (!empty($product['image'])) {
                $db_count++;
            }
            $secondary_images = $this->model_warehouse_product_product->getImages($product_id);
            if (!empty($secondary_images) && is_array($secondary_images)) {
                $db_count += count($secondary_images);
            }

            // Count eBay images
            $ebay_urls = $this->model_warehouse_marketplace_ebay_api->getImages($ebay_item_id);
            if (!empty($ebay_urls) && is_array($ebay_urls)) {
                $ebay_count = count($ebay_urls);
            }

            // If DB has fewer images than eBay, return error
            if ($db_count < $ebay_count) {
                $json['error'] = sprintf(
                    $this->language->get('error_update_insufficient_images'),
                    $db_count,
                    $ebay_count
                );
                $this->response->addHeader('Content-Type: application/json; charset=utf-8');
                $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return;
            }
        }
        $has_processable_listing = false;

        foreach ($marketplace_accounts as $marketplace_account_id => $marketplace_account) {
            $marketplace_id = (int)($marketplace_account['marketplace_id'] ?? 0);
            $marketplace_item_id = (string)($marketplace_account['marketplace_item_id'] ?? '');

            if ($marketplace_id !== 1 || $marketplace_item_id === '' || $marketplace_item_id === '0') {
                continue;
            }

            $has_processable_listing = true;

            try {
                $result = $this->model_warehouse_marketplace_listing->editToMarketplace($product_id, (int)$marketplace_account_id);

                // DEBUG TEMP
                $this->log->write('[updateListedProduct] product_id=' . $product_id . ' item_id=' . $marketplace_item_id . ' Ack=' . ($result['Ack'] ?? 'N/A') . ' result=' . json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

                if (isset($result['Ack']) && $result['Ack'] != 'Failure' && !isset($result['error'])) {
                    $_pm_row = $this->model_warehouse_marketplace_listing->getProductMarketplaceRow($marketplace_item_id);
                    if ($_pm_row) {
                        $_pm_row['error'] = '';
                        $_pm_row['to_update'] = 0;
                        $this->model_warehouse_marketplace_listing->editProductMarketplace($_pm_row);
                    }

                    $json['results'][] = [
                        'status' => 'success',
                        'product_id' => $product_id,
                        'marketplace_account_id' => (int)$marketplace_account_id,
                        'marketplace_item_id' => $marketplace_item_id
                    ];
                } else {
                    $error_payload = $result['Errors'] ?? $result['error'] ?? $result;
                    $error_json = is_string($error_payload)
                        ? $error_payload
                        : json_encode($error_payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    $_pm_row = $this->model_warehouse_marketplace_listing->getProductMarketplaceRow($marketplace_item_id);
                    if ($_pm_row) {
                        $_pm_row['error'] = (string)$error_json;
                        $_pm_row['to_update'] = 9;
                        $this->model_warehouse_marketplace_listing->editProductMarketplace($_pm_row);
                    }

                    $json['results'][] = [
                        'status' => 'error',
                        'product_id' => $product_id,
                        'marketplace_account_id' => (int)$marketplace_account_id,
                        'marketplace_item_id' => $marketplace_item_id,
                        'message' => $this->formatMarketplaceErrorMessage($error_payload)
                    ];
                }
            } catch (\Throwable $e) {
                $error_payload = ['exception' => ['LongMessage' => $e->getMessage()]];
                $error_json = json_encode($error_payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $_pm_row = $this->model_warehouse_marketplace_listing->getProductMarketplaceRow($marketplace_item_id);
                if ($_pm_row) {
                    $_pm_row['error'] = (string)$error_json;
                    $_pm_row['to_update'] = 9;
                    $this->model_warehouse_marketplace_listing->editProductMarketplace($_pm_row);
                }

                $json['results'][] = [
                    'status' => 'error',
                    'product_id' => $product_id,
                    'marketplace_account_id' => (int)$marketplace_account_id,
                    'marketplace_item_id' => $marketplace_item_id,
                    'message' => $e->getMessage()
                ];
            }
        }

        if (!$has_processable_listing) {
            $json['skipped'] = true;
            $json['reason'] = 'not_listed';
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function formatMarketplaceErrorMessage($error_payload): string {
        if (is_string($error_payload)) {
            return $error_payload;
        }

        $messages = [];

        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator((array)$error_payload));

        foreach ($iterator as $key => $value) {
            if (in_array($key, ['LongMessage', 'ShortMessage', 'message', 'error', 'SeverityCode'])) {
                $messages[] = (string)$value;
            }
        }

        $messages = array_values(array_unique(array_filter($messages)));

        if (!$messages) {
            return 'Unknown eBay error';
        }

        return implode(' | ', $messages);
    }
}