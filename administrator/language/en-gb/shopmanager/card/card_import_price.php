<?php
// Heading
$_['heading_title']          = 'Card Raw Import';

// Text
$_['text_home']              = 'Home';
$_['text_card_import']       = 'Card Price Import';
$_['text_upload_instructions'] = 'Upload a CSV file to import raw card data into the database.';
$_['text_csv_format']        = 'Required columns: title, category, year, brand, set, subset, player, card_number, attributes, team, variation, ungraded, grade_9, grade_10, front_image, ebay_sales';
$_['text_upload_success']    = 'CSV uploaded and imported successfully!';
$_['text_uploading']         = 'Uploading...';
$_['text_success']           = 'Success';
$_['text_error']             = 'Error';
$_['text_import_results']    = 'Import Results';
$_['text_import_summary']    = 'CSV file imported successfully! Here\'s a summary:';
$_['text_preview_title']     = 'Preview (first rows)';
$_['text_records_list']      = 'Records in Database';
$_['text_total_records']     = 'Total Records';
$_['text_no_records']        = 'No records found. Upload a CSV to get started.';
$_['text_truncate_confirm']  = 'WARNING: This will delete ALL records from the card_raw table. Are you sure?';
$_['text_delete_confirm']    = 'Delete selected records?';
$_['text_confirm_cancel']    = 'Are you sure you want to cancel?';

// Statistics
$_['text_total_cards']       = 'Total Cards';
$_['text_inserted']          = 'Inserted';
$_['text_skipped']           = 'Skipped (errors)';
$_['text_total_in_file']     = 'Total in File';
$_['text_in_database']       = 'In Database';

// Column headers
$_['column_card_raw_id']     = 'ID';
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
$_['column_ungraded']        = 'Ungraded';
$_['column_grade_9']         = 'Grade 9';
$_['column_grade_10']        = 'Grade 10';
$_['column_front_image']     = 'Image';
$_['column_ebay_sold_raw']     = 'Auction Raw';
$_['column_ebay_sold_graded']  = 'Auction Graded';
$_['column_ebay_list_raw']     = 'Buy Now Raw';
$_['column_ebay_list_graded']  = 'Buy Now Graded';
$_['column_ebay_checked_at']   = 'eBay Checked At';
$_['column_ebay_sales']         = 'eBay Sales';
$_['column_actions']           = 'Actions';
$_['column_status']          = 'Status';
$_['column_date_added']      = 'Date Added';

// Buttons
$_['button_upload']          = 'Upload CSV';
$_['button_save_to_db']       = 'Save to Database';
$_['button_delete_selected'] = 'Delete Selected';
$_['button_truncate']        = 'Clear All Records';
$_['button_cancel']          = 'Cancel';
$_['button_yes']             = 'Yes';
$_['button_no']              = 'No';
$_['button_ok']              = 'OK';
$_['button_close']           = 'Close';
$_['button_fetch_ebay']        = 'Fetch eBay Prices';
$_['button_sold_graded']       = 'Sold Graded';

// Errors
$_['error_permission']       = 'Warning: You do not have permission to perform this action!';
$_['error_no_file']          = 'No file uploaded.';
$_['error_invalid_file']     = 'Invalid file format. Please upload a CSV file.';
$_['error_empty_file']       = 'CSV file is empty or invalid format.';
$_['error_no_data']          = 'No records selected.';
$_['error_ajax']             = 'AJAX error occurred.';

// Filters
$_['text_filter_title']           = 'Title';
$_['text_filter_category']        = 'Category';
$_['text_filter_year']            = 'Year';
$_['text_filter_brand']           = 'Brand';
$_['text_filter_set']             = 'Set';
$_['text_filter_player']          = 'Player';
$_['text_filter_card_number']     = 'Card #';
$_['text_filter_min_price']       = 'Min $';
$_['text_filter_max_price']       = 'Max $';
$_['button_filter']               = 'Search';
$_['button_reset_filter']         = 'Reset';
$_['text_limit']                  = 'Per page';
$_['text_pagination_showing']     = 'Showing %d to %d of %d';
$_['text_already_imported']       = 'Already in Database';
$_['text_already_imported_msg']   = 'Warning: the database already contains %d records. Importing will add more records on top.';
$_['text_market_fetch_done']        = 'Fetch done';
$_['text_market_cached']            = 'cached';
$_['text_market_rate_limit']        = 'eBay API rate limit reached';
$_['text_market_updated']           = 'Market prices updated';
$_['text_bid_singular']              = 'bid';
$_['text_bid_plural']                = 'bids';
