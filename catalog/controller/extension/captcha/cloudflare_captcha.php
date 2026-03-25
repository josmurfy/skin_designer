<?php
class ControllerExtensionCaptchaCloudflareCaptcha extends Controller {
    public function index($error = array()) {
        $this->load->language('extension/captcha/cloudflare_captcha');
        
        // Charger la clé publique du captcha depuis la configuration
        $data['site_key'] = $this->config->get('cloudflare_captcha_key');
        
        // Retourne la vue du captcha
        return $this->load->view('extension/captcha/cloudflare_captcha.tpl', $data);
    }

    public function validate() {
      //print("<pre>".print_r ('12',true )."</pre>");
     //print("<pre>".print_r ($this->session,true )."</pre>");
        if (empty($this->session->data['ccaptcha'])) {
            $this->load->language('extension/captcha/cloudflare_captcha');
    
            if (!isset($this->request->post['turnstile_response']) || !$this->request->post['turnstile_response']) {
                return $this->language->get('error_captcha');
            }
    
            // Préparation de la requête POST pour la vérification de Turnstile
            $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
            $data = [
                'secret' => $this->config->get('cloudflare_captcha_secret'),
                'response' => $this->request->post['turnstile_response'],
                'remoteip' => $this->request->server['REMOTE_ADDR']
            ];
    
            // Initialiser cURL
            $ch = curl_init($url);
    
            // Configurer cURL pour une requête POST
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
            // Exécuter la requête et obtenir la réponse
            $response = curl_exec($ch);
    
            // Vérifier les erreurs de cURL
            if ($response === false) {
                
                return $this->language->get('error_captcha');
            }
    
            // Fermer cURL
            
    
            // Décoder la réponse JSON
            $result = json_decode($response, true);
    
            // Vérifier le succès de la vérification
            if (isset($result['success']) && $result['success'] === true) {
                $this->session->data['ccaptcha'] = true;
                return true;
            } else {
                return $this->language->get('error_captcha');
            }
        }
    }
    
}

