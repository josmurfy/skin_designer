# Installation rapide - eBay Card Upload Generator

## Étapes d'installation

### 1. Vérification des fichiers
Tous les fichiers suivants ont été créés:

#### Backend
- ✅ `administrator/controller/shopmanager/ebay/ebay_card_upload.php`
- ✅ `administrator/model/shopmanager/ebay/ebay_card_upload.php`
- ✅ `administrator/view/template/shopmanager/ebay/ebay_card_upload.twig`
- ✅ `administrator/view/javascript/shopmanager/ebay/ebay_card_upload.js`

#### Langues
- ✅ `administrator/language/en-gb/shopmanager/ebay/ebay_card_upload.php`
- ✅ `administrator/language/fr-fr/shopmanager/ebay/ebay_card_upload.php`
- ✅ `administrator/language/es-es/shopmanager/ebay/ebay_card_upload.php`

#### Documentation
- ✅ `docs/EBAY_CARD_UPLOAD_README.md`
- ✅ `docs/ebay_card_upload_example.csv`
- ✅ `docs/ebay_card_upload_output_example.csv`
- ✅ `docs/ebay_card_upload_table.sql`
- ✅ `docs/EBAY_CARD_UPLOAD_INSTALL.md` (ce fichier)

### 2. Créer la table de base de données (optionnel)
Si vous voulez activer l'historique des uploads:

```bash
mysql -u USERNAME -p DATABASE_NAME < docs/ebay_card_upload_table.sql
```

Ou via phpMyAdmin:
1. Ouvrir phpMyAdmin
2. Sélectionner la base de données OpenCart
3. Onglet "SQL"
4. Coller le contenu de `docs/ebay_card_upload_table.sql`
5. Exécuter

**Note**: Cette table est optionnelle. Le module fonctionne sans elle.

### 3. Vérifier les permissions
Assurez-vous que le dossier d'upload a les permissions correctes:

```bash
chmod 755 /home/n7f9655/public_html/storage_phoenixliquidation/upload/
```

### 4. Ajouter au menu admin (optionnel)
Pour accéder facilement au module depuis le menu:

Éditer: `administrator/controller/common/column_left.php`

Dans la section ShopManager, ajouter:

```php
// Après les autres entrées ShopManager
$shopmanager[] = [
    'name'     => 'eBay Card Upload',
    'href'     => $this->url->link('shopmanager/ebay/ebay_card_upload', 'user_token=' . $this->session->data['user_token']),
    'children' => []
];
```

### 5. Accès direct (sans modification du menu)
URL directe:
```
https://phoenixliquidation.ca/administrator/index.php?route=shopmanager/ebay/ebay_card_upload&user_token=VOTRE_TOKEN
```

Pour obtenir votre token:
1. Connectez-vous à l'admin
2. Copiez la partie `user_token=...` depuis l'URL
3. Collez dans l'URL ci-dessus

### 6. Test rapide
1. Ouvrir le module via l'URL directe
2. Télécharger le fichier exemple: `docs/ebay_card_upload_example.csv`
3. Uploader le fichier
4. Configurer les options
5. Générer le fichier eBay
6. Télécharger et vérifier le résultat

## Configuration initiale recommandée

### Paramètres par défaut
Les valeurs suivantes sont définies par défaut:

| Paramètre | Valeur | Description |
|-----------|--------|-------------|
| Type de listing | Multi-variation | Toutes les cartes dans une annonce |
| Titre | Sports Trading Cards... | Peut être modifié |
| Catégorie | 261328 | Sports Trading Cards |
| Condition | 3000 (Used) | Condition générale |
| Expédition | $4.99 | USPS First Class |
| Délai | 2 jours | Temps de traitement |

Ajustez selon vos besoins!

## Utilisation

### Workflow typique
1. **Préparer CSV**: Créer fichier avec colonnes requises
2. **Images**: Upload sur Google Drive avec partage public
3. **Upload**: Charger le CSV dans le module
4. **Configuration**: Ajuster catégorie, prix, conditions
5. **Génération**: Créer le fichier eBay
6. **Import eBay**: Utiliser File Exchange sur eBay

### Format CSV minimum
```csv
card_name,price,quantity,image_url,condition
"Card Name",9.99,1,"https://image.url","Good"
```

### Colonnes optionnelles enrichies
```csv
card_name,price,quantity,image_url,condition,year,brand,player,team
"Michael Jordan",49.99,1,"https://...","Excellent",1997,"Upper Deck","M. Jordan","Bulls"
```

## Import sur eBay

### Via File Exchange
1. Connectez-vous à eBay Seller Hub
2. Allez dans **Listings** → **File Exchange**
3. Téléchargez le fichier CSV généré
4. eBay validera et créera les listings
5. Vérifiez les erreurs éventuelles
6. Publiez les annonces

### Conseils eBay
- **Catégories**: Utilisez l'outil de catégories eBay pour trouver le bon ID
- **Images**: URLs doivent être accessibles publiquement (HTTPS recommandé)
- **Variations**: Maximum 250 variations par listing
- **Titre**: 80 caractères max (eBay tronquera automatiquement)
- **Descriptions**: HTML autorisé mais limité

## Dépannage rapide

### Module non accessible
```bash
# Vérifier les permissions
ls -la administrator/controller/shopmanager/ebay_card_upload.php

# Doit être lisible
-rw-r--r-- 1 user group ... ebay_card_upload.php
```

### Erreur 500
- Vérifier les logs: `storage_phoenixliquidation/logs/error.log`
- Activer le debug dans `config.php`: `define('DEBUG', true);`

### Upload échoue
```bash
# Vérifier taille max upload PHP
php -i | grep upload_max_filesize

# Augmenter si nécessaire dans php.ini:
upload_max_filesize = 10M
post_max_size = 10M
```

### Images Google Drive non affichées
1. Vérifier partage public activé
2. Utiliser format: `https://drive.google.com/uc?id=FILE_ID`
3. Tester URL dans navigateur
4. Alternative: Héberger sur serveur propre

## Support

### Documentation complète
Voir: `docs/EBAY_CARD_UPLOAD_README.md`

### Ressources
- OpenCart Guidelines: `docs/OPENCART_GUIDELINES.md`
- Dev Patterns: `docs/DEV_PATTERNS.md`
- Dev Guidelines: `docs/DEV_GUIDELINES.md`

### Améliorations futures
Pour suggérer des améliorations, modifier `docs/EBAY_CARD_UPLOAD_README.md` section "Support et améliorations"

---

**Installation terminée!** 🎉

Accédez au module et commencez à générer vos listings eBay!

**URL d'accès**: `https://phoenixliquidation.ca/administrator/index.php?route=shopmanager/ebay/ebay_card_upload&user_token=VOTRE_TOKEN`
