<?php
// Heading
$_['heading_title']       = 'Debug Logger';

// Text
$_['text_extension']      = 'Extensions';
$_['text_success']        = 'Paramètres du Debug Logger sauvegardés.';
$_['text_success_clear']  = 'Tous les rapports ont été supprimés.';
$_['text_enabled']        = 'Activé';
$_['text_general']        = 'Général';
$_['text_display']        = 'Affichage';
$_['text_capture']        = 'Options de capture';
$_['text_severity']       = 'Niveaux de sévérité';
$_['text_data']           = 'Gestion des données';
$_['text_stats']          = 'Statistiques';
$_['text_total_reports']  = 'Total rapports';
$_['text_open_reports']   = 'Ouverts / Non résolus';
$_['text_from_admin']     = 'Depuis Admin';
$_['text_from_catalog']   = 'Depuis Catalogue';
$_['text_show_admin']     = 'Afficher dans l\'Admin';
$_['text_show_catalog']   = 'Afficher en Vitrine';
$_['text_info_title']     = 'Comment ça marche';
$_['text_info_1']         = 'Installez le module pour créer la table DB et activer les événements.';
$_['text_info_2']         = 'Après sauvegarde, le bouton 🐛 Debug apparaît dans les zones activées.';
$_['text_info_3']         = 'Cliquez sur le bouton pour soumettre un rapport avec erreurs console et commentaire.';

// Entries
$_['entry_status']           = 'Statut';
$_['entry_admin_enable']     = 'Panneau Admin';
$_['entry_catalog_enable']   = 'Catalogue (Vitrine)';
$_['entry_capture_console']  = 'Capturer erreurs console';
$_['entry_capture_network']  = 'Capturer AJAX échoués';
$_['entry_require_comment']  = 'Commentaire obligatoire';
$_['entry_max_reports']      = 'Rapports max';
$_['entry_severity_bug']     = 'Bug';
$_['entry_severity_warning'] = 'Avertissement';
$_['entry_severity_info']    = 'Information';

// Help
$_['help_admin_enable']      = 'Affiche le bouton 🐛 dans la barre de navigation admin.';
$_['help_catalog_enable']    = 'Affiche un bouton 🐛 flottant sur toutes les pages de la vitrine.';
$_['help_capture_console']   = 'Capture automatiquement les console.error() et exceptions JS non gérées.';
$_['help_capture_network']   = 'Intercepte et journalise les requêtes AJAX/fetch échouées.';
$_['help_require_comment']   = 'Force l\'utilisateur à saisir un commentaire avant de soumettre un rapport.';
$_['help_max_reports']       = 'Nombre maximum de rapports à conserver. Les plus anciens sont supprimés automatiquement (0 = illimité).';
$_['help_severity']          = 'Choisissez les niveaux affichés dans la liste déroulante du formulaire.';

// Buttons
$_['button_save']         = 'Sauvegarder';
$_['button_cancel']       = 'Annuler';
$_['button_view_reports'] = 'Voir les rapports';
$_['button_clear_all']    = 'Effacer tous les rapports';

// Popup modal (injected via event)
$_['popup_title']          = 'Signaler un problème';
$_['popup_btn_trigger']    = 'Signaler un problème';
$_['popup_label_page']     = 'Page';
$_['popup_label_severity'] = 'Sévérité';
$_['popup_label_comment']  = 'Commentaire';
$_['popup_label_console']  = 'Erreurs console';
$_['popup_placeholder']    = 'Décrivez le problème...';
$_['popup_severity_bug']   = '🐛 Bug';
$_['popup_severity_warn']  = '⚠ Avertissement';
$_['popup_severity_info']  = 'ℹ Info';
$_['popup_btn_cancel']     = 'Annuler';
$_['popup_btn_save']       = 'Envoyer';
$_['popup_btn_reports']    = 'Voir les rapports';
$_['popup_tip_severity']   = 'Choisissez le niveau d\'impact du problème observé.';
$_['popup_tip_comment']    = 'Décrivez ce que vous faisiez et ce qui s\'est mal passé. Obligatoire si "Commentaire requis" est activé.';
$_['popup_tip_console']    = 'Erreurs JavaScript capturées automatiquement sur cette page.';
$_['popup_tip_reports']    = 'Accéder à la liste des rapports (administrateurs seulement).';

// Confirm
$_['text_confirm_clear']  = 'Supprimer TOUS les rapports ? Cette action est irréversible.';

// Error
$_['error_permission']    = 'Attention : vous n\'avez pas la permission de modifier le Debug Logger !';

// Pro features
$_['text_license']           = 'Licence';
$_['entry_license_key']      = 'Clé de licence';
$_['help_license_key']       = 'Entrez votre clé Pro (XXXX-XXXX-XXXX-XXXX) pour débloquer toutes les fonctionnalités.';
$_['text_free_limit']        = 'Version gratuite : limitée à 50 rapports. Passez en Pro pour des rapports illimités et des fonctionnalités avancées.';
$_['text_disabled']          = 'Désactivé';

$_['entry_capture_screenshot'] = 'Capture d\'écran';
$_['help_capture_screenshot']  = 'Prend automatiquement une capture d\'écran de la page lors du signalement (html2canvas).';
$_['popup_label_screenshot']   = 'Capture d\'écran';

$_['text_email']             = 'Notifications par courriel';
$_['entry_email_enable']     = 'Activer courriel';
$_['entry_email_to']         = 'Destinataire';
$_['help_email_to']          = 'Adresse courriel pour recevoir les alertes de rapports de bogues.';
$_['text_email_severity']    = 'Notifier pour';
$_['entry_email_bug']        = 'Bug';
$_['entry_email_warning']    = 'Avertissement';
$_['entry_email_info']       = 'Information';

$_['text_webhook']           = 'Notifications Webhook';
$_['entry_webhook_type']     = 'Service';
$_['entry_webhook_url']      = 'URL Webhook';
$_['help_webhook_url']       = 'Collez votre URL Webhook Slack ou Discord ici.';

$_['text_pro_title']         = 'Fonctionnalités Pro';
$_['text_pro_1']             = 'Rapports illimités';
$_['text_pro_2']             = 'Capture d\'écran (html2canvas)';
$_['text_pro_3']             = 'Notifications par courriel lors de nouveaux rapports';
$_['text_pro_4']             = 'Intégration Webhook Slack / Discord';
$_['text_pro_5']             = 'Export CSV / JSON + assignation de rapports';
