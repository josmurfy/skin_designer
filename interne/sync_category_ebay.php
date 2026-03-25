<?php
/**
 * Script de synchronisation des catégories eBay vers OpenCart 2.3
 * Utilise l'API GetCategories d'eBay pour mettre à jour les catégories OpenCart
 */

// Configuration eBay API
define('EBAY_APP_ID', 'YOUR_EBAY_APP_ID'); // Remplacez par votre App ID eBay
define('EBAY_DEV_ID', 'YOUR_EBAY_DEV_ID'); // Remplacez par votre Dev ID eBay
define('EBAY_CERT_ID', 'YOUR_EBAY_CERT_ID'); // Remplacez par votre Cert ID eBay
define('EBAY_TOKEN', 'YOUR_EBAY_TOKEN'); // Remplacez par votre token eBay
define('EBAY_SITE_ID', '0'); // 0 = US, 2 = UK, 77 = Germany, etc.

// Configuration OpenCart
define('OC_DB_HOSTNAME', 'localhost');
define('OC_DB_USERNAME', 'your_db_user');
define('OC_DB_PASSWORD', 'your_db_password');
define('OC_DB_DATABASE', 'your_opencart_db');
define('OC_DB_PREFIX', 'oc_'); // Préfixe des tables OpenCart

class EbayCategorySync {
    private $db;
    private $language_ids = [1 => 'en', 2 => 'fr']; // 1=English, 2=Français
    private $store_id = 0; // Store ID par défaut
    private $backup_timestamp;
    
    // Mappage des catégories principales eBay vers noms multilingues
    private $main_categories_mapping = [
        '550' => ['en' => 'Art & Antiques', 'fr' => 'Art & Antiquités'],
        '2984' => ['en' => 'Baby', 'fr' => 'Bébé'],
        '267' => ['en' => 'Books, Comics & Magazines', 'fr' => 'Livres, BD, Revues'],
        '12576' => ['en' => 'Computing', 'fr' => 'Informatique, Réseaux'],
        '1' => ['en' => 'Collectibles', 'fr' => 'Objets de Collection'],
        '619' => ['en' => 'Crafts', 'fr' => 'Artisanat'],
        '11450' => ['en' => 'Clothing, Shoes & Accessories', 'fr' => 'Vêtements, Accessoires'],
        '58058' => ['en' => 'Electronics', 'fr' => 'Électronique'],
        '26395' => ['en' => 'Health & Beauty', 'fr' => 'Santé, Beauté'],
        '11700' => ['en' => 'Home & Garden', 'fr' => 'Maison & Jardin'],
        '281' => ['en' => 'Jewelry & Watches', 'fr' => 'Bijoux & Montres'],
        '11233' => ['en' => 'Music', 'fr' => 'Musique'],
        '1249' => ['en' => 'Mobile Phones & Communication', 'fr' => 'Téléphones, GPS'],
        '870' => ['en' => 'Cameras & Photography', 'fr' => 'Photographie'],
        '10542' => ['en' => 'Stamps', 'fr' => 'Timbres'],
        '888' => ['en' => 'Sporting Goods', 'fr' => 'Sports'],
        '220' => ['en' => 'Toys & Games', 'fr' => 'Jouets, Jeux'],
        '6028' => ['en' => 'Travel', 'fr' => 'Voyages'],
        '1305' => ['en' => 'Tickets & Experiences', 'fr' => 'Tickets & Expériences'],
        '38583' => ['en' => 'Vehicle Parts & Accessories', 'fr' => 'Véhicules, Pièces'],
        '14339' => ['en' => 'Automotive', 'fr' => 'Tout pour le Véhicule'],
        '1281' => ['en' => 'Movies & TV', 'fr' => 'Films, DVD']
    ];
    
