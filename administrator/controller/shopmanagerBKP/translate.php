<?php
require_once DIR_VENDOR . '/autoload.php';

use Google\Cloud\Translate\V2\TranslateClient;

class ControllerShopManagerTranslate extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('shopmanager/translate'); // Charger les traductions (si nécessaire)

        $json = array('success' => '', 'error' => '');

        // Vérifier si l'utilisateur est connecté
        if (!$this->user->isLogged()) {
            $json['error'] = 'User not logged in';
        } else {
            // Vérification des données POST
            $text_field = $this->request->post['text_field'] ?? '';
            $targetLanguage = $this->request->post['targetLanguage'] ;

            if (empty(trim($text_field))) {
                $json['success'] = '';
            } elseif (empty(trim($targetLanguage))) {
                $json['error'] = 'La langue cible est requise.';
            } else {
                try {
                    // Spécifier le chemin vers le fichier de clé JSON du compte de service
                    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/translate.json');

                    // Initialisation du client de traduction
                    $translate = new TranslateClient();

                    // Traduction du texte
                    $result = $translate->translate($text_field, [
                        'target' => $targetLanguage,
                    ]);

                    $json['success'] = addslashes($result['text']);
                } catch (Exception $e) {
                    $json['error'] = 'Erreur lors de la traduction : ' . $e->getMessage();
                    $this->log->write('Translate error: ' . print_r($e->getMessage(), true));
                }
            }
        }
        $this->log->write('Translate response: ' . print_r($result, true));
       
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}

