<?php
// Original: warehouse/card/listing.php
// Heading
$_['heading_title']     = 'Card Listings';

// Text
$_['text_success']                = 'Success: You have modified card listings!';
$_['text_list']                   = 'Card Listing List';
$_['text_add']                    = 'Add Card Listing';
$_['text_edit']                   = 'Edit Card Listing';
$_['text_filter']                 = 'Filter';
$_['text_no_results']             = 'No card listings found.';
$_['text_confirm']                = 'Are you sure you want to delete this?';
$_['text_enabled']                = 'Enabled';
$_['text_disabled']               = 'Disabled';
$_['text_select']                 = '-- Select --';
$_['text_home']                   = 'Home';
$_['text_variation_info']         = 'Variations are managed through the card upload process.';
$_['text_variation_count']        = 'Variation Count';
$_['text_total_quantity']         = 'Total Quantity';
$_['text_not_listed']             = 'Not Listed';
$_['text_variations_managed']     = 'Card variations are automatically managed through the CSV upload process. Use the card management interface to add or modify cards within this listing.';
$_['text_cards_list']             = 'Individual Cards';
$_['text_no_cards']               = 'No cards found in this listing.';
$_['text_descriptions']           = 'Multi-language Descriptions';
$_['text_ebay_settings']          = 'eBay Integration Settings';
$_['text_yes']                    = 'Yes';
$_['text_no']                     = 'No';

// Column
$_['column_listing_id']           = 'ID';
$_['column_set_name']             = 'Set Name';
$_['column_sport']                = 'Card Type';
$_['column_year']                 = 'Year';
$_['column_manufacturer']         = 'Manufacturer';
$_['column_cards']                = 'Cards';
$_['column_quantity']             = 'QTY';
$_['column_status']               = 'Status';
$_['column_date_added']           = 'Date Added';
$_['column_ebay']                 = 'eBay';
$_['column_action']               = '';
$_['column_image']                = 'Image';
$_['column_location']             = 'Location';
$_['column_title']                = 'Title';
$_['column_player']               = 'Player';
$_['column_card_number']          = 'Card Number';
$_['column_price']                = 'Price';
$_['column_raw_price']            = 'Raw $';
$_['column_merge']                = 'Merge';
$_['column_name']                 = 'Name';
$_['column_value']                = 'Value';

// Entry
$_['entry_listing_id']            = 'Listing ID';
$_['entry_set_name']              = 'Set Name';
$_['entry_sport']                 = 'Sport';
$_['entry_year']                  = 'Year';
$_['entry_manufacturer']          = 'Manufacturer';
$_['entry_location']              = 'Location';
$_['entry_status']                = 'Status';
$_['entry_ebay_item_id']          = 'eBay Item ID';
$_['entry_cards']                 = 'Cards';
$_['entry_title']                 = 'Title';
$_['entry_description']           = 'Description';
$_['entry_meta_keyword']          = 'Meta Keywords';
$_['entry_ebay_category']         = 'eBay Category ID';
$_['entry_specifics']             = 'eBay Specifics';
$_['entry_limit']                 = 'Results per page';

// Tab
$_['tab_general']                 = 'General';
$_['tab_cards']                   = 'Cards';
$_['tab_descriptions']            = 'Descriptions';
$_['tab_ebay']                    = 'eBay Settings';
$_['tab_variations']              = 'Variations';

// Button
$_['button_add']                  = 'Add New';
$_['button_edit']                 = 'Edit';
$_['button_update_ebay']          = 'Update eBay Listing (variations, prices, quantities)';
$_['button_delete']               = 'Delete';
$_['button_filter']               = 'Filter';
$_['button_reset']                = 'Reset Filters';
$_['button_view_ebay']            = 'View on eBay';
$_['button_save']                 = 'Save';
$_['button_back']                 = 'Back';
$_['button_add_specific']         = 'Add Specific';

