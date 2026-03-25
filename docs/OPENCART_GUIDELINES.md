# 📘 OPENCART 4 GUIDELINES - Standards de Développement
**Framework OpenCart 4.x - MVC-L Pattern**

---

## 🤖 [INSTRUCTIONS POUR L'IA - LIRE EN PREMIER]

**⚠️ SI tu es une IA et que ce fichier est attaché:**

**TU DOIS suivre ces règles OpenCart 4 EN PLUS de DEV_GUIDELINES.md**

**RÈGLES ABSOLUES OpenCart:**

1. **Architecture MVC-L NON NÉGOCIABLE:**
   - Controller = Orchestration UNIQUEMENT (charge model, language, view)
   - Model = SQL queries UNIQUEMENT (pas de logique métier)
   - View/Twig = Affichage UNIQUEMENT (pas de calculs complexes)
   - Language = Textes UNIQUEMENT (pas de HTML ni logique)

2. **TOUJOURS respecter:**
   - `$this->load->model()` AVANT utilisation
   - `$this->load->language()` pour textes
   - `DB_PREFIX` devant noms de tables
   - Cast `(int)` sur tous les IDs
   - `$this->db->escape()` sur strings utilisateur
   - `DIR_APPLICATION`, `DIR_IMAGE` au lieu de chemins absolus

3. **INTERDIT dans OpenCart:**
   - ❌ SQL direct dans controller
   - ❌ HTML dans PHP (utiliser Twig)
   - ❌ Logique métier dans model
   - ❌ Chemins hardcodés
   - ❌ Variables globales `$_GET/$_POST` directes (utiliser `$this->request`)

4. **SI violation détectée:**
   - STOPPER immédiatement
   - Signaler l'erreur à l'utilisateur
   - Proposer correction selon standards OpenCart

**FLOWCHART décision modification:**
```
Demande modification OpenCart
    ↓
Quel composant modifier?
├─ Controller → Orchestration seulement, charger models/language
├─ Model → SQL queries seulement, pas de logique
├─ View/Twig → Affichage seulement, pas de calculs
└─ Language → Textes seulement, format PHP array
```

---

## 🎯 À LIRE AVEC DEV_GUIDELINES.md

**Ce fichier contient les règles spécifiques à OpenCart 4.**  
**DEV_GUIDELINES.md contient les règles spécifiques au projet PhoenixLiquidation.**

---

## 🏗️ ARCHITECTURE MVC-L

OpenCart utilise le pattern **MVC-L (Model-View-Controller-Language)**:

```
┌─────────────┐
│  CONTROLLER │ ← Point d'entrée via URL
└──────┬──────┘
       │ Charge et coordonne:
       ├──► MODEL      (SQL queries uniquement)
       ├──► LANGUAGE   (Textes traduisibles)
       ├──► VIEW/TWIG  (Template d'affichage)
       └──► LIBRARY    (Fonctions système)
```

### Responsabilités Claires

| Composant | Responsabilité | Interdictions |
|-----------|----------------|---------------|
| **Controller** | Charge modèles, prépare données, passe au view | ❌ PAS de SQL direct<br>❌ PAS de HTML |
| **Model** | SQL queries UNIQUEMENT | ❌ PAS de logique métier<br>❌ PAS d'affichage |
| **View/Twig** | Affichage HTML + JS inline simple | ❌ PAS de SQL<br>❌ PAS de logique complexe |
| **Language** | Textes traduisibles | ❌ PAS de logique<br>❌ PAS de HTML |

---

## 📂 STRUCTURE DES DOSSIERS

### Structure Standard d'un Module

