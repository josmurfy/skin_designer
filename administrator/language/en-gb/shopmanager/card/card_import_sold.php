<?php
// Heading
$_['heading_title']          = 'Sold Price Importer';

// Breadcrumb / nav
$_['text_home']              = 'Home';

// Instructions
$_['text_upload_instructions'] = 'Upload a CSV file to import sold card prices into the database. Each row in the CSV becomes one record.';
$_['text_csv_format']          = 'Required columns: title, category, year, brand, set_name, subset, player, card_number, attributes, team, variation, grader, grade, price, currency, type_listing, bids, total_sold, ebay_item_id, front_image, status, date_sold';
$_['text_uploading']           = 'Uploading...';
$_['text_preview_title']       = 'Preview — grouped by card number';
$_['text_records_list']        = 'Sold Records in Database';
$_['text_no_records']          = 'No records found. Upload a CSV to get started.';
$_['text_group']               = 'Group';
$_['text_missing_card_number'] = 'Missing card number';
$_['text_enabled']             = 'Enabled';
$_['text_disabled']            = 'Disabled';
$_['text_success']             = 'Success';
$_['text_error']               = 'Error';
$_['text_pagination']          = 'Showing %d-%d of %d';

// Import results
$_['text_import_results']    = 'Import Results';
$_['text_import_summary']    = 'CSV imported successfully!';
$_['text_total_in_file']     = 'Total in File';
$_['text_inserted']          = 'Inserted';
$_['text_skipped']           = 'Skipped (errors)';
$_['text_in_database']       = 'In Database';

// Confirm dialogs
$_['text_truncate_confirm']  = 'WARNING: This will delete ALL sold price records. Are you sure?';
$_['text_delete_confirm']    = 'Delete selected records?';

// Column headers
$_['column_card_price_sold_id'] = 'ID';
$_['column_title']           = 'Title';
$_['column_category']        = 'Category';
$_['column_year']            = 'Year';
$_['column_brand']           = 'Brand';
$_['column_set']             = 'Set';
$_['column_subset']          = 'Subset';
$_['column_player']          = 'Player';
$_['column_card_number']     = 'Card #';
$_['column_attributes']      = 'Attributes';
$_['column_team']            = 'Team';
$_['column_variation']       = 'Variation';
$_['column_grader']          = 'Grader';
$_['column_grade']           = 'Grade';
$_['column_price']           = 'Price';
$_['column_currency']        = 'Currency';
$_['column_type_listing']    = 'Type';
$_['column_bids']            = 'Bids';
$_['column_total_sold']      = 'Total Sold';
$_['column_ebay_item_id']    = 'eBay Item ID';
$_['column_front_image']     = 'Image';
$_['column_status']          = 'Status';
$_['column_date_sold']       = 'Date Sold';
$_['column_date_added']      = 'Date Added';
$_['column_actions']         = 'Actions';

// Buttons
$_['button_upload']          = 'Upload CSV';
$_['button_save_to_db']      = 'Save to Database';
$_['button_delete_selected'] = 'Delete Selected';
$_['button_truncate']        = 'Clear All Records';
$_['button_cancel']          = 'Cancel';
$_['button_yes']             = 'Yes';
$_['button_no']              = 'No';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Close';
$_['button_filter']          = 'Filter';
$_['button_reset_filter']    = 'Reset';
$_['button_remove']          = 'Remove row';

// Filters
$_['text_filter_title']              = 'Title';
$_['text_filter_category']           = 'Category';
$_['text_filter_year']               = 'Year';
$_['text_filter_brand']              = 'Brand';
$_['text_filter_set']                = 'Set';
$_['text_filter_player']             = 'Player';
$_['text_filter_card_number']        = 'Card #';
$_['text_filter_grader']             = 'Grader';
$_['text_filter_min_price']          = 'Min Price';
$_['text_filter_max_price']          = 'Max Price';
$_['text_filter_missing_card_number'] = 'Missing Card #';
$_['text_limit']                     = 'Per page';

// Errors
$_['error_permission']       = 'Warning: You do not have permission to perform this action!';
$_['error_no_file']          = 'No file uploaded.';
$_['error_invalid_file']     = 'Invalid file format. Please upload a CSV file.';
$_['error_empty_file']       = 'CSV file is empty or has invalid format.';
$_['error_no_data']          = 'No records selected.';
$_['error_ajax']             = 'AJAX error occurred.';
