<?php
// Heading
$_['heading_title']          = 'Importateur de Prix Vendus';

// Breadcrumb / nav
$_['text_home']              = 'Accueil';

// Instructions
$_['text_upload_instructions'] = 'Téléversez un fichier CSV pour importer les prix vendus de cartes dans la base de données. Chaque ligne du CSV devient un enregistrement.';
$_['text_csv_format']          = 'Colonnes requises: title, category, year, brand, set_name, subset, player, card_number, attributes, team, variation, grader, grade, price, currency, type_listing, bids, total_sold, ebay_item_id, front_image, status, date_sold';
$_['text_uploading']           = 'Téléversement...';
$_['text_preview_title']       = 'Aperçu — regroupé par numéro de carte';
$_['text_records_list']        = 'Enregistrements Vendus en Base de Données';
$_['text_no_records']          = 'Aucun enregistrement. Téléversez un CSV pour commencer.';
$_['text_group']               = 'Groupe';
$_['text_missing_card_number'] = 'Numéro de carte manquant';
$_['text_enabled']             = 'Activé';
$_['text_disabled']            = 'Désactivé';
$_['text_success']             = 'Succès';
$_['text_error']               = 'Erreur';
$_['text_pagination']          = 'Affichage %d-%d sur %d';

// Import results
$_['text_import_results']    = 'Résultats de l\'importation';
$_['text_import_summary']    = 'CSV importé avec succès!';
$_['text_total_in_file']     = 'Total dans le fichier';
$_['text_inserted']          = 'Insérés';
$_['text_skipped']           = 'Ignorés (erreurs)';
$_['text_in_database']       = 'En base de données';

// Confirm dialogs
$_['text_truncate_confirm']  = 'AVERTISSEMENT: Ceci supprimera TOUS les prix vendus. Êtes-vous sûr?';
$_['text_delete_confirm']    = 'Supprimer les enregistrements sélectionnés?';

// Column headers
$_['column_card_price_sold_id'] = 'ID';
$_['column_title']           = 'Titre';
$_['column_category']        = 'Catégorie';
$_['column_year']            = 'Année';
$_['column_brand']           = 'Marque';
$_['column_set']             = 'Set';
$_['column_subset']          = 'Sous-set';
$_['column_player']          = 'Joueur';
$_['column_card_number']     = 'No. Carte';
$_['column_attributes']      = 'Attributs';
$_['column_team']            = 'Équipe';
$_['column_variation']       = 'Variation';
$_['column_grader']          = 'Gradeur';
$_['column_grade']           = 'Grade';
$_['column_price']           = 'Prix';
$_['column_currency']        = 'Devise';
$_['column_type_listing']    = 'Type';
$_['column_bids']            = 'Enchères';
$_['column_total_sold']      = 'Total Vendus';
$_['column_ebay_item_id']    = 'ID eBay';
$_['column_front_image']     = 'Image';
$_['column_status']          = 'Statut';
$_['column_date_sold']       = 'Date Vendue';
$_['column_date_added']      = 'Date Ajoutée';
$_['column_actions']         = 'Actions';

// Buttons
$_['button_upload']          = 'Téléverser CSV';
$_['button_save_to_db']      = 'Enregistrer en BD';
$_['button_delete_selected'] = 'Supprimer Sélection';
$_['button_truncate']        = 'Vider tous les enregistrements';
$_['button_cancel']          = 'Annuler';
$_['button_yes']             = 'Oui';
$_['button_no']              = 'Non';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Fermer';
$_['button_filter']          = 'Filtrer';
$_['button_reset_filter']    = 'Réinitialiser';
$_['button_remove']          = 'Supprimer la ligne';

// Filters
$_['text_filter_title']               = 'Titre';
$_['text_filter_category']            = 'Catégorie';
$_['text_filter_year']                = 'Année';
$_['text_filter_brand']               = 'Marque';
$_['text_filter_set']                 = 'Set';
$_['text_filter_player']              = 'Joueur';
$_['text_filter_card_number']         = 'No. Carte';
$_['text_filter_grader']              = 'Gradeur';
$_['text_filter_min_price']           = 'Prix Min';
$_['text_filter_max_price']           = 'Prix Max';
$_['text_filter_missing_card_number'] = 'No. Carte manquant';
$_['text_limit']                      = 'Par page';

// Errors
$_['error_permission']       = 'Avertissement: Vous n\'avez pas la permission d\'effectuer cette action!';
$_['error_no_file']          = 'Aucun fichier téléversé.';
$_['error_invalid_file']     = 'Format de fichier invalide. Veuillez téléverser un fichier CSV.';
$_['error_empty_file']       = 'Le fichier CSV est vide ou a un format invalide.';
$_['error_no_data']          = 'Aucun enregistrement sélectionné.';
$_['error_ajax']             = 'Erreur AJAX survenue.';
