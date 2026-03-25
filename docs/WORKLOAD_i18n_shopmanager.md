# WORKLOAD — i18n Shopmanager (Multilingual OpenCart 4.x)
**Date:** 2026-03-18  
**Objectif:** Passer en revue tous les fichiers Twig et JS du module ShopManager et s'assurer que tout est internationalisé (EN / FR / ES) correctement.

---

## RÉSUMÉ DES PROBLÈMES TROUVÉS

| Priorité | Type | Détail | Statut |
|----------|------|--------|--------|
| 🔴 Critique | Fichiers lang manquants FR | 7 fichiers absents en fr-fr | ⬜ À faire |
| 🔴 Critique | Fichiers lang manquants ES | 17 fichiers absents en es-es | ⬜ À faire |
| 🔴 Critique | product.php FR: 55 clés manquantes | Formulaire produit entier non traduit en FR | ⬜ À faire |
| 🔴 Critique | fast_add.php FR: 128 clés manquantes | Fast-add non traduit en FR | ⬜ À faire |
| 🟠 Haut | fast_add_list.js: 20 strings hardcodés en FR | Admins EN/ES n'ont que du français | ⬜ À faire |
| 🟠 Haut | inventory/sync.twig: 17+ strings hardcodés EN | Aucune traduction possible | ⬜ À faire |
| 🟡 Moyen | order.php FR: 15 clés manquantes | | ⬜ À faire |
| 🟡 Moyen | ebay.php FR: 9 clés manquantes | | ⬜ À faire |
| 🟡 Moyen | product_form.twig: 6 strings hardcodés | | ⬜ À faire |
| 🟢 Bas | Clés isolées manquantes (8 fichiers) | 1 clé chacun | ⬜ À faire |
| 🟢 Bas | JS hardcodés autres (5 fichiers) | alerts/confirms | ⬜ À faire |

---

## SECTION 1 — FICHIERS LANG MANQUANTS

### 1.1 Fichiers manquants en **fr-fr**
| Fichier | Action | Statut |
|---------|--------|--------|
| `fr-fr/shopmanager/category_ebay.php` | Créer (copie traduite de en-gb) | ⬜ |
| `fr-fr/shopmanager/list_fast.php` | Créer | ⬜ |
| `fr-fr/shopmanager/marketplace_error_popup.php` | Créer | ⬜ |
| `fr-fr/shopmanager/ocr.php` | Créer | ⬜ |
| `fr-fr/shopmanager/print_report.php` | Créer | ⬜ |
| `fr-fr/shopmanager/product_search.php` | Créer | ⬜ |
| `fr-fr/shopmanager/wait_popup.php` | Créer | ⬜ |

### 1.2 Fichiers manquants en **es-es**
| Fichier | Action | Statut |
|---------|--------|--------|
| `es-es/shopmanager/alert_popup.php` | Créer | ⬜ |
| `es-es/shopmanager/catalog/category_specific.php` | Créer | ⬜ |
| `es-es/shopmanager/category_ebay.php` | Créer | ⬜ |
| `es-es/shopmanager/connect.php` | Créer | ⬜ |
| `es-es/shopmanager/ebay.php` | Créer | ⬜ |
| `es-es/shopmanager/fast_add.php` | Créer | ⬜ |
| `es-es/shopmanager/google.php` | Créer | ⬜ |
| `es-es/shopmanager/inventory.php` | Créer | ⬜ |
| `es-es/shopmanager/inventory/sync.php` | Créer | ⬜ |
| `es-es/shopmanager/list_fast.php` | Créer | ⬜ |
| `es-es/shopmanager/marketplace_error_popup.php` | Créer | ⬜ |
| `es-es/shopmanager/ocr.php` | Créer | ⬜ |
| `es-es/shopmanager/opencart.php` | Créer | ⬜ |
| `es-es/shopmanager/order.php` | Créer | ⬜ |
| `es-es/shopmanager/print_report.php` | Créer | ⬜ |
| `es-es/shopmanager/product_search.php` | Créer | ⬜ |
| `es-es/shopmanager/wait_popup.php` | Créer | ⬜ |

---

## SECTION 2 — CLÉS MANQUANTES DANS FICHIERS EXISTANTS

### 2.1 Clés manquantes dans **fr-fr** (fichiers existants)

| Fichier | Clés manquantes | Statut |
|---------|-----------------|--------|
| `fr-fr/shopmanager/product.php` | 55 clés (voir liste ci-dessous) | ⬜ |
| `fr-fr/shopmanager/fast_add.php` | 128 clés (voir liste ci-dessous) | ⬜ |
| `fr-fr/shopmanager/order.php` | 15 clés | ⬜ |
| `fr-fr/shopmanager/ebay.php` | 9 clés | ⬜ |
| `fr-fr/shopmanager/connect.php` | 4 clés | ⬜ |
| `fr-fr/shopmanager/catalog/category_specific.php` | 4 clés | ⬜ |
| `fr-fr/shopmanager/inventory.php` | 2 clés | ⬜ |
| `fr-fr/shopmanager/inventory/allocation.php` | 1 clé: `column_updated` | ⬜ |
| `fr-fr/shopmanager/inventory/location.php` | 1 clé: `column_updated` | ⬜ |
| `fr-fr/shopmanager/inventory/sync.php` | 1 clé: `column_product_id` | ⬜ |

