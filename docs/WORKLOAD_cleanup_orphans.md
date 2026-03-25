# WORKLOAD — Nettoyage Orphelins ShopManager (phoenixliquidation)
**Généré:** 2026-03-25  
**Méthode:** Analyse statique — JS vérifié dans twig+js, Controller dans js+twig+ctrl, Model dans ctrl+model (pattern OpenCart `->fn()`)  
**Règle:** NE RIEN SUPPRIMER sans vérification manuelle — ce rapport est une base de travail

---

## LÉGENDE
- `[twig]` = appelé dans un template .twig
- `[js]` = appelé dans un autre .js
- `[ctrl]` = appelé dans un controller PHP
- `[model]` = appelé dans un autre model PHP
- `[ORPHAN]` = aucun appel détecté → candidat à la suppression (vérifier manuellement)

---

## ═══════════════════════════════════════
## JS — FONCTIONS ORPHELINES
## ═══════════════════════════════════════

### view/javascript/shopmanager/ai.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `getProductSpecificMARDE` |
| ORPHAN | `verifySpecificOLD` |
| ORPHAN | `verifySpecificOLDNEW` |
| ORPHAN | `formatCompatibleModels` |
| ORPHAN | `buildAiDataImage` |

### view/javascript/shopmanager/card/card_import_price.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `marketCellHtml` |
| ORPHAN | `fetchMarketPricesPreview` |

### view/javascript/shopmanager/card/card_listing_tab_cards.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `refreshAllMarketPrices` |
| ORPHAN | `int` *(probable faux positif — fragment de code)* |

### view/javascript/shopmanager/card_manufacturer.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `cardManufacturerDelete` |
| ORPHAN | `cardManufacturerFilter` |
| ORPHAN | `cardManufacturerResetFilter` |
| ORPHAN | `cardManufacturerValidateForm` |

### view/javascript/shopmanager/catalog/category_form.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `generateDescriptionOLD` |

### view/javascript/shopmanager/catalog/product_list.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `formatMadeinCountryid` |
| ORPHAN | `formatSpecificsOLD` |
| ORPHAN | `formatMadeinCountryidOLD` |
| ORPHAN | `destroyPreview` |

### view/javascript/shopmanager/catalog/product_search.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `updateConditionOLD` |

### view/javascript/shopmanager/ebay.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `removeFromEbay` |
| ORPHAN | `relistToEbay` |
| ORPHAN | `addToEbay` |
| ORPHAN | `handleEbayAddUIUpdateOLD` |

### view/javascript/shopmanager/inventory/allocation.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `unselectAll` |
| ORPHAN | `checkedSku` |
| ORPHAN | `filterInventory` |
| ORPHAN | `confirmDelete` |

### view/javascript/shopmanager/marketplace_error_popup.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `safeJsonParse` |
| ORPHAN | `showErrorPopupOLD` |

### view/javascript/shopmanager/ocr_image_upload.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `updateCharacterCountOLD` |

### view/javascript/shopmanager/tools_old.js
| Status | Fonction |
|--------|----------|
| ORPHAN | `fixAccents` |
| ORPHAN | `addslashesNOT_USED` |
| ORPHAN | `ucwordsNOT_USED` |
| ORPHAN | `transformDataToFormattedStringOLD` |
| ORPHAN | `cleanHTMLOLD` |

---

## ═══════════════════════════════════════
## CONTROLLER — FONCTIONS ORPHELINES
## ═══════════════════════════════════════

### controller/shopmanager/card/card.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `fetchCardMarketPrice` |

### controller/shopmanager/card/card_importer.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `installTables` |
| ORPHAN | `listSaved` |

### controller/shopmanager/card/card_listing.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `publishMultiple` |

### controller/shopmanager/category_ebay.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `getCategoryEbayDetails` |

### controller/shopmanager/inventory/allocation.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `getTrimmedList` |

### controller/shopmanager/inventory/sync.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `quickStats` |

### controller/shopmanager/marketplace.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `editMarketplaceBulk` |

### controller/shopmanager/shipping.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `create_usps_acceptance` |

### controller/shopmanager/walmart.php
| Status | Fonction |
|--------|----------|
| ORPHAN | `authorize` |

---

## ═══════════════════════════════════════
## MODEL — FONCTIONS ORPHELINES
## ═══════════════════════════════════════

