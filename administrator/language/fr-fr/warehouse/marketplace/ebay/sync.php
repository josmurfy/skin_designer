<?php
// Original: warehouse/marketplace/ebay/sync.php
// Heading
$_['heading_title'] = 'Sync & Issues';

// Text
$_['text_overview'] = 'Vue d\'ensemble';
$_['text_inventory_health'] = 'Santé de l\'inventaire';
$_['text_sales_performance'] = 'Performance des ventes';
$_['text_marketplace_performance'] = 'Performance marketplace';
$_['text_top_products'] = 'Meilleurs produits';
$_['text_bottom_products'] = 'Produits lents (Non synchronisés récemment)';
$_['text_alerts'] = 'Alertes et avertissements';
$_['text_refresh'] = 'Rafraîchir';
$_['text_period'] = 'Période';
$_['text_today'] = 'Aujourd\'hui';
$_['text_week'] = '7 derniers jours';
$_['text_month'] = '30 derniers jours';
$_['text_year'] = 'Dernière année';
$_['text_export'] = 'Exporter CSV';
$_['text_loading'] = 'Chargement des données...';
$_['text_no_data'] = 'Aucune donnée disponible';
$_['text_category_performance'] = 'Performance par catégorie';
$_['text_location_analysis'] = 'Analyse par emplacement';

// Column
$_['column_metric'] = 'Métrique';
$_['column_value'] = 'Valeur';
$_['column_product'] = 'Produit';
$_['column_sku'] = 'SKU';
$_['column_sales'] = 'Unités vendues';
$_['column_revenue'] = 'Revenu';
$_['column_stock'] = 'Stock';
$_['column_category'] = 'Catégorie';
$_['column_location'] = 'Emplacement';
$_['column_count'] = 'Nombre';
$_['column_quantity'] = 'Quantité';
$_['column_status'] = 'Statut';
$_['column_ebay_id'] = 'ID article eBay';
$_['column_local_qty'] = 'Qté locale';
$_['column_unallocated'] = 'Non alloué';
$_['column_total'] = 'Total';
$_['column_ebay_available'] = 'Disponible eBay';
$_['column_difference'] = 'Différence';
$_['column_actions'] = 'Actions';

// Buttons
$_['button_sync_ebay'] = 'Sync vers eBay';
$_['button_print_report'] = 'Imprimer rapport';
$_['button_update_quantity'] = 'Mettre à jour quantité';
$_['button_import_from_ebay'] = 'Importer d\'eBay';
$_['button_refresh_item'] = 'Rafraîchir';
$_['button_import_non_selected'] = 'Importer non sélectionnés';

// Tooltips
$_['tooltip_import_from_ebay'] = 'Importer TOUTES les données produits d\'eBay (5000+ items, prend plusieurs minutes)';
$_['tooltip_refresh_item'] = 'Rafraîchir TOUT d\'eBay (prix, qnté, caractéristiques, dates)';

// Messages
$_['text_update_confirm'] = 'Mettre à jour la quantité eBay à %s?';
$_['text_update_success'] = 'Succès: Quantité mise à jour à %s sur eBay';
$_['text_update_error'] = 'Erreur: %s';
$_['text_updating'] = 'Mise à jour...';
$_['text_refresh_confirm'] = 'Rafraîchir TOUTES les données d\'eBay (prix, quantité, caractéristiques, dates)?';
$_['text_refresh_success'] = 'Article rafraîchi avec succès depuis eBay!';
$_['text_import_confirm'] = 'Ceci va importer toutes les données produits d\'eBay. Cela peut prendre plusieurs minutes pour 5000+ produits. Continuer?';

// Entry
$_['entry_period'] = 'Sélectionner la période';