// Help
$_['help_cards_list']             = 'All individual cards in this listing with their images, prices, and quantities.';
$_['help_descriptions']           = 'Enter title and description in each language for SEO and marketplace display.';
$_['help_ebay_settings']          = 'Configure eBay-specific settings such as category and item specifics.';
$_['help_ebay_category']          = 'Enter the eBay category ID for this card set.';

// Error
$_['error_warning']               = 'Warning: Please check the form carefully for errors!';
$_['error_permission']            = 'Warning: You do not have permission to modify card listings!';
$_['error_set_name']              = 'Set Name must be between 3 and 255 characters!';
$_['error_sport']                 = 'Sport is required!';
$_['error_session_expired']       = 'Session expired. Please log in again.';
$_['error_permission_denied']     = 'Permission denied!';
$_['error_listing_not_found']     = 'Listing not found.';
$_['error_no_descriptions']       = 'No descriptions found for this listing.';
$_['error_marketplace_account']   = 'Marketplace account not found for language_id: %s';
$_['error_publish_failed']        = 'Failed to publish for language_id %s: %s';
$_['error_no_listings_selected']  = 'No listings selected.';
$_['error_invalid_parameters']    = 'Invalid parameters.';
$_['error_ajax_error']            = 'AJAX Error';
$_['error_details']               = 'Details';
$_['error_no_descriptions_found'] = 'No descriptions found for this listing.';
$_['error_marketplace_not_found'] = 'Marketplace account not found (lang %s)';
$_['error_end_failed']            = 'Failed to end listing for lang %s: %s';
$_['error_no_published_listings'] = 'No published listings found to end.';
$_['text_loading_in_progress']    = 'Loading in progress...';
$_['text_completed']              = '✅ Completed!';
$_['text_confirm_publish_single'] = 'Do you want to publish this card set on eBay?';
$_['text_publishing_to_ebay']     = '🚀 Publishing to eBay in progress...';
$_['text_initializing_publication'] = 'Initializing publication...';
$_['text_preparing_card_set_data'] = 'Preparing card set data...';
$_['text_retrieving_descriptions'] = 'Retrieving multilingual descriptions...';
$_['text_connecting_ebay_accounts'] = 'Connecting to eBay accounts (EN + FR)...';
$_['text_sending_data_to_ebay']   = 'Sending data to eBay...';
$_['text_creating_card_variations'] = 'Creating card variations...';
$_['text_uploading_images']       = 'Uploading images in progress...';
$_['text_publication_failed']     = '❌ Publication failed';
$_['text_card_set_published_successfully'] = 'Card set published successfully!';
$_['text_publication_completed_successfully'] = '✅ Publication completed successfully!';
$_['text_communication_error']    = '❌ Communication error';
$_['text_no_listings_selected']   = 'No listings selected';
$_['text_confirm_publish_multiple'] = 'Publish %s listing(s) on eBay?';
$_['text_publishing_multiple_listings'] = 'Publishing %s listing(s) on eBay...';
$_['text_listings_to_publish']    = 'Listings to publish: %s';
$_['text_preparing_data']         = 'Preparing data...';
$_['text_success_published_count'] = '✅ Success: %s listing(s) published';
$_['text_listing_published']      = '✅ Listing #%s: Published';
$_['text_listing_failed']         = '❌ Listing #%s: %s';
$_['text_errors_count']           = 'Errors: %s';
$_['text_publication_completed']  = '✅ Publication completed!';
$_['text_ajax_error_with_details'] = 'AJAX Error: %s';
$_['text_confirm_end_listings']   = 'End %s eBay listing(s)? This action will remove the listings from sale.';
$_['text_ending_listings']        = 'Ending %s eBay listing(s)...';
$_['text_listings_to_end']        = 'Listings to end: %s';
$_['text_preparing']              = 'Preparing...';
$_['text_ending_failed']          = '❌ Ending failed';
$_['text_success_ended_count']    = '✅ Success: %s listing(s) ended';
$_['text_listing_ended']          = '✅ Listing #%s: Ended';
$_['text_ending_completed']       = '✅ Ending completed!';
$_['text_confirm_update_ebay']    = 'Update this eBay listing with latest cards, prices, and quantities?';
$_['text_updating_ebay']          = 'Updating eBay listing...';
$_['text_update_success']         = '✅ eBay listing updated successfully!';
$_['text_update_failed']          = '❌ Update failed';
$_['text_ebay_updated_success']   = '✅ Success: %s listing(s) updated on eBay';
$_['text_error_prefix']           = 'Error: %s';
$_['text_number_of_listings']     = 'Number of listings: %s';

