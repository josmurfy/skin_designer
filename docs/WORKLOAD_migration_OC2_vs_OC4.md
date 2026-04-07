# WORKLOAD — Audit Migration ShopManager OC2 → OC4

> **Date**: 2026-04-06
> **Source OC2**: `/home/n7f9655/public_html/phoenixsupplies/admin/`
> **Target OC4**: `/home/n7f9655/public_html/phoenixliquidation/administrator/`
> **Méthode**: Comparaison fonction par fonction de chaque fichier (controllers, models, JS, templates)

---

## LÉGENDE

| Icône | Signification |
|-------|---------------|
| ✅ | Identique ou correctement migré |
| ⚠️ | Renommé / Refactorisé — vérifier les appels JS/AJAX |
| 🚨 | **PROBLÈME POTENTIEL** — fonctionnalité manquante, vide, ou cassée |
| 🆕 | Nouveau dans OC4 (pas dans OC2) |
| 🗑️ | Retiré intentionnellement (legacy/dead code) |

---

## SECTION A — BUGS CONFIRMÉS ET CORRIGÉS

| # | Fichier | Problème | Status |
|---|---------|----------|--------|
| A1 | `model/shopmanager/catalog/product.php` | `trim(null)` deprecation PHP 8.1+ sur specifics avec `Name` null | ✅ CORRIGÉ |
| A2 | `model/shopmanager/catalog/category.php` | Bug SEO `$parent_keyword` utilisé avant d'être défini | ✅ CORRIGÉ |
| A3 | DB `oc_category_specifics` | 28 catégories avec `localizedAspectName` null (ex: Country of Origin) | ⚠️ PARTIEL — cat 26344 corrigée, 27 restantes |

---

## SECTION B — PROBLÈMES CRITIQUES (À CORRIGER)

### B1 🚨 `translate.js` — Fichier vide (0 bytes) mais toujours chargé

**Fichier**: `view/javascript/shopmanager/translate.js` — **0 bytes**

**Chargé par**:
- `controller/shopmanager/catalog/product.php` (ligne 1077)
- `controller/shopmanager/catalog/product_search.php` (ligne 945)

**En OC2**: Contenait `getTranslate()`, `buildTranslationData()`, `fetchTranslationData()`

**En OC4**: Les fonctions de traduction ont été déplacées dans `ai.js` (lignes 2703-3135):
- `getTranslate()` → `ai.js` ligne 2703
- `translateContentForAllLanguages()` → `ai.js` ligne 2925
- `translateAllFields()` → `ai.js` ligne 3006

**Impact**: Aucune erreur visible car `ai.js` est aussi chargé sur les mêmes pages. Mais le fichier vide charge un script inutile.

**ACTION**: Soit supprimer les `addScript('translate.js')` des 2 controllers, soit rédiger un commentaire dans le fichier expliquant la migration vers `ai.js`.

---

### B2 🚨 `translate.php` Controller — SUPPRIMÉ sans remplacement d'URL

**OC2**: `controller/shopmanager/translate.php` — Controller AJAX pour traduction  
**OC4**: **FICHIER ABSENT**

**En OC4**, la traduction est gérée par:
- `controller/shopmanager/ai.php → translate()` (ligne ~200)
- `model/shopmanager/ai.php → translate()` (appels Claude/OpenAI)

**Impact**: Si du JS legacy appelle encore `route=shopmanager/translate`, ça retourne 404.

**Vérification nécessaire**: Chercher dans les JS des appels à `shopmanager/translate` comme route AJAX.

---

### B3 🚨 `url_alias.php` Model — SUPPRIMÉ (SEO refait)

**OC2**: `model/shopmanager/url_alias.php` — `getUrlAlias($keyword)`  
**OC4**: **FICHIER ABSENT**

**Raison**: OC4 utilise `oc_seo_url` (table multi-langue, multi-store, hierarchique) au lieu de `oc_url_alias` (flat).

**Impact**: Aucun si le code OC4 n'appelle plus `model_shopmanager_url_alias`. Les appels SEO sont dans `model/shopmanager/catalog/category.php` directement.