    public function __construct() {
        // Génère le timestamp pour les backups
        $this->backup_timestamp = date('Y_m_d_H_i_s');
        
        // Connexion à la base de données
        try {
            $this->db = new PDO(
                'mysql:host=' . OC_DB_HOSTNAME . ';dbname=' . OC_DB_DATABASE,
                OC_DB_USERNAME,
                OC_DB_PASSWORD,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Connexion à la base de données réussie\n";
        } catch (PDOException $e) {
            die("❌ Erreur de connexion à la base de données : " . $e->getMessage() . "\n");
        }
    }
    
    /**
     * Récupère les catégories depuis eBay API
     */
    public function getEbayCategories() {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
            <RequesterCredentials>
                <eBayAuthToken>' . EBAY_TOKEN . '</eBayAuthToken>
            </RequesterCredentials>
            <DetailLevel>ReturnAll</DetailLevel>
            <CategorySiteID>' . EBAY_SITE_ID . '</CategorySiteID>
            <LevelLimit>3</LevelLimit>
        </GetCategoriesRequest>';
        
        $headers = [
            'Content-Type: text/xml;charset=utf-8',
            'X-EBAY-API-COMPATIBILITY-LEVEL: 967',
            'X-EBAY-API-DEV-NAME: ' . EBAY_DEV_ID,
            'X-EBAY-API-APP-NAME: ' . EBAY_APP_ID,
            'X-EBAY-API-CERT-NAME: ' . EBAY_CERT_ID,
            'X-EBAY-API-CALL-NAME: GetCategories',
            'X-EBAY-API-SITEID: ' . EBAY_SITE_ID
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.ebay.com/ws/api.dll');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_error($ch)) {
            echo "❌ Erreur cURL : " . curl_error($ch) . "\n";
            return false;
        }
        
        
        
        if ($http_code !== 200) {
            echo "❌ Erreur HTTP : " . $http_code . "\n";
            return false;
        }
        
        try {
            $xml_response = simplexml_load_string($response);
            if ($xml_response->Ack == 'Success') {
                echo "✅ Catégories eBay récupérées avec succès\n";
                return $xml_response;
            } else {
                echo "❌ Erreur eBay API : " . $xml_response->Errors->LongMessage . "\n";
                return false;
            }
        } catch (Exception $e) {
            echo "❌ Erreur parsing XML : " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Crée un backup des tables existantes avec timestamp
     */
    public function backupTables() {
        $tables = [
            OC_DB_PREFIX . 'category',
            OC_DB_PREFIX . 'category_description',
            OC_DB_PREFIX . 'category_to_store',
            OC_DB_PREFIX . 'category_to_layout'
        ];
        
        echo "💾 Création des backups avec timestamp {$this->backup_timestamp}...\n";
        
        try {
            foreach ($tables as $table) {
                $backup_table = $table . '_backup_' . $this->backup_timestamp;
                
                // Vérifie si la table existe
                $stmt = $this->db->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$table]);
                
                if ($stmt->rowCount() > 0) {
                    // Crée une copie de la table
                    $this->db->exec("CREATE TABLE {$backup_table} AS SELECT * FROM {$table}");
                    echo "  ✅ Backup créé: {$backup_table}\n";
                } else {
                    echo "  ⚠️ Table {$table} n'existe pas, backup ignoré\n";
                }
            }
            
            echo "✅ Tous les backups ont été créés\n";
            return true;
            
        } catch (PDOException $e) {
            echo "❌ Erreur lors du backup : " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Restaure les tables depuis le backup le plus récent
     */
    public function restoreFromBackup($timestamp = null) {
        if (!$timestamp) {
            // Trouve le backup le plus récent
            $stmt = $this->db->query("SHOW TABLES LIKE '" . OC_DB_PREFIX . "category_backup_%'");
            $backups = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($backups)) {
                echo "❌ Aucun backup trouvé\n";
                return false;
            }
            
            // Trie pour obtenir le plus récent
            rsort($backups);
            $latest_backup = $backups[0];
            
            // Extrait le timestamp
            preg_match('/_backup_(.+)$/', $latest_backup, $matches);
            $timestamp = $matches[1];
        }
        
        $tables = [
            'category',
            'category_description', 
            'category_to_store',
            'category_to_layout'
        ];
        
        echo "🔄 Restauration depuis le backup {$timestamp}...\n";
        
        try {
            foreach ($tables as $table_name) {
                $table = OC_DB_PREFIX . $table_name;
                $backup_table = $table . '_backup_' . $timestamp;
                
                // Vérifie si le backup existe
                $stmt = $this->db->prepare("SHOW TABLES LIKE ?");
                $stmt->execute([$backup_table]);
                
                if ($stmt->rowCount() > 0) {
                    // Supprime la table actuelle et restaure
                    $this->db->exec("DROP TABLE IF EXISTS {$table}");
                    $this->db->exec("CREATE TABLE {$table} AS SELECT * FROM {$backup_table}");
                    echo "  ✅ {$table} restaurée\n";
                } else {
                    echo "  ⚠️ Backup {$backup_table} introuvable\n";
                }
            }
            
            echo "✅ Restauration terminée\n";
            return true;
            
        } catch (PDOException $e) {
            echo "❌ Erreur lors de la restauration : " . $e->getMessage() . "\n";
            return false;
        }
    }
    public function cleanOldCategories() {
        try {
            $tables = [
                OC_DB_PREFIX . 'category',
                OC_DB_PREFIX . 'category_description',
                OC_DB_PREFIX . 'category_to_store',
                OC_DB_PREFIX . 'category_to_layout'
            ];
            
            foreach ($tables as $table) {
                $this->db->exec("DELETE FROM {$table} WHERE category_id > 0");
            }
            
            // Reset auto increment
            $this->db->exec("ALTER TABLE " . OC_DB_PREFIX . "category AUTO_INCREMENT = 1");
            
            echo "✅ Anciennes catégories supprimées\n";
        } catch (PDOException $e) {
            echo "❌ Erreur lors du nettoyage : " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Insère une catégorie dans OpenCart
     */
    public function insertCategory($category_id, $parent_id, $name, $level) {
        try {
            // Insertion dans la table category
            $stmt = $this->db->prepare("
                INSERT INTO " . OC_DB_PREFIX . "category 
                (category_id, image, parent_id, `top`, `column`, sort_order, status, date_modified, date_added) 
                VALUES (?, '', ?, ?, 1, ?, 1, NOW(), NOW())
            ");
            
            $top = ($level <= 1) ? 1 : 0; // Top level pour les catégories principales
            $sort_order = $level * 10;
            
            $stmt->execute([$category_id, $parent_id, $top, $sort_order]);
            
            // Insertion dans category_description
            $stmt = $this->db->prepare("
                INSERT INTO " . OC_DB_PREFIX . "category_description 
                (category_id, language_id, name, description, meta_title, meta_description, meta_keyword) 
                VALUES (?, ?, ?, ?, ?, ?, '')
            ");
            
            $description = "Catégorie {$name} importée depuis eBay";
            $stmt->execute([
                $category_id, 
                $this->language_id, 
                $name, 
                $description, 
                $name, 
                $description
            ]);
            
            // Insertion dans category_to_store
            $stmt = $this->db->prepare("
                INSERT INTO " . OC_DB_PREFIX . "category_to_store 
                (category_id, store_id) VALUES (?, ?)
            ");
            $stmt->execute([$category_id, $this->store_id]);
            
            return true;
            
        } catch (PDOException $e) {
            echo "❌ Erreur insertion catégorie {$category_id} : " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Traite et synchronise les catégories
     */
    public function syncCategories() {
        $ebay_data = $this->getEbayCategories();
        
        if (!$ebay_data) {
            return false;
        }
        
        echo "🧹 Nettoyage des anciennes catégories...\n";
        $this->cleanOldCategories();
        
        echo "📥 Traitement des catégories eBay...\n";
        
        $categories = [];
        $processed = 0;
        
        // Parse des catégories eBay
        foreach ($ebay_data->CategoryArray->Category as $category) {
            $cat_id = (string)$category->CategoryID;
            $cat_name = (string)$category->CategoryName;
            $parent_id = (string)$category->CategoryParentID;
            $level = (int)$category->CategoryLevel;
            
            // Limite aux 3 premiers niveaux pour éviter la surcharge
            if ($level > 3) continue;
            
            // Utilise le mappage français pour les catégories principales
            if ($level == 1 && isset($this->main_categories_mapping[$cat_id])) {
                $cat_name = $this->main_categories_mapping[$cat_id];
            }
            
            $categories[$cat_id] = [
                'id' => $cat_id,
                'name' => $cat_name,
                'parent_id' => $parent_id == $cat_id ? 0 : $parent_id,
                'level' => $level
            ];
        }
        
        // Trie les catégories par niveau pour insérer les parents en premier
        uasort($categories, function($a, $b) {
            return $a['level'] <=> $b['level'];
        });
        
        // Insertion des catégories
        foreach ($categories as $category) {
            if ($this->insertCategory(
                $category['id'], 
                $category['parent_id'], 
                $category['name'], 
                $category['level']
            )) {
                $processed++;
                if ($processed % 50 == 0) {
                    echo "📊 {$processed} catégories traitées...\n";
                }
            }
        }
        
        echo "✅ Synchronisation terminée : {$processed} catégories importées\n";
        
        // Mise à jour du cache OpenCart si nécessaire
        $this->clearOpenCartCache();
        
        return true;
    }
    
    /**
     * Vide le cache OpenCart
     */
    private function clearOpenCartCache() {
        try {
            // Vide les tables de cache si elles existent
            $cache_tables = [
                OC_DB_PREFIX . 'category_filter',
                OC_DB_PREFIX . 'category_path'
            ];
            
            foreach ($cache_tables as $table) {
                $this->db->exec("DELETE FROM {$table}");
            }
            
            echo "🗑️ Cache OpenCart vidé\n";
        } catch (PDOException $e) {
            // Les tables de cache peuvent ne pas exister selon la version
            echo "ℹ️ Nettoyage cache ignoré\n";
        }
    }
    
    /**
     * Affiche les statistiques
     */
    public function showStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN parent_id = 0 THEN 1 ELSE 0 END) as top_level,
                    SUM(CASE WHEN `top` = 1 THEN 1 ELSE 0 END) as visible_top
                FROM " . OC_DB_PREFIX . "category
            ");
            
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "\n📈 STATISTIQUES:\n";
            echo "- Total catégories : {$stats['total']}\n";
            echo "- Catégories racines : {$stats['top_level']}\n";
            echo "- Catégories visibles en top : {$stats['visible_top']}\n";
            
        } catch (PDOException $e) {
            echo "❌ Erreur statistiques : " . $e->getMessage() . "\n";
        }
    }
}

// Exécution du script
echo "🚀 Démarrage de la synchronisation eBay -> OpenCart\n";
echo "================================================\n";

$sync = new EbayCategorySync();

if ($sync->syncCategories()) {
    $sync->showStats();
    echo "\n✅ Synchronisation réussie ! Vos catégories eBay sont maintenant disponibles dans OpenCart.\n";
    echo "💡 N'oubliez pas de vérifier l'affichage dans votre back-office OpenCart.\n";
} else {
    echo "\n❌ Échec de la synchronisation. Vérifiez vos paramètres API eBay.\n";
}

echo "\n================================================\n";
echo "🏁 Script terminé\n";
?>