# WORKLOAD: Card Database Normalization

> **Objectif** : Architecture 3 niveaux : `oc_card_set` (info du set) → `oc_card` (carte individuelle + prix référence) → `oc_card_price_sold` / `oc_card_price_active` (prix marché).  
> Réutiliser la table `oc_card` existante en y ajoutant les colonnes pricing. Les cartes sans `listing_id` et `quantity=0` = données de référence (non listées sur eBay).
> 
> **Date** : 2026-04-09  
> **Statut** : 🔴 PLANIFIÉ — NE PAS EXÉCUTER SANS APPROBATION

---

## 1. ARCHITECTURE ACTUELLE (problèmes)

```
oc_card_set (1551 rows) ──── Table PLATE: mélange info set + info carte + prix
     ├── category, year, brand, set_name          ← Niveau SET  (4 sets distincts)
     ├── player, card_number, team, subset, etc.  ← Niveau CARTE
     └── ungraded, grade_9, grade_10              ← Niveau PRIX RÉFÉRENCE

oc_card_price_sold (7980 rows)  ── Duplique 12 colonnes de card_set, PAS de FK
oc_card_price_active (12916 rows) ── FK card_raw_id OK, mais title dupliqué

oc_card_listing (237 rows) ── Set eBay (set_name, year, brand, sport + eBay fields)
oc_card (2872 rows) ────────── Carte pour listing eBay (FK listing_id, NOT NULL CASCADE)
```

**Problèmes :**
- `oc_card_set` est plate — mélange 3 niveaux d'info
- `oc_card_price_sold` duplique player/year/brand/set_name × 7980 rows
- `oc_card` et `oc_card_set` contiennent les même cartes sans lien entre eux
- `oc_card.listing_id` NOT NULL + CASCADE → impossible d'avoir une carte "référence seule"

---

## 2. NOUVELLE ARCHITECTURE (3 niveaux)

```
┌────────────────────────┐
│     oc_card_set         │  Niveau 1: INFO DU SET
│ card_set_id (PK)       │  "1979 O-Pee-Chee Hockey"
│ set_name               │
│ category (sport)       │
│ year                   │
│ brand                  │
│ subset                 │
│ status, date_added     │
│ date_modified          │
└──────────┬─────────────┘
           │ 1:N
           ▼
┌────────────────────────────────────────────────┐
│              oc_card                            │  Niveau 2: CARTE INDIVIDUELLE
│ card_id (PK)                                   │
│ card_set_id (FK → oc_card_set) ── NEW          │  ← Lien vers le set
│ listing_id (FK → oc_card_listing) ── NULLABLE  │  ← NULL si pas listeé
│ player_name, card_number, team_name            │  (existant)
│ title, year, brand                             │  (existant)
│ attributes ── NEW                              │
│ variation  ── NEW                              │
│ front_image ── NEW                             │  ← photo par défaut du lien
│ ungraded, grade_9, grade_10 ── NEW             │  ← prix référence
│ quantity (0 = référence seule, pas listée)     │  (existant)
│ price, status, published, ...                  │  (existant)
└──────────┬─────────────────────────────────────┘
           │ 1:N
           ▼
┌─────────────────────────────┐  ┌──────────────────────────────┐
│   oc_card_price_sold        │  │   oc_card_price_active       │
│ card_price_sold_id (PK)     │  │ active_id (PK)               │
│ card_id (FK → oc_card) NEW  │  │ card_id (FK → oc_card) RENAME│
│ grader, grade, price        │  │ price_usd, price_cad         │
│ currency, type_listing      │  │ grader, grade, grade_score   │
│ bids, total_sold            │  │ ebay_item_id, url, picture   │
│ ebay_item_id                │  │ is_graded, condition_type    │
│ date_sold, status           │  │ keyword, status              │
│ date_added, date_modified   │  │ date_added, date_modified    │
│ (PLUS de player/year/brand) │  │ (card_raw_id → card_id)      │
└─────────────────────────────┘  └──────────────────────────────┘

oc_card_listing reste INCHANGÉ (set eBay avec fields eBay-spécifiques)
  → Optionnel futur : ajouter card_set_id FK pour lier listing ↔ set référence
```

