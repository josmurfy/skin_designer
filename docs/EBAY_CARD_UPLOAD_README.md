# eBay Card Upload Generator

## Description
Ce module permet de générer des fichiers CSV formatés pour l'upload eBay à partir d'un fichier CSV contenant des informations sur des cartes sportives ou autres types de cartes. Le système supporte deux modes d'upload:

1. **Listings individuels** - Chaque carte devient une annonce eBay séparée
2. **Listing multi-variations** - Toutes les cartes sont regroupées dans une seule annonce avec variations

## Fichiers créés

### Backend (MVC-L)
- **Controller**: `administrator/controller/shopmanager/ebay/ebay_card_upload.php`
- **Model**: `administrator/model/shopmanager/ebay/ebay_card_upload.php`
- **View (Twig)**: `administrator/view/template/shopmanager/ebay/ebay_card_upload.twig`
- **JavaScript**: `administrator/view/javascript/shopmanager/ebay/ebay_card_upload.js`

### Langue (i18n)
- **Anglais**: `administrator/language/en-gb/shopmanager/ebay/ebay_card_upload.php`
- **Français**: `administrator/language/fr-fr/shopmanager/ebay/ebay_card_upload.php`
- **Espagnol**: `administrator/language/es-es/shopmanager/ebay/ebay_card_upload.php`

### Documentation
- **Exemple CSV**: `docs/ebay_card_upload_example.csv`

## Format du fichier CSV

### Colonnes requises
Le fichier CSV doit contenir au minimum les colonnes suivantes (ordre flexible):

| Colonne | Description | Exemple |
|---------|-------------|---------|
| `title` | Titre complet de la carte | "1993-94 Fleer #22 Alonzo Mourning" |
| `description` | Description HTML de la carte | "<p>This is a nice example...</p>" |
| `sale_price` | Prix de vente | 1.34 |
| `front_image` | URL de l'image frontale | https://storage.googleapis.com/... |
| `condition` | État de la carte | Near Mint or Better |

### Colonnes optionnelles
| Colonne | Description | Exemple |
|---------|-------------|---------|
| `year` | Année de la carte | 1993 |
| `brand` | Marque/fabricant | Fleer, Topps, Panini |
| `set` | Nom du set | 1993-94 Fleer |
| `player` | Nom du joueur | Alonzo Mourning |
| `card_number` | Numéro de la carte | 22 |
| `team` | Équipe | Charlotte Hornets |
| `back_image` | URL de l'image arrière | https://storage.googleapis.com/... |
| `market_price` | Prix du marché | 1.34 |
| `category` | Catégorie | BASKETBALL |
| `sku` | SKU interne | 846 |

### Exemple de fichier CSV
```csv
title,description,sale_price,year,brand,set,player,card_number,team,condition,front_image,back_image
"1993-94 Fleer #22 Alonzo Mourning","<p>This is a nice example...</p>",1.34,1993,Fleer,"1993-94 Fleer","Alonzo Mourning",22,"Charlotte Hornets","Near Mint or Better",https://storage.googleapis.com/...front.jpg,https://storage.googleapis.com/...back.jpg
"1994-95 Topps #172 Danny Ferry","<p>This is a nice example...</p>",4.98,1994,Topps,"1994-95 Topps","Danny Ferry",172,"Cleveland Cavaliers","Near Mint or Better",https://storage.googleapis.com/...front.jpg,https://storage.googleapis.com/...back.jpg
```

Voir fichier complet: `docs/batch-870409-export.csv` (exemple réel avec 50 cartes)

## Utilisation

### 1. Accès au module
Dans l'interface admin OpenCart:
```
Administration → ShopManager → eBay → Card Upload
```
Ou via URL directe:
```
https://phoenixliquidation.ca/administrator/index.php?route=shopmanager/ebay/ebay_card_upload&user_token=VOTRE_TOKEN
```

### 2. Préparation du fichier CSV
1. Créez un fichier CSV avec les colonnes requises
2. Remplissez les données pour chaque carte
3. Pour les images Google Drive:
   - Partagez l'image (lien public)
   - Utilisez le format: `https://drive.google.com/uc?id=ID_DE_FICHIER`
   - Alternative: Utilisez n'importe quelle URL d'image accessible publiquement

### 3. Upload et configuration
1. **Télécharger le CSV**: Cliquez sur "Upload CSV" et sélectionnez votre fichier
2. **Configurer l'annonce**:
   - **Type de listing**: Single (individuel) ou Multi (variations)
   - **Titre**: Pour les listings multi-variations (max 80 caractères)
   - **Catégorie eBay**: ID de catégorie (ex: 261328 pour Sports Trading Cards)
   - **Condition**: État général des cartes (New, Used, Good, etc.)
   - **Prix d'expédition**: Coût d'expédition en USD
   - **Délai de traitement**: Nombre de jours pour l'expédition