### model/shopmanager/ai.php *(même contenu que claude_ai.php)*
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getTitleBAD` |
| ORPHAN | public | `translateNOT_USED` |

### model/shopmanager/attribute.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `editAttribute` |
| ORPHAN | public | `deleteAttribute` |
| ORPHAN | public | `getAttributeDescriptions` |
| ORPHAN | public | `getTotalAttributes` |
| ORPHAN | public | `getTotalAttributesByAttributeGroupId` |

### model/shopmanager/card/card_import_price.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `deleteCardPrice` |
| ORPHAN | public | `getPriceByCard` |
| ORPHAN | private | `normalizeEbaySalesForStorage` |

### model/shopmanager/card/card_listing.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `checkListingExists` |
| ORPHAN | public | `getCardListingSpecifics` |
| ORPHAN | public | `updateStatus` |
| ORPHAN | public | `getEbayItemId` |
| ORPHAN | public | `getLegacyEbayItemIdOLD` |
| ORPHAN | public | `backfillInventoryKeys` |
| ORPHAN | public | `getCardBothSidesImagePaths` |
| ORPHAN | public | `downloadAndCacheCardImages` |
| ORPHAN | public | `getSavedLotDescription` |
| ORPHAN | private | `detectSport` |

### model/shopmanager/card/card_manufacturer.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getCardManufacturers` |
| ORPHAN | public | `nameExists` |

### model/shopmanager/card/card_market.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | private | `parseItem` |
| ORPHAN | private | `getMarketplaceId` |
| ORPHAN | private | `getSiteGlobalId` |
| ORPHAN | private | `getEbayCredentials` |
| ORPHAN | private | `refreshBearerToken` |
| ORPHAN | private | `curlPost` |

### model/shopmanager/card/card_type.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getCardType` |
| ORPHAN | public | `getTotalCardTypes` |

### model/shopmanager/catalog/category.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getCategoryDEFAULT` |
| ORPHAN | public | `getCategoriesLeaf_WRONG` |
| ORPHAN | public | `editSpecificTranslation` |

### model/shopmanager/catalog/category_specific.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `editCategorySpecificTranslation` |
| ORPHAN | public | `getCategoriesWithoutSpecifics` |

### model/shopmanager/catalog/product.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getDescriptionsNOT_USED2` |
| ORPHAN | public | `getDescriptions_NOTUSED` |
| ORPHAN | public | `editProductError` |
| ORPHAN | public | `getProductsByCategoryId` |
| ORPHAN | public | `getProductsCondition_NOTUSED` |

### model/shopmanager/catalog/product_search.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `clearCache` |
| ORPHAN | public | `processSearchData` |
| ORPHAN | public | `getAllManufacturersOLD` |
| ORPHAN | public | `fetchRemoteContent` |
| ORPHAN | public | `getInfoSourcesPrice` |
| ORPHAN | public | `getSpecificsPRODUCTSEARCH` |
| ORPHAN | private | `getIdentifierValue` |

### model/shopmanager/catalog/product_specific.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getAllSpecificKeysByCategory` |

### model/shopmanager/claude_ai.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getTitleBAD` |
| ORPHAN | public | `translateNOT_USED` |

### model/shopmanager/condition.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getConditionEbay` |
| ORPHAN | public | `getConditionEbayToCategory` |
| ORPHAN | public | `getConditions` |

### model/shopmanager/connect.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getAccountConnect` |
| ORPHAN | public | `getTotalAccountConnect` |

### model/shopmanager/download.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `editDownload` |
| ORPHAN | public | `deleteDownload` |
| ORPHAN | public | `getDownloadDescriptions` |
| ORPHAN | public | `getTotalDownloads` |

### model/shopmanager/ebay.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getInventory` |
| ORPHAN | public | `findingApiCompletedItems` |
| ORPHAN | public | `findingApiActiveItemsPlusShipping` |
| ORPHAN | public | `getItemVariationsPrices` |
| ORPHAN | private | `buildRelistItemRequest` |
| ORPHAN | private | `checkEbayImage` |
| ORPHAN | private | `updateInventoryOffers` |

### model/shopmanager/ebaytemplate.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `buildBatchDescription` |
| ORPHAN | private | `generateCardListingDescription` |
| ORPHAN | private | `generateCardListingSpecifics` |
| ORPHAN | private | `generateCardListingShippingDetails` |
| ORPHAN | private | `generateEbayCardListing` |

### model/shopmanager/fast_add.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getProductOptionValue` |
| ORPHAN | public | `getProductDiscounts` |

