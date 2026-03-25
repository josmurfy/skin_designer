# WORKLOAD — Lot eBay Listing (card_listing_form)

**Créé le** : 2026-03-01  
**Statut** : 🔴 À DÉMARRER  
**Objectif** : Ajouter l'option "Vendre en lot unique" dans `card_listing_form` — un seul listing eBay fixe (sans variations) pour TOUTES les cartes du listing, avec prix basé sur `oc_card.price` (raw/non-gradé), description HTML éclatante et photos en mosaïque.

---

## CONTEXTE TECHNIQUE

### Ce qui existe déjà
| Ressource | Localisation | Utilisable pour lot |
|---|---|---|
| `ebay.php::add()` | model/shopmanager/ebay.php:8 | ✅ Trading API AddItem (listing simple) |
| `ebay.php::buildEbayHeaders()` | :350 | ✅ Headers Trading API |
| `ebay.php::addCardListing()` | :158 | ⚠️ Multi-variation seulement |
| `ebay.php::endCardListing()` | :2063 | ✅ Réutilisable pour terminer un lot |
| `ebay.php::migrateImagesToEbay()` | :3820 | ✅ Upload images vers eBay |
| `card_listing.php::generateBatchDescription()` | :1178 | ⚠️ Multi-cards HTML (à adapter) |
| `card_listing.php::generateBatchTitle()` | :1189 | ⚠️ À adapter pour lot |
| `card_listing.php::getCardImageUrls()` | model | ✅ URLs images par card_id |

### Colonnes DB disponibles
- `oc_card_listing` : `set_name`, `subset`, `sport`, `year`, `brand`, `price`, `total_quantity`, `condition_id`
- `oc_card` : `card_id`, `price`, `quantity`, `player_name`, `team_name`, `card_number`, `condition_name`

### Colonnes DB à ajouter
- `oc_card_listing.lot_ebay_item_id` VARCHAR(50) — item ID eBay du lot
- `oc_card_listing.lot_status` TINYINT(1) DEFAULT 0 — 0=non publié, 1=actif, 2=terminé
- `oc_card_listing.lot_price` DECIMAL(15,4) — prix calculé/overridé
- `oc_card_listing.lot_date_published` DATETIME

---

## 1. MIGRATION DB

**Fichier** : nouvelle migration SQL

```sql
ALTER TABLE `oc_card_listing`
  ADD COLUMN `lot_ebay_item_id`    VARCHAR(50)    NULL     AFTER `status`,
  ADD COLUMN `lot_status`          TINYINT(1)     NOT NULL DEFAULT 0 AFTER `lot_ebay_item_id`,
  ADD COLUMN `lot_price`           DECIMAL(15,4)  NULL     AFTER `lot_status`,
  ADD COLUMN `lot_date_published`  DATETIME       NULL     AFTER `lot_price`;
```

**Action** : exécuter en DB + ajouter dans l'install SQL du module

- [ ] Ajouter les 4 colonnes à `oc_card_listing`

---

## 2. MODEL — card_listing.php

### 2a. `getLotPriceSummary(int $listing_id): array`
Calcule le prix total brut des cartes + quantité totale.
```php
// Plancher : LOT_FLOOR_PRICE_PER_CARD = 0.25
// Retourne :
// [
//   'total_price'   => float,  // SUM(MAX(price, 0.25) * quantity)
//   'card_count'    => int,    // COUNT(card_id) — lignes distinctes
//   'total_qty'     => int,    // SUM(quantity)
//   'per_card_avg'  => float,  // total_price / total_qty
//   'floored_count' => int,    // nombre de cartes avec prix plancher appliqué
// ]
// SQL :
// SELECT COUNT(*) as card_count,
//        SUM(quantity) as total_qty,
//        SUM(GREATEST(COALESCE(price,0), 0.25) * quantity) as total_price,
//        SUM(CASE WHEN COALESCE(price,0) < 0.25 THEN 1 ELSE 0 END) as floored_count
// FROM oc_card WHERE listing_id = ?
```
- [ ] Implémenter `getLotPriceSummary()`

### 2b. `generateLotTitle(int $listing_id): string`
Titre eBay ≤ 80 chars pour un lot.
Format : `{count}x {year} {brand} {set_name} Cards Lot {sport} - You Pick or Lot`
- [ ] Implémenter `generateLotTitle()`

