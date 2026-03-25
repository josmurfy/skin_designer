<?php
// Heading
$_['heading_title']    = 'Card Bulk Importer';

// Text
$_['text_home']        = 'Home';
$_['text_upload_instructions'] = 'Upload CSV file to create eBay multi-variation listings';
$_['text_csv_file']    = 'CSV File';
$_['text_csv_format']  = 'Required columns: title, sale_price. Optional: year, brand, condition, front_image, back_image';
$_['text_preview_title'] = 'Preview & Edit Cards';
$_['text_listing_configuration'] = 'Listing Configuration';
$_['text_listing_type'] = 'Listing Type';
$_['text_multi_variation'] = 'Multi-variation (all cards in one listing)';
$_['text_single_listings'] = 'Single listings (one card per listing)';
$_['text_upload_success'] = 'CSV uploaded successfully!';
$_['text_generate_success'] = 'eBay CSV file generated successfully!';
$_['text_generation_complete'] = 'Generation Complete';
$_['text_ebay_file_ready'] = 'Your eBay CSV file is ready for download!';
$_['text_uploading']   = 'Uploading';
$_['text_upload_modal_title'] = 'Uploading your CSV';
$_['text_upload_modal_subtitle'] = 'Preparing the preview and checking your cards.';
$_['text_upload_modal_hint'] = 'This may take a few seconds for larger files.';
$_['text_grading_potential_detected'] = 'Grading potential detected.';
$_['text_grading_listing_menu_hint'] = 'Listing menu options are now available for this import.';
$_['text_grading_group_badge'] = 'Grading potential';
$_['text_generating']  = 'Generating';
$_['text_saving']      = 'Saving...';
$_['text_error']       = 'Error';
$_['text_brand_mismatch_block_save'] = 'Fix brand mismatch issues in preview before saving to database.';
$_['text_brand_title_mismatch_block_save'] = 'Brand/title mismatch detected: %s. Brand must be present in each card title.';

// Preview
$_['text_card_title']  = 'Card Title';
$_['text_price']       = 'Price';
$_['text_condition']   = 'Condition';
$_['text_year_brand']  = 'Year / Brand';
$_['text_front_image'] = 'Front Image';
$_['text_back_image']  = 'Back Image';
$_['text_group_title'] = 'Group Title';
$_['text_cards_in_group'] = 'Cards in Group';

// Statistics
$_['text_total_cards']   = 'Total Cards';
$_['text_groups_count']  = 'Grouped Listings';
$_['text_with_images']   = 'With Images';
$_['text_without_images'] = 'Without Images';
$_['text_price_range']   = 'Price Range';

// Entry
$_['entry_listing_title'] = 'Listing Title';
$_['entry_category']      = 'eBay Category ID';
$_['entry_condition']     = 'Condition';
$_['entry_shipping_price'] = 'Shipping Price';
$_['entry_handling_time'] = 'Handling Time';

// Column
$_['column_row']        = '#';
$_['column_card_title'] = 'Card Title';
$_['column_price']      = 'Price';
$_['column_condition']  = 'Condition';
$_['column_year']       = 'Year';
$_['column_brand']      = 'Brand';
$_['column_images']     = 'Images';

// Button
$_['button_upload']    = 'Upload CSV';
$_['button_generate']  = 'Generate eBay CSV';
$_['button_download']  = 'Download eBay File';
$_['button_cancel']    = 'Cancel';

// Help
$_['help_listing_type']  = 'Multi-variation: All cards in one listing with dropdown selector. Single: Each card becomes separate listing.';
$_['help_listing_title'] = 'Title for the eBay listing (max 80 characters). Only used for multi-variation listings.';
$_['help_category']      = 'eBay category ID (e.g., 261328 for Sports Trading Cards)';

// Placeholder
$_['text_placeholder_title']       = 'Card title (required)';
$_['text_placeholder_price']       = '9.99';
$_['text_placeholder_condition']   = 'Near Mint or Better';
$_['text_placeholder_year']        = 'Year';
$_['text_placeholder_brand']       = 'Brand/Manufacturer';
$_['text_placeholder_front_image'] = 'https://...front.jpg';
$_['text_placeholder_back_image']  = 'https://...back.jpg';
$_['text_placeholder_listing_title'] = 'Sports Trading Cards - Multiple Cards Available';