```
admin/
├── controller/
│   └── module/
│       └── my_module.php          ← Controller admin
├── model/
│   └── module/
│       └── my_module.php          ← Model admin (optionnel)
├── view/
│   └── template/
│       └── module/
│           └── my_module.twig     ← Template admin
└── language/
    ├── en-gb/
    │   └── module/
    │       └── my_module.php      ← Traduction anglais
    └── fr-fr/
        └── module/
            └── my_module.php      ← Traduction français

catalog/
├── controller/
│   └── module/
│       └── my_module.php          ← Controller frontend
├── model/
│   └── module/
│       └── my_module.php          ← Model frontend
├── view/
│   └── theme/
│       └── [theme_name]/
│           └── template/
│               └── module/
│                   └── my_module.twig  ← Template frontend (par thème)
└── language/
    ├── en-gb/
    │   └── module/
    │       └── my_module.php
    └── fr-fr/
        └── module/
            └── my_module.php
```

**Points clés:**
- Admin: Un seul template
- Catalog: Un template par thème (dans `view/theme/[theme_name]/`)

---

## 🔌 CHARGEMENT DES FICHIERS

### 1. Charger un Fichier Language

```php
// Dans le controller
$this->load->language('module/my_module');

// Récupérer le texte
$data['heading_title'] = $this->language->get('heading_title');

// Définir le titre de la page
$this->document->setTitle($this->language->get('heading_title'));
```

**Dans le fichier language (admin/language/en-gb/module/my_module.php):**
```php
<?php
$_['heading_title'] = 'My Module';
$_['text_success'] = 'Success: Settings saved!';
$_['entry_status'] = 'Status';
```

### 2. Charger un Fichier Model

```php
// Charger le model
$this->load->model('setting/setting');

// Utiliser une fonction du model
$this->model_setting_setting->editSetting('my_module', $this->request->post);

// Format: model_[folder]_[filename]->function()
// Exemples:
$this->load->model('catalog/product');
$products = $this->model_catalog_product->getProducts();

$this->load->model('module/my_module');
$data = $this->model_module_my_module->myCustomFunction();
```

**⚠️ Convention de nommage:**
- Path: `setting/setting` → Class: `model_setting_setting`
- Path: `catalog/product` → Class: `model_catalog_product`
- Les `/` deviennent des `_`

### 3. Charger un Template

```php
// Dans le controller (OpenCart 3.x+)
return $this->load->view('module/my_module', $data);

// Ancienne méthode (OpenCart 2.x)
$this->template = 'module/my_module.tpl';
$this->response->setOutput($this->load->view($this->template, $data));
```

### 4. Charger une Library

```php
// Les libraries sont dans system/library/
// Elles sont accessibles directement via $this

$this->document->setTitle('My Page');
$this->session->data['success'] = 'Action completed!';
$this->response->setOutput($output);
$this->url->link('catalog/product', 'product_id=1');
```

---

## 🎛️ CONTROLLER PATTERNS

### Structure Basique d'un Controller

```php
<?php
namespace Opencart\Admin\Controller\Module;

class MyModule extends \Opencart\System\Engine\Controller {
    
    // Fonction principale (route: index.php?route=module/my_module)
    public function index(): void {
        // 1. Charger language
        $this->load->language('module/my_module');
        
        // 2. Définir le titre
        $this->document->setTitle($this->language->get('heading_title'));
        
        // 3. Gérer la soumission du formulaire
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('module_my_module', $this->request->post);
            
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true));
        }
        
        // 4. Préparer les données pour le view
        $data['heading_title'] = $this->language->get('heading_title');
        
        // 5. Charger et retourner le view
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('module/my_module', $data));
    }
    
    // Fonction install (route: index.php?route=module/my_module/install)
    public function install(): void {
        // Créer tables, ajouter permissions, etc.
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "my_module` ...");
    }
    
    // Fonction uninstall
    public function uninstall(): void {
        // Nettoyer: supprimer tables, settings, etc.
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "my_module`");
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_my_module');
    }
}
```

### Accès via URL

**Format URL OpenCart:**
```
admin/index.php?route=folder/subfolder/file&function
```

**Exemples:**
- `admin/index.php?route=module/my_module` → `controller/module/my_module.php::index()`
- `admin/index.php?route=module/my_module&install` → `controller/module/my_module.php::install()`
- `catalog/index.php?route=product/product&product_id=50` → `catalog/controller/product/product.php::index()`

---

## 🗄️ MODEL PATTERNS

### Règles Absolues

**✅ AUTORISÉ dans les Models:**
- Requêtes SQL (SELECT, INSERT, UPDATE, DELETE)
- Retourner des données brutes
- Fonctions de transformation de données DB

**⛔ INTERDIT dans les Models:**
- Logique métier complexe
- Affichage HTML
- Manipulation de sessions
- Redirection

### Structure Basique d'un Model

```php
<?php
namespace Opencart\Admin\Model\Module;