// Stats
$_['stat_total_products'] = 'Total produits';
$_['stat_active_products'] = 'Produits actifs';
$_['stat_inventory_value'] = 'Valeur inventaire';
$_['stat_orders_count'] = 'Commandes';
$_['stat_revenue'] = 'Revenu';
$_['stat_avg_order_value'] = 'Valeur moy. commande';
$_['stat_new_products'] = 'Nouveaux produits';
$_['stat_low_stock'] = 'Stock faible';
$_['stat_out_of_stock'] = 'Rupture de stock';
$_['stat_without_location'] = 'Sans emplacement';
$_['stat_without_image'] = 'Sans image';
$_['stat_unallocated'] = 'Inventaire non alloué';
$_['stat_ebay_listed'] = 'Listé sur eBay';
$_['stat_ready_to_list'] = 'Prêt à lister';
$_['stat_with_errors'] = 'Avec erreurs marketplace';
$_['stat_avg_listing_price'] = 'Prix moy. inscription';
$_['stat_completed_orders'] = 'Commandes complétées';
$_['stat_completed_revenue'] = 'Revenu complété';
$_['stat_avg_stock_level'] = 'Niveau moy. stock';

// Success
$_['text_success'] = 'Succès: Données analytiques rafraîchies!';

// Error
$_['error_permission'] = 'Attention: Vous n\'avez pas la permission d\'accéder aux analytiques!';

// Sync Dashboard
$_['text_sync_progress'] = 'Progression de la synchronisation';
$_['text_starting_sync'] = 'Démarrage de la synchronisation...';
$_['text_listed_ebay'] = 'Listé sur eBay';
$_['text_not_listed_qty'] = 'NON Listé (qté > 0)';
$_['text_marketplace_errors'] = 'Erreurs marketplace';
$_['text_not_synced'] = 'Non synchronisés';
$_['text_not_imported'] = 'Non importés';
$_['text_to_update_ebay'] = 'À mettre à jour sur eBay';
$_['text_quantity_mismatch'] = 'Différence de quantité';
$_['text_price_mismatch'] = 'Différence de prix';
$_['text_specifics_mismatch'] = 'Différence caractéristiques';
$_['text_sync_ebay'] = 'Sync eBay';
$_['text_refresh_data'] = 'Rafraîchir les données';

// Tabs
$_['tab_errors'] = 'Erreurs';
$_['tab_not_listed'] = 'Non listés';
$_['tab_not_synced'] = 'Non synchronisés';
$_['tab_mismatch'] = 'Différences';
$_['tab_price_mismatch'] = 'Diff. Prix';
$_['tab_qty_mismatch'] = 'Diff. Qté';
$_['tab_specifics_mismatch'] = 'Diff. Caract.';
$_['tab_condition_mismatch'] = 'Diff. Condition';
$_['tab_category_mismatch'] = 'Diff. Catégorie';
$_['tab_not_imported']    = 'Non importés';
$_['tab_to_update']       = 'À mettre à jour';
$_['tab_slow_moving'] = 'Lents';
$_['text_not_imported_info'] = 'Ces produits sont listés sur eBay mais n\'ont jamais été importés (pas de date last_import).';
$_['text_to_update_info']   = 'Ces produits ont des modifications locales en attente d\'envoi vers eBay (to_update = 1).';
$_['text_no_not_imported']  = 'Aucun produit en attente d\'importation!';
$_['text_no_to_update']     = 'Aucun produit en attente de mise à jour sur eBay!';
$_['button_update_all_ebay'] = 'Mettre TOUT à jour sur eBay';
$_['text_no_not_synced']    = 'Tous les produits sont à jour!';