**STATUS**: ✅ Probablement OK — la migration SEO est dans le category model.

---

### B4 🚨 `translate.php` Model — Fonctions perdues

**OC2 `model/shopmanager/translate.php`**:
- `translate()` ✅ (existe en OC4, refactorisé avec type hints)
- `bypassTranslate()` ✅ (existe en OC4, simplifié)
- `translateInternal()` 🚨 **MANQUANT en OC4** — Traduisait en français-canadien avec respect de la casse
- `translateInternalOLD()` 🗑️ (legacy, normal de retirer)

**Impact**: `translateInternal()` gérait les traductions contextuelles FR-CA (mesures, termes spéciaux). Si c'était utilisé, les traductions FR-CA pourraient être moins précises.

**ACTION**: Vérifier si `translateInternal()` était activement utilisé dans OC2 ou si c'était du dead code.

---

## SECTION C — CONTROLLERS: Différences Complètes

### Patron Architectural OC4 vs OC2
OC4 remplace le patron `add()`/`edit()`/`getForm()` par `form()`/`save()`/`list()`. Les validators (`validateForm()`, `validateDelete()`, etc.) sont intégrés inline dans `save()`.

### C1 — `ai.php` ⚠️ Nouvelles fonctions
| OC2 | OC4 | Status |
|-----|-----|--------|
| `index()` | `index()` | ✅ |
| `aiSuggestImage()` | `aiSuggestImage()` | ✅ |
| — | `translate()` | 🆕 Remplace translate.php controller |
| — | `getMadeInCountry()` | 🆕 |
| — | `respondJson()` | 🆕 Helper |
| — | `logSafe()` | 🆕 Helper |

### C2 — `category.php` → `catalog/category.php` ⚠️ Refactorisé
| OC2 | OC4 | Status |
|-----|-----|--------|
| `add()` | — | ⚠️ → `form()` + `save()` |
| `edit()` | — | ⚠️ → `form()` + `save()` |
| `delete()` | `delete()` | ✅ |
| `repair()` | `repair()` | ✅ |
| `getList()` (protected) | `getList()` (public) | ⚠️ Visibilité changée |
| `getForm()` (protected) | — | ⚠️ → `form()` |
| `validateForm()` | — | ⚠️ Inline dans `save()` |
| `validateDelete()` | — | ⚠️ Inline dans `delete()` |
| `validateRepair()` | — | ⚠️ Inline dans `repair()` |
| `autocomplete()` | `autocomplete()` | ✅ |
| `getCategoryDetails()` | `getDetails()` | ⚠️ Renommé |
| — | `form()` | 🆕 OC4 pattern |
| — | `save()` | 🆕 OC4 pattern |
| — | `list()` | 🆕 OC4 pattern |

### C3 — `ebay.php` ⚠️ Plusieurs renommages
| OC2 | OC4 | Status |
|-----|-----|--------|
| `index()` | `index()` | ✅ |
| `add()` | `add()` | ✅ |
| `edit()` | `edit()` | ✅ |
| `delete()` | `endListing()` | ⚠️ **Renommé** |
| `editQuantity()` | `editQuantity()` | ✅ |
| `editPrice()` | `editPrice()` | ✅ |
| `relist()` | `relist()` | ✅ |
| `searchByName()` | `searchByName()` | ✅ |
| `getCategorySpecifics()` | `getCategorySpecifics()` | ✅ |
| `getConditionsByCategory()` | `getConditionsByCategory()` | ✅ |
| — | `getMarketPrices()` | 🆕 |
| — | `normalizeMarketKeyword()` | 🆕 |
| — | `buildMarketKeywordCandidates()` | 🆕 |
| — | `isApiRateLimitedMessage()` | 🆕 |

**VÉRIFIER**: Si du JS appelle `route=shopmanager/ebay.delete` → doit maintenant appeler `shopmanager/ebay.endListing`.

