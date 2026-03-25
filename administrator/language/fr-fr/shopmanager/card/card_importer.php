<?php
// Heading
$_['heading_title']    = 'Importateur de Cartes en Bloc';

// Text
$_['text_home']        = 'Accueil';
$_['text_upload_instructions'] = 'Téléchargez un fichier CSV pour créer des listings multi-variation eBay';
$_['text_csv_file']    = 'Fichier CSV';
$_['text_csv_format']  = 'Colonnes requises: title, sale_price. Optionnel: year, brand, condition, front_image, back_image';
$_['text_preview_title'] = 'Aperçu et Modification des Cartes';
$_['text_listing_configuration'] = 'Configuration du Listing';
$_['text_listing_type'] = 'Type de Listing';
$_['text_multi_variation'] = 'Multi-variation (toutes les cartes dans un seul listing)';
$_['text_single_listings'] = 'Listings individuels (une carte par listing)';
$_['text_upload_success'] = 'CSV téléchargé avec succès!';
$_['text_generate_success'] = 'Fichier CSV eBay généré avec succès!';
$_['text_generation_complete'] = 'Génération Terminée';
$_['text_ebay_file_ready'] = 'Votre fichier CSV eBay est prêt à être téléchargé!';
$_['text_uploading']   = 'Téléchargement en cours';
$_['text_upload_modal_title'] = 'Téléchargement du CSV';
$_['text_upload_modal_subtitle'] = 'Préparation de la prévisualisation et vérification de vos cartes.';
$_['text_upload_modal_hint'] = 'Cela peut prendre quelques secondes pour les fichiers plus volumineux.';
$_['text_grading_potential_detected'] = 'Potentiel de grading détecté.';
$_['text_grading_listing_menu_hint'] = 'Les options du menu listing sont maintenant disponibles pour cet import.';
$_['text_grading_group_badge'] = 'Potentiel grading';
$_['text_generating']  = 'Génération en cours';
$_['text_saving']      = 'Sauvegarde...';
$_['text_error']       = 'Erreur';
$_['text_brand_mismatch_block_save'] = 'Corrigez les conflits de marque dans la prévisualisation avant de sauvegarder en base de données.';
$_['text_brand_title_mismatch_block_save'] = 'Conflit marque/titre détecté : %s. La marque doit être présente dans chaque titre de carte.';

// Preview
$_['text_card_title']  = 'Titre de la Carte';
$_['text_price']       = 'Prix';
$_['text_condition']   = 'Condition';
$_['text_year_brand']  = 'Année / Marque';
$_['text_front_image'] = 'Image Avant';
$_['text_back_image']  = 'Image Arrière';

// Statistics
$_['text_total_cards']   = 'Total Cartes';
$_['text_with_images']   = 'Avec Images';
$_['text_without_images'] = 'Sans Images';
$_['text_price_range']   = 'Plage de Prix';

// Entry
$_['entry_listing_title'] = 'Titre du Listing';
$_['entry_category']      = 'ID Catégorie eBay';
$_['entry_condition']     = 'Condition';
$_['entry_shipping_price'] = "Prix d'Expédition";
$_['entry_handling_time'] = 'Délai de Traitement';

// Column
$_['column_row']        = '#';
$_['column_card_title'] = 'Titre de la Carte';
$_['column_price']      = 'Prix';
$_['column_condition']  = 'Condition';
$_['column_year']       = 'Année';
$_['column_brand']      = 'Marque';
$_['column_images']     = 'Images';

// Button
$_['button_upload']    = 'Télécharger CSV';
$_['button_generate']  = 'Générer CSV eBay';
$_['button_download']  = 'Télécharger Fichier eBay';
$_['button_cancel']    = 'Annuler';

// Help
$_['help_listing_type']  = 'Multi-variation: Toutes les cartes dans un listing avec sélecteur dropdown. Individuel: Chaque carte devient un listing séparé.';
$_['help_listing_title'] = 'Titre du listing eBay (max 80 caractères). Utilisé uniquement pour les listings multi-variation.';
$_['help_category']      = 'ID de catégorie eBay (ex: 261328 pour Sports Trading Cards)';

// Placeholder
$_['text_placeholder_title']       = 'Titre de la carte (requis)';
$_['text_placeholder_price']       = '9.99';
$_['text_placeholder_condition']   = 'Near Mint or Better';
$_['text_placeholder_year']        = 'Année';
$_['text_placeholder_brand']       = 'Marque/Fabricant';
$_['text_placeholder_front_image'] = 'https://...front.jpg';
$_['text_placeholder_back_image']  = 'https://...back.jpg';
$_['text_placeholder_listing_title'] = 'Cartes de Sport - Plusieurs Cartes Disponibles';

// Confirm
$_['text_confirm_cancel'] = 'Êtes-vous sûr de vouloir annuler? Toutes les modifications non sauvegardées seront perdues.';

