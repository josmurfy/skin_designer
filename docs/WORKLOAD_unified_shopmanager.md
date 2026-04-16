# WORKLOAD : ShopManager Unifié Multi-DB

## Résumé de la Demande

Un **seul ShopManager** (hébergé sur phoenixliquidation) qui gère **2 bases de données** et **3 types de produits** en symbiose totale — CRUD produits + listings marketplace (eBay + futurs).

---

## Les 3 Types de Produits

| Type | Préfixe | Base de données | Critère d'assignation | Particularités |
|------|---------|-----------------|----------------------|----------------|
| **Retail** | `RET_` | `phoenixliquidation` | Produit avec UPC | Standard, prix consommateur |
| **Card** | `CARD_` / `XX_CARD_` | `phoenixliquidation` | Table `oc_card` séparée | Logique différente (grading, sets, prix marché) |
| **Commercial** | `COM_` | `phoenixsupplies` | Produit sans UPC | Lots, wholesale, pas de code-barres |

**Règle de routage** : UPC → retail (phoenixliquidation) / Pas de UPC → commercial (phoenixsupplies)

---

## Opérations Requises (sur les 2 DB)

### Produits
- **Lister** tous les produits (RET + COM + CARD) dans une vue unifiée
- **Créer** un produit dans la bonne DB selon la présence de UPC
- **Modifier** prix, quantité, description — peu importe la DB source
- **Supprimer** avec cascade (images, descriptions, marketplace entries)

### Marketplace (eBay + futurs)
- **Lister** sur eBay depuis les 2 DB
- **Modifier** (prix, quantité, specifics) — `editQuantity`, `editPrice`, `editToMarketplace`
- **Supprimer** listing eBay
- **Sync** état eBay ↔ DB locale (quantité vendue, fin de listing)