class MyModule extends \Opencart\System\Engine\Model {
    
    // Récupérer un élément par ID
    public function getItem(int $item_id): array {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "my_module` WHERE item_id = '" . (int)$item_id . "'");
        
        return $query->row;
    }
    
    // Récupérer plusieurs éléments
    public function getItems(array $data = []): array {
        $sql = "SELECT * FROM `" . DB_PREFIX . "my_module`";
        
        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        if (isset($data['start']) || isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    
    // Ajouter un élément
    public function addItem(array $data): int {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "my_module` SET 
            name = '" . $this->db->escape($data['name']) . "',
            value = '" . $this->db->escape($data['value']) . "',
            status = '" . (int)$data['status'] . "'
        ");
        
        return $this->db->getLastId();
    }
    
    // Mettre à jour un élément
    public function editItem(int $item_id, array $data): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "my_module` SET 
            name = '" . $this->db->escape($data['name']) . "',
            value = '" . $this->db->escape($data['value']) . "',
            status = '" . (int)$data['status'] . "'
            WHERE item_id = '" . (int)$item_id . "'
        ");
    }
    
    // Supprimer un élément
    public function deleteItem(int $item_id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "my_module` WHERE item_id = '" . (int)$item_id . "'");
    }
    
    // Compter le total
    public function getTotalItems(array $data = []): int {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "my_module`";
        
        if (!empty($data['filter_name'])) {
            $sql .= " WHERE name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        $query = $this->db->query($sql);
        
        return (int)$query->row['total'];
    }
}
```

**Points clés:**
- Toujours utiliser `DB_PREFIX` pour les tables
- Toujours échapper les données: `$this->db->escape()`
- Toujours caster les IDs: `(int)$id`
- Retourner `$query->row` pour un résultat, `$query->rows` pour plusieurs

---

## 🎨 VIEW/TWIG PATTERNS

### Structure Basique Twig (OpenCart 4)

```twig
{{ header }}{{ column_left }}

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1>{{ heading_title }}</h1>
      <ul class="breadcrumb">
        {% for breadcrumb in breadcrumbs %}
          <li><a href="{{ breadcrumb.href }}">{{ breadcrumb.text }}</a></li>
        {% endfor %}
      </ul>
    </div>
  </div>

  <div class="container-fluid">
    {% if error_warning %}
      <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> {{ error_warning }}</div>
    {% endif %}
    
    {% if success %}
      <div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> {{ success }}</div>
    {% endif %}

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> {{ text_form }}</h3>
      </div>
      <div class="panel-body">
        <form action="{{ action }}" method="post" enctype="multipart/form-data">
          
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name">{{ entry_name }}</label>
            <div class="col-sm-10">
              <input type="text" name="name" value="{{ name }}" id="input-name" class="form-control" />
              {% if error_name %}
                <div class="text-danger">{{ error_name }}</div>
              {% endif %}
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status">{{ entry_status }}</label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                {% if status %}
                  <option value="1" selected="selected">{{ text_enabled }}</option>
                  <option value="0">{{ text_disabled }}</option>
                {% else %}
                  <option value="1">{{ text_enabled }}</option>
                  <option value="0" selected="selected">{{ text_disabled }}</option>
                {% endif %}
              </select>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
// JavaScript inline simple pour events
$('#button-save').on('click', function() {
    $('form').submit();
});
</script>