// Error
$_['error_no_file']          = 'Aucun fichier téléchargé';
$_['error_invalid_file']     = 'Format de fichier invalide. Veuillez télécharger un fichier CSV.';
$_['error_empty_file']       = 'Le fichier CSV est vide ou au format invalide';
$_['error_no_data']          = 'Aucune donnée de carte disponible';
$_['error_generation_failed'] = 'Échec de la génération du fichier CSV eBay';
$_['error_permission']       = "Attention : Vous n'avez pas la permission d'effectuer cette action!";
$_['error_ajax']             = 'Erreur AJAX survenue';

// Brand/Manufacturer validation
$_['text_brand_not_found']   = 'Marque non trouvée';
$_['text_brand_not_found_message'] = 'La marque "%s" n\'existe pas dans la base de données.<br>Voulez-vous l\'ajouter?';
$_['button_add_brand']       = 'Ajouter la Marque';
$_['button_cancel_brand']    = 'Annuler';
$_['text_brand_added']       = 'La marque "%s" a été ajoutée avec succès!';
$_['text_brand_validating']  = 'Validation de la marque...';
$_['error_brand_failed']     = 'Échec de l\'ajout de la marque. Veuillez réessayer.';
// Import Results Modal
$_['text_import_results']    = 'Résultats d\'importation';
$_['text_import_summary']    = 'Fichier CSV importé avec succès! Voici un résumé de vos données:';
// Modal Dialogs
$_['text_success']           = 'Succès';
$_['text_view_listing']      = 'Voir l\'annonce sauvegardée?';
$_['text_view_all_listings'] = 'Voir toutes les annonces?';
$_['text_upload_error']      = 'Erreur de téléchargement';
$_['text_save_error']        = 'Erreur de sauvegarde';
$_['text_save_success']      = 'Enregistré avec succès dans la base de données!';
$_['text_save_success_reload'] = 'Tout est prêt. Cliquez sur OK pour recommencer un nouvel import.';
$_['text_no_data_error']     = 'Aucune donnée de carte disponible. Veuillez d\'abord télécharger un fichier CSV.';
$_['button_yes']             = 'Oui';
$_['button_no']              = 'Non';
$_['button_ok']              = 'OK';$_['button_close']           = 'Fermer';

// Groupe
$_['text_group_title']       = 'Titre du groupe';
$_['text_cards_in_group']    = 'Cartes dans le groupe';
$_['text_groups_count']      = 'Listes groupées';

// Zone dépôt fichier & notice auto-groupement
$_['text_drop_here']          = 'Cliquez ou glissez-déposez votre CSV ici';
$_['text_auto_grouped']       = 'Annonces auto-groupées';
$_['text_auto_grouped_desc']  = 'Les cartes sont automatiquement organisées par SET avec un tri intelligent. Les cartes identiques sont combinées avec la quantité.';

// Section Politiques eBay
$_['text_ebay_policies']      = 'Politiques commerciales eBay';
$_['text_configured_auto']    = 'Configuré automatiquement';

// Modal de confirmation sauvegarde
$_['text_save_confirm_title'] = 'Sauvegarder les annonces dans la base de données?';
$_['text_save_confirm_desc']  = 'Cela créera des annonces multi-variation dans votre base de données.';
$_['text_ebay_disabled']      = 'La publication eBay est actuellement désactivée pour le débogage.';
$_['button_confirm_save']     = 'Confirmer la sauvegarde';
$_['button_save_to_db']       = 'Sauvegarder dans la BD';

// Tableau de prévisualisation
$_['text_already_exists']         = 'EXISTE DÉJÀ';
$_['text_placeholder_location']   = 'emplacement...';
$_['text_total_prefix']           = 'Total de';
$_['text_cards']                  = 'cartes';
$_['text_unique']                 = 'unique';
$_['text_ebay_title_label']       = 'Titre eBay';
$_['column_qty']                  = 'Qté';
$_['button_remove_line']            = 'Supprimer ligne';
$_['button_remove_listing']         = 'Supprimer annonce';
$_['text_remove_card_line_confirm'] = 'Supprimer cette ligne de carte de la prévisualisation ?';
$_['text_remove_listing_confirm']   = 'Supprimer cette annonce complète de la prévisualisation ?';
$_['text_remaining_listings']         = 'Annonces restantes';
$_['text_remaining_cards']            = 'Cartes restantes';
$_['button_fetch_market_prices']        = 'Vérifier prix eBay';
$_['text_market_fetch_progress_done']   = 'prix mis à jour';
$_['text_market_column_auction']        = 'Enchère';
$_['text_market_column_buy_now']        = 'Achat immédiat';
$_['text_market_url_missing']          = 'URL non configurée.';
$_['text_market_no_rows']              = 'Aucune carte dans la prévisualisation.';
$_['text_market_checking']             = 'Vérification des prix eBay...';
$_['text_market_api_limit_reached']     = 'Limite API eBay atteinte. Vérification arrêtée, prix actuels conservés.';
$_['text_market_fallback_kept']         = 'Les prix actuels des cartes sont conservés en secours.';
$_['text_market_manual_raw']            = 'Non gradée';
$_['text_market_manual_graded']         = 'Gradée';
$_['text_market_manual_sold_graded']    = 'Vendues gradées';
$_['text_market_apply_raw_buy_now']       = 'Appliquer le prix Achat immédiat non gradée';