// eBay Health Column
$_['column_ebay_health']          = 'eBay ✓';
$_['column_ebay_health_title']    = 'eBay Health: offer_id / published / EPS images';
$_['button_batch_ebay_sync']      = 'eBay Sync';
$_['text_confirm_batch_sync']     = 'Run eBay sync (migrate images + sync offers + republish) on %s listing(s)?';
$_['text_batch_sync_running']     = '🔄 eBay Sync running on %s listing(s)...';
$_['text_batch_sync_completed']   = '✅ Sync completed: %s OK, %s errors';
$_['text_batch_sync_failed']      = '❌ eBay Sync failed';
$_['text_migrate']                = 'Migrate images';
$_['text_sync']                   = 'Sync offers';
$_['text_republish']              = 'Republish';
$_['text_refresh']                = 'Refresh';

// eBay Batch management
$_['text_ebay_batches_panel']         = 'eBay Listings (Batches)';
$_['button_recalculate_batches']      = 'Recalculate Batches';
$_['button_calculating']              = 'Calculating…';
$_['column_batch']                    = 'Batch';
$_['column_group_key']                = 'Group Key';
$_['column_variations']               = 'Variations';
$_['text_batch_status_published']     = 'Published';
$_['text_grand_total']                = 'Grand Total (all batches)';
$_['text_batch_status_ended']         = 'Ended';
$_['text_batch_status_draft']         = 'Draft';
$_['text_no_batches_assigned']        = 'No batches assigned yet. Click <strong>Recalculate Batches</strong> to assign cards to eBay listings.';
$_['text_no_batches_empty']           = 'No batches assigned.';
$_['text_needs_batch_warning_title']  = 'Action required — %s cards detected.';
$_['text_needs_batch_warning_body']   = 'This listing exceeds 250 variations. Cards are <strong>not yet assigned to eBay batches</strong>. Without this step, publishing will fail or cards will be misassigned.';
$_['button_calculate_batches_now']    = 'Calculate Batches Now';
$_['text_batch_migrated']             = 'eBay Item ID <strong>%s</strong> migrated from description to Batch 1.';
$_['text_batch_multi_info']           = '%s eBay listings will be used for this card set.';
$_['text_batch_ajax_error']           = 'AJAX error — check server logs.';
// Tab Descriptions
$_['help_tab_descriptions']           = 'Each eBay batch has its own title and description — generated from the cards in that batch when publishing.';
$_['text_badge_live']                 = 'Live';
$_['text_ebay_id']                    = 'eBay ID';
$_['text_specifics']                  = 'Specifics';
$_['text_no_description_stored']      = 'No description stored — will be set on next publish.';
$_['text_no_specifics']               = 'No specifics — click Recalculate to populate.';
$_['text_not_published_yet']          = 'Not published to eBay yet — showing the full listing description.';
$_['text_no_description_yet']         = 'No description yet — save the listing to generate.';

