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
$_['text_info_2']         = 'Après sauvegarde, le bouton Debug apparaît dans les zones activées.';
$_['text_info_3']         = 'Cliquez sur le bouton pour soumettre un rapport avec erreurs console et commentaire.';
$_['text_active_title']   = 'Debug Logger est actif';
$_['text_not_enabled']    = 'Activez le module et sauvegardez pour commencer à capturer les rapports.';
$_['text_notifications_pro_only'] = 'Les notifications nécessitent une licence Pro. Entrez votre clé dans l\'onglet Licence.';
$_['text_license_active'] = 'Licence Pro active — toutes les fonctionnalités débloquées.';

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
$_['help_admin_enable']      = 'Affiche le bouton Debug dans la barre de navigation admin.';
$_['help_catalog_enable']    = 'Affiche un bouton Debug flottant sur toutes les pages de la vitrine.';
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
$_['popup_severity_bug']   = 'Bug';
$_['popup_severity_warn']  = 'Avertissement';
$_['popup_severity_info']  = 'Info';
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
$_['text_test_email_title']  = 'Test de connexion';
$_['button_test_email']      = 'Envoyer un courriel test';
$_['help_test_email']        = 'Envoie un courriel test au destinataire ci-dessus avec les paramètres mail du magasin.';
$_['text_test_email_sent']   = 'Courriel test envoyé à %s — vérifiez votre boîte de réception (et spam).';
$_['text_test_email_failed'] = 'Échec de l\'envoi du courriel test : %s';
$_['text_test_email_invalid'] = 'Veuillez entrer une adresse courriel valide ci-dessus.';

$_['text_pro_title']         = 'Fonctionnalités Pro';
$_['text_pro_1']             = 'Rapports illimités';
$_['text_pro_1_desc']        = 'Plus de limite de 50. Conservez tout votre historique.';
$_['text_pro_2']             = 'Capture d\'écran';
$_['text_pro_2_desc']        = 'Capture automatique avec éditeur d\'annotation intégré.';
$_['text_pro_3']             = 'Notifications par courriel';
$_['text_pro_3_desc']        = 'Soyez notifié des nouveaux rapports avec capture en pièce jointe.';
$_['text_pro_4']             = 'Intégration Webhook';
$_['text_pro_4_desc']        = 'Envoyez les rapports vers Slack ou Discord instantanément.';
$_['text_pro_5']             = 'Export des rapports';
$_['text_pro_5_desc']        = 'Téléchargez les rapports en CSV ou JSON.';
$_['text_pro_6']             = 'Assignation des rapports';
$_['text_pro_6_desc']        = 'Assignez les rapports aux membres de l\'équipe.';

// Tabs
$_['tab_general']            = 'Général';
$_['tab_capture']            = 'Capture';
$_['tab_notifications']      = 'Notifications';
$_['tab_appearance']         = 'Apparence';
$_['tab_updates']            = 'Mises à jour';
$_['tab_permissions']        = 'Permissions';
$_['tab_license']            = 'Licence & Pro';

// Updates tab
$_['text_current_version']   = 'Version actuelle';
$_['text_latest_version']    = 'Dernière version';
$_['text_update_available']  = 'Une nouvelle version est disponible !';
$_['text_up_to_date']        = 'Vous utilisez la dernière version.';
$_['text_checking_update']   = 'Vérification des mises à jour...';
$_['text_update_error']      = 'Impossible de vérifier les mises à jour. Réessayez plus tard.';
$_['text_changelog']         = 'Notes de version';
$_['text_update_source']     = 'Les mises à jour sont vérifiées depuis GitHub :';
$_['button_check_update']    = 'Vérifier les mises à jour';
$_['button_download_update'] = 'Télécharger';
$_['button_view_release']    = 'Voir la release';
$_['button_install_update']  = 'Installer la mise à jour';
$_['text_installing']        = 'Téléchargement et installation en cours...';
$_['text_install_success']   = 'Mise à jour installée avec succès ! La version %s est maintenant active. Veuillez rafraîchir la page.';
$_['text_install_download_error'] = 'Impossible de télécharger le fichier de mise à jour.';
$_['text_install_extract_error']  = 'Impossible d\'extraire l\'archive de mise à jour.';
$_['text_version_history']       = 'Historique des versions';
$_['text_version_history_hint']  = 'Cliquez sur l\'onglet Mises à jour pour charger l\'historique.';
$_['text_version_installed']     = 'INSTALLÉE';
$_['text_version_newer']         = 'NOUVEAU';
$_['text_version_downgrade']     = 'Installer cette version';
$_['text_confirm_downgrade']     = 'Êtes-vous sûr de vouloir installer une version plus ancienne ? Cela écrasera la version actuelle.';
$_['button_refresh']             = 'Actualiser';