// Table Headers
$_['text_products_errors'] = 'Produits avec erreurs marketplace';
$_['text_products_not_listed'] = 'Produits NON listés sur eBay (quantité > 0)';
$_['text_products_not_synced'] = 'Produits non synchronisés vers eBay';
$_['text_quantity_mismatches'] = 'Différences de quantité (phoenixliquidation seulement)';
$_['text_slow_moving_items'] = 'Articles à rotation lente (Non synchronisés depuis 90+ jours)';
$_['text_no_products'] = 'Aucun produit trouvé.';
$_['text_error_details'] = 'Détails de l\'erreur';
$_['text_ebay_error'] = 'Erreur eBay';
$_['text_error_code'] = 'Code d\'erreur';
$_['text_error_count'] = 'Nombre d\'erreurs';
$_['text_last_sync'] = 'Dernière sync';
$_['text_days_ago'] = 'il y a %s jours';
$_['text_never'] = 'Jamais';
$_['text_edit_product'] = 'Modifier produit';
$_['text_no_errors'] = 'Aucune erreur marketplace trouvée!';
$_['text_all_listed'] = 'Tous les produits avec stock sont listés sur eBay!';
$_['text_all_synced'] = 'Tous les produits sont synchronisés!';
$_['text_no_mismatch'] = 'Aucune différence de quantité trouvée!';
$_['text_never_synced'] = 'Jamais synchronisé';
$_['text_not_listed'] = 'Non listé';
$_['text_solutions'] = 'Solutions rapides:';
$_['text_error_stats'] = 'Statistiques d\'erreurs (%s types trouvés)';
$_['text_products_info'] = 'Ces produits ont des quantités différentes sur eBay vs inventaire local (Quantité + Non alloué)';
$_['text_print_all'] = 'Tout imprimer (%s)';
$_['text_not_listed_info'] = 'Ces produits ont du stock mais ne sont PAS listés sur eBay marketplace';
$_['text_deselect_all'] = 'Tout désélectionner';

// Mismatch pages
$_['text_mismatch_found'] = '%s différence(s) trouvée(s)';
$_['text_no_mismatch_found'] = 'Aucune différence de %s trouvée.';
$_['text_price'] = 'Prix';
$_['text_quantity'] = 'Quantité';
$_['text_specifics'] = 'Caractéristiques';
$_['text_condition'] = 'Condition';
$_['text_category'] = 'Catégorie';
$_['text_local'] = 'Local';
$_['text_ebay'] = 'eBay';
$_['text_diff'] = 'Diff';
$_['text_sync_to_ebay'] = 'Exporter vers eBay';
$_['text_sync_from_ebay'] = 'Importer de eBay';
$_['button_refresh'] = 'Rafraîchir';
$_['column_price'] = 'Prix';
$_['column_quantity'] = 'Quantité';
$_['column_specifics'] = 'Caractéristiques';
$_['column_condition'] = 'Condition';
$_['column_category'] = 'Catégorie';
$_['text_no_leaf_category'] = 'Le produit n\'a pas de catégorie feuille (leaf=1)';
$_['text_category_values_differ'] = 'Les catégories locale et eBay sont différentes';

// Clé manquante (ajoutée)
$_['column_product_id']           = 'ID produit';

$_['button_edit'] = 'Modifier';
// Clés JS
$_['text_confirm_sync_all']     = 'Ceci synchronisera tous les produits avec eBay. Cela peut prendre plusieurs minutes pour 5000+ produits. Continuer ?';
$_['text_error_sync_url']       = 'Erreur : URL de synchronisation non configurée';
$_['text_confirm_sync_product'] = 'Synchroniser le produit "%s" sur le marketplace eBay ?';
$_['text_confirm_refresh_all']  = 'Rafraîchir TOUTES les données depuis eBay (prix, quantité, spécificités, dates) ?';
$_['text_confirm_import_non_selected'] = 'Importer maintenant les produits non sélectionnés ? GetItem sera exécuté uniquement pour les produits sélectionnés.';

// Écart nombre d'images
$_['tab_image_mismatch']       = 'Écart Images';
$_['column_oc_images']         = 'Images OC';
$_['column_ebay_images']       = 'Images eBay';
$_['column_diff']              = 'Diff';
$_['text_image_mismatch_info'] = 'Produits dont le nombre d\'images dans OpenCart diffère de ce qui a été publié sur eBay. Lancez un sync pour mettre à jour les compteurs.';

