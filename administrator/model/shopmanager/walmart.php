<?php
namespace Opencart\Admin\Model\Shopmanager;


use GuzzleHttp\Client;
use WalmartApiClient\Http\TransportService;
use WalmartApiClient\Exception\Handler\ApiExceptionHandler;
use WalmartApiClient\Exception\ApiException;

class Walmart extends \Opencart\System\Engine\Model {

    public function __construct($registry, $site = '') {
        parent::__construct($registry);
        $this->site = $site;
        $this->credentials = $this->getApiCredentials();

        // Crée le client HTTP Guzzle
        $transport = new Client();

        // Crée un gestionnaire d'exceptions
        $handler = new ApiExceptionHandler();

        // Fournir la clé API
        $apiKey = $this->credentials['client_id'];

        // URL de l'API Walmart Marketplace USA
        $apiBaseUrl = 'https://marketplace.walmartapis.com/v3/';

        // Crée une instance de TransportService avec l'URL de l'API de Walmart USA
        $this->client = new TransportService($transport, $handler, $apiKey, null, $apiBaseUrl);
    }

    public function getApiCredentials($marketplace_account_id = 3) {
        $this->load->model('shopmanager/marketplace');
        $connectionapi = $this->model_shopmanager_marketplace->getMarketplaceAccount(['customer_id' => 10, 'filter_marketplace_account_id' => $marketplace_account_id]);
        $connectionapi = isset($connectionapi[$marketplace_account_id]) ? $connectionapi[$marketplace_account_id] : $connectionapi;
        //print("<pre>".print_r ($connectionapi,true )."</pre>"); 
        
        //$this->redirectToAuthorizationEndpoint($connectionapi);
        
        $newAccessTokenData = $this->getAccessToken($connectionapi);

        
       //print("<pre>".print_r ($newAccessTokenData,true )."</pre>"); 
        if (isset($newAccessTokenData)) {
            $connectionapi['bearer_token'] = $newAccessTokenData;
        }
        //print("<pre>".print_r (439,true )."</pre>"); 
        //print("<pre>".print_r ($newAccessTokenData,true )."</pre>"); 
        return $connectionapi;
    }

    public function getAccessToken($connectionapi) {
        $client = new Client();
        $url = 'https://marketplace.walmartapis.com/v3/token';

        $clientId = $connectionapi['client_id'];
        $clientSecret = $connectionapi['client_secret'];
        $encodedCredentials = base64_encode($clientId . ':' . $clientSecret);
        //print("<pre>".print_r (439,true )."</pre>"); 
        //print("<pre>".print_r ($encodedCredentials,true )."</pre>"); 
        //print("<pre>".print_r (uniqid(),true )."</pre>"); 
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'WM_SVC.NAME' => 'Walmart Marketplace',
            'WM_QOS.CORRELATION_ID' => uniqid(),
            'Authorization' => 'Basic ' . $encodedCredentials,
            'Accept' => 'application/json',
        ];

        $body = [
            'grant_type' => 'client_credentials',
        ];

        try {
            $response = $client->post($url, [
                'headers' => $headers,
                'form_params' => $body
            ]);

            $responseArray = json_decode($response->getBody()->getContents(), true);
            //print("<pre>".print_r (72,true )."</pre>"); 
            //print("<pre>".print_r ($responseArray,true )."</pre>"); 
            if (isset($responseArray['access_token'])) {
                // Stocker l'access_token et l'heure d'expiration dans un cookie
                setcookie('walmart_bearer_token', $responseArray['access_token'], time() + 3600, "/");
                setcookie('access_token_expiry_time', time() + 900, time() + 900, "/"); // 900 secondes = 15 minutes
    
                return $responseArray['access_token'];
            } else {
                throw new Exception("Token non reçu. Réponse: " . json_encode($responseArray));
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            echo "⚠️ Erreur 400 : " . $responseBody;
            return null;
        } catch (\Exception $e) {
            echo "❌ Erreur : " . $e->getMessage();
            return null;
        }
    }

    public function getValidAccessToken($clientId, $clientSecret, $refreshToken) {
        // Vérifier si le token a expiré
        $accessTokenExpirationTime = isset($_COOKIE['access_token_expiry_time']) ? $_COOKIE['access_token_expiry_time'] : 0;
    
        if (time() > $accessTokenExpirationTime) {
            // Si l'access_token a expiré, rafraîchit-le avec le refresh_token
            echo "🔄 Rafraîchissement du token...";
            return $this->refreshAccessToken($refreshToken);
        }
    
        // Si le token est encore valide, on l'utilise
        return isset($_COOKIE['walmart_bearer_token']) ? $_COOKIE['walmart_bearer_token'] : null;
    }

    public function refreshAccessToken($connectionapi) {
        $client = new Client();
        $url = 'https://marketplace.walmartapis.com/v3/token';

        $clientId = $connectionapi['client_id'];
        $clientSecret = $connectionapi['client_secret'];
        $refreshToken = $connectionapi['refresh_token'];
        $encodedCredentials = base64_encode($clientId . ':' . $clientSecret);
        //print("<pre>".print_r (135,true )."</pre>"); 
        //print("<pre>".print_r ($encodedCredentials,true )."</pre>"); 
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . $encodedCredentials,
            'Accept' => 'application/json',
        ];
    
