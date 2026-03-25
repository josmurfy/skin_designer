# Multi-Variation Card Listing - Database Schema

## Architecture bilingue (EN/FR)

### Principe clé: UN listing eBay par langue

Chaque langue (English/French) a son **propre listing eBay** avec son propre `ebay_item_id`.

## Structure des tables

### 1. `oc_card_listing` (Table maître)
**Une ligne par SET + sport** (ex: "1993-94 Fleer" + "Basketball")

| Champ | Type | Description |
|-------|------|-------------|
| `listing_id` | INT PK | ID unique du listing interne |
| `set_name` | VARCHAR(255) | Nom du SET (ex: "1993-94 Fleer") |
| `sport` | VARCHAR(50) | Sport depuis CSV category (Basketball, Hockey, etc.) |
| `ebay_category_id` | VARCHAR(50) | ID catégorie eBay |
| `year` | INT(4) | Année du set |
| `brand` | VARCHAR(100) | Marque (Fleer, Panini, etc.) |
| `quantity_total` | INT | Nombre total de cartes |
| `price` | DECIMAL | Prix moyen |
| `status` | TINYINT | 0=draft, 1=actif, 2=terminé |

**Note**: `ebay_item_id` a été **RETIRÉ** de cette table car chaque langue a son propre listing eBay.

---

### 2. `oc_card_listing_description` (Multi-langue)
**Deux lignes par listing** (une EN, une FR)

| Champ | Type | Description |
|-------|------|-------------|
| `listing_id` | INT PK | Référence à `oc_card_listing` |
| `language_id` | INT PK | 1=English, 2=Français |
| `title` | VARCHAR(255) | Titre eBay (max 80 caractères) |
| `description` | TEXT | Description HTML |
| **`ebay_item_id`** | VARCHAR(50) | **ID du listing eBay pour cette langue** |
| `date_published` | DATETIME | Date de publication sur eBay |
| `meta_keyword` | VARCHAR(255) | Mots-clés SEO |

**⚠️ IMPORTANT**: `ebay_item_id` est maintenant ici car:
- Le listing English a son propre `ebay_item_id` (language_id=1)
- Le listing French a son propre `ebay_item_id` (language_id=2)
- Deux listings eBay séparés pour le même SET de cartes

---

### 3. `oc_card_variation` (Variations de cartes)
**Une ligne par carte individuelle**

| Champ | Type | Description |
|-------|------|-------------|
| `variation_id` | INT PK | ID unique de la variation |
| `listing_id` | INT FK | Référence au listing parent |
| `sku` | VARCHAR(100) | Format: `{card_number}_{player_name}` |
| `card_number` | VARCHAR(50) | Numéro de carte (#22, #45, etc.) |
| `player_name` | VARCHAR(255) | Nom du joueur |
| `team_name` | VARCHAR(255) | Équipe |
| `year` | INT(4) | Année |
| `brand` | VARCHAR(100) | Marque |
| `condition_name` | VARCHAR(100) | État (Near Mint, etc.) |
| `price` | DECIMAL | Prix CAD |
| `quantity` | INT | Quantité disponible |

**Note**: Les variations sont **partagées** entre les deux langues (même variations pour EN et FR).

---

### 4. `oc_card_variation_image` (Images des cartes)
**2 lignes par carte** (front + back)

| Champ | Type | Description |
|-------|------|-------------|
| `image_id` | INT PK | ID unique |
| `variation_id` | INT FK | Référence à `oc_card_variation` |
| `image_type` | VARCHAR(20) | 'front' ou 'back' |
| `image_url` | VARCHAR(500) | URL de l'image |
| `sort_order` | INT | Ordre d'affichage |

---

## Flux de publication eBay

### Étape 1: Création du listing interne
```sql
INSERT INTO oc_card_listing (set_name, sport, year, brand, ...)
VALUES ('1993-94 Fleer', 'Basketball', 1993, 'Fleer', ...);
-- Obtenir listing_id = 150
```

### Étape 2: Insertion des descriptions (EN + FR)
```sql
-- English
INSERT INTO oc_card_listing_description 
(listing_id, language_id, title, description)
VALUES (150, 1, '1993-94 Fleer Basketball Cards - Multiple Players', '<p>English description...</p>');

-- French
INSERT INTO oc_card_listing_description 
(listing_id, language_id, title, description)
VALUES (150, 2, 'Cartes Basketball 1993-94 Fleer - Plusieurs joueurs', '<p>Description française...</p>');
```

### Étape 3: Publication sur eBay (2 fois)
```php
// Publish English version
$ebay_response_en = $ebay->addItem([
    'title' => 'English title',
    'description' => 'English description',
    ...
]);
$ebay_item_id_en = $ebay_response_en['ItemID']; // ex: 185123456789

// Update English description
UPDATE oc_card_listing_description 
SET ebay_item_id = '185123456789',
    date_published = NOW()
WHERE listing_id = 150 AND language_id = 1;

// Publish French version
$ebay_response_fr = $ebay->addItem([
    'title' => 'French title',
    'description' => 'French description',
    ...
]);
$ebay_item_id_fr = $ebay_response_fr['ItemID']; // ex: 185987654321

// Update French description
UPDATE oc_card_listing_description 
SET ebay_item_id = '185987654321',
    date_published = NOW()
WHERE listing_id = 150 AND language_id = 2;
```

### Résultat final
- **1 listing interne** (listing_id=150) dans `oc_card_listing`
- **2 listings eBay** (ebay_item_id différents) dans `oc_card_listing_description`
- **N variations** partagées entre les deux listings eBay

---

## Requête pour récupérer les listings avec eBay IDs

```sql
SELECT 
    l.listing_id,
    l.set_name,
    l.sport,
    d.language_id,
    d.title,
    d.ebay_item_id,
    d.date_published,
    COUNT(v.variation_id) as card_count
FROM oc_card_listing l
LEFT JOIN oc_card_listing_description d ON l.listing_id = d.listing_id
LEFT JOIN oc_card_variation v ON l.listing_id = v.listing_id
WHERE l.status = 1
GROUP BY l.listing_id, d.language_id
ORDER BY l.date_added DESC;
```

Exemple de résultat:
| listing_id | set_name | sport | language_id | ebay_item_id | card_count |
|------------|----------|-------|-------------|--------------|------------|
| 150 | 1993-94 Fleer | Basketball | 1 | 185123456789 | 5 |
| 150 | 1993-94 Fleer | Basketball | 2 | 185987654321 | 5 |

---

## Migration depuis ancienne structure

Si vous aviez `ebay_item_id` dans `oc_card_listing`:

```sql
-- Copier vers descriptions English
UPDATE oc_card_listing_description d
INNER JOIN oc_card_listing l ON d.listing_id = l.listing_id
SET d.ebay_item_id = l.ebay_item_id,
    d.date_published = l.date_published
WHERE d.language_id = 1 AND l.ebay_item_id IS NOT NULL;

-- Retirer de card_listing
ALTER TABLE oc_card_listing DROP COLUMN ebay_item_id;
```

---

**Dernière mise à jour**: 2026-02-10  
**Validé sur**: OpenCart 4.x + MySQL 8.0