### 4. Aperçu et génération
1. **Prévisualiser**: Vérifiez les données dans le tableau d'aperçu
2. **Générer**: Cliquez sur "Generate eBay CSV"
3. Le fichier sera créé au format eBay File Exchange

### 5. Téléchargement
1. Cliquez sur "Download File" pour télécharger le CSV généré
2. Importez-le directement sur eBay via File Exchange

## Format de sortie eBay

Le fichier généré respecte le format **eBay File Exchange** avec les en-têtes appropriés:

### Mode Single Listings
Chaque ligne = 1 annonce eBay distincte avec:
- Action, Category, Title, Description
- Price, Quantity, Image URL
- Format (FixedPrice), Duration (GTC)
- Shipping options, Location, Condition

### Mode Multi-Variation
- 1ère ligne = Annonce principale avec détails complets
- Lignes suivantes = Variations (autres cartes) avec prix/image différents
- Les variations utilisent les colonnes personnalisées (C:Card Name, C:Condition, etc.)

## Catégories eBay courantes pour cartes

| Catégorie | ID eBay |
|-----------|---------|
| Sports Trading Cards | 261328 |
| Non-Sport Trading Cards | 222 |
| Trading Card Singles | 183050 |
| Pokémon Cards | 183454 |
| Magic: The Gathering | 19107 |
| Yu-Gi-Oh! Cards | 31395 |

## Codes de condition eBay

| Code | Description |
|------|-------------|
| 1000 | New |
| 1500 | New other (see details) |
| 3000 | Used |
| 4000 | Very Good |
| 5000 | Good |
| 6000 | Acceptable |

## URLs d'images Google Drive

Pour utiliser des images depuis Google Drive:

1. **Télécharger l'image sur Google Drive**
2. **Partager le fichier**: Clic droit → Partager → "Toute personne disposant du lien"
3. **Copier l'ID du fichier**: 
   - URL partagée: `https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz/view?usp=sharing`
   - ID: `1AbCdEfGhIjKlMnOpQrStUvWxYz`
4. **Utiliser dans le CSV**: `https://drive.google.com/uc?id=1AbCdEfGhIjKlMnOpQrStUvWxYz`

## Dépannage

### Erreur "No file uploaded"
- Vérifiez que vous avez sélectionné un fichier avant de cliquer sur Upload

### Erreur "Invalid file format"
- Le fichier doit être au format .csv
- Vérifiez l'encodage (UTF-8 recommandé)

### Erreur "CSV file is empty"
- Le fichier ne contient pas de données
- Vérifiez que les en-têtes de colonnes sont présents
- Assurez-vous qu'il y a au moins une ligne de données

### Images non affichées
- Vérifiez que les URLs sont accessibles publiquement
- Testez l'URL dans un navigateur web
- Pour Google Drive, assurez-vous que le partage est activé

## Points techniques

### Respecte les guidelines OpenCart
- **MVC-L Pattern**: Séparation stricte Controller/Model/View
- **Controller**: Orchestration uniquement (pas de SQL, pas de HTML)
- **Model**: SQL queries seulement (utilise `DB_PREFIX`)
- **View/Twig**: Affichage seulement (pas de logique)
- **Language**: 3 langues (EN/FR/ES)

### JavaScript décentralisé
- Fonctions utilitaires dupliquées pour isolation
- Pas de dépendance à tools.js centralisé
- Compatibilité: jQuery, Bootstrap 5, FontAwesome

### Sécurité
- Validation des fichiers uploadés (type, taille)
- Escape des données utilisateur (XSS protection)
- User token requis pour toutes les actions AJAX
- Cast des IDs en integers dans le model

## Accès via menu admin

Pour ajouter au menu ShopManager, modifier:
```
administrator/controller/common/column_left.php
```

Ajouter:
```php
$data['menus'][] = [
    'id'       => 'menu-shopmanager-ebay-card-upload',
    'icon'     => 'fa-solid fa-file-csv',
    'name'     => 'eBay Card Upload',
    'href'     => $this->url->link('shopmanager/ebay/ebay_card_upload', 'user_token=' . $this->session->data['user_token']),
    'children' => []
];
```

## Support et améliorations

### Améliorations futures possibles
- [ ] Support de templates d'annonces personnalisés
- [ ] Validation des catégories eBay via API
- [ ] Prévisualisation des annonces avant génération
- [ ] Import d'images depuis dossier local
- [ ] Historique des uploads avec réutilisation
- [ ] Export vers d'autres marketplaces (Walmart, Amazon)

### Contact
Pour des questions ou améliorations, consulter:
- `docs/DEV_GUIDELINES.md` - Standards de développement
- `docs/OPENCART_GUIDELINES.md` - Conventions OpenCart
- `docs/DEV_PATTERNS.md` - Patterns réutilisables

---

**Dernière mise à jour**: 2026-02-09  
**Version**: 1.0.0  
**Développé pour**: OpenCart 4.x / PhoenixLiquidation