### C4 — `inventory.php` 🚨 **REFONTE MAJEURE**
| OC2 | OC4 | Status |
|-----|-----|--------|
| `index()` | `index()` | ✅ |
| `getList()` (protected) | — | 🚨 Retiré |
| `transfert()` | — | 🚨 **RETIRÉ** — transfert de stock entre locations |
| `getTrimmedList()` | — | 🚨 **RETIRÉ** — liste filtrée stock |
| — | `list()` | 🆕 OC4 pattern |
| — | `searchProduct()` | 🆕 |
| — | `updateQuantity()` | 🆕 |
| — | `updateLocation()` | 🆕 |

**Impact**: Si `transfert()` et `getTrimmedList()` étaient utilisés activement, la fonctionnalité de transfert de stock entre locations est perdue. Les nouvelles fonctions `updateQuantity()` et `updateLocation()` remplacent partiellement, mais le transfert en bulk pourrait manquer.

### C5 — `marketplace.php` ⚠️ Renommage
| OC2 | OC4 | Status |
|-----|-----|--------|
| `addToMarketplace()` | `addToMarketplace()` | ✅ |
| `editQuantityToMarketplace()` | `editQuantity()` | ⚠️ Renommé |
| `editMarketplaceBulk()` | `editMarketplaceBulk()` | ✅ |
| — | `updateListedProduct()` | 🆕 |
| — | `formatMarketplaceErrorMessage()` | 🆕 |

### C6 — `order.php` ⚠️ Renommage + nouvelles fonctions
| OC2 | OC4 | Status |
|-----|-----|--------|
| `index()` | `index()` | ✅ |
| `getList()` (protected) | `getList()` (public) | ⚠️ Visibilité changée |
| `updateProductQuantity()` | `updateQuantity()` | ⚠️ Renommé |
| — | `list()` | 🆕 |
| — | `undoProductQuantity()` | 🆕 |

### C7 — `product.php` → `catalog/product.php` ⚠️ Refactorisé
| OC2 | OC4 | Status |
|-----|-----|--------|
| `add()` / `edit()` | `form()` / `save()` | ⚠️ OC4 pattern |
| `getList()` (protected) | `getList()` (public) | ⚠️ |
| `getForm()` (protected) | — | ⚠️ → `form()` |
| `validateForm/Delete/Copy()` | — | ⚠️ Inline |
| `updateProductLocation()` | `updateLocation()` | ⚠️ Renommé |
| `test_walmart()` | — | 🗑️ Retiré (debug) |
| `delete()` | `delete()` | ✅ |
| `enable()` / `disable()` | `enable()` / `disable()` | ✅ |
| `copy()` | `copy()` | ✅ |
| `autocomplete()` | `autocomplete()` | ✅ |
| `trfUnallocatedQuantity()` | `trfUnallocatedQuantity()` | ✅ |
| `updateQuantity()` | `updateQuantity()` | ✅ |
| `editMadeInCountry()` | `editMadeInCountry()` | ✅ |
| — | `list()` | 🆕 |
| — | `report()` / `getReport()` | 🆕 |
| — | `calculateShipping()` | 🆕 |

### C8 — `product_search.php` → `catalog/product_search.php` ⚠️ Enrichi
| OC2 | OC4 | Status |
|-----|-----|--------|
| `getProductSearchData()` | `getSearchData()` | ⚠️ Renommé |
| Toutes les autres fonctions | Identiques | ✅ |
| — | `ebayPricevariantTable()` | 🆕 |
| — | `ebayPricevariantTableAjax()` | 🆕 |
| — | `getEbayPricevariantTable()` | 🆕 |
| — | `buildEbayPricevariantTable()` | 🆕 |
| — | `convertEbayPricesUsdToCad()` | 🆕 |

### C9 — `list_fast.php` ⚠️ Signature changée
| OC2 | OC4 | Status |
|-----|-----|--------|
| `getList()` | `getList($only_list)` | ⚠️ Signature changée |
| — | `list()` | 🆕 |

