# Debug: Regroupement des Cartes par Prix - Instructions

**Date**: 2026-02-16  
**Objectif**: Vérifier que les images sont regroupées selon le prix et que les quantités sont additionnées correctement avant l'insertion SQL.

---

## 🔧 Modifications Effectuées

### 1. **Publication eBay Désactivée (Temporaire)**
- **Fichier**: `administrator/controller/shopmanager/ebay/variant_listing_creator.php`
- **Ligne**: ~750
- **Changement**: `publishToEbay()` commenté et remplacé par un message d'erreur pour le debug
- **Pourquoi**: Permet de tester le regroupement et l'insertion SQL sans publier sur eBay

```php
// TEMPORARILY DISABLED FOR DEBUGGING
$publish_result = [
    'success' => false,
    'error' => 'eBay publishing disabled for debugging'
];
```

### 2. **Regroupement par Prix avec Addition des Quantités**
- **Fichier**: `administrator/model/shopmanager/card/card_listing.php`
- **Méthode**: `convertGroupToListing()`
- **Ligne**: ~1870
- **Logique**:
  - Les cartes sont regroupées par prix (arrondi à 2 décimales)
  - Les quantités sont additionnées pour les cartes au même prix
  - Les images sont combinées (max 12 images par variation - limite eBay)
  - Les métadonnées (player, team, card_number) sont agrégées

**Exemple**:
```
Avant:
- Card A: $1.95, qty=1, 2 images
- Card B: $1.95, qty=1, 2 images
- Card C: $1.95, qty=1, 2 images

Après regroupement:
- Variation: $1.95, qty=3, 6 images (front_1, back_1, front_2, back_2, front_3, back_3)
```

