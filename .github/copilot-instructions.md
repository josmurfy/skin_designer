# GitHub Copilot Instructions - PhoenixLiquidation

> **eCommerce liquidation platform built on OpenCart 4.x with custom ShopManager module for eBay integration & AI-powered automation**

## Project Architecture

### Core Framework: OpenCart 4.x (MVC-L Pattern)
```
administrator/
├── controller/shopmanager/    # Orchestration layer (NO SQL, NO HTML)
├── model/shopmanager/          # Database layer (SQL ONLY, NO logic)
├── view/
│   ├── template/shopmanager/   # Twig templates (NO logic)
│   └── javascript/shopmanager/ # Frontend JS (decentralized, autonomous)
└── language/*/shopmanager/     # i18n (EN/FR/ES)

interne/                        # Legacy standalone tools (PHP scripts)
catalog/                        # Public storefront
Lib/Ebay/                       # eBay SDK integration
```

**Critical Rule**: OpenCart's **MVC-L** must be strictly respected:
- **Controller** = Load models/views/language, coordinate flow
- **Model** = SQL queries ONLY (use `DB_PREFIX`, cast IDs with `(int)`)
- **View/Twig** = Display ONLY (pass data from controller)
- **Language** = Translations ONLY (3 languages: EN/FR/ES)

### ShopManager Module (Custom Business Logic)
Primary e-commerce management system with:
- **Product/Category CRUD** with eBay-specific fields (condition, specifics, site_id)
- **eBay API Integration** (listing sync, category mapping, OAuth token refresh)
- **AI Features** (OCR text extraction, image generation, translation)
- **Inventory Management** (barcode generation, location tracking, fast-add workflow)
- **Multi-marketplace** (eBay primary, Walmart integration in progress)

## Key Technical Patterns

### 1. JavaScript Decentralization (Production Safety Over DRY)
**Context**: Previously had centralized `tools.js` causing cascade failures  
**Current Pattern**: Each module has autonomous JS files with duplicated utility functions

```javascript
// EVERY .js file includes these at top (from tools.js):
function htmlspecialchars(str) { /* ... */ }
function htmlspecialchars_decode(str) { /* ... */ }
function ucwords(str) { /* ... */ }
// This duplication is INTENTIONAL - do not refactor to shared file
```

**Why**: Bug in shared file = entire admin panel breaks. Duplication = isolation.

### 2. Internationalization (i18n) - 3 Languages Always
```php
// Language file: administrator/language/en-gb/shopmanager/product.php
$_['text_confirm_delete'] = 'Are you sure?';

// Twig template injects as JS globals:
<script>
var TEXT_CONFIRM_DELETE = '{{ text_confirm_delete|escape('js') }}';
</script>

// JavaScript uses the variable:
if (confirm(TEXT_CONFIRM_DELETE)) { /* ... */ }
```

**Always implement**: EN (primary), FR, ES  
**Never hardcode** strings in JS/PHP - use language keys

### 3. eBay Integration Specifics
```php
// eBay category specifics stored as JSON in category_description table:
[
  "category_description" => [
    1 => ["specifics" => ["Brand" => ["Name" => "Brand", "Value" => "Sony"]]]
  ]
]

// eBay site_id determines marketplace (0=US, 2=Canada, 3=UK, 77=Germany)
// Leaf categories (leaf=1) have item specifics from eBay taxonomy
```

**Critical Files**:
- `administrator/model/shopmanager/ebay.php` - eBay API calls, token management
- `administrator/controller/shopmanager/ebay.php` - getCategorySpecifics(), searchByName()
- JS: `category_form.js` - Dynamic specifics table generation

### 4. AI Integration Points
```php
// OCR: Extract text from product images
administrator/controller/shopmanager/ocr.php
// Uses external API to extract product details from photos

// Image Generation: AI-suggested category images
administrator/controller/shopmanager/ai.php -> aiSuggestImage()
// Generates images from category descriptions

// Translation: Multi-language content generation
administrator/model/shopmanager/translate.php
```

### 5. Database Conventions
- **Prefix**: Always use `DB_PREFIX` constant (defaults to `oc_`)
- **Security**: Cast integers: `(int)$category_id`, escape strings: `$this->db->escape($name)`
- **Custom fields**: ShopManager extends OpenCart tables with columns like `ebay_category_id`, `site_id`, `leaf`, `specifics`

## Essential Developer Commands

### Yoda Bot Chat Commands
```bash
# Initialize AI session with project context
"bonjour yoda"          # Loads DEV_GUIDELINES.md, OPENCART_GUIDELINES.md, DEV_PATTERNS.md

# During development
"backup"                # Create timestamped .backup files before changes
"pattern X"             # Apply reusable code pattern from DEV_PATTERNS.md
"parfait yoda"          # Document new pattern after completing work

# End of session
"byebye"                # Cleanup debug code, remove temp files, offer archiving
```

### Backup Protocol (MANDATORY Before Edits)
```bash
# AI MUST create backups before any code modification:
cp file.php file.php.backup
cp file.js file.js.backup
# Restore: cp file.php.backup file.php
```

## Common Tasks & Examples