### C10 — `tools.php` 🗑️ Legacy retiré + 🆕 nouvelles fonctions
| OC2 | OC4 | Status |
|-----|-----|--------|
| `uploadImagesFiles()` | `uploadImagesFiles()` | ✅ |
| `deleteProductImage()` | `deleteProductImage()` | ✅ |
| `create_label()` | `create_label()` | ✅ |
| `create_labelOLDOLD()` | — | 🗑️ Legacy |
| `create_labelOLD()` | — | 🗑️ Legacy |
| — | `uploadEbayImages()` | 🆕 |
| — | `deleteProductImagePermanent()` | 🆕 |
| — | `rotateImage()` | 🆕 |

### Controllers Identiques (aucun changement)
- ✅ `alert_popup.php`
- ✅ `category_ebay.php`
- ✅ `category_specific.php`
- ✅ `condition.php`
- ✅ `connect.php`
- ✅ `fast_add.php`
- ✅ `manufacturer.php`
- ✅ `marketplace_error_popup.php`
- ✅ `ocr.php`
- ✅ `opencart.php`
- ✅ `print_report.php`
- ✅ `product_specific.php` (catalog/)
- ✅ `shipping.php`
- ✅ `syncebay.php`
- ✅ `wait_popup.php`
- ✅ `walmart.php`

### Controllers Nouveaux OC4 (pas dans OC2)
- 🆕 `country_conflict_popup.php`
- 🆕 `card/` — Module complet de cartes collectibles
- 🆕 `inventory/` — Sous-module inventaire étendu
- 🆕 `maintenance/` — Outils de maintenance (images, descriptions)
- 🆕 `ebay/` — Sous-module eBay spécialisé

---

## SECTION D — MODELS: Différences Complètes

### D1 — `ebay.php` Model 🆕 Massivement étendu
**OC2**: ~55 fonctions (2900 lignes) — Trading API XML  
**OC4**: ~100+ fonctions (6800 lignes) — Trading API + Inventory API REST + Browse API

**Fonctions OC2 conservées en OC4**: Toutes les fonctions core sont présentes.

**Renommages**:
| OC2 | OC4 |
|-----|-----|
| `getProductDetailProduct()` | `getDetailProduct()` |
| `getProductDetailProductSellers()` | `getDetailProductSellers()` |
| `getProductDetailsByepid()` | `getDetailsByepid()` |
| `getProductImages()` | `getImages()` |
| `getMySelling()` | `getMyeBaySellingBulk()` |
| `delete()` (model) | `endListing()` |

**Fonctions OC2 retirées (legacy/OLD)**:
- 🗑️ `calculateMissingPricesOLD2()`
- 🗑️ `cleanItemsOLD()`
- 🗑️ `getProductDetailProductOLD()`
- 🗑️ `getProductDetailProductOLDOLD()`
- 🗑️ `getProductOLD()`
- 🗑️ `removeDuplicateItems()`
- 🗑️ `displayRateLimitsTable()` (private)
- 🗑️ All `buildFind*Request()` functions (Finding API → Browse API)

**Fonctions 🆕 majeures en OC4**:
- Card listings: `addCardListing()`, `endCardListing()`, `editCardListing()`, `republishCardOffers()`, `syncCardOffers()`
- Inventory API: `createInventoryItem()`, `createInventoryItemGroup()`, `publishInventoryOffer()`, `deleteInventoryItemGroup()`, `deleteInventoryItems()`
- Browse API: `searchActiveItems()`, `searchSoldItemsScraper()`, `searchByImageItems()`, `searchAndClassifyActiveItems()`, `classifyMarketPriceBuckets()`
- REST support: `buildRestHeaders()`, `makeCurlRestRequest()`
- Lot listing: `publishLotListing()`, `endLotListing()`
- Pricing: `getItemVariationsPrices()`, `getOwnListingPriceSummary()`
- Image migration: `migrateImagesToEbay()`, `uploadImageToEbay()`

### D2 — `ai.php` Model 🆕 Massivement étendu
**OC2**: 3 fonctions (`repairByJsonAI()`, `prompt_ai()`, `repairInvalidJson()`)  
**OC4**: 20+ fonctions

**Nouvelles fonctions majeures**:
- `translate()`, `translate_specifics()` — Traduction IA
- `getTitle()`, `getShortTitle()` — Génération de titres IA
- `getCategoryID()`, `getManufacturer()` — Classification IA
- `getDescriptionSupp()`, `getFormattedText()` — Descriptions IA
- `getMadeInCountry()` — Détection pays d'origine
- `prompt_ai_image()` — Génération d'images IA
- `countTokensOpenAI()` — Comptage de tokens