### 2c. `generateLotDescription(int $listing_id): string`
Description HTML éclatante (même style que `generateBatchDescription` mais pour un lot).
Structure :
1. **Header** — fond sombre, titre du lot, sport, année, brand
2. **Stats rapides** — `{N} cards | {total_qty} total qty | Avg $X.XX/card`
3. **Tableau des cartes** — colonnes : `Card #` | `Player` | `Team` | `Condition` | `Qty`
   - Trié par `CAST(card_number AS UNSIGNED) ASC`
   - Toutes les cartes incluses (pas de troncature — eBay supporte long HTML)
4. **Note prix plancher** — si `floored_count > 0` : afficher note discrète
5. **Footer shipping + trust** — même style que `generateBatchDescription`
```html
<!-- Exemple structure -->
<div style="font-family:Arial;color:#333;">
  <!-- 1. Header -->
  <div style="background:#1a1a2e;color:#fff;padding:14px 20px;text-align:center;">
    <h2>🏆 {set_name} {year} - Complete Lot of {total_qty} Cards</h2>
    <p>{sport} | {brand} | All Raw/Ungraded</p>
  </div>
  <!-- 2. Stats -->
  <div style="background:#f8f9fa;padding:10px 20px;">
    📦 {card_count} unique cards | 🃏 {total_qty} total | 💰 ~${per_card_avg}/card avg
  </div>
  <!-- 3. Tableau -->
  <table style="width:100%;border-collapse:collapse;">
    <thead><tr><th>Card #</th><th>Player</th><th>Team</th><th>Condition</th><th>Qty</th></tr></thead>
    <tbody>
      {foreach card}
      <tr><td>#{card_number}</td><td>{player_name}</td><td>{team_name}</td><td>{condition_name}</td><td>{quantity}</td></tr>
      {/foreach}
    </tbody>
  </table>
  <!-- 4. Footer -->
  <div>🌎 Ships Worldwide | 🛒 Combined shipping | ✅ Satisfaction guaranteed</div>
</div>
```
- [ ] Implémenter `generateLotDescription()`

### 2d. `saveLotInfo(int $listing_id, string $ebay_item_id, float $price): void`
Sauvegarde `lot_ebay_item_id`, `lot_status=1`, `lot_price`, `lot_date_published`.
- [ ] Implémenter `saveLotInfo()`

### 2e. `getLotInfo(int $listing_id): array`
Lecture des colonnes `lot_*` de `oc_card_listing`.
- [ ] Implémenter `getLotInfo()`

---

## 3. MODEL — ebay.php

### 3a. `publishLotListing(int $listing_id, array $site_setting, int $marketplace_account_id): array`
Utilise **Trading API AddItem** (pas Inventory API).
```php
// Construit le XML AddItem :
// - <Title> depuis generateLotTitle()
// - <Description> depuis generateLotDescription()
// - <PrimaryCategory> depuis ebay_category_id
// - <StartPrice> = lot_price ou getLotPriceSummary()
// - <Quantity> = 1 (c'est un lot, quantité toujours 1)
// - <ConditionID> depuis condition_id
// - <PictureDetails> = URLs des mosaïques (max 12 images eBay)
// - <ListingType> = FixedPriceItem
// - <ListingDuration> = GTC
// Réutilise buildEbayHeaders('AddItem')
// Retourne ['success' => bool, 'ebay_item_id' => string, 'error' => string]
```
- [ ] Implémenter `publishLotListing()`

### 3b. `endLotListing(int $listing_id, int $marketplace_account_id): array`
Termine le lot eBay (réutilise `endCardListing` ou appel direct EndItem).
- [ ] Implémenter `endLotListing()`

---

## 4. CONTROLLER — card/card_listing.php

### 4a. `publishLot(): void`
Action AJAX POST `card/card_listing.publishLot`.
```php
// 1. Vérifie permission
// 2. Appelle generateMosaicImages() → upload vers eBay via migrateImages
// 3. Appelle ebay.publishLotListing()
// 4. Sauvegarde résultat via saveLotInfo()
// 5. Retourne JSON {success, ebay_item_id, url, error}
```
- [ ] Implémenter `publishLot()`

### 4b. `endLot(): void`
Action AJAX POST `card/card_listing.endLot`.
- [ ] Implémenter `endLot()`

### 4c. `getLotPreview(): void`
Action AJAX GET — retourne titre + description + prix calculé pour preview dans le form.
- [ ] Implémenter `getLotPreview()`

---

## 5. IMAGES — Mosaïque