#### product.php FR — 55 clés manquantes
```
button_check_all, button_disable, button_enable, button_feed,
button_list_on_marketplace, button_print, button_product_search,
button_relist_on_marketplace, button_remove_from_marketplace,
button_update_marketplace, column_condition_id, column_location,
column_made_in_country_id, column_marketplace_item_id, column_sources,
column_unallocated_quantity, entry__search, entry_category_id,
entry_marketplace, entry_product_id, error_category_not_leaf,
error_height, error_length, error_location, error_made_in_country_id,
error_manufacturer_id, error_shipping_cost, error_weight, error_width,
placeholder_search, placeholder_sourcecode, tab_product_search, tab_specifics,
text_category_id, text_drag_drop, text_error_listing, text_feed_all_products,
text_filter_invalid_price, text_filter_name_length, text_image_upload,
text_keyword, text_list_all_products, text_listed, text_missing_image_file,
text_name_empty, text_name_gt_80, text_no_data, text_not_listed,
text_price_negative, text_recognized_text, text_sources_error,
text_sources_not_set, text_sources_set, text_specifics_error,
text_specifics_na, text_specifics_not_set, text_specifics_set, text_url_sold
```

#### fast_add.php FR — 128 clés manquantes (total)
> La plupart sont partagées avec product.php — utiliser les mêmes traductions FR une fois faites.

### 2.2 Clés manquantes dans **es-es** (fichiers existants)

| Fichier | Clé manquante | Statut |
|---------|---------------|--------|
| `es-es/shopmanager/card/card_listing.php` | `column_location` = "Location" | ⬜ |
| `es-es/shopmanager/maintenance_image.php` | `text_maintenance` = "Maintenance" | ⬜ |
| `es-es/shopmanager/product.php` | `text_keyword` | ⬜ |

---

## SECTION 3 — STRINGS HARDCODÉS DANS TWIG

### 3.1 `inventory/sync.twig` — 17+ strings hardcodés EN
| String | Ligne approx | Remplacement suggéré |
|--------|-------------|---------------------|
| "Import ALL products data from eBay..." | title attr L16 | `{{ text_import_from_ebay_tooltip }}` |
| "Import from eBay" (bouton) | L17 | `{{ button_import_from_ebay }}` |
| "Refresh ALL from eBay..." | title L274 | `{{ text_refresh_all_tooltip }}` |
| "Edit" | title L277 | `{{ button_edit }}` |
| "Edit Product" | title L319 | `{{ text_edit_product }}` |
| "Price" (th) | L406 | `{{ column_price }}` |
| "Quantity" (th) | L407 | `{{ column_quantity }}` |
| "Specifics" (th) | L408 | `{{ column_specifics }}` |
| "Sync → eBay" | title L428,449,467 | `{{ text_sync_to_ebay }}` |
| "Sync ← eBay" | title L431,452,470 | `{{ text_sync_from_ebay }}` |
| "Product" (th) | L532 | `{{ column_name }}` |
| "eBay Item ID" (th) | L533 | `{{ column_ebay_item_id }}` |
| "Stock" (th) | L534 | `{{ column_stock }}` |
| "Price" (th) | L535 | `{{ column_price }}` |
| "Units Sold" (th) | L536 | `{{ column_units_sold }}` |
| "Last Synced" (th) | L537 | `{{ column_last_synced }}` |
| "Never synced" | span L558 | `{{ text_never_synced }}` |
| "No data available" | p L567 | `{{ text_no_data }}` |
| Statut | ⬜ |

### 3.2 `product_form.twig` — 6 strings hardcodés
| String | Remplacement | Statut |
|--------|-------------|--------|
| "Translate to all languages" (title) | `{{ text_translate_all }}` | ⬜ |
| "AI Suggestion" | `{{ text_ai_suggestion }}` | ⬜ |
| "None" (checkbox label) | `{{ text_none }}` | ⬜ |
| "All Sold" (link) | `{{ text_all_sold }}` | ⬜ |
| "View Website" (link) | `{{ text_view_website }}` | ⬜ |
| "View Website Sold" (link) | `{{ text_view_website_sold }}` | ⬜ |

### 3.3 `fast_add_form.twig` — 1 string
| String | Remplacement | Statut |
|--------|-------------|--------|
| "UPC" (label hardcodé sans lang) | `{{ entry_upc }}` | ⬜ |

---