// Force refresh
$_['button_force_refresh']        = 'Forcer Refresh Complet';
$_['tooltip_force_refresh']       = 'Ré-importer TOUTES les données depuis eBay (catégorie, condition, spécificités, images) même si déjà en BD. Plus lent — appelle GetItem pour chaque produit.';
$_['text_confirm_force_refresh']  = 'Ceci va appeler GetItem sur TOUS les produits listés pour rafraîchir catégorie, condition, spécificités et images. Beaucoup plus lent et consomme plus de quota API eBay. Continuer ?';

// Correction en masse des images
$_['button_close']                   = 'Fermer';
$_['button_bulk_fix_images']         = 'Corriger toutes les images';
$_['button_fix_single_image']        = 'Importer les images eBay pour ce produit';
$_['text_bulk_fix_tooltip']          = "Importer les images eBay pour TOUS les produits avec un écart, puis réinitialiser leur compteur d'images à 0 pour revalider au prochain sync.";
$_['text_bulk_fix_confirm']          = "Cette action va télécharger les images eBay pour tous les produits avec un écart et remplacer leurs images OC actuelles. Le compteur ebay_image_count sera remis à 0 et revalidé au prochain import eBay. Continuer ?";
$_['text_bulk_fix_modal_title']      = 'Import en masse des images eBay';
$_['text_bulk_fix_processing']       = 'Importation des images eBay pour tous les produits avec écart… Veuillez patienter.';
$_['text_bulk_fix_imported']         = 'Importés';
$_['text_bulk_fix_skipped']          = 'Ignorés';
$_['text_bulk_fix_errors']           = 'Erreurs';
$_['text_bulk_fix_reset_info']       = "Après l'import, le compteur eBay (ebay_image_count) a été remis à 0 pour chaque produit. Lancez 'Importer depuis eBay' ou 'Forcer Refresh Complet' pour récupérer le vrai compte eBay.";
$_['text_bulk_fix_error_details']    = 'Produits en erreur :';

// Image Backup Scan
$_['button_scan_image_backup']       = 'Scanner image_backup';
$_['tooltip_scan_image_backup']      = 'Compte les fichiers images dans image_backup/data/product/ pour chaque produit et sauvegarde le total en base.';
$_['text_scan_backup_confirm']       = 'Ceci va scanner le répertoire image_backup et compter les images pour tous les produits. Quelques secondes. Continuer ?';
$_['text_scan_backup_complete']      = 'Scan sauvegarde terminé';
$_['column_backup_images']           = 'Backup';
$_['text_backup_not_scanned']        = 'N/A';

// Tableau de mismatch backup & popup
$_['text_backup_table_title']        = 'OC vs Backup — Produits avec plus d\'images en backup qu\'en OC';
$_['text_backup_table_info']         = 'Ces produits ont plus d\'images dans image_backup que dans OpenCart. Utilisez le bouton pour les examiner et les transférer.';
$_['column_backup_extra']            = 'En extra dans Backup';
$_['button_open_backup_popup']       = 'Réviser';
$_['text_popup_backup_title']        = 'Images Backup — Produit #%s';
$_['button_transfer_to_oc']          = 'Transférer vers OC';
$_['button_delete_from_backup']      = 'Supprimer du Backup';
$_['text_backup_select_all']         = 'Tout sélectionner';
$_['text_backup_no_files']           = 'Aucune image backup trouvée pour ce produit.';
$_['text_backup_already_in_oc']      = 'Déjà dans OC';
$_['text_backup_type_primary']       = 'Principale';
$_['text_backup_type_secondary']     = 'Secondaire';
$_['text_backup_transferred']        = '%d image(s) transférée(s) vers OC.';
$_['text_backup_deleted']            = '%d image(s) supprimée(s) du backup.';
$_['text_backup_confirm_delete']     = 'Voulez-vous vraiment supprimer définitivement les images backup sélectionnées ?';