### model/shopmanager/inventory/sync.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getOverview` |
| ORPHAN | public | `getInventoryHealth` |
| ORPHAN | public | `getSalesPerformance` |
| ORPHAN | public | `getMarketplacePerformance` |
| ORPHAN | public | `getTopProducts` |
| ORPHAN | public | `getCategoryPerformance` |
| ORPHAN | public | `getLocationAnalysis` |
| ORPHAN | public | `getTrendData` |
| ORPHAN | public | `getMarketplaceSyncStats` |
| ORPHAN | private | `checkColumnExists` |
| ORPHAN | private | `checkTableExists` |

### model/shopmanager/list_fast.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getProductOptionValue` |
| ORPHAN | public | `getProductDiscounts` |

### model/shopmanager/localisation/length_class.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `addLengthClass` |
| ORPHAN | public | `editLengthClass` |
| ORPHAN | public | `deleteLengthClass` |
| ORPHAN | public | `getLengthClass` |
| ORPHAN | public | `getLengthClassDescriptionByUnit` |
| ORPHAN | public | `getLengthClassDescriptions` |
| ORPHAN | public | `getTotalLengthClasses` |

### model/shopmanager/localisation/weight_class.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `addWeightClass` |
| ORPHAN | public | `editWeightClass` |
| ORPHAN | public | `deleteWeightClass` |
| ORPHAN | public | `getWeightClass` |
| ORPHAN | public | `getWeightClassDescriptionByUnit` |
| ORPHAN | public | `getWeightClassDescriptions` |
| ORPHAN | public | `getTotalWeightClasses` |

### model/shopmanager/maintenance/product_description.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getProductDescription` |
| ORPHAN | public | `updateSupplementalFields` |

### model/shopmanager/maintenance_image.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getValidatedCount` |
| ORPHAN | public | `updateMaintenanceData` |

### model/shopmanager/manufacturer.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getManufacturerByID` |

### model/shopmanager/marketplace.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `editMarketplaceAccount` |
| ORPHAN | public | `deleteProductMarketplaceOLD` |
| ORPHAN | public | `syncMarketplaceProductSpecifics` |
| ORPHAN | public | `getSyncJSON` |
| ORPHAN | public | `editProductSyncJSON` |

### model/shopmanager/opencart.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getTotalAccount` |
| ORPHAN | public | `getNewOrders` |
| ORPHAN | public | `get_url_contents` |

### model/shopmanager/option.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `editOption` |
| ORPHAN | public | `deleteOption` |
| ORPHAN | public | `getOptionDescriptions` |
| ORPHAN | public | `getOptionValueDescriptions` |
| ORPHAN | public | `getTotalOptions` |

### model/shopmanager/recurring.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `editRecurring` |
| ORPHAN | public | `copyRecurring` |
| ORPHAN | public | `deleteRecurring` |
| ORPHAN | public | `getTotalRecurrings` |

### model/shopmanager/shipping.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | private | `get_usps_rate_OLD` |
| ORPHAN | private | `sendPostRequest` |

### model/shopmanager/tools.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `convert_one_array_to_string` |
| ORPHAN | public | `flattenArrayEbay` |
| ORPHAN | public | `splitNamesEbay` |
| ORPHAN | public | `countUppercase` |
| ORPHAN | public | `countWords` |
| ORPHAN | public | `extractUPC` |
| ORPHAN | public | `replaceImageBackground` |
| ORPHAN | public | `getHighestResolutionImage` |
| ORPHAN | public | `clearArrayValuesAndReturnPrettyJsonOLD` |
| ORPHAN | private | `getLongestString` |
| ORPHAN | private | `convertOneArrayToString` |
| ORPHAN | private | `flattenArrayDUPLI` |

### model/shopmanager/upctmp.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getOLD` |

### model/shopmanager/walmart.php
| Status | Visibilité | Fonction |
|--------|-----------|----------|
| ORPHAN | public | `getValidAccessToken` |

---

## RÉSUMÉ

| Catégorie | Fonctions USED | Fonctions ORPHAN |
|-----------|---------------|-----------------|
| JS | ~500 | **~50** |
| Controller | ~150 | **9** |
| Model (public) | ~350 | **~90** |
| Model (private) | ~120 | **~25** |
| **TOTAL** | **~1120** | **~174** |

---

## PROCÉDURE AVANT SUPPRESSION
1. Vérifier manuellement que la fonction n'est pas appelée dynamiquement (`call_user_func`, `$$fn`, etc.)
2. Vérifier qu'elle n'est pas utilisée dans les fichiers `catalog/` (storefront)
3. `git commit` avant toute suppression de masse
4. Supprimer par petits groupes (fichier par fichier) et tester

---
*Généré par analyse statique — peut contenir des faux positifs pour les fonctions appelées dynamiquement*
