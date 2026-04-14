<?php
/**
 * eBay OAuth2 Callback Proxy
 *
 * eBay redirects here after user consent. Since OpenCart admin requires user_token
 * in the URL (validated by startup/login.php), this proxy extracts user_token from
 * the base64-encoded state parameter and redirects to the actual OC4 controller
 * with user_token injected.
 *
 * RuName redirect URL in eBay Developer Portal should be set to:
 *   https://phoenixliquidation.ca/administrator/ebay_oauth_callback.php
 */

// Only GET/HEAD requests expected
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'HEAD'])) {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Decode state parameter to extract user_token
$state = $_GET['state'] ?? '';
$state_data = json_decode(base64_decode($state), true);

if (!$state_data || empty($state_data['ut'])) {
    http_response_code(400);
    exit('Invalid or missing state parameter. Please try the OAuth flow again from the Marketplace page.');
}

$user_token = $state_data['ut'];

// Build the internal OC4 callback URL with user_token
$params = [
    'route'      => 'shopmanager/marketplace/marketplace.callbackEbay',
    'user_token' => $user_token,
];

// Forward all parameters from eBay (code, state, expires_in, error, error_description)
foreach (['code', 'state', 'expires_in', 'error', 'error_description'] as $key) {
    if (isset($_GET[$key])) {
        $params[$key] = $_GET[$key];
    }
}

$redirect_url = 'index.php?' . http_build_query($params);

header('Location: ' . $redirect_url, true, 302);
exit;