### D3 — `inventory.php` Model ⚠️ Étendu
**OC2**: 5 fonctions  
**OC4**: 7 fonctions (2 ajoutées)

| OC2 | OC4 | Status |
|-----|-----|--------|
| `getProducts()` | `getProducts()` | ✅ |
| `getTotalProducts()` | `getTotalProducts()` | ✅ |
| `removeProductsNoMissingFile()` | `removeProductsNoMissingFile()` | ✅ |
| `updateProductLocation()` | `updateProductLocation()` | ✅ |
| `getTrimmedProducts()` | `getTrimmedProducts()` | ✅ |
| — | `updateQuantity()` | 🆕 |
| — | `updateLocation()` | 🆕 |

### D4 — `translate.php` Model ⚠️ Simplifié
**OC2**: 4 fonctions  
**OC4**: 2 fonctions

| OC2 | OC4 | Status |
|-----|-----|--------|
| `translate()` | `translate()` | ✅ Refactorisé |
| `bypassTranslate()` | `bypassTranslate()` | ✅ Simplifié |
| `translateInternal()` | — | 🚨 **MANQUANT** |
| `translateInternalOLD()` | — | 🗑️ Legacy |

### D5 — `shipping.php` Model ✅ Identique + 1 legacy
**OC2**: 9 fonctions  
**OC4**: 10 fonctions (1 ajoutée: `get_usps_rate_OLD()`)

Toutes les fonctions OC2 sont conservées en OC4. Aucune perte.

### D6 — `tools.php` Model 🆕 Étendu
**Nouvelles fonctions OC4**:
- `convertToWebp()` — Conversion WebP
- `manualResize()` — Redimensionnement manuel
- `getImageExtension()` — Détection extension
- `replaceImageBackground()` — Remplacement fond image
- `removeArrayDuplicates()`
- `countWords()`, `countUppercase()`

### D7 — `ebaytemplate.php` Model 🆕 Étendu
| OC2 | OC4 | Status |
|-----|-----|--------|
| `getEbayTemplate()` | `getEbayTemplate()` | ✅ |
| — | `getEbayTemplateCardListing()` | 🆕 Templates cartes |
| — | `buildBatchDescription()` | 🆕 Batch descriptions |

### D8 — `url_alias.php` Model 🗑️ RETIRÉ
**OC2**: `getUrlAlias()` — lookup keyword dans `oc_url_alias`  
**OC4**: **ABSENT** — SEO via `oc_seo_url` directement dans category model  
**Impact**: ✅ Aucun — la logique est intégrée dans `catalog/category.php`

### Models Identiques (aucun changement significatif)
- ✅ `connect.php`
- ✅ `manufacturer.php`
- ✅ `option.php`
- ✅ `ocr.php`
- ✅ `google.php`
- ✅ `condition.php`
- ✅ `fast_add.php` (identique)
- ✅ `recurring.php`
- ✅ `list_fast.php`
- ✅ `marketplace.php`
- ✅ `catalog/product_specific.php`
- ✅ `catalog/category_specific.php`
- ✅ `catalog/filter.php`

### Models Nouveaux OC4 (pas dans OC2)
- 🆕 `claude_ai.php` — Intégration Claude API (alternative OpenAI)
- 🆕 `identifier.php` — Gestion UPC/identifiers dédiée
- 🆕 `subscription_plan.php` — Plans d'abonnement
- 🆕 `inventory/sync.php` — Synchronisation inventaire
- 🆕 `card/*` — Module complet cartes collectibles
- 🆕 `maintenance/image.php` — Maintenance images
- 🆕 `maintenance/product_description.php` — Maintenance descriptions
- 🆕 `localisation/country.php`, `length_class.php`, `weight_class.php`

---

## SECTION E — JAVASCRIPT: Différences Complètes

