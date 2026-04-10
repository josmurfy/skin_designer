# Debug Logger — Guide développeur

**Extension OpenCart 4.x** | Version 3.2.1 | Par PhoenixLiquidation

---

## Qu'est-ce que Debug Logger ?

Debug Logger ajoute un **bouton de signalement de bugs** sur chaque page de ton admin OpenCart (et optionnellement sur le storefront public). Quand un admin clique le bouton :

1. Un modal s'ouvre avec l'URL de la page courante (pré-rempli)
2. Les erreurs JavaScript sont **capturées automatiquement** en arrière-plan (console.error, exceptions)
3. Les requêtes réseau échouées peuvent aussi être capturées (fetch/AJAX qui retournent une erreur)
4. L'utilisateur choisit une sévérité (Bug / Warning / Info) et écrit un commentaire
5. En mode Pro : une capture d'écran de la page est prise et l'utilisateur peut l'annoter (dessiner, flèches, texte)
6. Tout est sauvegardé en base de données et consultable dans les pages **Reports** et **Analytics**

C'est un outil interne pour ton équipe — pas un outil destiné aux clients du store.

---

## Comment ça s'installe

L'extension s'installe via le Extension Installer d'OpenCart 4 (upload du fichier `.ocmod.zip`). L'installation :

- Crée **2 tables MySQL** (les rapports + les tags)
- Enregistre **3 événements** OpenCart pour injecter le bouton dans les pages
- Ajoute un menu **Debug Logger** dans la sidebar admin (column_left)

Aucune modification des fichiers core d'OpenCart. Tout passe par le système d'événements.

---

## Les 3 pages principales

### Page Settings
**Accès** : Menu sidebar → Debug Logger → Settings  
**Route** : `extension/debug_logger/module/debug_logger`

C'est la page de configuration avec 7 onglets :