// Confirm
$_['text_confirm_cancel'] = 'Are you sure you want to cancel? All unsaved changes will be lost.';

// Error
$_['error_no_file']          = 'No file uploaded';
$_['error_invalid_file']     = 'Invalid file format. Please upload a CSV file.';
$_['error_empty_file']       = 'CSV file is empty or invalid format';
$_['error_no_data']          = 'No card data available';
$_['error_generation_failed'] = 'Failed to generate eBay CSV file';
$_['error_permission']       = 'Warning: You do not have permission to perform this action!';
$_['error_ajax']             = 'AJAX error occurred';

// Brand/Manufacturer validation
$_['text_brand_not_found']   = 'Brand not found';
$_['text_brand_not_found_message'] = 'The brand "%s" does not exist in the database.<br>Would you like to add it?';
$_['button_add_brand']       = 'Add Brand';
$_['button_cancel_brand']    = 'Cancel';
$_['text_brand_added']       = 'Brand "%s" has been added successfully!';
$_['text_brand_validating']  = 'Validating brand...';
$_['error_brand_failed']     = 'Failed to add brand. Please try again.';

// Import Results Modal
$_['text_import_results']    = 'Import Results';
$_['text_import_summary']    = 'CSV file imported successfully! Here\'s a summary of your data:';

// Modal Dialogs
$_['text_success']           = 'Success';
$_['text_view_listing']      = 'View saved listing?';
$_['text_view_all_listings'] = 'View all listings?';
$_['text_upload_error']      = 'Upload Error';
$_['text_save_error']        = 'Save Error';
$_['text_save_success']      = 'Successfully saved to database!';
$_['text_save_success_reload'] = 'Everything is ready. Click OK to start a new import.';
$_['text_no_data_error']     = 'No card data available. Please upload a CSV file first.';
$_['button_yes']             = 'Yes';
$_['button_no']              = 'No';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Close';

// Drop zone & Auto-group notice
$_['text_drop_here']          = 'Click or drag &amp; drop your CSV here';
$_['text_auto_grouped']       = 'Auto-grouped listings';
$_['text_auto_grouped_desc']  = 'Cards are automatically organized by SET with intelligent sorting. Identical cards are combined with quantity.';

// eBay Policies section
$_['text_ebay_policies']      = 'eBay Business Policies';
$_['text_configured_auto']    = 'Configured Automatically';

// Save confirmation modal
$_['text_save_confirm_title'] = 'Save Listings to Database?';
$_['text_save_confirm_desc']  = 'This will create multi-variation listings in your database.';
$_['text_ebay_disabled']      = 'eBay publishing is currently disabled for debugging.';
$_['button_confirm_save']     = 'Confirm Save';
$_['button_save_to_db']       = 'Save to Database';

// Preview list table
$_['text_already_exists']         = 'ALREADY EXISTS';
$_['text_placeholder_location']   = 'location...';
$_['text_total_prefix']           = 'Total of';
$_['text_cards']                  = 'cards';
$_['text_unique']                 = 'unique';
$_['text_ebay_title_label']       = 'eBay Title';
$_['column_qty']                  = 'Qty';
$_['button_remove_line']            = 'Remove line';
$_['button_remove_listing']         = 'Remove listing';
$_['text_remove_card_line_confirm'] = 'Remove this card line from the preview?';
$_['text_remove_listing_confirm']   = 'Remove this entire listing from the preview?';
$_['text_remaining_listings']         = 'Listings remaining';
$_['text_remaining_cards']            = 'Cards remaining';
$_['button_fetch_market_prices']        = 'Check eBay prices';
$_['text_market_fetch_progress_done']   = 'prices updated';
$_['text_market_column_auction']        = 'Auction';
$_['text_market_column_buy_now']        = 'Buy Now';
$_['text_market_url_missing']          = 'URL not configured.';
$_['text_market_no_rows']              = 'No cards in preview.';
$_['text_market_checking']             = 'Checking eBay prices...';
$_['text_market_api_limit_reached']     = 'eBay API limit reached. Stopping checks and keeping current prices.';
$_['text_market_fallback_kept']         = 'Current card prices are kept as fallback.';
$_['text_market_manual_raw']            = 'Ungraded';
$_['text_market_manual_graded']         = 'Graded';
$_['text_market_manual_sold_graded']    = 'Sold Graded';
$_['text_market_apply_raw_buy_now']       = 'Apply raw Buy Now price';