### Règle d'affichage dans oc_card :
| quantity | listing_id | Signification |
|---|---|---|
| > 0 | NOT NULL | Carte active sur eBay (listing existant) |
| 0 | NOT NULL | Carte dans un listing mais épuisée |
| 0 | NULL | **Carte de référence** (importée pour prix, pas listée) |

---

## 3. DONNÉES ACTUELLES & MIGRATION

### Stats de migration
| Source | Total | Déjà dans oc_card | À migrer |
|---|---|---|---|
| oc_card_set rows | 1,551 | 354 (match exact par card_number+year+brand+player) | **1,223** nouvelles rows dans oc_card |
| oc_card_listing sets | 207 | — | Les 3 sets manquants ont besoin de card_set rows |
| oc_card rows existantes | 2,872 | 354 (enrichir avec prix) | 2,518 à lier à un card_set |

### Sets distincts (4 dans card_set actuel, 207 dans card_listing)
```
card_set actuel:
  1979 O-Pee-Chee (HOCKEY, 393 cartes)     → PAS dans card_listing
  1991 O-Pee-Chee (HOCKEY, 520 cartes)     → PAS dans card_listing
  1991 Kayo (BOXING?, 242 cartes)           → Dans card_listing ✓
  1993 Topps (BASKETBALL, 396 cartes)       → PAS dans card_listing

card_listing: 207 sets (HOCKEY, BASKETBALL, BASEBALL, FOOTBALL, BOXING)
  → Chacun doit recevoir un card_set_id via la nouvelle table oc_card_set
```

---

## 4. DDL MIGRATION SQL

### Phase 2A : Créer la nouvelle `oc_card_set` (SET-level only)

```sql
-- Sauvegarder l'ancienne table
RENAME TABLE oc_card_set TO oc_card_set_backup;

-- Nouvelle table SET-level
CREATE TABLE oc_card_set (
  card_set_id    INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  set_name       VARCHAR(255) NOT NULL DEFAULT '',
  category       VARCHAR(128) NOT NULL DEFAULT '' COMMENT 'Sport: HOCKEY, BASKETBALL, etc.',
  year           VARCHAR(10)  NOT NULL DEFAULT '',
  brand          VARCHAR(128) NOT NULL DEFAULT '',
  subset         VARCHAR(255) NOT NULL DEFAULT '',
  status         TINYINT(1) NOT NULL DEFAULT 1,
  date_added     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  date_modified  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (card_set_id),
  UNIQUE KEY uk_set (year, brand, set_name, subset),
  KEY idx_category (category),
  KEY idx_year (year),
  KEY idx_brand (brand)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Peupler depuis les 4 sets distincts de l'ancien card_set
INSERT INTO oc_card_set (set_name, category, year, brand, subset)
SELECT DISTINCT set_name, category, year, brand, ''
FROM oc_card_set_backup;

-- Peupler depuis card_listing (207 sets manquants)
INSERT IGNORE INTO oc_card_set (set_name, category, year, brand, subset)
SELECT DISTINCT cl.set_name, COALESCE(cl.sport, ''), cl.year, cl.brand, COALESCE(cl.subset, '')
FROM oc_card_listing cl
WHERE NOT EXISTS (
  SELECT 1 FROM oc_card_set cs 
  WHERE cs.year = cl.year AND cs.brand = cl.brand AND cs.set_name = cl.set_name
    AND cs.subset = COALESCE(cl.subset, '')
);
```

### Phase 2B : Modifier `oc_card` (ajouter colonnes + FK nullable)