{{ footer }}
```

**Variables Twig:**
- Passer du controller: `$data['variable'] = 'value';`
- Utiliser dans Twig: `{{ variable }}`
- Conditions: `{% if variable %}...{% endif %}`
- Boucles: `{% for item in items %}...{% endfor %}`

---

## 🌍 TRADUCTIONS (LANGUAGE)

### Structure d'un Fichier Language

```php
<?php
// admin/language/en-gb/module/my_module.php

// Heading
$_['heading_title']    = 'My Module';

// Text
$_['text_extension']   = 'Extensions';
$_['text_success']     = 'Success: You have modified My Module!';
$_['text_edit']        = 'Edit My Module';
$_['text_enabled']     = 'Enabled';
$_['text_disabled']    = 'Disabled';

// Entry (form fields)
$_['entry_name']       = 'Name';
$_['entry_status']     = 'Status';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify My Module!';
$_['error_name']       = 'Name must be between 3 and 64 characters!';
```

**Convention de nommage:**
- `heading_*` = Titres de page
- `text_*` = Textes généraux
- `entry_*` = Labels de champs de formulaire
- `button_*` = Textes de boutons
- `error_*` = Messages d'erreur
- `help_*` = Textes d'aide

**⚠️ TOUJOURS créer 2 fichiers:**
- `language/en-gb/module/my_module.php`
- `language/fr-fr/module/my_module.php`

---

## 🔐 SÉCURITÉ

### 1. Échapper les Données SQL

```php
// ✅ BON
$this->db->query("SELECT * FROM product WHERE name = '" . $this->db->escape($name) . "'");

// ⛔ MAUVAIS (SQL Injection)
$this->db->query("SELECT * FROM product WHERE name = '" . $name . "'");
```

### 2. Valider les Données POST

```php
// Dans le controller
if ($this->request->server['REQUEST_METHOD'] == 'POST') {
    // Valider
    if (!$this->validate()) {
        $this->error['warning'] = $this->language->get('error_permission');
        return;
    }
    
    // Filtrer
    $data = [
        'name' => $this->db->escape($this->request->post['name']),
        'value' => (int)$this->request->post['value'],
        'status' => isset($this->request->post['status']) ? (int)$this->request->post['status'] : 0
    ];
}

// Fonction de validation
protected function validate(): bool {
    if (!$this->user->hasPermission('modify', 'module/my_module')) {
        $this->error['warning'] = $this->language->get('error_permission');
    }
    
    if (strlen($this->request->post['name']) < 3 || strlen($this->request->post['name']) > 64) {
        $this->error['name'] = $this->language->get('error_name');
    }
    
    return !$this->error;
}
```

### 3. Permissions Admin

```php
// Vérifier permission
if (!$this->user->hasPermission('modify', 'module/my_module')) {
    $this->session->data['error'] = $this->language->get('error_permission');
    $this->response->redirect($this->url->link('marketplace/extension'));
}
```

### 4. Token CSRF (OpenCart 4)

```php
// Ajouter token aux URLs
$data['action'] = $this->url->link('module/my_module', 'user_token=' . $this->session->data['user_token']);

// Dans Twig
<form action="{{ action }}" method="post">
  <input type="hidden" name="user_token" value="{{ user_token }}" />
</form>
```

---

## 📊 DATABASE QUERIES

### Conventions

```php
// Préfixe DB_PREFIX
$this->db->query("SELECT * FROM `" . DB_PREFIX . "product`");

// Un résultat
$query = $this->db->query("SELECT * FROM product WHERE product_id = '1'");
$product = $query->row;

// Plusieurs résultats
$query = $this->db->query("SELECT * FROM product");
$products = $query->rows;

// Compter
$query = $this->db->query("SELECT COUNT(*) as total FROM product");
$total = $query->row['total'];

// Dernier ID inséré
$this->db->query("INSERT INTO product SET name = 'Test'");
$product_id = $this->db->getLastId();
```

### Performance

```php
// ⛔ MAUVAIS (N+1 queries)
$products = $this->model_catalog_product->getProducts();
foreach ($products as $product) {
    $category = $this->model_catalog_category->getCategory($product['category_id']);
}