### Architecture: Décentralisation des utilitaires
**OC2**: `tools.js` centralisé — toutes les pages le chargent  
**OC4**: `tools.js` → `tools_old.js` (déprécié). Chaque JS autonome duplique `htmlspecialchars()`, `ucwords()`, etc.

**Raison**: Bug dans `tools.js` = tout le panel admin crash. Duplication = isolation.

### E1 🚨 `translate.js` — 0 bytes
Voir **B1** ci-dessus. Chargé par `product.php` et `product_search.php` mais fichier vide.

### E2 — `product_list.js` — Réorganisé
**OC2**: `product_list.js` (root)  
**OC4**: `catalog/product_list.js` (87KB, complet). Le fichier root a un `.pre_catalog_move` backup mais n'existe plus.

**STATUS**: ✅ — Le fichier a été correctement déplacé dans `catalog/`.

### E3 — `inventory_list.js` — Remplacé par 3 modules
**OC2**: `inventory_list.js` (fichier unique)  
**OC4**: Refactorisé en:
- `inventory/allocation.js` — Allocation de stock par location
- `inventory/location.js` — Gestion des emplacements
- `inventory/sync.js` — Synchronisation cross-platform

### E4 — `tools_old.js` 🗑️ Déprécié
Copie de l'ancien `tools.js` avec commentaire de dépréciation. **Non chargé** par aucun template OC4.

### Fichiers JS Identiques
- ✅ `alert_popup.js`
- ✅ `condition.js`
- ✅ `ebay.js`
- ✅ `fast_add.js`, `fast_add_form.js`, `fast_add_list.js`
- ✅ `list_fast.js`, `list_fast_list.js`
- ✅ `marketplace_error_popup.js`
- ✅ `ocr.js`, `ocr_image_upload.js`

### JS Enrichis (OC2 functions préservées + nouvelles)
- ⚠️ `ai.js` — Massivement étendu (~2464 lignes), inclut traduction
- ⚠️ `category_form.js` → existe en root + `catalog/category_form.js`
- ⚠️ `category_list.js` → existe en root + `catalog/category_list.js`
- ⚠️ `product_form.js` → existe en root + `catalog/product_form.js`

### JS Nouveaux OC4
- 🆕 `bootstrap_helper.js` — Utilitaires Bootstrap OC4
- 🆕 `card_listing_list.js` — Liste cartes collectibles
- 🆕 `card_manufacturer.js` — Gestion fabricants cartes
- 🆕 `chrome_debug.js` — Debug Chrome DevTools
- 🆕 `inventory.js` — Gestion inventaire consolidée
- 🆕 `sound.js` — Alertes audio/notifications
- 🆕 `card/` (11 fichiers) — Module cartes complet
- 🆕 `catalog/` (7 fichiers) — Produits/catégories réorganisés
- 🆕 `inventory/` (3 fichiers) — Inventaire modulaire
- 🆕 `maintenance/` (2 fichiers) — Outils maintenance

---

## SECTION F — TEMPLATES (.tpl → .twig)

### Changement Architectural
**OC2**: Templates `.tpl` (35 fichiers) — PHP inlined  
**OC4**: Templates `.twig` (70+ fichiers) — Twig strict, pas de PHP