// eBay Health Check
$_['button_health_check']             = 'eBay Health Check';
$_['text_health_check_running']       = 'Checking eBay listings…';
$_['text_health_check_completed']     = 'Health check completed';
$_['text_health_check_failed']        = 'Health check failed';
$_['text_health_ok']                  = 'eBay OK';
$_['text_health_error']               = 'eBay Error';
$_['text_health_ended']               = 'eBay Listing Ended';
$_['text_health_unchecked']           = 'Never checked or > 6 months';
$_['text_health_no_item_id']          = 'No eBay Item ID';
$_['text_health_group_key_missing']   = 'Group key not found on eBay';
$_['text_health_group_key_mismatch']  = 'Group key mismatch';
// ────────────────────────────── Merge variant letters
$_['text_letter_merged']       = '%d variant group(s) auto-merged (price < $3.00 and spread < 50%)';
$_['text_letter_warn_price']   = '%d group(s) NOT merged — price spread too high (≥50%) or price ≥$3.00';
$_['text_letter_warn_format']  = '%d group(s) skipped — irregular card number format';
$_['text_ebay_live_warning']   = 'Warning: %d card(s) being merged are currently LIVE on eBay';
$_['text_pending_deletes']     = '%d eBay offer(s) queued for deletion';
$_['btn_merge_anyway']         = 'Merge anyway';
$_['btn_keep_separate']        = 'Keep separate';
$_['legend_regen_green']       = 'Will be auto-merged (price < $3 and spread < 50%)';
$_['legend_regen_orange']      = 'Warning — high price spread or price ≥$3.00, not merged';
$_['legend_regen_red']         = 'Warning — irregular format, never auto-merged';
// ────────────────────────────── Lot eBay
$_['text_lot_panel_title']     = 'eBay Lot — sell all cards as a single lot';
$_['text_lot_published_ok']    = 'Lot published on eBay';
$_['text_lot_publish_failed']  = 'Lot publication failed';
$_['text_lot_ended_ok']        = 'eBay lot ended successfully';
$_['text_lot_end_failed']      = 'Failed to end eBay lot';
$_['text_lot_not_published']   = 'Lot not yet published on eBay';
$_['text_lot_price_label']     = 'Lot price (USD)';
$_['text_lot_stat_cards']      = 'Unique cards';
$_['text_lot_stat_qty']        = 'Total quantity';
$_['text_lot_stat_price']      = 'Auto-calculated price';
$_['text_lot_stat_floored']    = 'Cards at $0.01 floor';
$_['button_publish_lot']       = 'Publish Lot on eBay';
$_['button_end_lot']           = 'End Lot';
$_['button_lot_refresh']       = 'Refresh';
$_['text_lot_price_help']      = 'Leave blank to use auto-calculated price. Override saved before publishing.';
$_['text_lot_weight']          = 'Weight';
$_['text_lot_dimensions']      = 'Dimensions (L × W × H)';
$_['text_lot_price_reset']     = 'Reset to calculated price';
$_['text_lot_publishing']      = 'Publishing...';
$_['text_lot_confirm_publish'] = 'Publish all cards in this listing as a single eBay lot?';
$_['text_lot_confirm_end']     = 'End this eBay lot? The listing will be permanently removed.';
$_['text_lot_price_error']     = 'Lot price must be greater than $0.00.';
$_['text_lot_published_live']  = 'Lot published';
$_['button_generate_lot_desc']  = 'Regenerate Description';
$_['button_save_lot_desc']      = 'Save Description';
$_['button_generate_lot_images'] = 'Generate Mosaic Images';
$_['text_lot_desc_saved_ok']    = 'Description saved!';
$_['text_lot_images_generated'] = 'Mosaic images generated';
$_['text_lot_no_images']        = 'No images yet. Click "Generate" to create mosaic images.';
$_['text_lot_images']           = 'Mosaic Images';
$_['column_price_sold']          = 'Auction $';
$_['column_price_list']          = 'Buy Now $';
$_['button_fetch_market_prices'] = 'Fetch Market Prices';
$_['text_market_price_progress'] = 'Fetching prices...';
$_['text_market_fetch_progress_done'] = 'prices updated.';
$_['text_market_api_limit_reached'] = 'eBay API limit reached. Stopping checks.';
$_['text_market_fallback_kept'] = 'Manual buttons displayed for the remaining rows.';
$_['text_market_manual_sold_graded'] = 'Sold Graded';
$_['text_our_ebay_price']         = 'Our eBay';
$_['text_condition_short']        = 'Cond.';
$_['text_found_condition_short']  = 'Found';
$_['text_variant_short']          = 'Variant';

// JS text vars
$_['text_error_prefix'] = 'Error: ';