| Onglet | Contenu |
|--------|---------|
| **General** | Activer/désactiver le module, activer le bouton admin et/ou catalog, voir les stats rapides |
| **Capture** | Choisir ce que le JS capture automatiquement (console, réseau, screenshot), exiger un commentaire, choisir les sévérités disponibles |
| **Notifications** | (Pro) Email automatique à chaque nouveau rapport, webhooks Slack ou Discord |
| **Appearance** | (Pro) Couleur du bouton/modal, position du bouton (navbar, coin de l'écran), taille |
| **Updates** | Vérifier les mises à jour sur GitHub, voir l'historique des versions, installer en 1 clic |
| **Permissions** | Choisir quels groupes d'utilisateurs admin voient le bouton debug |
| **License** | Entrer la clé Pro, voir les features débloquées |

### Page Reports
**Accès** : Menu sidebar → Debug Logger → Log Reports  
**Route** : `extension/debug_logger/module/debug_logger.reports`

C'est la page où on consulte et gère tous les rapports. On peut :
- **Filtrer** par statut (ouvert/fermé), par source (admin/catalog), par tag
- **Éditer inline** : cliquer sur le commentaire ou la résolution pour le modifier sur place
- **Tagger** les rapports (ajouter/retirer des étiquettes)
- **Assigner** un rapport à un admin (Pro)
- **Changer la sévérité** via un dropdown qui change de couleur en temps réel
- **Actions en masse** : cocher plusieurs rapports → fermer, rouvrir ou supprimer d'un coup
- **Exporter** en CSV ou JSON (Pro)
- Voir le **console log**, le **network log**, et la **capture d'écran** de chaque rapport

### Page Analytics
**Accès** : Menu sidebar → Debug Logger → Settings → bouton Analytics (ou via Reports)  
**Route** : `extension/debug_logger/module/debug_logger.analytics`

Dashboard visuel avec :
- **7 compteurs** : total rapports, ouverts, fermés, temps moyen de résolution, bugs, warnings, infos
- **4 graphiques Chart.js** : tendance quotidienne (30 jours), répartition par sévérité (doughnut), activité par heure (24h), répartition admin vs catalog (pie)
- **3 tableaux** : pages les plus problématiques, issues récurrentes, activité récente

---

## Base de données

L'extension crée 2 tables et utilise 4 tables OpenCart en lecture seule.

### Tables créées

**`{prefix}debug_report`** — Les rapports de bugs

| Colonne | Type | Rôle |
|---------|------|------|
| `id` | INT AUTO_INCREMENT | Identifiant unique |
| `url` | TEXT | URL de la page où le bug a été signalé |
| `console_log` | MEDIUMTEXT | Erreurs JavaScript capturées automatiquement |
| `network_log` | MEDIUMTEXT | Requêtes réseau échouées |
| `screenshot` | MEDIUMTEXT | Capture d'écran en base64 (Pro) |
| `comment` | TEXT | Commentaire de l'utilisateur |
| `resolution` | TEXT | Notes de résolution (rempli par l'admin qui corrige) |
| `admin_user` | VARCHAR(255) | Qui a soumis le rapport |
| `assigned_to` | INT | À qui le rapport est assigné (user_id, Pro) |
| `severity` | VARCHAR(20) | `bug`, `warning` ou `info` |
| `source` | VARCHAR(20) | `admin` ou `catalog` |
| `status` | TINYINT | 0 = ouvert, 1 = fermé |
| `date_added` | DATETIME | Date de création |

Index sur : `status`, `severity`, `assigned_to`

**`{prefix}debug_logger_tags`** — Étiquettes associées aux rapports

| Colonne | Type | Rôle |
|---------|------|------|
| `tag_id` | INT AUTO_INCREMENT | Identifiant du tag |
| `report_id` | INT | Lien vers `debug_report.id` |
| `tag_name` | VARCHAR(100) | Nom du tag (ex: "CSS", "mobile", "urgent") |

### Tables OpenCart utilisées (lecture seule)

| Table | Usage |
|-------|-------|
| `oc_setting` | Stocke toute la configuration du module (23+ clés avec le préfixe `module_debug_logger_*`) |
| `oc_user` | Liste des admins pour l'assignation de rapports |
| `oc_user_group` | Groupes d'utilisateurs pour les permissions d'accès au bouton |
| `oc_event` | Enregistrement des 3 événements qui injectent le bouton/modal/menu |

### Migrations automatiques

La table évolue entre les versions. L'extension vérifie automatiquement les colonnes manquantes à chaque chargement de la page Settings et ajoute ce qui manque :

| Version | Colonne ajoutée |
|---------|----------------|
| v2.2.0 | `screenshot` (MEDIUMTEXT) |
| v2.5.0 | `assigned_to` (INT) + index |
| v3.0.0 | `resolution` (TEXT) + table tags |

Pas besoin de lancer de migration manuellement — c'est automatique et idempotent.

---

## Les fichiers importants

### Côté admin

| Fichier | Rôle |
|---------|------|
| `admin/controller/module/debug_logger.php` | Le cerveau — gère les 3 pages + tous les appels AJAX (save, update, tags, bulk, export, etc.) |
| `admin/controller/event/header.php` | Injecte le bouton + modal dans le header admin, et le menu dans la sidebar |
| `admin/model/module/debug_logger.php` | Toutes les requêtes SQL (28 méthodes : CRUD rapports, tags, analytics, stats) |
| `admin/model/module/debug_logger_license.php` | Validation de la licence Pro (format `XXXX-XXXX-XXXX-XXXX`) |
| `admin/view/template/module/debug_logger.twig` | Template de la page Settings (7 onglets) |
| `admin/view/template/module/debug_logger_reports.twig` | Template de la page Reports (cartes, filtres, bulk actions) |
| `admin/view/template/module/debug_logger_analytics.twig` | Template de la page Analytics (Chart.js, stats, tableaux) |
| `admin/view/javascript/debug_logger.js` | JS injecté dans le header admin : capture console/réseau, modal, éditeur screenshot |
| `admin/view/javascript/chart.min.js` | Chart.js v4.4.7 pour les graphiques analytics |
| `admin/view/javascript/html2canvas.min.js` | Librairie pour capturer le DOM en image (Pro) |
| `admin/view/stylesheet/debug_logger.css` | Style du modal et du bouton debug (thème sombre) |
| `admin/language/{en-gb,fr-fr,es-es}/module/debug_logger.php` | Traductions (258+ clés par langue) |

### Côté catalog (storefront)

| Fichier | Rôle |
|---------|------|
| `catalog/controller/debug_logger.php` | Endpoint AJAX pour sauvegarder un rapport depuis le storefront (pas d'auth requise) |
| `catalog/controller/event/header.php` | Injecte un bouton flottant + modal simplifié sur le storefront |
| `catalog/model/module/debug_logger.php` | INSERT rapport + count + prune (3 méthodes seulement) |
| `catalog/view/javascript/debug_logger.js` | JS simplifié : capture console, modal basique, pas de screenshot |
| `catalog/view/stylesheet/debug_logger.css` | Style du bouton flottant + modal catalog |

---

## Comment le bouton arrive dans les pages

L'extension utilise le **système d'événements** d'OpenCart 4. Trois événements sont enregistrés :

| Événement | Déclencheur | Ce qu'il fait |
|-----------|-------------|---------------|
| `debug_logger_admin` | Après le rendu du header admin | Injecte le HTML du bouton, du modal, le CSS et le JS dans la page. Le bouton apparaît dans la navbar ou en position flottante selon la configuration. |
| `debug_logger_catalog` | Après le rendu du header storefront | Même principe mais version simplifiée : bouton flottant ambre en bas à droite, pas de screenshot. |
| `debug_logger_menu` | Avant le rendu du column_left admin | Ajoute le menu « Debug Logger » avec 3 sous-items (Dashboard, Log Reports, Settings) dans la sidebar admin. |

Le JS injecté dans le header intercepte les erreurs console et réseau **en continu** dès le chargement de la page. Quand l'utilisateur clique le bouton debug, le modal s'ouvre avec les erreurs déjà capturées.

---

## Configuration (clés dans `oc_setting`)

Toutes les clés utilisent le préfixe `module_debug_logger_`. Voici les principales :

### Général
| Clé | Défaut | Description |
|-----|--------|-------------|
| `_status` | 0 | Module activé oui/non |
| `_admin_enable` | 1 | Bouton visible dans l'admin |
| `_catalog_enable` | 0 | Bouton visible sur le storefront |
| `_max_reports` | 500 | Limite de rapports stockés (les plus anciens sont purgés) |

### Capture
| Clé | Défaut | Description |
|-----|--------|-------------|
| `_capture_console` | 1 | Intercepter `console.error()` et `window.onerror` |
| `_capture_network` | 0 | Intercepter les `fetch()` qui échouent |
| `_capture_screenshot` | 0 | Capture d'écran via html2canvas (Pro) |
| `_require_comment` | 0 | Le commentaire est obligatoire pour soumettre |
| `_severity_bug` / `_warning` / `_info` | 1 | Quels niveaux de sévérité sont proposés |

### Notifications (Pro uniquement)
| Clé | Description |
|-----|-------------|
| `_email_enable` | Envoyer un email à chaque nouveau rapport |
| `_email_to` | Adresse destinataire |
| `_email_bug` / `_warning` / `_info` | Filtrer par sévérité |
| `_webhook_type` | `slack` ou `discord` |
| `_webhook_url` | URL du webhook |

### Apparence (Pro uniquement)
| Clé | Défaut | Description |
|-----|--------|-------------|
| `_btn_color` | #dc2626 | Couleur du bouton |
| `_header_color` | #1e293b | Couleur du header modal |
| `_accent_color` | #3b82f6 | Couleur d'accent |
| `_btn_position` | navbar | `navbar`, `bottom-right`, `bottom-left`, `top-right`, `top-left` |
| `_btn_size` | medium | `small`, `medium`, `large` |

### Permissions
| Clé | Description |
|-----|-------------|
| `_allowed_groups` | JSON array des `user_group_id` autorisés (vide = tout le monde) |

### Licence
| Clé | Description |
|-----|-------------|
| `_license_key` | Clé format `XXXX-XXXX-XXXX-XXXX` pour débloquer le Pro |

---

## Free vs Pro

| Fonctionnalité | Free | Pro |
|----------------|------|-----|
| Rapports admin + catalog | ✅ | ✅ |
| Capture console/réseau | ✅ | ✅ |
| Capture d'écran + annotation | ❌ | ✅ |
| Notifications email | ❌ | ✅ |
| Webhooks Slack/Discord | ❌ | ✅ |
| Export CSV/JSON | ❌ | ✅ |
| Assignation rapports | ❌ | ✅ |
| Personnalisation visuelle | ❌ | ✅ |
| Limite rapports | 50 | illimité |

La licence Pro est validée **par format seulement** (regex). Toute clé `XXXX-XXXX-XXXX-XXXX` valide débloque le Pro — il n'y a pas de vérification serveur.

---

## Mise à jour automatique

L'onglet **Updates** dans Settings :

1. Vérifie l'API GitHub Releases du repo `josmurfy/debug-logger-releases`
2. Compare la version installée avec la dernière release
3. Affiche le changelog et l'historique complet
4. Permet l'installation en 1 clic (télécharge le ZIP, extrait, écrase les fichiers)
5. Cache les résultats pendant 6 heures pour éviter le rate-limiting GitHub

---

## Langues

3 langues supportées côté admin : **anglais** (en-gb), **français** (fr-fr), **espagnol** (es-es).

Les fichiers de langue contiennent 258+ clés chacun couvrant toutes les pages et le modal.

**Note** : Le côté catalog (storefront) a ses chaînes **hardcodées en anglais** — pas encore traduit.

---

## Ce qui reste à faire

### Priorité haute

| # | Description |
|---|-------------|
| 1 | **Validation de licence réelle** — N'importe quelle clé au bon format débloque le Pro. Il faut une vérification serveur ou cryptographique. |
| 2 | **Pagination des rapports** — La page Reports charge tout en une requête (max 500). Pas de pagination, pas de navigation par pages. Avec 10 000+ rapports ça sera lent. |
| 3 | **Recherche texte** — Aucun champ de recherche dans les rapports. On ne peut pas chercher par URL, commentaire ou contenu de console log. |
| 4 | **Traductions catalog** — Le bouton/modal du storefront est en anglais hardcodé. Les fichiers de langue FR/ES n'existent pas côté catalog. |

### Priorité moyenne

| # | Description |
|---|-------------|
| 5 | **Filtre par date** — Les rapports n'ont aucun filtre temporel (aujourd'hui, 7 jours, 30 jours, plage personnalisée). |
| 6 | **Tri des rapports** — Toujours trié par ID DESC. Pas d'option pour trier par sévérité, date, status ou assignation. |
| 7 | **Bouton Test Webhook** — Il y a un bouton « Test Email » mais rien pour tester la config Slack/Discord. |
| 8 | **Screenshot côté catalog** — Si la capture d'écran est activée dans les settings, le catalog l'ignore sans rien dire. |
| 9 | **Rate limiting catalog** — Le endpoint catalog accepte les POST sans authentification et sans limite. Un visiteur pourrait spammer la table. |

### Priorité basse

| # | Description |
|---|-------------|
| 10 | **Export avec filtre tag** — L'export CSV/JSON ne tient pas compte du filtre par tag. |
| 11 | **FK + cascade SQL** — La table tags n'a pas de FOREIGN KEY vers la table rapports. Si on supprime un rapport directement en SQL (pas via l'interface), les tags orphelins restent. |
| 12 | **Accessibilité** — Pas d'attributs ARIA sur les modals et boutons. Pas de focus trap. |
| 13 | **Dark mode** — Le toggle dark/light a été retiré en v3.2.1 quand les pages ont été intégrées dans le layout OC4. OpenCart 4 n'a pas de dark mode natif. |

### Roadmap suggérée

- **v3.3.0** — Pagination + recherche + filtre date dans Reports
- **v3.4.0** — Validation licence serveur + traductions catalog
- **v3.5.0** — Test webhook + rate limiting + dark mode global
- **v4.0.0** — Tests automatisés + accessibilité ARIA

---

*Version 3.2.1 — Avril 2026*