```sql
-- Ajout de colonnes manquantes
ALTER TABLE oc_card
  ADD COLUMN card_set_id  INT(10) UNSIGNED DEFAULT NULL AFTER listing_id,
  ADD COLUMN attributes   TEXT NOT NULL DEFAULT '' AFTER team_name,
  ADD COLUMN variation    VARCHAR(255) NOT NULL DEFAULT '' AFTER attributes,
  ADD COLUMN front_image  TEXT NOT NULL DEFAULT '' AFTER variation,
  ADD COLUMN ungraded     DECIMAL(15,4) DEFAULT NULL AFTER front_image,
  ADD COLUMN grade_9      DECIMAL(15,4) DEFAULT NULL AFTER ungraded,
  ADD COLUMN grade_10     DECIMAL(15,4) DEFAULT NULL AFTER grade_9,
  ADD KEY idx_card_set_id (card_set_id);

-- Rendre listing_id nullable (pour cartes de référence sans listing)
ALTER TABLE oc_card DROP FOREIGN KEY fk_variation_listing;
ALTER TABLE oc_card MODIFY listing_id INT(11) DEFAULT NULL;
ALTER TABLE oc_card ADD CONSTRAINT fk_card_listing 
  FOREIGN KEY (listing_id) REFERENCES oc_card_listing(listing_id) ON DELETE SET NULL;

-- Lier les oc_card existantes à leur card_set
UPDATE oc_card c
JOIN oc_card_set cs ON c.year = cs.year AND c.brand = cs.brand 
  AND CONCAT(c.year, ' ', c.brand) = cs.set_name AND cs.subset = ''
SET c.card_set_id = cs.card_set_id;

-- Pour les cards de listing avec subset
UPDATE oc_card c
JOIN oc_card_listing cl ON c.listing_id = cl.listing_id
JOIN oc_card_set cs ON cl.year = cs.year AND cl.brand = cs.brand 
  AND cl.set_name = cs.set_name AND COALESCE(cl.subset, '') = cs.subset
SET c.card_set_id = cs.card_set_id
WHERE c.card_set_id IS NULL;
```

### Phase 2C : Migrer les 1,223 cartes de référence (oc_card_set_backup → oc_card)

```sql
-- Enrichir les 354 cartes qui matchent (ajouter les prix)
UPDATE oc_card c
JOIN oc_card_set_backup csb 
  ON c.card_number = csb.card_number AND c.year = csb.year 
  AND c.brand = csb.brand AND c.player_name = csb.player
SET c.attributes  = csb.attributes,
    c.variation   = csb.variation,
    c.front_image = csb.front_image,
    c.ungraded    = csb.ungraded,
    c.grade_9     = csb.grade_9,
    c.grade_10    = csb.grade_10;

-- Insérer les 1,223 cartes de référence (pas dans oc_card encore)
INSERT INTO oc_card (
  listing_id, card_set_id, title, card_number, player_name, team_name,
  year, brand, attributes, variation, front_image,
  ungraded, grade_9, grade_10,
  quantity, status, published, price, date_added, date_modified
)
SELECT 
  NULL,  -- pas de listing
  cs.card_set_id,
  csb.title,
  csb.card_number,
  csb.player,
  csb.team,
  csb.year,
  csb.brand,
  csb.attributes,
  csb.variation,
  csb.front_image,
  csb.ungraded,
  csb.grade_9,
  csb.grade_10,
  0,     -- quantity = 0 → référence seule
  1,     -- status actif
  0,     -- pas publié
  0,     -- pas de prix listing
  csb.date_added,
  csb.date_modified
FROM oc_card_set_backup csb
JOIN oc_card_set cs ON csb.year = cs.year AND csb.brand = cs.brand AND csb.set_name = cs.set_name
WHERE NOT EXISTS (
  SELECT 1 FROM oc_card c 
  WHERE c.card_number = csb.card_number AND c.year = csb.year 
    AND c.brand = csb.brand AND c.player_name = csb.player
);
```