## SECTION 4 — STRINGS HARDCODÉS DANS JS

### 4.1 `fast_add_list.js` — 20 strings FR hardcodés (CRITIQUE)
> Tous les alerts/confirms sont en français hardcodé. Nécessite:
> 1. Ajouter les clés dans les 3 fichiers lang (fast_add.php)
> 2. Injecter dans le Twig (fast_add_list.twig) comme `var TEXT_* = '{{ key|escape('js') }}';`
> 3. Remplacer les strings hardcodés par les variables JS

| String FR hardcodé | Clé suggérée |
|--------------------|-------------|
| "La quantité est 0..." | `TEXT_QTY_ZERO` |
| "La localisation ne peut pas être vide." | `TEXT_LOCATION_EMPTY` |
| "Confirmer la nouvelle localisation et le transfert ?" | `TEXT_CONFIRM_LOCATION` |
| "Erreur lors de la mise à jour : " | `TEXT_ERROR_UPDATE` |
| "Erreur lors de l'appel à l'API" | `TEXT_ERROR_API` |
| "Confirmer le transfert de quantité ?" | `TEXT_CONFIRM_QTY_TRANSFER` |
| "Erreur lors de la mise à jour." | `TEXT_ERROR_UPDATE` |
| "Veuillez entrer une quantité non allouée valide." | `TEXT_INVALID_QTY` |
| "Quantité non allouée et quantité totale mises à jour..." | `TEXT_QTY_UPDATED` |
| "Erreur lors de la mise à jour de la quantité." | `TEXT_ERROR_QTY_UPDATE` |
| + 10 autres variations | | Statut | ⬜ |

### 4.2 `inventory/sync.js` — 4 strings EN hardcodés
| String | Clé suggérée | Statut |
|--------|-------------|--------|
| "This will synchronize all products..." | `TEXT_CONFIRM_SYNC_ALL` | ⬜ |
| "Error: Sync URL not configured" | `TEXT_ERROR_SYNC_URL` | ⬜ |
| "Sync product …" | `TEXT_CONFIRM_SYNC` | ⬜ |
| "Refresh ALL data from eBay...?" | `TEXT_CONFIRM_REFRESH_ALL` | ⬜ |

### 4.3 `inventory/location.js` — 3 strings EN
| String | Clé suggérée | Statut |
|--------|-------------|--------|
| "Please select a country" | `TEXT_SELECT_COUNTRY` | ⬜ |
| "Error saving country: " | `TEXT_ERROR_SAVE_COUNTRY` | ⬜ |
| "Error saving country" | `TEXT_ERROR_SAVE_COUNTRY` | ⬜ |

### 4.4 `card_manufacturer.js` — 1 string EN
| String | Clé suggérée | Statut |
|--------|-------------|--------|
| "Please select at least one manufacturer to delete." | `TEXT_SELECT_ONE` | ⬜ |

### 4.5 `card/card_list.js` — 1 string FR
| String | Clé suggérée | Statut |
|--------|-------------|--------|
| "Erreur: " | `TEXT_ERROR` | ⬜ |

### 4.6 `fast_add_form.js` — 2 strings hardcodés
| String | Clé suggérée | Statut |
|--------|-------------|--------|
| "Please enter a valid 12 or 13 digit UPC." | `TEXT_INVALID_UPC` | ⬜ |
| "Catégorie non trouvée ou le chemin est manquant." | `TEXT_CATEGORY_NOT_FOUND` | ⬜ |

### 4.7 `product_list.js` — 3 strings
| String | Clé suggérée | Statut |
|--------|-------------|--------|
| "Succès: " (FR) | `TEXT_SUCCESS` | ⬜ |
| "Error: " (EN, x2) | `TEXT_ERROR` | ⬜ |

---

## ORDRE D'EXÉCUTION RECOMMANDÉ

1. ✅ **Analyse** (terminée)
2. ⬜ **Créer fichiers lang manquants FR** (7 fichiers)
3. ⬜ **Créer fichiers lang manquants ES** (17 fichiers)
4. ⬜ **Ajouter clés manquantes FR** (product.php 55 clés, fast_add.php 128 clés, order.php 15, ebay.php 9, etc.)
5. ⬜ **Ajouter clés manquantes ES** (3 clés isolées)
6. ⬜ **Fix Twig — inventory/sync.twig** (17 strings → variables lang)
7. ⬜ **Fix Twig — product_form.twig** (6 strings)
8. ⬜ **Fix Twig — fast_add_form.twig** (1 string)
9. ⬜ **Fix JS — fast_add_list.js** (20 strings FR)
10. ⬜ **Fix JS — inventory/sync.js** (4 strings)
11. ⬜ **Fix JS — Autres** (location, card_manufacturer, card_list, fast_add_form, product_list)
12. ⬜ **Passe 2 — Double-check complet**

---

*Généré automatiquement par audit i18n — 2026-03-18*