### Adding eBay Category Specifics
```javascript
// category_form.js - Dynamic specifics from eBay API
function getCategorySpecifics(categoryId, site_id) {
    $.ajax({
        url: 'index.php?route=shopmanager/ebay.getCategorySpecifics',
        data: { category_id: categoryId, site_id: site_id },
        success: function(json) {
            updateCategorySpecificsTable(json.data, specifics_row);
        }
    });
}
```

### OpenCart Controller Pattern
```php
// administrator/controller/shopmanager/example.php
class Example extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('shopmanager/example');      // Load translations
        $this->load->model('shopmanager/example');         // Load model
        
        $data['items'] = $this->model_shopmanager_example->getItems(); // Get data
        
        $this->response->setOutput($this->load->view('shopmanager/example', $data));
    }
}
```

### Model SQL Pattern
```php
// administrator/model/shopmanager/example.php
public function getItems(): array {
    $sql = "SELECT * FROM " . DB_PREFIX . "table WHERE id = '" . (int)$id . "'";
    return $this->db->query($sql)->rows;
}
```

## Critical Gotchas

1. **Never mix JS in Twig templates** - Move to `.js` files in `view/javascript/shopmanager/`
2. **eBay tokens expire** - Implement OAuth refresh in `model/shopmanager/ebay.php->refreshAccessToken()`
3. **Summernote WYSIWYG** - Load before accessing: Check for `.summernote` class existence
4. **Image paths** - Use `DIR_IMAGE` constant, not hardcoded `/image/`
5. **User token** - Always pass `&user_token=` in admin AJAX URLs for security

## File Lifecycle Rules

### Permanent Files (Never Delete)
```
docs/DEV_GUIDELINES.md          # Project coding standards
docs/OPENCART_GUIDELINES.md     # OpenCart-specific rules
docs/DEV_PATTERNS.md            # Reusable code solutions
docs/COMMANDS.md                # Yoda bot commands
docs/archives/*                 # Historical records
```

### Temporary Files (Delete on "byebye")
```
docs/WORKLOAD_*.md             # Current task tracking
docs/LOG_*.md                  # Session logs
docs/REPORT_*.md               # Completion reports
**/*.backup                    # Code backups (unless rollback needed)
```

## Integration Points

### eBay API
- **Auth**: OAuth 2.0 with token refresh (expires every 2 hours)
- **Endpoints**: Trading API (GetCategories, AddItem), Browse API (search)
- **Rate limits**: Respect eBay's call limits (5000/day typical)

### External Services
- **Algopix**: Product data enrichment (interne/algopix.php)
- **OCR API**: Text extraction from images (administrator/controller/shopmanager/ocr.php)
- **USPS/ShipStation**: Shipping integration (administrator/controller/shopmanager/shipping.php)

## Debugging & Logs

```php
// Enable OpenCart error reporting:
// config.php & administrator/config.php
define('DIR_LOGS', '/home/n7f9655/public_html/storage_phoenixliquidation/logs/');

// Custom logging:
$this->log->write('Debug: ' . print_r($data, true));

// Check logs:
tail -f /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log
```

## Git / GitHub Workflow

### Repository
- **Remote**: `https://github.com/josmurfy/phoenixliquidation` (private)
- **Branch**: `main`
- **Git user**: `josmurfy` / `info@phoenixsupplies.ca`

### Daily Workflow (After completing a task)
```bash
git add administrator/controller/shopmanager/ebay.php   # fichiers modifiés
git commit -m "fix(ebay): correction editPrice quand item en promotion"
git push
```

### Commit Convention
```
feat(module):   nouvelle fonctionnalité
fix(module):    correction de bug
refactor:       restructuration sans changement de comportement
style:          formatting, pas de logique
docs:           documentation seulement
```

### Restore a File (Instead of .backup)
```bash
# Annuler toutes les modifications non-commitées sur un fichier
git checkout -- administrator/controller/shopmanager/ebay.php

# Revenir à la version d'un commit précédent
git show HEAD~1:administrator/controller/shopmanager/ebay.php > ebay.php

# Voir l'historique d'un fichier
git log --oneline administrator/controller/shopmanager/ebay.php
```

### Branch for Risky Features
```bash
git checkout -b feature/walmart-integration   # nouvelle branche
# ... développement ...
git checkout main && git merge feature/walmart-integration   # si ça marche
git branch -d feature/walmart-integration                    # cleanup
```

### NO MORE .backup Files
> Git remplace entièrement les fichiers `.backup`. Ne jamais créer de `.backup` manuellement.
> Utiliser `git checkout --` pour restaurer n'importe quel fichier instantanément.

---

## When You Don't Know

1. **Search existing patterns**: `grep -r "similar_pattern" administrator/`
2. **Check DEV_PATTERNS.md**: Likely has reusable solution
3. **Reference OpenCart docs**: Core framework conventions at OpenCart.com
4. **Ask user to attach**:
   - `DEV_GUIDELINES.md` - Project-specific rules
   - `OPENCART_GUIDELINES.md` - Framework standards
   - Relevant controller/model files for context

---

**Last Updated**: 2026-03-25  
**Maintained By**: AI Agents + Human Developer Team  
**Contact**: Attach `DEV_GUIDELINES.md` to chat for full context