### Orders (déjà en place)
- Process order → update quantité dans la bonne DB
- Update eBay uniquement pour les produits **locaux** (fix fait aujourd'hui)

---

## Problèmes Actuels

### 1. Cross-DB Dispersé et Hacky
```
Actuellement :
- order.php      → mysqli_connect() direct avec helpers privés
- marketplace.php → aucune gestion cross-DB (bug COM_ d'aujourd'hui)
- catalog/product.php → local seulement
- shipping.php   → hardcoded credentials (pas encore corrigé)
```
Chaque fichier réinvente sa propre connexion sister. Pas de couche unifiée.

### 2. Arborescence Confuse
```
controller/shopmanager/
├── catalog/          # product.php (1 fichier = 2800+ lignes)
├── inventory/        # sync.php, allocation.php
├── card/             # card.php, import.php, export.php
├── ebay.php          # Mixte: API + category specifics
├── marketplace.php   # Listings eBay
├── order.php         # Orders eBay
├── shipping.php      # USPS/ShipStation
├── ocr.php, ai.php   # AI tools
└── 15+ autres fichiers...
```
**Problèmes** :
- Pas clair si un fichier gère phoenixliquidation seul ou les 2 DB
- `catalog/product.php` = méga-fichier fourre-tout
- Pas de séparation marketplace-agnostique (tout est eBay-only)
- Helpers cross-DB dupliqués ou absents

### 3. Collision de product_id
Les `product_id` de phoenixsupplies peuvent être identiques aux `product_id` de phoenixliquidation. Quand le code fait `str_replace("COM_", "")`, l'ID numérique résultant peut pointer vers un produit local DIFFÉRENT.

### 4. Order → eBay update skip pour COM_ (P0.5)
**État actuel** : Le fix P0 ajoute `$is_local_product` qui **skip** le `editQuantity` eBay quand un produit COM_ est traité depuis phoenixliquidation. Ça évite le crash et la mise à jour du mauvais produit.

**Problème** : Quand on process un order sur phoenixliquidation avec un produit COM_, la quantité stock est bien mise à jour dans phoenixsupplies (via `updateSisterQuantity`), MAIS le listing eBay n'est PAS mis à jour. Le stock eBay reste désynchronisé jusqu'au prochain sync.

**Ce qu'il faut** : Que `updateQuantity()` et `undoProductQuantity()` dans order.php puissent :
1. Chercher les `marketplace_accounts_id` dans la **sister DB** (pas la locale)
2. Appeler `editQuantity` avec le bon contexte (produit sister, pas local)
3. Ou déléguer à un service cross-DB qui sait dans quelle DB chercher le marketplace entry

**Dépend de** : P1 → P2 → P3 (chemin critique direct)

**Logique** : Une fois multidb (P1) créé, on modifie `editQuantity` immédiatement (P2), puis order.php l'utilise (P3). Trois phases et le skip P0 disparaît. Pas besoin d'attendre l'arborescence, la vue unifiée ou le marketplace agnostique.

```
P1: multidb.php
 └─ P2: marketplace->editQuantity($product_id, $account_id, $source_db)
     └─ P3: order.php → editQuantity($product_id, $account_id, 'supplies')
         └─ Supprime le skip P0 → eBay synchronisé pour COM_
```

---

## Vision Cible

### Couche d'Abstraction DB (Priorité 1)
Un **service unique** pour accéder aux 2 bases :

```php
// Idée : un model/shopmanager/multidb.php (ou db_router.php)
class MultiDb {
    public function getProduct(int $product_id, string $site): array { }
    public function updateQuantity(int $product_id, string $site, int $qty): bool { }
    public function getSisterConnection(): mysqli { }
    // $site = 'liquidation' | 'supplies'
}
```
→ Plus jamais de `mysqli_connect()` dispersé dans 5 fichiers.

### Routage Produit (Priorité 2)
Centraliser la logique "quel type / quelle DB" :

```php
// Déterminer le type et la DB cible à partir du SKU ou UPC
function resolveProduct(string $sku_or_prefix): ['type' => 'RET'|'COM'|'CARD', 'db' => 'liquidation'|'supplies']
```

### Marketplace Agnostique (Priorité 3)
Préparer le terrain pour Walmart/Amazon :

```
model/shopmanager/marketplace/
├── interface.php     # Interface commune (list, edit, delete, sync)
├── ebay.php          # Implémentation eBay
├── walmart.php       # Futur
└── router.php        # Dispatch vers le bon marketplace
```

### Vue Produit Unifiée (P6) — Liste unique, badges par type

**Principe** : Un seul écran pour tout l'inventaire, avec distinction visuelle claire.

**Pas deux écrans séparés** :
- Switching constant entre pages = perte de temps
- Les orders eBay mélangent déjà RET et COM
- Double maintenance UI

**Pas un mélange invisible** :
- L'opérateur doit savoir quelle DB il touche
- Les règles diffèrent (COM_ pas d'UPC, autre site web)
- Supprimer un produit = pas la même DB

**Maquette** :
```
┌─────────────────────────────────────────────────────────────┐
│ 🔍 Search...    [All ▼] [Retail] [Commercial] [Cards]       │
├─────────────────────────────────────────────────────────────┤
│ ☐ 13145 [RET]  Sony WH-1000XM5     qty:3  $349.99  eBay ● │
│ ☐ 50098 [COM]  Lot 50x USB Cables  qty:12  $89.99  eBay ● │
│ ☐ 27351 [RET]  Samsung Galaxy S24  qty:1  $199.99  eBay ● │
│ ☐ CARD  [CARD] 1979 OPC Gretzky    qty:4   $45.00  eBay ● │
│ ☐ 50092 [COM]  Pallet Electronics  qty:1  $450.00  —      │
└─────────────────────────────────────────────────────────────┘
```

**Éléments UI** :
- **Badge couleur** : `[RET]` vert, `[COM]` bleu, `[CARD]` orange
- **Filtre** : dropdown ou tabs pour filtrer par type
- **Tri** : par type, date, statut eBay, quantité
- **Actions contextuelles** : Edit/Delete savent quelle DB appeler grâce au type
- **Colonne DB source** : optionnelle, pour les opérateurs avancés

### Arborescence Nettoyée (Priorité 4)
Réorganisation proposée (à valider) :

```
controller/shopmanager/
├── product/              # CRUD produit unifié (RET + COM + CARD)
│   ├── product.php       # Liste/Form unifié
│   ├── card.php          # Spécificités cartes
│   └── import.php        # Import CSV/bulk
├── marketplace/          # Listings (multi-marketplace)
│   ├── marketplace.php   # Vue/actions communes
│   ├── ebay.php          # Spécificités eBay API
│   └── sync.php          # Sync bidirectionnel
├── order/                # Orders (déjà cross-DB)
│   └── order.php
├── inventory/            # Stock, allocation, locations
│   ├── allocation.php
│   └── barcode.php
├── tools/                # AI, OCR, shipping
│   ├── ai.php
│   ├── ocr.php
│   └── shipping.php
└── settings/             # Config, category mapping
    └── category.php
```

---

## Contraintes & Risques

1. **Pas de downtime** — migration incrémentale, pas de big bang
2. **Backward compatible** — les URLs/routes existantes doivent continuer à fonctionner
3. **Cards = logique séparée** — ne pas tenter d'unifier avec les produits standards
4. **product_id collision** — toujours qualifier avec le type/site, jamais utiliser un ID nu
5. **Credentials** — config constants uniquement (DB_SISTER_DATABASE), jamais hardcodé

---

## Ordre de Travail

| Phase | Tâche | Dépend de | Impact |
|-------|-------|-----------|--------|
| **P0** | ✅ Fix COM_ collision — skip temporaire | — | Bug critique résolu |
| **P1** | Créer `model/shopmanager/multidb.php` | — | Couche cross-DB unique, élimine 5+ connexions |
| **P2** | `marketplace->editQuantity()` cross-DB | P1 | Accepte `$source_db`, cherche produit+marketplace dans la bonne DB |
| **P3** | `order.php` : remplace le skip P0 par editQuantity cross-DB | P1+P2 | COM_ met à jour eBay correctement, plus de désync |
| **P4** | `ebaytemplate.php` : préfixe COM_ basé sur DB source, pas hostname | P1 | Permet lister COM_ depuis phoenixliquidation |
| **P5** | Refactorer `order.php` complet pour utiliser multidb | P1 | Nettoie les helpers privés dispersés |
| **P6** | Vue produit unifiée (RET + COM dans même écran) | P1 | UX unifiée |
| **P7** | Restructurer arborescence | P1→P5 stables | Organisation propre |
| **P8** | Interface marketplace agnostique | P2 stable | Prêt pour Walmart |

**Chemin critique** : P1 → P2 → P3 (élimine le skip P0 = plus de survente)
**Cosmétique** : P6, P7 (quand le fonctionnel est solide)
**Futur** : P8 (Walmart pas urgent)

---

## Logique SKU eBay Existante (ebaytemplate.php)

La construction du SKU eBay est dans `model/shopmanager/ebaytemplate.php` :

```php
// Ligne 1242 — Produits standard
$com = ((!isset($product['upc']) || $product['upc']=='') && $is_phoenixsupplies) ? 'COM_' : '';
$result .= '<SKU>' . $com . $product['product_id'] . '</SKU>';

// Ligne 1091 — Card listings
$result .= '<SKU>CARD_LIST' . $listing_data['listing_id'] . '</SKU>';
// Variations: 'CARD_' . $variation['card_id']
```

| Type | Condition actuelle | SKU eBay |
|------|-------------------|----------|
| Retail | UPC présent OU host = phoenixliquidation | `{product_id}` |
| Commercial | Pas UPC ET host = phoenixsupplies | `COM_{product_id}` |
| Card | Card listing flow | `CARD_LIST{listing_id}` / `CARD_{card_id}` |

### ⚠️ Problème pour l'unification
La logique `COM_` dépend de `$_SERVER['HTTP_HOST']` (vérifie si on est sur phoenixsupplies).
**Si on gère tout depuis phoenixliquidation**, le check `$is_phoenixsupplies` sera TOUJOURS false →
les produits commerciaux ne recevront jamais le préfixe `COM_`.

**Fix requis (P3)** : Baser le préfixe sur la **DB source du produit** plutôt que le hostname :
```php
// Futur : basé sur la DB source, pas sur HTTP_HOST
$com = ($product['_source_db'] === 'supplies' && empty($product['upc'])) ? 'COM_' : '';
```

---

## Questions Ouvertes

1. Les cards ont-elles des listings marketplace ? Ou seulement RET et COM ?
2. phoenixsupplies a-t-il son propre admin/ShopManager ? Ou tout se fait depuis phoenixliquidation ?
3. Un produit peut-il changer de type (ex: COM → RET si on ajoute un UPC après) ?
4. Les marketplace accounts (eBay) sont-ils partagés entre les 2 sites ou séparés ?

---

*Créé le 2026-04-14 — Contexte: bug COM_ editQuantity + discussion vision unifiée*