### Phase 2D : Modifier `oc_card_price_sold` (ajouter card_id FK, drop doublons)

```sql
-- Ajouter card_id FK
ALTER TABLE oc_card_price_sold 
  ADD COLUMN card_id INT(11) DEFAULT NULL AFTER card_price_sold_id,
  ADD KEY idx_card_id (card_id);

-- Peupler card_id via match card_number + set_name → card → card_set
UPDATE oc_card_price_sold s
JOIN oc_card_set cs ON s.set_name = cs.set_name AND s.year = cs.year AND s.brand = cs.brand
JOIN oc_card c ON c.card_set_id = cs.card_set_id 
  AND c.card_number = s.card_number AND c.player_name = s.player
SET s.card_id = c.card_id;

-- VÉRIF AVANT DROP : 0 nulls
SELECT COUNT(*) FROM oc_card_price_sold WHERE card_id IS NULL;

-- Supprimer colonnes dupliquées
ALTER TABLE oc_card_price_sold
  DROP COLUMN title,
  DROP COLUMN category,
  DROP COLUMN year,
  DROP COLUMN brand,
  DROP COLUMN set_name,
  DROP COLUMN subset,
  DROP COLUMN player,
  DROP COLUMN card_number,
  DROP COLUMN attributes,
  DROP COLUMN team,
  DROP COLUMN variation,
  DROP COLUMN front_image;
```

### Phase 2E : Modifier `oc_card_price_active` (card_raw_id → card_id)

```sql
-- Mapper card_raw_id (ancien PK card_set flat) → card_id (nouveau)
ALTER TABLE oc_card_price_active ADD COLUMN card_id INT(11) DEFAULT NULL AFTER active_id;

UPDATE oc_card_price_active a
JOIN oc_card_set_backup csb ON a.card_raw_id = csb.card_raw_id
JOIN oc_card c ON c.card_number = csb.card_number AND c.year = csb.year 
  AND c.brand = csb.brand AND c.player_name = csb.player
SET a.card_id = c.card_id;

-- VÉRIF
SELECT COUNT(*) FROM oc_card_price_active WHERE card_id IS NULL;

-- Remplacer la colonne
ALTER TABLE oc_card_price_active 
  DROP KEY card_raw_id,
  DROP COLUMN card_raw_id,
  ADD KEY idx_card_id (card_id);

-- Même chose pour oc_card_price_raw
ALTER TABLE oc_card_price_raw ADD COLUMN card_id INT(11) DEFAULT NULL AFTER raw_id;
ALTER TABLE oc_card_price_raw DROP COLUMN card_raw_id, ADD KEY idx_card_id (card_id);
```

### Phase 2F : Optionnel — Lier oc_card_listing → oc_card_set

```sql
ALTER TABLE oc_card_listing 
  ADD COLUMN card_set_id INT(10) UNSIGNED DEFAULT NULL AFTER listing_id,
  ADD KEY idx_card_set_id (card_set_id);

UPDATE oc_card_listing cl
JOIN oc_card_set cs ON cl.year = cs.year AND cl.brand = cs.brand 
  AND cl.set_name = cs.set_name AND COALESCE(cl.subset, '') = cs.subset
SET cl.card_set_id = cs.card_set_id;
```

---

## 5. MODÈLES PHP — OpenCart naming conventions

### 5A. Nouveau modèle `model/shopmanager/card/card_set.php` (RÉÉCRIRE)

**Classe** : `CardSet` | **Namespace** : `Opencart\Admin\Model\Shopmanager\Card`

| Méthode | Description |
|---|---|
| `addCardSet(array $data): int` | INSERT oc_card_set |
| `editCardSet(int $card_set_id, array $data): void` | UPDATE oc_card_set |
| `deleteCardSet(int $card_set_id): void` | DELETE oc_card_set |
| `getCardSet(int $card_set_id): array` | SELECT single set |
| `getCardSets(array $data = []): array` | SELECT list avec filtres |
| `getTotalCardSets(array $data = []): int` | COUNT avec filtres |
| `getFilteredDistinct(string $field, array $context): array` | Cascading dropdowns (utilise oc_card JOIN oc_card_set) |