### 3. **Logs Détaillés pour Debug**
- **Fichier**: `administrator/model/shopmanager/card/card_listing.php`
- **Méthodes**: `convertGroupToListing()` et `saveListing()`
- **Logs ajoutés**:
  - Nombre de cartes dans chaque groupe
  - Détails de chaque carte (titre, prix, images)
  - Groupes créés par prix
  - Variations finales (prix, quantité, nombre d'images)
  - Valeurs calculées avant INSERT SQL
  - Requête SQL complète
  - Confirmation d'insertion pour chaque variation et image

---

## 📊 Comment Tester

### Étape 1: Préparer le Monitoring des Logs
**Option A - Vider les logs et surveiller en temps réel**:
```bash
cd /home/n7f9655/public_html/phoenixliquidation
./watch_logs.sh
```

**Option B - Voir uniquement les logs de listing**:
```bash
cd /home/n7f9655/public_html/phoenixliquidation
./view_listing_logs.sh
```

**Option C - Voir directement le fichier error.log**:
```bash
tail -f /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log
```

### Étape 2: Tester le Workflow dans l'Admin

1. **Charger un fichier CSV** dans l'outil Variant Listing Creator
2. **Éditer les cartes** dans l'onglet "Edit Cards" (optionnel)
3. **Cliquer sur "Save to Database"**
4. **Observer les logs** dans le terminal pour voir:
   - Le regroupement par prix
   - L'addition des quantités
   - La combinaison des images
   - L'insertion SQL avec toutes les valeurs

### Étape 3: Vérifier les Résultats

**Dans les Logs (`error.log`)**:
```
=== convertGroupToListing: Starting price-based grouping ===
Total cards in group: 10
Card #0: title=Wayne Gretzky, price=1.95
Card #1: title=Mario Lemieux, price=1.95
Card #2: title=Bobby Orr, price=10.00
...
Price groups created: 2
Created variation: price=1.95, quantity=2, images=4
Created variation: price=10.00, quantity=1, images=2
Final variations count: 2
=== convertGroupToListing: Completed ===

=== saveListing: Starting SQL Insert Debug ===
Listing Data: {"set_name":"1990-91 Upper Deck","sport":"Hockey",...}
Variations Count: 2
Variation #0: {"title":"(2x) Wayne Gretzky","price":"1.95","quantity":2,"images_count":4,...}
...
quantity_total: 3
average_price: 4.3
SQL Query: INSERT INTO `oc_card_listing` SET ...
Listing created with ID: 123
Inserting 2 variations for listing_id 123
...
All variations inserted successfully
```

**Dans la Base de Données**:
```sql
-- Vérifier le listing
SELECT * FROM oc_card_listing WHERE listing_id = [ID];

-- Vérifier les variations (cards)
SELECT card_id, title, price, quantity 
FROM oc_card 
WHERE listing_id = [ID] 
ORDER BY price;

-- Vérifier les images
SELECT c.card_id, c.title, c.price, c.quantity, COUNT(i.image_id) as image_count
FROM oc_card c
LEFT JOIN oc_card_image i ON c.card_id = i.card_id
WHERE c.listing_id = [ID]
GROUP BY c.card_id;
```

---

## ✅ Points de Vérification

### Regroupement par Prix ✓
- [ ] Les cartes avec le même prix sont regroupées dans une seule variation
- [ ] Les quantités sont additionnées correctement (ex: 3 cartes à $1.95 = qty 3)
- [ ] Le titre de la variation indique la quantité: `(3x) Wayne Gretzky`

### Combinaison des Images ✓
- [ ] Toutes les images (front + back) sont ajoutées à la variation
- [ ] Les images sont numérotées: `front_1`, `back_1`, `front_2`, `back_2`, etc.
- [ ] Maximum 12 images par variation (limite eBay)
- [ ] Chaque image est insérée dans `oc_card_image` avec le bon `card_id`

### Insertion SQL ✓
- [ ] `quantity_total` = somme de toutes les quantités des variations
- [ ] `average_price` = moyenne des prix de toutes les variations
- [ ] Toutes les variations sont insérées dans `oc_card`
- [ ] Toutes les images sont insérées dans `oc_card_image`
- [ ] Les descriptions EN et FR sont créées dans `oc_card_listing_description`

---

## 🐛 Problèmes Connus à Surveiller

1. **Images Manquantes**: Si une carte n'a pas d'images, elle sera quand même regroupée
2. **Prix avec Décimales**: Les prix sont arrondis à 2 décimales pour le regroupement
3. **Limite des 12 Images**: Si plus de 6 cartes au même prix (6 × 2 images), seules les 12 premières seront gardées
4. **Métadonnées Multiples**: Si plusieurs players/teams dans un groupe, ils sont séparés par des virgules

---

## 🔄 Réactiver la Publication eBay

Une fois le debug terminé et tout validé:

**Fichier**: `administrator/controller/shopmanager/ebay/variant_listing_creator.php`  
**Ligne**: ~750

```php
// Step 4: Automatically publish to eBay Canada (English + French)
try {
    $publish_result = $this->publishToEbay($listing_id);
} catch (\Exception $e) {
    $publish_result = [
        'success' => false,
        'error' => 'Exception during eBay publishing: ' . $e->getMessage()
    ];
} catch (\Throwable $t) {
    $publish_result = [
        'success' => false,
        'error' => 'Fatal error during eBay publishing: ' . $t->getMessage()
    ];
}
```

**Supprimer les logs de debug** (optionnel):
- Retirer tous les `error_log()` ajoutés dans `convertGroupToListing()` et `saveListing()`

---

## 📝 Fichiers Modifiés

1. **administrator/controller/shopmanager/ebay/variant_listing_creator.php**
   - publishToEbay() désactivé temporairement

2. **administrator/model/shopmanager/card/card_listing.php**
   - convertGroupToListing(): Ajout du regroupement par prix
   - saveListing(): Ajout de logs détaillés pour debug

3. **watch_logs.sh** (créé)
   - Script pour vider et surveiller error.log en temps réel

4. **view_listing_logs.sh** (créé)
   - Script pour voir uniquement les logs de listing

---

## 💡 Commandes Utiles

```bash
# Vider complètement les logs avant un nouveau test
> /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log

# Voir les 50 dernières lignes des logs
tail -50 /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log

# Chercher uniquement les logs de regroupement
grep "Price groups created" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log

# Compter combien de variations ont été créées
grep "Final variations count" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log

# Vérifier les quantités calculées
grep "quantity_total" /home/n7f9655/public_html/storage_phoenixliquidation/logs/error.log
```

---

**Prêt à tester!** 🚀

Utilise `./watch_logs.sh` dans un terminal, puis clique sur "Save to Database" dans l'admin pour voir tous les détails du regroupement et de l'insertion SQL.