### Logique mosaïque dynamique
```
total_cards = COUNT(cartes avec front_image)
max_images  = 12  (limite eBay)

cards_per_tile = ceil(total_cards / max_images)
grid_n         = ceil(sqrt(cards_per_tile))  → grille NxN

Ex: 80 cartes → 7 cartes/tile → grid 3×3 → 9 tiles de 9 = 81 slots
Ex: 12 cartes → 1 carte/tile  → 1×1      → 12 images individuelles (qualité max)
Ex: 200 cartes→ 17/tile       → 5×5      → 8 tiles de 25
```

### Contraintes eBay
- Max **12 images** par listing
- Image min 500×500px, recommandée **1600×1600px**
- Format JPG, fond blanc entre cases

### Paramètres de `composeMosaicTile()`
- Taille tuile : `1600×1600px`
- Padding entre cartes : `4px` fond blanc
- Chaque cellule : `floor(1600/N) - 8` px
- Librairie : **Imagick** (meilleure qualité)
- Output : `/image/shopmanager/lot_{listing_id}/mosaic_{n}.jpg` qualité 88%

### Fonctions à créer dans card_listing.php
- `generateMosaicImages(int $listing_id): array` — calcule grille dynamique, retourne chemins locaux
- `composeMosaicTile(array $image_local_paths, int $grid_n, int $tile_index): string` — Imagick, retourne chemin
- `getCardFrontImagePaths(int $listing_id): array` — retourne chemins locaux des `front_image` de chaque carte

- [ ] Implémenter `getCardFrontImagePaths()`
- [ ] Implémenter `composeMosaicTile()` avec **Imagick**
- [ ] Implémenter `generateMosaicImages()`

---

## 6. VUE — card_listing_form.twig

### Section "Lot eBay" à ajouter dans le form
Position : après la section "Batches/Variations", dans un nouveau tab ou un panel collapsible.

```html
<!-- Section Lot eBay -->
<div id="panel-lot-listing" class="card mt-3">
  <div class="card-header">
    <h5>{{ text_lot_listing }}</h5>
  </div>
  <div class="card-body">
    <!-- Prix calculé (readonly) -->
    <div class="row mb-2">
      <label>{{ entry_lot_calculated_price }}</label>
      <input type="text" id="lot-calculated-price" readonly />
      <label>{{ entry_lot_price_override }}</label>
      <input type="number" id="lot-price-override" name="lot_price" />
    </div>
    <!-- Statut actuel -->
    <div id="lot-status-badge">...</div>
    <!-- Titre preview -->
    <div id="lot-title-preview"></div>
    <!-- Boutons -->
    <button id="btn-lot-preview">{{ button_lot_preview }}</button>
    <button id="btn-lot-publish">{{ button_lot_publish }}</button>
    <button id="btn-lot-end" style="display:none">{{ button_lot_end }}</button>
    <!-- Lien eBay si publié -->
    <a id="lot-ebay-link" href="#" target="_blank"></a>
  </div>
</div>
```

Variables Twig à passer depuis le controller :
- `lot_info` : tableau (lot_ebay_item_id, lot_status, lot_price, lot_date_published)
- `lot_calculated` : résultat de getLotPriceSummary()

- [ ] Ajouter section dans `card_listing_form.twig`
- [ ] Passer `lot_info` et `lot_calculated` depuis controller `form()`

---

## 7. JS — card_listing_form.js

### Fonctions à ajouter
```javascript
// Preview : charge titre + description + prix calculé
function loadLotPreview() {
    $.ajax({ url: '...card_listing.getLotPreview&listing_id=...', ... });
}

// Publier le lot
function publishLot() {
    if (!confirm(TEXT_LOT_CONFIRM_PUBLISH)) return;
    // POST → card_listing.publishLot
    // Met à jour le badge + lien eBay sur succès
}

// Terminer le lot
function endLot() {
    if (!confirm(TEXT_LOT_CONFIRM_END)) return;
    // POST → card_listing.endLot
}
```

- [ ] Ajouter `loadLotPreview()`, `publishLot()`, `endLot()` dans `card_listing_form.js`
- [ ] Brancher sur les boutons du Twig

---

## 8. LANGUAGE — 3 locales

### Clés à ajouter dans `card/card_listing.php` (EN/FR/ES)