### 5B. Modèle existant `model/shopmanager/card/card.php` (MODIFIER)

**Nouvelles méthodes / modifications :**

| Méthode | Description |
|---|---|
| `getCard(int $card_id): array` | Existant — ajouter JOIN oc_card_set |
| `getCards(array $data = []): array` | Existant — ajouter filter par card_set_id + prix référence |
| `getTotalCards(array $data = []): int` | Existant |
| `addCard(array $data): int` | Existant — ajouter card_set_id, front_image, ungraded, grade_9, grade_10 |
| `editCard(int $card_id, array $data): void` | Existant — idem |
| `getCardsByCardSetId(int $card_set_id): array` | NOUVEAU — toutes les cartes d'un set |
| `getTotalCardsByCardSetId(int $card_set_id): int` | NOUVEAU |

### 5C. Modèle `model/shopmanager/card/import/card_sold_importer.php` (MODIFIER)

| Ancien | Nouveau | Raison |
|---|---|---|
| `insertSoldRecord(array $row): int` | `addCardPriceSold(array $data): int` | Pattern OC + INSERT avec card_id au lieu des 12 colonnes |
| `getSoldRecords(array $data): array` | `getCardPriceSolds(array $data): array` | Pattern OC + SELECT via JOIN oc_card |
| `getTotalSoldRecords(array $data): int` | `getTotalCardPriceSolds(array $data): int` | Pattern OC |

### 5D. Modèle `model/shopmanager/card/import/card_price_active.php` (MODIFIER)

| Ancien | Nouveau | Raison |
|---|---|---|
| `insertRaw()` | `addCardPriceRaw()` | Pattern OC |
| `insertActive()` | `addCardPriceActive()` | Pattern OC + card_id au lieu de card_raw_id |
| `getActiveList()` | `getCardPriceActives()` | Pattern OC |
| `getActiveTotalRows()` | `getTotalCardPriceActives()` | Pattern OC |
| `getCardSetAll()` | `getCards()` | Utilise oc_card au lieu de oc_card_set_backup |
| `getCardSetWithoutActivePrices()` | `getCardsWithoutActivePrices()` | Nom clair |

### 5E. Modèle `model/shopmanager/card/import/card_set_importer.php` (MODIFIER)

| Ancien | Nouveau | Raison |
|---|---|---|
| `getCardPrices()` | `getCardSets()` | Import de sets (CSV → oc_card_set + oc_card) |
| `getTotalCardPrices()` | `getTotalCardSets()` | Pattern OC |
| Table cible: `oc_card_set` flat | Cible: `oc_card_set` + `oc_card` | Split INSERT en 2 temps |

---

## 6. INVENTAIRE COMPLET DES FICHIERS

### SQL (1 script)
| # | Fichier | Action |
|---|---|---|
| SQL-1 | `dev/migrate_card_normalization.sql` | Script complet phases 2A → 2F |

### Models PHP (5 fichiers)
| # | Fichier | Action |
|---|---|---|
| M-1 | `model/shopmanager/card/card_set.php` | RÉÉCRIRE — Set-level CRUD (plus de colonnes carte/prix) |
| M-2 | `model/shopmanager/card/card.php` | MODIFIER — Ajouter card_set_id, JOIN card_set, nouvelles colonnes |
| M-3 | `model/shopmanager/card/import/card_set_importer.php` | MODIFIER — Import targete oc_card_set + oc_card |
| M-4 | `model/shopmanager/card/import/card_sold_importer.php` | MODIFIER — card_id FK, rename fonctions, JOIN card |
| M-5 | `model/shopmanager/card/import/card_price_active.php` | MODIFIER — card_id FK, rename fonctions |

