# Restructuration Menu & Arborescence Warehouse

> Basé sur la lecture complète des 40 controllers actifs — chaque nom/emplacement reflète le code réel.
> **shopmanager/** reste intact (zéro risque live) — **warehouse/** = nouvelle arborescence propre avec multidb.

---


## Structure FINALE sur le serveur

> Pattern = **card/** : mêmes sous-dossiers dans controller, model, language (×3), twig, JS.
> Seul le controller est listé — model/lang/twig/js suivent le même chemin.

### 📦 product/ — Cycle de vie produit

```
controller/shopmanager/product/
├── product.php             ← catalog/product.php         — CRUD produit complet (3000+ lignes, variants, images, specifics)
├── research.php            ← catalog/product_search.php  — Recherche externe (eBay + Algopix + Google + UPC DBs + AI) pour enrichir un produit
├── specific.php            ← catalog/product_specific.php — AJAX: mapping attributs eBay (name→replacement term)
├── receive.php             ← fast_add.php                — Réception stock via barcode scan + UPC lookup + AI enrichment
├── publish.php             ← list_fast.php               — Publication rapide vers eBay (bulk listing + template pre-fill)
├── category.php            ← catalog/category.php        — CRUD catégories OC (SEO URLs, filtres, layouts, multi-store)
├── category_specific.php   ← category_specific.php       — Gestion specifics par catégorie eBay (dropdown UI, leaf categories)
├── manufacturer.php        ← manufacturer.php            — CRUD marques/fabricants + autocomplete
└── condition.php           ← condition.php               — CRUD conditions (New/Used/Refurbished) + mapping eBay
```

**Pourquoi :**
- `research.php` (pas search) : on cherche pas dans NOS produits, on _recherche_ des infos externes (eBay/Algopix/Google/UPC)
- `receive.php` (pas intake) : "recevoir du stock" — universel, clair en anglais et en français
- `publish.php` : produit prêt → publie sur marketplace (agnostique eBay/Amazon/Etsy)
- `specific.php` (pas product_specific) : c'est du mapping d'attributs, pas une fiche produit

### 🃏 card/ — Cartes

```
controller/shopmanager/card/
├── card.php                ← (inchangé)                  — CRUD / inventaire cartes (index du module)
├── listing.php             ← card_listing.php            — Listings eBay des cartes
├── manufacturer.php        ← card_manufacturer.php       — Fabricants de cartes
├── market.php              ← card_market.php             — Prix marché / comparatifs
├── set.php                 ← (À CRÉER) CRUD / affichage sets (oc_card_set) — model card_set.php existe déjà
├── search.php              ← (inchangé)                  — Recherche cartes
└── import/
    ├── card.php            ← card_importer.php           — Import CSV cartes
    ├── set.php             ← card_set_importer.php       — Import sets complets
    ├── sold.php            ← card_sold_importer.php      — Import ventes complétées
    └── active.php          ← card_price_active.php       — Import listings actifs
```

**Pourquoi :**
- `card_` redondant : on est déjà dans `card/`, pas besoin de répéter
- `_importer` redondant : on est déjà dans `import/`
- `card/card.php` garde son nom : pattern OpenCart (comme `product/product.php`)
- `sold` vs `active` : ventes passées vs listings en cours — zéro ambiguïté

### 🏪 marketplace/ — Connexions & listings marketplace

```
controller/shopmanager/marketplace/
├── connection.php          ← marketplace/marketplace.php — CRUD comptes multi-marketplace + OAuth2 (tokens, credentials)
├── listing.php             ← marketplace.php (root)      — Dashboard listings actifs (add/update/delete/getStatus/getListings)
└── ebay/
    ├── api.php             ← ebay.php (root)             — API eBay : getCategories, getCategorySpecifics, searchByName, getImageVariations
    ├── category.php        ← catalog/category_ebay.php   — Mapping catégories OC↔eBay (CRUD + leaf validation + specifics sync)
    └── sync.php            ← inventory/sync.php          — Sync bi-directionnel OC↔eBay : prix/qty/specifics/condition/catégorie/images (2000+ lignes)
```

**Pourquoi :**
- `connection.php` et `listing.php` restent au root : ils gèrent TOUS les marketplaces
- `ebay/` sous-dossier : regroupe tout ce qui est 100% spécifique eBay
- `api.php` (pas ebay.php) : on est déjà dans `ebay/`, pas besoin de répéter
- `category.php` (pas category_ebay.php) : même logique
- Futur multi-marketplace : `amazon/`, `etsy/` suivront le même pattern

### 📋 order/ — Commandes & expédition

```
controller/shopmanager/order/
├── order.php               ← order.php (root)    — Dashboard commandes + sync marketplace + status updates
└── shipping.php            ← shipping.php (root)  — Calcul frais USPS + génération labels (AJAX, lié à l'expédition de commandes)
```

**Pourquoi shipping ici :** `calculateRate()`, `getShippingMethods()`, `sendLabel()` — c'est du fulfillment de commandes, pas un outil générique.

### 📊 inventory/ — Stock physique (QUASI INCHANGÉ)

```
controller/shopmanager/inventory/
├── location.php            — (inchangé) Inventaire par emplacement physique (SKU search, qty updates, barcode scan)
├── allocation.php          — (inchangé) Assignation produit → emplacement warehouse + country of origin
└── label.php               ← print_report.php    — Impression étiquettes inventaire (QR codes, CSV export, rapports)
```

**Pourquoi label ici :** `generateReport()` produit des étiquettes QR pour l'inventaire physique — c'est un outil d'inventaire, pas de l'analytics.

### 🔧 maintenance/ — Maintenance données

```
controller/shopmanager/maintenance/
├── image.php               — (inchangé) Diagnostics images : orphelines, missing, low-res, WebP conversion, sync eBay
└── description.php         ← product_description.php — Produits avec descriptions incomplètes + suggestion AI
```

**Pourquoi :**
- `description.php` (pas product_description) : on est dans `maintenance/`, "product" est implicite

### 🛠️ tools/ — Outils & utilitaires

```
controller/shopmanager/tools/
├── ai.php                  ← ai.php (root)       — Génération AI : titres, descriptions, traductions (OpenAI)
├── ocr.php                 ← ocr.php (root)      — Extraction texte depuis images produit (OCR API)
├── google.php              ← google.php (root)    — Google Custom Search (images + texte) + Cloud Translation
├── utility.php             ← tools.php (root)     — Image cleanup, bulk uploads, CSV export, data validation (pas de UI)
└── store.php               ← opencart.php (root)  — Configuration multi-store OC (getStoreList, getSettings, testConnection)
```

**Pourquoi :**
- `google.php` garde son nom : il fait searchImage + searchText + translate, pas juste des images
- `utility.php` (pas tools.php) : tools/tools.php serait bizarre
- `store.php` (pas opencart.php) : c'est la gestion des stores OC, "store" est plus descriptif

### 🪟 popup/ — Modals AJAX (pas dans le menu)

```
controller/warehouse/popup/
├── alert.php               ← alert_popup.php             — Modal alerte admin
├── wait.php                ← wait_popup.php              — Modal loading/progress (import, sync, batch)
├── country_conflict.php    ← country_conflict_popup.php  — Résolution conflits géographiques marketplace
└── marketplace_error.php   ← marketplace_error_popup.php — Affichage erreurs API marketplace
```

### ⚙️ setting/ — Configuration warehouse (À CRÉER)

```
controller/warehouse/setting/
├── database.php            — (À CRÉER) CRUD connexions DB : host/user/pass/dbname + test connection AJAX + toggle active
└── setting.php             — (À CRÉER) Config générale warehouse (préférences, feature flags, futur: clés API)
```

**Pourquoi :**
- `database.php` : gère les connexions multi-DB (phoenixliquidation, phoenixsupplies, futures) — remplace les constantes hardcodées
- `setting.php` : config générale, extensible (futur: `api.php` pour clés eBay/Algopix/Google)
- `multidb.php` (model service) lit les settings de `database.php` au lieu de DB_SISTER_DATABASE

### 📚 Models service (sans controller — restent au root model/)

```
model/shopmanager/
├── multidb.php             ← (P1 À CRÉER) Connexion cross-DB phoenixliquidation ↔ phoenixsupplies
├── algopix.php             ← Enrichissement produit (Algopix API)
├── attribute.php           ← Attributs OpenCart
├── download.php            ← Downloads OC
├── ebaytemplate.php        ← Construction XML listing eBay
├── identifier.php          ← UPC/EAN/ISBN lookup
├── option.php              ← Options produit OC
├── recurring.php           ← Abonnements OC
├── subscription_plan.php   ← Plans OC
├── translate.php           ← Traduction AI multi-langue
├── upctmp.php              ← UPC temporaires
├── walmart.php             ← Intégration Walmart (en dev)
├── catalog/filter.php      ← Filtres catégorie OC
└── localisation/           ← Pays, poids, dimensions
```

### 📚 JS utilitaires (root JS, pas liés à un controller)

```
view/javascript/shopmanager/
├── bootstrap_helper.js     ← Helper Bootstrap global
├── chrome_debug.js         ← Debug Chrome (candidat suppression)
└── sound.js                ← Sons notification
```

---

## Menu Proposé (column_left.php)

```
🚀 Warehouse
│
├── 📦 Produits                          ← Recevoir → Gérer → Enrichir → Publier
│   ├── Réception stock            → warehouse/product/receive
│   ├── Tous les produits          → warehouse/product/product
│   ├── Recherche externe          → warehouse/product/research
│   ├── Publier sur marketplace    → warehouse/product/publish
│   ├── Catégories ▸
│   │   ├── Catégories             → warehouse/product/category
│   │   └── Specifics catégorie    → warehouse/product/category_specific
│   ├── Fabricants                 → warehouse/product/manufacturer
│   └── Conditions                 → warehouse/product/condition
│
├── 🃏 Cartes                            ← Inventaire → Sets → Listings → Recherche → Import
│   ├── Inventaire cartes          → warehouse/card/card
│   ├── Sets                       → warehouse/card/set
│   ├── Listings cartes            → warehouse/card/listing
│   ├── Marché / Prix              → warehouse/card/market
│   ├── Recherche                  → warehouse/card/search
│   ├── Fabricants cartes          → warehouse/card/manufacturer
│   └── Import ▸
│       ├── Importer cartes        → warehouse/card/import/card
│       ├── Importer sets          → warehouse/card/import/set
│       ├── Importer ventes        → warehouse/card/import/sold
│       └── Listings actifs        → warehouse/card/import/active
│
├── 🏪 Marketplace                       ← Connexion → Listings → eBay tools
│   ├── Connexions & comptes       → warehouse/marketplace/connection
│   ├── Listings actifs            → warehouse/marketplace/listing
│   └── eBay ▸
│       ├── Mapping catégories     → warehouse/marketplace/ebay/category
│       ├── Sync OC↔eBay          → warehouse/marketplace/ebay/sync
│       └── Explorer API eBay      → warehouse/marketplace/ebay/api
│
├── 📋 Commandes                         ← Traiter → Expédier
│   ├── Traiter commandes          → warehouse/order/order
│   └── Expédition / USPS          → warehouse/order/shipping
│
├── 📊 Inventaire                        ← Localiser → Assigner → Étiqueter
│   ├── Localisations              → warehouse/inventory/location
│   ├── Allocation                 → warehouse/inventory/allocation
│   └── Étiquettes                 → warehouse/inventory/label
│
├── 🛠️ Outils                           ← AI → OCR → Google → Utilitaires → Config
│   ├── AI Content                 → warehouse/tools/ai
│   ├── OCR Images                 → warehouse/tools/ocr
│   ├── Google Search              → warehouse/tools/google
│   ├── Upload / Utility           → warehouse/tools/utility
│   └── Config Stores              → warehouse/tools/store
│
└── 🔧 Maintenance                      ← Rarement utilisé
    ├── Images                     → warehouse/maintenance/image
    └── Descriptions produits      → warehouse/maintenance/description
│
└── ⚙️ Configuration                    ← Admin / fondation
    ├── Bases de données           → warehouse/setting/database
    └── Paramètres                 → warehouse/setting/setting
```

---

## Résumé des déplacements

### Fichiers qui BOUGENT (34 controllers)
| Actuel | → Proposé | Raison |
|--------|-----------|--------|
| `catalog/product.php` | `product/product.php` | catalog→product |
| `catalog/product_search.php` | `product/research.php` | recherche externe, pas interne |
| `catalog/product_specific.php` | `product/specific.php` | simplifié |
| `catalog/category.php` | `product/category.php` | catalog→product |
| `catalog/category_ebay.php` | `marketplace/ebay/category.php` | sous-dossier ebay/ |
| `category_specific.php` (root) | `product/category_specific.php` | root→product |
| `fast_add.php` | `product/receive.php` | réception stock |
| `list_fast.php` | `product/publish.php` | renommé |
| `manufacturer.php` | `product/manufacturer.php` | root→product |
| `condition.php` | `product/condition.php` | root→product |
| `marketplace/marketplace.php` | `marketplace/connection.php` | renommé (OAuth/tokens/credentials) |
| `marketplace.php` (root) | `marketplace/listing.php` | renommé (listing dashboard) |
| `ebay.php` | `marketplace/ebay/api.php` | sous-dossier ebay/ |
| `inventory/sync.php` | `marketplace/ebay/sync.php` | sous-dossier ebay/ |
| `order.php` | `order/order.php` | root→order |
| `shipping.php` | `order/shipping.php` | root→order (fulfillment) |
| `print_report.php` | `inventory/label.php` | renommé (étiquettes QR inventaire) |
| `ai.php` | `tools/ai.php` | root→tools |
| `ocr.php` | `tools/ocr.php` | root→tools |
| `google.php` | `tools/google.php` | root→tools |
| `tools.php` | `tools/utility.php` | renommé (tools/tools.php serait bizarre) |
| `opencart.php` | `tools/store.php` | renommé (config multi-store) |
| `alert_popup.php` | `popup/alert.php` | root→popup |
| `wait_popup.php` | `popup/wait.php` | root→popup |
| `country_conflict_popup.php` | `popup/country_conflict.php` | root→popup |
| `marketplace_error_popup.php` | `popup/marketplace_error.php` | root→popup |
| `card/card_listing.php` | `card/listing.php` | préfixe card_ redondant |
| `card/card_manufacturer.php` | `card/manufacturer.php` | préfixe card_ redondant |
| `card/card_market.php` | `card/market.php` | préfixe card_ redondant |
| `card/import/card_importer.php` | `card/import/card.php` | préfixe + suffixe redondants |
| `card/import/card_set_importer.php` | `card/import/set.php` | préfixe + suffixe redondants |
| `card/import/card_sold_importer.php` | `card/import/sold.php` | préfixe + suffixe redondants |
| `card/import/card_price_active.php` | `card/import/active.php` | préfixe redondant |

### Fichiers qui NE BOUGENT PAS (5)
| Fichier | Raison |
|---------|--------|
| `card/card.php` | Index du module, garde son nom ✅ |
| `card/search.php` | Déjà clean ✅ |
| `inventory/location.php` | Déjà en place ✅ |
| `inventory/allocation.php` | Déjà en place ✅ |
| `maintenance/image.php` | Déjà en place ✅ |
| `maintenance/product_description.php` | `maintenance/description.php` | préfixe product_ redondant |

### À archiver (.old)
- `inventory.php` (root) → doublon de `inventory/location.php`

---

## Statut migration

### ✅ PHASE 1 COMPLÉTÉE — Migration shopmanager/ → warehouse/ (2026-04-15)

**Script**: `/tmp/migrate_to_warehouse.sh` — copie + rename + namespace transform
**Résultat**: 303 fichiers migrés, 0 référence shopmanager restante (hors comments/vars)

| Couche | Quantité | Statut |
|--------|----------|--------|
| Controllers | 40 | ✅ Namespaces + paths |
| Models | 48 | ✅ Namespaces + paths + vars |
| Twig | 77 | ✅ Routes + paths |
| JavaScript | 39 | ✅ Routes |
| Language EN/FR/ES | 33 × 3 = 99 | ✅ Paths |
| **Total** | **303** | ✅ |

**Transformations appliquées :**
- `namespace Opencart\Admin\Controller\Shopmanager\*` → `Warehouse\*` (avec sous-namespaces corrigés)
- `$this->load->model/view/language/controller('shopmanager/...')` → `warehouse/...`
- `$this->model_shopmanager_*->` → `$this->model_warehouse_*->`
- `route=shopmanager/...` → `route=warehouse/...`
- Fichiers data copiés (RateWS.wsdl, install_multivariation.sql)
- **shopmanager/ = INTACT** — zéro modification

### ⏳ PHASE 2 — À faire

- [ ] Créer `setting/database.php` (controller + model + lang + twig) — CRUD connexions DB + test AJAX
- [ ] Créer `setting/setting.php` (controller + model + lang + twig) — Config générale warehouse
- [ ] Créer `multidb.php` (model service) — lit les settings de database.php au lieu de DB_SISTER_DATABASE
- [ ] Créer `card/set.php` (controller) — CRUD sets (model card_set.php existe déjà)
- [ ] Mettre à jour `column_left.php` pour ajouter le menu Warehouse
- [ ] Ajouter permissions warehouse/* dans user_group
- [ ] Tests fonctionnels par section

---

*Créé le 2026-04-14 — Mis à jour le 2026-04-15 — Basé sur lecture complète des 40 controllers*
*Companion de WORKLOAD_unified_shopmanager.md*
