# WORKLOAD — Smart Buttons : Feed/List/End Selected

**Date**: 2026-03-24  
**Scope**: `product.twig`, `product_list.twig`, `product_list.js`, language files (EN/FR/ES), `product.php` controller (mineur)

---

## Objectif

Remplacer la logique actuelle des boutons bulk (basée sur les filtres actifs) par une logique basée sur **l'état des produits sélectionnés** (checkboxes cochées).

---

## Vocabulaire / Définitions

| Terme | Signification dans le codebase |
|---|---|
| **listed** | `marketplace_item_id` existe ET icône verte (`_green-` dans le `src` de l'img) |
| **fed** | `has_sources_flag == 1` → bouton RSS est vert (btn-success) dans la colonne "Specifics" |

---

## Matrice des cas → bouton affiché

| État des produits sélectionnés | Bouton à afficher | Fonction JS appelée |
|---|---|---|
| Tous listed + **aucun** fed | **Feed Selected** *(ex Feed All Products)* | `handleFeedList()` |
| Tous listed + **certains** fed *(au moins 1)* | **End Selected** *(NOUVEAU)* | `handleEndList()` |
| Aucun listed + aucun fed | **Feed Selected** *(ex Feed All Products)* | `handleFeedList()` |
| Pas listed + **tous** fed | **List Selected** *(ex List All Products)* | `handleAddList()` |
| Sélection vide ou cas mixte | aucun des trois (masqués) | — |

> **Note** : les boutons `button-update-selected-marketplace` (Update) et `editQuantityToMarketplaceBulk` (Qty) ne sont PAS touchés.

---

## Analyse des fonctions existantes — IMPACT & SÉCURITÉ

### ✅ Fonctions RÉUTILISÉES sans modification

| Fonction | Fichier | Usage actuel | Plan |
|---|---|---|---|
| `handleFeedList(productId?)` | `product_list.js:1391` | Feed un ou plusieurs produits (sources). Appelée par `handleFeed()` (single) et par le bouton Feed All | **Aucun changement** – juste l'appelant change |
| `handleAddList()` *(ex `handleList`)* | `product_list.js:1348` | Simule le clic sur tous les liens `addToMarketplace` non-listés de la page | **Renommé** – ✅ fait |
| `ebay.php::delete()` | `controller/shopmanager/ebay.php:97` | End Listing eBay d'un produit + reset `marketplace_item_id` en DB | **Aucun changement** – sera appelé en boucle par la NEW fonction JS |
| `updateSelectedMarketplaceListings()` | `product_list.js:1241` | Update les listings existants (bouton Update) | **Aucun changement** |

### 🔧 Fonctions MODIFIÉES (avec impact)

| Fonction | Fichier | Modification | Utilisée ailleurs ? |
|---|---|---|---|
| `updateListFeedButton()` | `product_list.js:6` | Réécrire : au lieu de lire `filter_specifics`/`filter_sources`, analyser les checkboxes cochées + l'état DOM de chaque ligne | ✅ Appelée dans `product.twig:350` (callback AJAX filtre) → reste compatible car signature inchangée |

### 🆕 Fonctions à CRÉER

| Fonction | Fichier | Description |
|---|---|---|
| `handleEndList()` *(ex `endSelectedListings`)* | `product_list.js` | Itère les checkboxes sélectionnées, récupère le `marketplace_item_id` de chaque product row (via hidden input ou img `onclick`), appelle `shopmanager/ebay.delete` pour chaque, met à jour l'icône via `setMarketplaceListingState()` |

---

## Fichiers modifiés

### 1. `administrator/language/en-gb/shopmanager/product.php`
**Ajouter** :
```php
$_['text_end_selected']    = 'End Selected';
$_['button_end_selected']  = 'End Selected';
```

### 2. `administrator/language/fr-fr/shopmanager/product.php`
**Ajouter** :
```php
$_['text_end_selected']    = 'Terminer Sélectionnés';
$_['button_end_selected']  = 'Terminer Sélectionnés';
```

### 3. `administrator/language/es-es/shopmanager/product.php`
**Ajouter** :
```php
$_['text_end_selected']    = 'Terminar Seleccionados';
$_['button_end_selected']  = 'Terminar Seleccionados';
```

### 4. `administrator/view/template/shopmanager/product.twig`

**4a. Ajouter le bouton `#button-end-selected`** (masqué par défaut) dans la zone des boutons header (après `#button-list-feed`) :
```twig
<button type="button" id="button-end-selected" class="btn btn-danger d-none" onclick="handleEndList();" data-bs-toggle="tooltip" title="{{ text_end_selected }}">
  <i class="fa-solid fa-stop"></i> {{ text_end_selected }}
</button>
```

**4b. Passer `#button-list-feed` en `d-none` par défaut** (sa visibilité sera gérée par JS) :  
Ajouter `d-none` à la classe du bouton existant.

**4c. Ajouter le JS global** `TEXT_END_SELECTED` dans le bloc `<script>` en bas :
```javascript
var TEXT_END_SELECTED = '{{ text_end_selected|default("End Selected")|escape('js') }}';
```

**4d. Brancher l'événement checkbox** sur `updateListFeedButton()` :
```javascript
$(document).on('change', "input[name^='selected']", function() {
    if (typeof window.updateListFeedButton === 'function') {
        window.updateListFeedButton();
    }
});
// Aussi sur "select all"
$(document).on('change', "input[type='checkbox'][onclick*='selected']", function() {
    setTimeout(function() {
        if (typeof window.updateListFeedButton === 'function') {
            window.updateListFeedButton();
        }
    }, 50);
});
```

### 5. `administrator/view/javascript/shopmanager/product_list.js`

**5a. Réécrire `updateListFeedButton()`** :

```javascript
function updateListFeedButton() {
    var listFeedBtn  = document.getElementById('button-list-feed');
    var endBtn       = document.getElementById('button-end-selected');
    if (!listFeedBtn) return;

    // Récupérer les IDs des produits sélectionnés
    var selectedIds = [];
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(cb) {
        if (cb.value) selectedIds.push(cb.value);
    });

    // Masquer tous les boutons par défaut
    listFeedBtn.classList.add('d-none');
    if (endBtn) endBtn.classList.add('d-none');

    if (selectedIds.length === 0) return; // rien de sélectionné

    var allListed  = true;  // tous ont icône verte
    var noneListed = true;  // aucun n'a icône verte
    var anyFed     = false; // au moins 1 a has_sources = 1 (btn-success RSS)
    var allFed     = true;  // tous ont has_sources = 1

    selectedIds.forEach(function(pid) {
        // Vérifier "listed" : span marketplace-account-id-{pid}-* contient img avec _green-
        var listed = false;
        document.querySelectorAll('[id^="marketplace-account-id-' + pid + '-"]').forEach(function(span) {
            var img = span.querySelector('img');
            if (img && img.getAttribute('src') && img.getAttribute('src').indexOf('_green-') !== -1) {
                listed = true;
            }
        });
        if (!listed) allListed = false;
        if (listed)  noneListed = false;

        // Vérifier "fed" : bouton RSS btn-success dans la ligne du produit
        var row = document.querySelector('tr[data-product-id="' + pid + '"]');
        var isFed = false;
        if (row) {
            // Le bouton RSS (fa-rss) est btn-success quand has_sources == 1
            var rssBtn = row.querySelector('button .fa-rss');
            if (rssBtn && rssBtn.closest('button').classList.contains('btn-success')) {
                isFed = true;
            }
            // Fallback : btn-warning = not fed (has_sources_flag == 0)
        }
        if (isFed) anyFed = true;
        else allFed = false;
    });

    // Cas 1 & 3 : tous listed + aucun fed  OU  aucun listed + aucun fed → Feed Selected
    if ((allListed && !anyFed) || (noneListed && !anyFed)) {
        listFeedBtn.innerHTML = '<i class="fa-solid fa-rss"></i> ' + (window.TEXT_FEED_ALL_PRODUCTS || 'Feed Selected');
        listFeedBtn.onclick = function() { handleFeedList(); };
        listFeedBtn.className = listFeedBtn.className.replace(/\bbtn-\w+/g, '').trim() + ' btn btn-warning';
        listFeedBtn.classList.remove('d-none');
        return;
    }

    // Cas 2 : tous listed + au moins 1 fed → End Selected
    if (allListed && anyFed) {
        if (endBtn) endBtn.classList.remove('d-none');
        return; // handleEndList() est sur #button-end-selected
    }

    // Cas 4 : pas tous listed + tous fed → List Selected
    if (!allListed && allFed) {
        listFeedBtn.innerHTML = '<i class="fa-solid fa-list"></i> ' + (window.TEXT_LIST_ALL_PRODUCTS || 'List Selected');
        listFeedBtn.onclick = function() { handleAddList(); };
        listFeedBtn.className = listFeedBtn.className.replace(/\bbtn-\w+/g, '').trim() + ' btn btn-primary';
        listFeedBtn.classList.remove('d-none');
        return;
    }
}
```

**5b. Ajouter `endSelectedListings()`** (après `updateListFeedButton`) :

```javascript
function handleEndList() {
    var tokenElement = document.querySelector("input[name='user_token']");
    var user_token   = tokenElement ? tokenElement.value : '';
    if (!user_token) { alert('Token not found'); return; }

    var selectedIds = [];
    document.querySelectorAll("input[name^='selected']:checked").forEach(function(cb) {
        if (cb.value) selectedIds.push(cb.value);
    });
    if (selectedIds.length === 0) { alert(TEXT_UPDATE_MARKETPLACE_NO_SELECTION || 'No product selected'); return; }

    showLoadingPopup(TEXT_END_SELECTED || 'Ending selected listings...');  // handleEndList
    var currentIndex = 0;

    function processNext() {
        if (currentIndex >= selectedIds.length) {
            finishLoadingPopup();
            updateListFeedButton();
            return;
        }
        var productId = selectedIds[currentIndex];

        // Récupérer marketplace_account_id et marketplace_item_id depuis le DOM
        var targets = [];
        document.querySelectorAll('[id^="marketplace-account-id-' + productId + '-"]').forEach(function(span) {
            var img = span.querySelector('img');
            if (!img) return;
            var src = img.getAttribute('src') || '';
            if (src.indexOf('_green-') === -1) return; // seulement les listés verts

            var idParts = span.id.split('-'); // marketplace-account-id-{pid}-{account_id}
            var marketplace_account_id = idParts[idParts.length - 1];

            // Récupérer marketplace_item_id via href du lien parent
            var link = span.querySelector('a');
            var marketplace_item_id = '';
            if (link) {
                // L'URL du lien contient le item_id (ex: https://www.ebay.com/itm/296605947039)
                // on récupère depuis l'hidden input si disponible
                var hiddenUrl = document.querySelector('input[name="url_product_' + productId + '_' + marketplace_account_id + '"]');
                // Ou on essaie de lire depuis le href qui a été remplacé avec l'item_id
                var href = link.getAttribute('href') || '';
                var match = href.match(/\/(\d{10,})/);
                if (match) marketplace_item_id = match[1];
            }
            if (marketplace_item_id) targets.push({ marketplace_account_id, marketplace_item_id });
        });

        if (targets.length === 0) {
            appendLoadingMessage('⚠️ Produit #' + productId + ' : aucun listing actif trouvé', 'warning');
            currentIndex++;
            processNext();
            return;
        }

        // Appel séquentiel pour chaque compte marketplace du produit
        var subIndex = 0;
        function processSubTarget() {
            if (subIndex >= targets.length) { currentIndex++; processNext(); return; }
            var t = targets[subIndex];
            appendLoadingMessage('🛑 End listing #' + t.marketplace_item_id + ' (produit #' + productId + ')', 'info');
            $.ajax({
                url: 'index.php?route=shopmanager/ebay.delete&user_token=' + user_token,
                type: 'POST',
                data: { product_id: productId, marketplace_item_id: t.marketplace_item_id, marketplace_account_id: t.marketplace_account_id },
                dataType: 'json',
                success: function(json) {
                    if (json.success) {
                        setMarketplaceListingState(productId, t.marketplace_account_id, 'grey', '');
                        appendLoadingMessage('✅ Listing #' + t.marketplace_item_id + ' terminé', 'success');
                    } else {
                        appendLoadingMessage('❌ Erreur produit #' + productId + ': ' + (json.message || 'Erreur inconnue'), 'error');
                    }
                    subIndex++; processSubTarget();
                },
                error: function(xhr, opts, err) {
                    appendLoadingMessage('❌ Erreur AJAX produit #' + productId + ': ' + err, 'error');
                    subIndex++; processSubTarget();
                }
            });
        }
        processSubTarget();
    }
    processNext();
}
```

---

## Ce qui N'est PAS modifié (garanti safe)

- `handleFeedList()` — corps inchangé, signature inchangée
- `handleList()` — corps inchangé, signature inchangée
- `updateSelectedMarketplaceListings()` — inchangé
- `editQuantityToMarketplaceBulk()` — inchangé
- `ebay.php::delete()` — inchangé (déjà utilisé single)
- `ebay.php::add()` / `relist()` — inchangés
- `product.php` controller (ligne 304) — optionnel : peut rester tel quel, la logique JS prend le dessus. On peut simplifier mais c'est non-prioritaire.

---

## Ordre d'exécution recommandé

1. ✅ Backups : `product.twig.backup`, `product_list.js.backup`, lang files `.backup`
2. Language files EN → FR → ES
3. `product.twig` : nouveau bouton + `d-none` sur bouton existant + JS globals + event checkbox
4. `product_list.js` : réécrire `updateListFeedButton()` + ajouter `endSelectedListings()`
5. Test manuel :
   - Sélectionner produits non-listed non-fed → vérifier "Feed Selected" apparaît
   - Sélectionner produits listed non-fed → vérifier "Feed Selected" apparaît
   - Sélectionner produits listed et certains fed → vérifier "End Selected" apparaît
   - Sélectionner produits non-listed et tous fed → vérifier "List Selected" apparaît
   - Tester clic "End Selected" → vérifier icône passe au gris

---

---

## Clarifications confirmées (2026-03-24)

### ✅ Traitement séquentiel (un produit à la fois)
`handleEndList()` *(renommé depuis `endSelectedListings`)* traite un produit à la fois dans un loop, exactement comme `updateSelectedMarketplaceListings()`. Chaque étape affiche un message dans le loading popup.

### ✅ Icônes eBay — noms confirmés
Les fichiers existent dans `image/cache/catalog/marketplace/` :
- `ebay_green-25x25.png` → listing actif
- `ebay_red-25x25.png` → erreur
- `ebay_grey-25x25.png` → non listé (même variantes pour `_ca`, `_motors_ca`)

`getMarketplaceThumbStateUrl(src, 'grey')` fonctionne déjà (replace `_green-` ou `_red-` → `_grey-`).

### ⚠️ Problème identifié : `setMarketplaceListingState()` n'a pas de cas `grey`
La fonction actuelle (ligne 1207) gère `green` et `red` mais PAS `grey`. Si on passe `'grey'`, le span devient vide. Il faut ajouter le cas `grey` qui remet l'icône cliquable → `addToMarketplace(...)` comme dans le template original.

**Impact** : modification de `setMarketplaceListingState()`. Utilisée à 2 endroits :
- `product_list.js:1323` — state `'green'` → non affecté
- `product_list.js:1326` — state `'red'` → non affecté
→ Ajout du `else if (state === 'grey')` est **rétro-compatible et sans risque**.

### ⚠️ Problème identifié : `marketplace_id` manquant dans le DOM pour l'état grey
Le onclick du lien gris dans le template est :  
```twig
onclick="addToMarketplace('{{ product.product_id }}','{{ marketplace_account_id }}','{{ marketplace_id }}',true);"
```
`marketplace_id` (eBay=9, etc.) n'est PAS dans un hidden input actuellement. Pour que `setMarketplaceListingState(..., 'grey', ...)` puisse reconstruire ce onclick, il faut :

**Solution** : ajouter dans `product_list.twig` (dans le span `.marketplace-account-id`) un hidden input :
```twig
<input type="hidden" name="marketplace_id_{{ product.product_id }}_{{ marketplace_account_id }}" value="{{ marketplace_id }}" />
```
Puis le lire dans `setMarketplaceListingState` via :
```javascript
var mpIdInput = document.querySelector('input[name="marketplace_id_'+productId+'_'+marketplaceAccountId+'"]');
var marketplace_id = mpIdInput ? mpIdInput.value : '9';
```

### ✅ Confirmation : processing one at a time dans le popup
Même pattern que `updateSelectedMarketplaceListings` : sequential AJAX + `appendLoadingMessage` à chaque étape, `finishLoadingPopup()` à la fin.

---

## Ordre d'exécution recommandé (mis à jour)

1. ✅ Backups des fichiers modifiés
2. Language files EN → FR → ES (`text_end_selected`)
3. `product_list.twig` : ajouter hidden input `marketplace_id_{pid}_{account_id}`
4. `product_list.js` :
   a. Ajouter cas `grey` dans `setMarketplaceListingState()`
   b. Réécrire `updateListFeedButton()`
   c. Ajouter `handleEndList()` *(ex `endSelectedListings`)*
   
   > **Note JS file** : Ces 3 fonctions restent dans `product_list.js` (non déplacées vers `ebay.js`). Raison : `ebay.js` est aussi chargé sur la page `product_form` (edit individuel). Ces bulk actions sont exclusives à la page liste → `product_list.js` est le seul fichier correct.
5. `product.twig` : nouveau bouton `#button-end-selected` + `d-none` sur bouton existant + JS globals + event checkbox
6. Tests