### Controllers PHP (5 fichiers)
| # | Fichier | Changements |
|---|---|---|
| C-1 | `controller/shopmanager/card/search.php` | Utiliser card.php + card_set.php, adapter getList() |
| C-2 | `controller/shopmanager/card/import/card_set_importer.php` | Adapter appels fonctions renommées |
| C-3 | `controller/shopmanager/card/import/card_sold_importer.php` | Adapter appels + JOIN data |
| C-4 | `controller/shopmanager/card/import/card_price_active.php` | card_raw_id → card_id, fonctions renommées |
| C-5 | `controller/shopmanager/card/import/card_importer.php` | card_raw_id → card_id |

### Twig Templates (5 fichiers)
| # | Fichier | Changements |
|---|---|---|
| T-1 | `template/shopmanager/card/search_list.twig` | card_raw_id → card_id dans data |
| T-2 | `template/shopmanager/card/import/card_set_importer_list.twig` | card_raw_id → card_set_id |
| T-3 | `template/shopmanager/card/import/card_price_active.twig` | card_raw_id → card_id |
| T-4 | `template/shopmanager/card/import/card_sold_importer_list.twig` | Colonnes depuis JOIN |
| T-5 | `template/shopmanager/card/card_listing.twig` | Vérifier refs |

### JavaScript (1 fichier)
| # | Fichier | Changements |
|---|---|---|
| J-1 | `javascript/shopmanager/card/import/card_price_active.js` | card_raw_id → card_id |

### Dev scripts (1 fichier)
| # | Fichier | Changements |
|---|---|---|
| D-1 | `dev/create_tables_card_price.php` | Mettre à jour DDL |

### Language files (0 changements)
Pas de références aux noms de colonnes DB.

---

## 7. ORDRE D'EXÉCUTION

### Phase 1 : Git + Backup
```
[ ] 1.1  git add -A && git commit -m "pre-normalization snapshot"
[ ] 1.2  git checkout -b refactor/card-db-normalization
[ ] 1.3  mysqldump des 4 tables card_set, card_price_sold, card_price_active, card_price_raw
```

### Phase 2 : SQL Migration
```
[ ] 2.1  Créer dev/migrate_card_normalization.sql
[ ] 2.2  Exécuter 2A: Nouvelle oc_card_set (set-level)
[ ] 2.3  Exécuter 2B: ALTER oc_card (ajout colonnes + FK nullable)
[ ] 2.4  Exécuter 2C: Migrer 1223 cartes → oc_card + enrichir 354 existantes
[ ] 2.5  Vérifier: SELECT COUNT(*) FROM oc_card WHERE card_set_id IS NOT NULL → 1551+
[ ] 2.6  Exécuter 2D: oc_card_price_sold add card_id + drop doublons
[ ] 2.7  Exécuter 2E: oc_card_price_active card_raw_id → card_id
[ ] 2.8  Exécuter 2F: Lier oc_card_listing → oc_card_set (optionnel)
[ ] 2.9  Vérifications post-migration (section 8)
```

### Phase 3 : Models PHP
```
[ ] 3.1  M-1: Réécrire card_set.php (CRUD set-level seulement)
[ ] 3.2  M-2: Modifier card.php (ajouter card_set_id, nouvelles colonnes, JOINs)
[ ] 3.3  M-3: Modifier card_set_importer.php (INSERT split set + card)
[ ] 3.4  M-4: Modifier card_sold_importer.php (card_id FK, rename)
[ ] 3.5  M-5: Modifier card_price_active.php (card_id FK, rename)
```

### Phase 4 : Controllers
```
[ ] 4.1  C-1: search.php (adapter aux nouveaux models)
[ ] 4.2  C-2: import/card_set_importer.php
[ ] 4.3  C-3: import/card_sold_importer.php
[ ] 4.4  C-4: import/card_price_active.php
[ ] 4.5  C-5: import/card_importer.php
```