// ✅ BON (JOIN)
$sql = "SELECT p.*, c.name as category_name 
        FROM " . DB_PREFIX . "product p 
        LEFT JOIN " . DB_PREFIX . "category c ON p.category_id = c.category_id";
```

---

## 🔧 SYSTÈME D'ÉVÉNEMENTS (EVENTS)

### Enregistrer un Event

```php
// Dans install()
$this->load->model('setting/event');
$this->model_setting_event->addEvent('my_module', 'catalog/controller/checkout/success/before', 'module/my_module/beforeCheckoutSuccess');
```

### Handler d'Event

```php
// Dans le controller
public function beforeCheckoutSuccess(&$route, &$args, &$output): void {
    // Faire quelque chose avant checkout success
    $this->log->write('Checkout success triggered');
}
```

### Supprimer un Event

```php
// Dans uninstall()
$this->load->model('setting/event');
$this->model_setting_event->deleteEventByCode('my_module');
```

---

## 📦 BONNES PRATIQUES

### 1. Réutiliser les Models Existants

```php
// ✅ BON - Utiliser model existant
$this->load->model('catalog/product');
$products = $this->model_catalog_product->getProducts();

// ⛔ MAUVAIS - Réécrire la même query
$query = $this->db->query("SELECT * FROM product");
```

### 2. Suivre les Conventions de Nommage

```php
// Fichiers: snake_case
my_module.php
product_list.twig

// Classes: PascalCase
class MyModule extends Controller

// Fonctions: camelCase
public function getProductData()

// Variables: camelCase
$productId = 123;

// DB columns: snake_case
product_id, made_in_country_id
```

### 3. Gestion d'Erreurs

```php
// Dans le controller
if (!$this->validate()) {
    $data['error_warning'] = $this->error['warning'];
    $data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
}

// Dans Twig
{% if error_warning %}
  <div class="alert alert-danger">{{ error_warning }}</div>
{% endif %}
```

### 4. Install/Uninstall Propre

```php
public function install(): void {
    // Créer tables
    $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "my_module` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;");
    
    // Ajouter events
    $this->load->model('setting/event');
    $this->model_setting_event->addEvent('my_module', 'catalog/view/*/before', 'module/my_module/eventHandler');
}