        $body = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,  // Le refresh_token
        ];

        try {
            //error_log("Requesting new access token with refresh token: $refreshToken");
            $response = $client->post($url, [
                'headers' => $headers,
                'form_params' => $body
            ]);

            $responseArray = json_decode($response->getBody()->getContents(), true);
            //print("<pre>".print_r (135,true )."</pre>"); 
            //print("<pre>".print_r ($responseArray,true )."</pre>"); 
            if (isset($responseArray['access_token'])) {
                // Stocker le nouveau token et l'heure d'expiration
                setcookie('walmart_bearer_token', $responseArray['access_token'], time() + 3600, "/");
                setcookie('access_token_expiry_time', time() + 900, time() + 900, "/");
    
                return $responseArray['access_token'];
            } else {
                throw new Exception("Token non reçu. Réponse: " . json_encode($responseArray));
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            error_log("⚠️ Error 400: " . $responseBody);
            return null;
        } catch (\Exception $e) {
            error_log("❌ Error: " . $e->getMessage());
            return null;
        }
    }

    public function add($product, $quantity = 0, $site_setting = [], $marketplace_account_id = null) {
        $product_id = isset($product['product_id']) ? $product['product_id'] : '';
        $name = isset($product['description'][1]['name']) ? $product['description'][1]['name'] : '';
        $price = isset($product['price']) ? $product['price'] : 0.0;
        $category = isset($product['category_name']) ? $product['category_name'] : '';
        $description = isset($product['meta_description'][1]) ? $product['meta_description'][1] : '';
        $brand = isset($product['brand']) ? $product['brand'] : '';
        $weight = isset($product['weight']) ? $product['weight'] : 0.0;
        $product_images = $this->model_shopmanager_catalog_product->getProductImages($product_id);
        $images = $this->generatePictures($product_images, $product['image']);
        $upc = isset($product['upc']) ? $product['upc'] : '';

        if (empty($upc)) {
            throw new Exception('L\'UPC est requis pour ajouter un produit sur Walmart');
        }

        $data = [
            'MPItem' => [
                'sku' => $product_id,
                'productName' => $name,
                'price' => [
                    'currency' => 'USD',
                    'amount' => $price
                ],
                'productIdentifiers' => [
                    'productIdType' => 'UPC',
                    'productId' => $upc
                ],
                'productCategory' => $category,
                'productDescription' => $description,
                'brand' => $brand,
                'images' => $images,
                'shippingWeight' => [
                    'value' => $weight,
                    'unit' => 'LB'
                ],
                'fulfillmentLagTime' => 2,
                'inventory' => [
                    'quantity' => $quantity,
                    'fulfillmentLagTime' => 2
                ]
            ]
        ];
        //print("<pre>".print_r ($data,true )."</pre>"); 

        if (!empty($site_setting)) {
            // Logique supplémentaire pour appliquer des paramètres spécifiques
        }

        if ($marketplace_account_id !== null) {
            // Logique supplémentaire pour traiter des informations en fonction de l'ID du compte marketplace
        }

        // Appeler la méthode d'ajout de produit via un feed sur Walmart
        $headers = ['Content-Type' => 'multipart/form-data'];
        //return $this->makeApiRequest('feeds?feedType=MP_ITEM', 'POST', $data, $headers);
    }

    private function generatePictures($images, $image_princ) {
        $Image_1 = $this->domain . '/image/' . $image_princ;
        $productImages = [];
        $productImages[] = ['url' => $Image_1];

        $i = 1;
        foreach ($images as $image) {
            if ($i < 13) {
                $productImages[] = ['url' => $this->domain . '/image/' . $image['image']];
                $i++;
            }
        }

        return $productImages;
    }


    private function makeApiRequest($endpoint, $method = 'GET', $data = [], $headers = []) {
        $client = new Client();
        $url = 'https://marketplace.walmartapis.com/v3/' . $endpoint;

        // Get the access token
        $accessToken = isset($_COOKIE['walmart_bearer_token']) ? $_COOKIE['walmart_bearer_token'] : null;
        if (!$accessToken) {
            throw new Exception('Access token is missing.');
        }

        // Default headers
        $defaultHeaders = [
            'WM_SEC.ACCESS_TOKEN' => $accessToken,
            'Accept' => 'application/json',
            'WM_QOS.CORRELATION_ID' => uniqid(),
            'WM_SVC.NAME' => 'Walmart Marketplace',
        ];

        // Merge default headers with custom headers
        $headers = array_merge($defaultHeaders, $headers);

        // Request options
        $options = [
            'headers' => $headers,
        ];

        // Add data to the request if it's a POST or PUT request
        if ($method === 'POST' || $method === 'PUT') {
            if (isset($headers['Content-Type']) && $headers['Content-Type'] === 'multipart/form-data') {
                $options['multipart'] = [
                    [
                        'name' => 'file',
                        'contents' =>  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                        'filename' => 'feed.json'
                    ]
                ];
            } else {
                $options['json'] = $data;
            }
        }

        try {
            $response = $client->request($method, $url, $options);
            $responseBody = $response->getBody()->getContents();
            return json_decode($responseBody, true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            error_log("⚠️ Error: " . $responseBody);
            throw new Exception("Client error: " . $responseBody);
        } catch (\Exception $e) {
            error_log("❌ Error: " . $e->getMessage());
            throw new Exception("Error: " . $e->getMessage());
        }
    }
}
