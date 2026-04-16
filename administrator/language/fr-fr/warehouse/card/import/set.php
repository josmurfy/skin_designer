<?php
// Original: shopmanager/card/import/card_set_importer.php
// Heading
$_['heading_title']          = 'Importation Brute de Cartes';

// Text
$_['text_home']              = 'Accueil';
$_['text_card_import']       = 'Importation Prix de Cartes';
$_['text_upload_instructions'] = 'Téléversez un fichier CSV pour importer des données brutes de cartes dans la base de données.';
$_['text_csv_format']        = 'Colonnes requises: title, category, year, brand, set, subset, player, card_number, attributes, team, variation, ungraded, grade_9, grade_10, front_image, ebay_sales';
$_['text_upload_success']    = 'CSV téléversé et importé avec succès!';
$_['text_uploading']         = 'Téléversement...';
$_['text_success']           = 'Succès';
$_['text_error']             = 'Erreur';
$_['text_import_results']    = 'Résultats de l\'importation';
$_['text_import_summary']    = 'Fichier CSV importé avec succès! Voici un résumé:';
$_['text_preview_title']     = 'Aperçu (premières lignes)';
$_['text_records_list']      = 'Enregistrements en base de données';
$_['text_total_records']     = 'Total des enregistrements';
$_['text_no_records']        = 'Aucun enregistrement. Téléversez un CSV pour commencer.';
$_['text_truncate_confirm']  = 'AVERTISSEMENT: Ceci supprimera TOUS les enregistrements de la table card_raw. Êtes-vous sûr?';
$_['text_delete_confirm']    = 'Supprimer les enregistrements sélectionnés?';
$_['text_confirm_cancel']    = 'Êtes-vous sûr de vouloir annuler?';

// Statistics
$_['text_total_cards']       = 'Total de cartes';
$_['text_inserted']          = 'Insérés';
$_['text_skipped']           = 'Ignorés (erreurs)';
$_['text_total_in_file']     = 'Total dans le fichier';
$_['text_in_database']       = 'En base de données';

// Column headers
$_['column_card_raw_id']     = 'ID';
$_['column_title']           = 'Titre';
$_['column_category']        = 'Catégorie';
$_['column_year']            = 'Année';
$_['column_brand']           = 'Marque';
$_['column_set']             = 'Set';
$_['column_subset']          = 'Sous-set';
$_['column_player']          = 'Joueur';
$_['column_card_number']     = 'No. carte';
$_['column_attributes']      = 'Attributs';
$_['column_team']            = 'Équipe';
$_['column_variation']       = 'Variation';
$_['column_ungraded']        = 'Non gradée';
$_['column_grade_9']         = 'Grade 9';
$_['column_grade_10']        = 'Grade 10';
$_['column_front_image']     = 'Image';
$_['column_ebay_sold_raw']     = 'Auction Raw';
$_['column_ebay_sold_graded']  = 'Auction Graded';
$_['column_ebay_list_raw']     = 'Buy Now Raw';
$_['column_ebay_list_graded']  = 'Buy Now Graded';
$_['column_ebay_checked_at']   = 'Vérifié eBay le';
$_['column_ebay_sales']         = 'Ventes eBay';
$_['column_actions']           = 'Actions';
$_['column_status']          = 'Statut';
$_['column_date_added']      = 'Date d\'ajout';

// Buttons
$_['button_upload']          = 'Téléverser CSV';
$_['button_save_to_db']      = 'Enregistrer dans la base de données';
$_['button_delete_selected'] = 'Supprimer sélection';
$_['button_truncate']        = 'Vider tous les enregistrements';
$_['button_cancel']          = 'Annuler';
$_['button_yes']             = 'Oui';
$_['button_no']              = 'Non';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Fermer';
$_['button_fetch_ebay']        = 'Récupérer prix eBay';
$_['button_sold_graded']       = 'Sold Graded';
$_['button_merge_preview']     = 'Fusionner';

// Errors
$_['error_permission']       = 'Attention: Vous n\'avez pas la permission d\'effectuer cette action!';
$_['error_no_file']          = 'Aucun fichier téléversé.';
$_['error_invalid_file']     = 'Format de fichier invalide. Veuillez téléverser un fichier CSV.';
$_['error_empty_file']       = 'Le fichier CSV est vide ou dans un format invalide.';
$_['error_no_data']          = 'Aucun enregistrement sélectionné.';
$_['error_ajax']             = 'Une erreur AJAX s\'est produite.';

// Filtres
$_['text_filter_title']           = 'Titre';
$_['text_filter_category']        = 'Catégorie';
$_['text_filter_year']            = 'Année';
$_['text_filter_brand']           = 'Marque';
$_['text_filter_set']             = 'Set';
$_['text_filter_player']          = 'Joueur';
$_['text_filter_card_number']     = 'No. carte';
$_['text_filter_min_price']       = 'Min $';
$_['text_filter_max_price']       = 'Max $';
$_['button_filter']               = 'Rechercher';
$_['button_reset_filter']         = 'Réinitialiser';
$_['text_limit']                  = 'Par page';
$_['text_pagination_showing']     = 'Affichage de %d à %d sur %d';
$_['text_already_imported']       = 'Déjà en base de données';
$_['text_already_imported_msg']   = 'Attention : la base de données contient déjà %d enregistrements. Importer ajoutera des enregistrements en plus.';
$_['text_market_fetch_done']        = 'Recherche terminée';
$_['text_market_cached']            = 'en cache';
$_['text_market_rate_limit']        = 'Limite API eBay atteinte';
$_['text_market_updated']           = 'Prix marché mis à jour';
$_['text_bid_singular']              = 'enchère';
$_['text_bid_plural']                = 'enchères';
$_['text_use_filters']               = 'Utilisez les filtres pour afficher les cartes correspondant à des ventes.';