// Appearance tab (Pro)
$_['text_appearance_pro_only'] = 'La personnalisation de l\'apparence nécessite une licence Pro.';
$_['text_appearance_colors'] = 'Couleurs';
$_['text_appearance_layout'] = 'Disposition du bouton';
$_['text_appearance_preview'] = 'Aperçu';
$_['entry_btn_color']        = 'Couleur du bouton';
$_['help_btn_color']         = 'Couleur d\'arrière-plan du bouton de signalement.';
$_['entry_header_color']     = 'Couleur en-tête modale';
$_['help_header_color']      = 'Couleur d\'arrière-plan de l\'en-tête du popup.';
$_['entry_accent_color']     = 'Couleur d\'accentuation';
$_['help_accent_color']      = 'Couleur utilisée pour les bordures, liens et focus.';
$_['entry_btn_position']     = 'Position du bouton';
$_['help_btn_position']      = 'Où le bouton de signalement apparaît sur la page.';
$_['entry_btn_size']         = 'Taille du bouton';
$_['text_pos_navbar']        = 'Barre de navigation (défaut)';
$_['text_pos_bottom_right']  = 'Bas droite';
$_['text_pos_bottom_left']   = 'Bas gauche';
$_['text_pos_top_right']     = 'Haut droite';
$_['text_pos_top_left']      = 'Haut gauche';
$_['text_size_small']        = 'Petit';
$_['text_size_medium']       = 'Moyen';
$_['text_size_large']        = 'Grand';
$_['button_reset_defaults']  = 'Rétablir par défaut';

// Permissions tab
$_['text_permissions_info']  = 'Sélectionnez les groupes d\'utilisateurs autorisés à voir et utiliser le bouton Debug Logger. Si aucun n\'est sélectionné, tous les groupes ont accès.';
$_['text_allowed_groups']    = 'Groupes autorisés';
$_['help_allowed_groups']    = 'Cochez les groupes qui doivent voir le bouton de signalement Debug Logger dans le panneau admin.';
$_['text_group_name']        = 'Nom du groupe';

// v3.0.0 — Tags, Résolution, Actions groupées
$_['text_tags']              = 'Tags';
$_['text_add_tag']           = 'Ajouter un tag';
$_['text_resolution']        = 'Résolution';
$_['text_resolution_hint']   = 'Documentez la correction, le contournement ou la cause ici.';
$_['text_bulk_selected']     = '%d sélectionné(s)';
$_['text_bulk_close']        = 'Fermer la sélection';
$_['text_bulk_open']         = 'Rouvrir la sélection';
$_['text_bulk_delete']       = 'Supprimer la sélection';
$_['text_bulk_confirm_delete'] = 'Supprimer les rapports sélectionnés ?';
$_['text_assignment_email']  = 'Notification d\'assignation envoyée à %s.';
$_['text_filter_tag']        = 'Filtrer par tag';

// v3.1.0 — Analytique & Mode sombre
$_['text_analytics']         = 'Analytique';
$_['text_analytics_title']   = 'Tableau de bord analytique';
$_['text_total_reports_stat'] = 'Total des rapports';
$_['text_open_stat']         = 'Ouvert';
$_['text_closed_stat']       = 'Fermé';
$_['text_avg_resolution']    = 'Temps moyen de résolution';
$_['text_reports_per_day']   = 'Rapports / Jour (30 derniers jours)';
$_['text_severity_dist']     = 'Distribution par sévérité';
$_['text_activity_by_hour']  = 'Activité par heure (30 derniers jours)';
$_['text_source_dist']       = 'Distribution par source';
$_['text_top_pages']         = 'Pages avec le plus d\'erreurs';
$_['text_recurring_issues']  = 'Problèmes récurrents (même URL + sévérité)';
$_['text_recent_activity']   = 'Activité récente';
$_['text_no_data']           = 'Aucune donnée pour le moment.';
$_['text_no_recurring']      = 'Aucun schéma récurrent détecté.';
$_['button_dark_mode']       = 'Mode sombre';
$_['button_light_mode']      = 'Mode clair';
$_['text_settings']          = 'Paramètres';
$_['text_reports']           = 'Rapports';

// v3.2.0 — Menu Admin (column_left)
$_['text_menu_title']        = 'Debug Logger';
$_['text_menu_dashboard']    = 'Tableau de bord';
$_['text_menu_reports']      = 'Rapports de log';
$_['text_menu_settings']     = 'Paramètres';

// v3.3.0
$_['popup_label_files']      = 'Fichiers chargés';
$_['popup_tip_files']        = 'Fichiers PHP, Twig, JS et CSS chargés sur cette page.';

// v3.3.2 — toast messages
$_['popup_toast_saved']      = 'Rapport #%s enregistré';
$_['popup_toast_error']      = 'Erreur';

// v3.3.2 — screenshot editor
$_['popup_ss_edit']          = 'Modifier la capture';
$_['popup_ss_done']          = 'Terminé';
$_['popup_ss_cancel']        = 'Annuler';
$_['popup_ss_draw']          = 'Dessiner';
$_['popup_ss_arrow']         = 'Flèche';
$_['popup_ss_rect']          = 'Rectangle';
$_['popup_ss_text']          = 'Texte';
$_['popup_ss_undo']          = 'Annuler';
$_['popup_ss_reset']         = 'Réinitialiser';
$_['popup_ss_thin']          = 'Fin';
$_['popup_ss_normal']        = 'Normal';
$_['popup_ss_thick']         = 'Épais';
$_['popup_ss_prompt']        = 'Texte :';