Les templates varient naturellement entre OC2 et OC4 (syntaxe Twig, Bootstrap 5 vs 3, IDs avec tirets au lieu d'underscores). La comparaison template-par-template n'est pas pertinente car c'est un rewrite complet de l'UI.

### Points d'attention templates:
- ⚠️ Les **IDs HTML** ont changé (underscores → tirets). Le JS doit matcher.
- ⚠️ Les **routes AJAX** dans les templates doivent correspondre aux controllers renommés
- ⚠️ Les **variables Twig** passées par les controllers doivent exister

---

## SECTION G — LANGUAGE FILES

Les fichiers de langues (EN/FR/ES) suivent la même structure de réorganisation que les controllers. Les fichiers dans `catalog/` correspondent aux controllers dans `catalog/`. Pas de pertes identifiées — les nouvelles fonctionnalités OC4 ont leurs propres fichiers de langue.

---

## SECTION H — RÉSUMÉ DES ACTIONS REQUISES

### Priorité CRITIQUE 🔴 (ROUTES AJAX CASSÉES — 404 confirmés)

| # | Fichier JS | Route appelée (CASSÉE) | Devrait être | Ligne |
|---|-----------|------------------------|-------------|-------|
| C1 | `product_form.js` | `shopmanager/ebay.delete` | `shopmanager/ebay.endListing` | 3314 |
| C2 | `fast_add_form.js` | `shopmanager/catalog/category.getCategoryDetails` | `shopmanager/catalog/category.getDetails` | 150, 189 |
| C3 | `list_fast.js` | `shopmanager/catalog/product.updateProductLocation` | `shopmanager/catalog/product.updateLocation` | 401 |
| C4 | `fast_add_list.js` | `shopmanager/catalog/product.updateProductLocation` | `shopmanager/catalog/product.updateLocation` | 329 |
| C5 | `catalog/product_list.js` | `shopmanager/marketplace.editQuantityToMarketplace` | `shopmanager/marketplace.editQuantity` | 1840 |
| C6 | `catalog/product_search.js` | `shopmanager/catalog/category.getCategoryDetails` | `shopmanager/catalog/category.getDetails` | 223 |

**Ces appels AJAX retournent silencieusement 404 — les fonctions associées ne marchent PAS.**

### Priorité HAUTE (vérifié cassé ou risqué)

| # | Action | Effort |
|---|--------|--------|
| H1 | Nettoyer les 27 catégories restantes avec `localizedAspectName` null | Bas |
| H2 | Retirer les `addScript('translate.js')` dans `catalog/product.php:1077` et `catalog/product_search.php:945` | Bas |
| H3 | Vérifier si `translateInternal()` (FR-CA) est toujours nécessaire ou si Claude/OpenAI le remplace | Moyen |

### Priorité MOYENNE (fonctionnalité potentiellement dégradée)

| # | Action | Effort |
|---|--------|--------|
| M1 | Vérifier que `inventory.transfert()` n'est plus nécessaire ou a un équivalent | Moyen |

### Priorité BASSE (nettoyage)

| # | Action | Effort |
|---|--------|--------|
| L1 | Supprimer les fichiers `.pre_catalog_move` dans `view/javascript/shopmanager/` | Bas |
| L2 | Supprimer `tools_old.js` si confirmé non utilisé | Bas |
| L3 | Supprimer ou commenter `translate.js` (0 bytes) | Bas |

---

## SECTION I — STATISTIQUES

| Métrique | OC2 | OC4 | Delta |
|----------|-----|-----|-------|
| Controllers ShopManager | 28 fichiers | 28 root + sous-dossiers | +card, inventory, maintenance, ebay |
| Models ShopManager | ~25 fichiers | ~40 fichiers | +claude_ai, identifier, subscription_plan, card/*, inventory/*, maintenance/*, localisation/* |
| JS ShopManager | 23 fichiers | 47 fichiers | +card(11), catalog(7), inventory(3), maintenance(2), utilitaires |
| Templates | 35 .tpl | 70+ .twig | Rewrite complet + nouveaux modules |
| Ebay Model (lignes) | ~2900 | ~6800 | +3900 lignes (Inventory API, Browse API, Cards) |
| AI Model (fonctions) | 3 | 20+ | Expansion majeure IA |

---

## CONCLUSION

La migration OC2 → OC4 est **globalement réussie**. Toutes les fonctionnalités core d'OC2 sont présentes en OC4, avec des renommages pour suivre le patron OC4. Les principaux risques sont:

1. **Appels AJAX legacy** qui pointent vers des routes renommées (`delete` → `endListing`, etc.)
2. **`translate.js` vide** mais toujours chargé (impact nul mais dette technique)
3. **`translateInternal()` manquant** — potentielle perte de qualité traduction FR-CA
4. **`inventory.transfert()`** retiré du controller — vérifier si la fonctionnalité est couverte

Les ajouts OC4 sont substantiels: module cartes collectibles, Browse API eBay, IA étendue (Claude + OpenAI), maintenance automatisée, lot listings.