### Phase 5 : Templates + JS
```
[ ] 5.1  T-1 à T-5: Twig templates
[ ] 5.2  J-1: card_price_active.js
```

### Phase 6 : Validation
```
[ ] 6.1  php -l sur tous les fichiers modifiés
[ ] 6.2  Test: Card Search (filter + list)
[ ] 6.3  Test: Card Set Importer (upload CSV)
[ ] 6.4  Test: Card Sold Importer (list + insert)
[ ] 6.5  Test: Card Price Active (fetch + process + list)
[ ] 6.6  Test: Card Importer (price lookup)
[ ] 6.7  git commit + merge si OK
[ ] 6.8  DROP TABLE oc_card_set_backup (après 1 semaine sans problème)
```

---

## 8. VÉRIFICATIONS POST-MIGRATION

```sql
-- Compteurs
SELECT 'oc_card_set' as tbl, COUNT(*) as cnt FROM oc_card_set;       -- ~211+ sets
SELECT 'oc_card' as tbl, COUNT(*) as cnt FROM oc_card;               -- ~4095 (2872 + 1223)
SELECT 'oc_card_price_sold', COUNT(*) FROM oc_card_price_sold;       -- 7980 (inchangé)
SELECT 'oc_card_price_active', COUNT(*) FROM oc_card_price_active;   -- 12916 (inchangé)

-- 0 orphelins
SELECT COUNT(*) FROM oc_card WHERE card_set_id IS NULL AND listing_id IS NULL;  -- 0
SELECT COUNT(*) FROM oc_card_price_sold WHERE card_id IS NULL;                   -- 0
SELECT COUNT(*) FROM oc_card_price_active WHERE card_id IS NULL;                 -- 0

-- JOINs fonctionnent
SELECT c.card_id, c.player_name, cs.set_name, cs.category,
  s.price, s.grader, s.date_sold
FROM oc_card c
JOIN oc_card_set cs ON cs.card_set_id = c.card_set_id
LEFT JOIN oc_card_price_sold s ON s.card_id = c.card_id
WHERE c.ungraded > 50
LIMIT 5;

-- Cartes de référence (pas listées)
SELECT COUNT(*) FROM oc_card WHERE listing_id IS NULL AND quantity = 0;  -- ~1223
```

---

## 9. RISQUES ET MITIGATION

| Risque | Impact | Mitigation |
|---|---|---|
| CASCADE DELETE listing → perd la carte | CRITIQUE | FK changée en ON DELETE SET NULL |
| card_set_importer CSV utilise colonnes plates | Import cassé | Adapter parser: ligne CSV → INSERT card_set + INSERT card |
| card_raw_id référencé partout | Erreurs PHP | Recherche exhaustive + remplacement systématique |
| Performance JOIN 3 tables | Minime | Index card_set_id + card_id déjà prévus, tables < 15K rows |
| Backup table prend de l'espace | Négligeable | DROP après 1 semaine de validation |

---

## 10. DIAGRAMME FINAL

```
oc_card_type (17)              oc_card_manufacturer (136)
     │                                │
     │ card_type_id                   │ manufacturer_id
     ▼                                ▼
oc_card_listing (237) ◄──────── oc_card_manufacturer_card_type
     │ listing_id                 
     │ card_set_id (NEW FK)       oc_card_set (211+)
     │                                │ card_set_id
     ▼                                ▼
oc_card (4095+) ◄──── card_set_id FK ─┘
     │ card_id          listing_id FK ─┘
     │
     ├──→ oc_card_price_sold (7980)     via card_id FK
     ├──→ oc_card_price_active (12916)  via card_id FK
     ├──→ oc_card_price_raw (staging)   via card_id FK
     ├──→ oc_card_image (8052)          via card_id FK existant
     └──→ oc_card_grading_company (10)  ref table
```