public function uninstall(): void {
    // Supprimer tables
    $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "my_module`");
    
    // Supprimer settings
    $this->load->model('setting/setting');
    $this->model_setting_setting->deleteSetting('module_my_module');
    
    // Supprimer events
    $this->load->model('setting/event');
    $this->model_setting_event->deleteEventByCode('my_module');
}
```

---

## 🚀 MIGRATION OPENCART 2.x → 4.x

### Changements Majeurs

| OpenCart 2.x | OpenCart 4.x |
|--------------|---------------|
| `$this->load->view('template')` | `return $this->load->view('template', $data)` |
| `.tpl` files | `.twig` files |
| `$data['text']` | Passer directement: `$data['text']` |
| `<?php echo $text; ?>` | `{{ text }}` |
| Pas de namespace | `namespace Opencart\Admin\Controller\Module;` |
| `token=` | `user_token=` |

### Template PHP → Twig

```php
// OpenCart 2.x (.tpl)
<?php echo $heading_title; ?>
<?php if ($error) { ?>
  <div class="error"><?php echo $error; ?></div>
<?php } ?>
<?php foreach ($items as $item) { ?>
  <div><?php echo $item['name']; ?></div>
<?php } ?>
```

```twig
{# OpenCart 4.x (.twig) #}
{{ heading_title }}
{% if error %}
  <div class="error">{{ error }}</div>
{% endif %}
{% for item in items %}
  <div>{{ item.name }}</div>
{% endfor %}
```

---

## ⚠️ WORKFLOW OBLIGATOIRE - Développement OpenCart

**CHAQUE modification OpenCart doit suivre CE workflow:**

### 📝 ÉTAPE 1: Analyse & Planification
```
[ ] Identifier composant(s) concerné(s) (Controller/Model/View/Language)
[ ] Vérifier responsabilité correcte (voir tableau MVC-L)
[ ] grep_search pour trouver fichiers existants similaires
[ ] Lire code existant pour comprendre pattern utilisé
```

### 💾 ÉTAPE 2: Backup
```
[ ] Backup TOUS les fichiers à modifier (règle DEV_GUIDELINES)
[ ] Vérifier que backup n'existe pas déjà pour cette session
```

### 🔨 ÉTAPE 3: Modification
```
[ ] Controller:
    - $this->load->model() en haut si besoin
    - $this->load->language() pour textes
    - Préparer $data pour view
    - return $this->load->view() à la fin

[ ] Model:
    - SQL queries UNIQUEMENT
    - DB_PREFIX devant tables
    - (int) cast sur IDs
    - $this->db->escape() sur strings
    - return résultats (array/bool/int)

[ ] View/Twig:
    - {{ variable }} pour afficher
    - {% if %} pour conditions simples
    - {% for %} pour boucles
    - |escape('html') sur données user
    - |escape('js') si passé au JavaScript

[ ] Language:
    - Format: $_['text_xxx'] = 'Text here';
    - 3 fichiers: en-gb, fr-fr, es-es
    - Pas de HTML (juste texte)
```

### ✅ ÉTAPE 4: Validation
```
[ ] Syntaxe PHP valide (pas d'erreur parse)
[ ] Respect MVC-L (SQL dans model, pas dans controller)
[ ] Traductions complètes (EN/FR/ES)
[ ] Pas de chemin absolu hardcodé
[ ] Pas de $_GET/$_POST direct (utiliser $this->request)
[ ] Variables passées dans $data avant view
```

### 🧪 ÉTAPE 5: Test Mental
```
[ ] "Si j'appelle cette URL, que se passe-t-il?"
[ ] "Est-ce que toutes les variables existent?"
[ ] "Est-ce que les textes sont traduisibles?"
[ ] "Est-ce sécurisé contre SQL injection?"
```

---

## ❌ ERREURS OPENCART COMMUNES

**🚨 Erreurs que les IA font souvent avec OpenCart - RELIRE avant tâche**

### 1️⃣ SQL dans le Controller
**Erreur:**
```php
// ❌ INTERDIT
public function index() {
    $sql = "SELECT * FROM " . DB_PREFIX . "product";
    $result = $this->db->query($sql);
}
```

**Correct:**
```php
// ✅ BON - Controller
public function index() {
    $this->load->model('shopmanager/product');
    $products = $this->model_shopmanager_product->getProducts();
}

// ✅ BON - Model (model/shopmanager/product.php)
public function getProducts() {
    $sql = "SELECT * FROM " . DB_PREFIX . "product";
    return $this->db->query($sql)->rows;
}
```

---

### 2️⃣ Oublier charger model/language
**Erreur:**
```php
// ❌ INTERDIT - model pas chargé
public function save() {
    $this->model_shopmanager_product->addProduct($data); // FATAL ERROR!
}
```

**Correct:**
```php
// ✅ BON - charger AVANT utiliser
public function save() {
    $this->load->model('shopmanager/product');
    $this->model_shopmanager_product->addProduct($data);
}
```

---

### 3️⃣ Logique métier dans Model
**Erreur:**
```php
// ❌ INTERDIT - calculs dans model
public function getProductPrice($product_id) {
    $product = $this->getProduct($product_id);
    if ($product['quantity'] < 5) {
        $price = $product['price'] * 0.8; // Discount logic
    }
    return $price;
}
```

**Correct:**
```php
// ✅ BON - Model retourne données brutes
public function getProduct($product_id) {
    $sql = "SELECT * FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id;
    return $this->db->query($sql)->row;
}

// ✅ BON - Controller fait la logique
public function index() {
    $this->load->model('shopmanager/product');
    $product = $this->model_shopmanager_product->getProduct($product_id);
    
    if ($product['quantity'] < 5) {
        $product['discounted_price'] = $product['price'] * 0.8;
    }
    
    $data['product'] = $product;
}
```

---

### 4️⃣ Utiliser $_GET/$_POST directement
**Erreur:**
```php
// ❌ INTERDIT
$product_id = $_GET['product_id'];
$name = $_POST['name'];
```

**Correct:**
```php
// ✅ BON
$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
$name = isset($this->request->post['name']) ? $this->request->post['name'] : '';
```

---

### 5️⃣ Oublier DB_PREFIX
**Erreur:**
```php
// ❌ INTERDIT
$sql = "SELECT * FROM product";
```

**Correct:**
```php
// ✅ BON
$sql = "SELECT * FROM " . DB_PREFIX . "product";
```

---

### 6️⃣ Chemins hardcodés
**Erreur:**
```php
// ❌ INTERDIT
require_once('/home/n7f9655/public_html/phoenixliquidation/administrator/config.php');
$image_path = '/var/www/html/image/product.jpg';
```

**Correct:**
```php
// ✅ BON
require_once(DIR_APPLICATION . 'config.php');
$image_path = DIR_IMAGE . 'catalog/product.jpg';
```

---

### 7️⃣ Passer view incorrectement
**Erreur:**
```php
// ❌ INTERDIT - OpenCart 2.x style
public function index() {
    $this->load->view('shopmanager/product_list');
}
```

**Correct:**
```php
// ✅ BON - OpenCart 4.x style
public function index() {
    $data['heading_title'] = 'Products';
    $data['products'] = $products;
    
    return $this->load->view('shopmanager/product_list', $data);
}
```

---

### 8️⃣ HTML dans fichier Language
**Erreur:**
```php
// ❌ INTERDIT
$_['text_warning'] = '<div class="alert">Warning!</div>';
```

**Correct:**
```php
// ✅ BON - texte seulement
$_['text_warning'] = 'Warning!';

// ✅ BON - HTML dans Twig
{% if text_warning %}
  <div class="alert">{{ text_warning }}</div>
{% endif %}
```

---

### 9️⃣ Oublier escape dans Twig
**Erreur:**
```twig
{# ❌ DANGEREUX - XSS possible #}
<div>{{ user_input }}</div>
<script>const msg = "{{ user_message }}";</script>
```

**Correct:**
```twig
{# ✅ BON - échappé selon contexte #}
<div>{{ user_input|escape('html') }}</div>
<script>const msg = "{{ user_message|escape('js') }}";</script>
```

---

### 🔟 Ne pas retourner dans Model
**Erreur:**
```php
// ❌ INTERDIT - pas de return
public function addProduct($data) {
    $sql = "INSERT INTO " . DB_PREFIX . "product SET name = '" . $this->db->escape($data['name']) . "'";
    $this->db->query($sql);
    // Pas de return!
}
```

**Correct:**
```php
// ✅ BON - retourner ID inséré
public function addProduct($data) {
    $sql = "INSERT INTO " . DB_PREFIX . "product SET name = '" . $this->db->escape($data['name']) . "'";
    $this->db->query($sql);
    return $this->db->getLastId();
}
```

---

## 📚 RESSOURCES OFFICIELLES

**Documentation:**
- [OpenCart Developer Guide](https://github.com/opencart/opencart/wiki/Developer-Guide)
- [Coding Standards](https://github.com/opencart/opencart/wiki/Coding-Standards)
- [Module Development](https://docs.opencart.com/en-gb/developer/module/)
- [Loading Files](https://docs.opencart.com/en-gb/developer/loading/)

**Communauté:**
- [OpenCart Forum](https://forum.opencart.com/)
- [GitHub Repository](https://github.com/opencart/opencart)
- [GitHub Issues](https://github.com/opencart/opencart/issues)

---

**Version:** 1.0  
**Date:** 2026-01-06  
**Basé sur:** OpenCart 4.x Official Documentation  
**À lire avec:** DEV_GUIDELINES.md (règles projet PhoenixLiquidation)