```php
// EN
$_['text_lot_listing']           = 'Sell as eBay Lot (Single Listing)';
$_['text_lot_status_inactive']   = 'Not listed';
$_['text_lot_status_active']     = 'Active on eBay';
$_['text_lot_status_ended']      = 'Ended';
$_['entry_lot_calculated_price'] = 'Calculated Price (raw)';
$_['entry_lot_price_override']   = 'Override Price ($)';
$_['button_lot_preview']         = 'Preview Lot';
$_['button_lot_publish']         = 'Publish as Lot on eBay';
$_['button_lot_end']             = 'End Lot Listing';
$_['text_lot_confirm_publish']   = 'Publish ALL cards as a single eBay lot?';
$_['text_lot_confirm_end']       = 'End this lot listing on eBay?';
$_['text_lot_card_count']        = '%d cards in this lot';
$_['text_lot_ebay_link']         = 'View on eBay';
$_['text_lot_success_publish']   = 'Lot published successfully on eBay!';
$_['text_lot_success_end']       = 'Lot listing ended on eBay.';
$_['error_lot_no_images']        = 'No card images available for mosaic generation.';
$_['error_lot_already_active']   = 'A lot listing is already active. End it first.';
```

- [ ] Ajouter clés dans `en-gb/shopmanager/card/card_listing.php`
- [ ] Ajouter clés dans `fr-fr/shopmanager/card/card_listing.php`
- [ ] Ajouter clés dans `es-es/shopmanager/card/card_listing.php`

---

## 9. ORDRE D'EXÉCUTION RECOMMANDÉ

```
1. DB migration (ALTER TABLE)
2. Model: getLotPriceSummary + getLotInfo + saveLotInfo
3. Model: generateLotTitle + generateLotDescription
4. Model: composeMosaicTile + generateMosaicImages (GD)
5. Model ebay.php: publishLotListing + endLotListing
6. Controller: publishLot + endLot + getLotPreview
7. Twig: section lot dans card_listing_form
8. Controller form(): passer lot_info + lot_calculated
9. JS: loadLotPreview + publishLot + endLot
10. Language: 3 fichiers
```

---

## 10. QUESTIONS OUVERTES

- [x] **GD ou Imagick** disponible sur le serveur ? → **GD ✅ + Imagick ✅** — on utilisera **Imagick** (meilleure qualité de resampling)
- [ ] **Prix lot** : somme brute `SUM(price*quantity)` ou prix moyen × total_quantity depuis listing ?
- [ ] **Cartes per mosaïque** : 9 (3×3) ou 16 (4×4) ?
- [ ] **Lot quantity** sur eBay = 1 (un seul lot) ou = nbr de cartes (pour affichage) ?
- [ ] **Titre** : inclure le nombre de cartes ? ex: "50x 2021 Topps Baseball Cards Lot #1-250"

## 10. QUESTIONS OUVERTES / DÉCISIONS

- [x] **GD ou Imagick** disponible sur le serveur ? → **GD ✅ + Imagick ✅** — on utilisera **Imagick** (meilleure qualité de resampling)

- [x] **Prix plancher** : si `oc_card.price = 0` ou NULL → plancher **$0.25/carte**  
  Stats DB réelles : min=$0.99, avg=$1.62 — mais le plancher protège les futures cartes non pricées.  
  Constante : `LOT_FLOOR_PRICE_PER_CARD = 0.25`  
  Logique : `effective_price = MAX(price, 0.25) * quantity` par carte → SUM = lot_price brut

- [x] **Stratégie mosaïque dynamique** (eBay max 12 images) :
  ```
  cards_per_tile = ceil(total_cards / 12)
  grid_size = ceil(sqrt(cards_per_tile))  → grille NxN minimale
  
  Exemples :
  ≤12 cartes   → 1 carte/image  → 1×1  (photos individuelles, best quality)
  13–48        → 2–4/image      → 2×2  
  49–108       → 5–9/image      → 3×3  
  109–192      → 10–16/image    → 4×4  
  193–300      → 17–25/image    → 5×5  
  300+         → 26–36/image    → 6×6  
  ```
  → Toujours max 12 images uploadées sur eBay.  
  → Fond blanc, padding 4px entre cartes, taille résultante 1600×1600px.

- [x] **Description** : inclure tableau complet des cartes avec `card_number`, `player_name`, `team_name`, `condition_name`, `quantity`  
  Format HTML : tableau trié par card_number, tronqué visuellement à 50 lignes avec toggle "Show all".

- [x] **Quantité eBay du lot** = **1** (un seul lot physique)  
  Le nombre de cartes est affiché dans le titre et la description, pas comme quantité eBay.

- [ ] **Prix lot** : somme brute `SUM(MAX(price,0.25) * quantity)` — confirmer avec le user

---

**Prochaine étape** : confirmer le prix lot → démarrer par la migration DB (section 1).
