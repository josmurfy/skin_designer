<?php
// Heading
$_['heading_title']       = 'Debug Logger';

// Text
$_['text_extension']      = 'Extensions';
$_['text_success']        = 'Debug Logger settings saved successfully.';
$_['text_success_clear']  = 'All reports have been cleared.';
$_['text_enabled']        = 'Enabled';
$_['text_general']        = 'General';
$_['text_display']        = 'Display';
$_['text_capture']        = 'Capture Options';
$_['text_severity']       = 'Severity Levels';
$_['text_data']           = 'Data Management';
$_['text_stats']          = 'Statistics';
$_['text_total_reports']  = 'Total Reports';
$_['text_open_reports']   = 'Open / Unresolved';
$_['text_from_admin']     = 'From Admin';
$_['text_from_catalog']   = 'From Catalog';
$_['text_show_admin']     = 'Show in Admin Panel';
$_['text_show_catalog']   = 'Show in Storefront';
$_['text_info_title']     = 'How it works';
$_['text_info_1']         = 'Install the module to create the DB table and enable events.';
$_['text_info_2']         = 'After saving, the 🐛 Debug button appears on enabled areas.';
$_['text_info_3']         = 'Click the button to submit a report with console errors and a comment.';

// Entries
$_['entry_status']           = 'Status';
$_['entry_admin_enable']     = 'Admin Panel';
$_['entry_catalog_enable']   = 'Catalog (Frontend)';
$_['entry_capture_console']  = 'Capture Console Errors';
$_['entry_capture_network']  = 'Capture Failed AJAX';
$_['entry_require_comment']  = 'Require Comment';
$_['entry_max_reports']      = 'Max Reports';
$_['entry_severity_bug']     = 'Bug';
$_['entry_severity_warning'] = 'Warning';
$_['entry_severity_info']    = 'Info';

// Help
$_['help_admin_enable']      = 'Shows the 🐛 button in the admin navbar for logged-in users.';
$_['help_catalog_enable']    = 'Shows a floating 🐛 button on all storefront pages.';
$_['help_capture_console']   = 'Automatically captures JavaScript console.error() and uncaught exceptions.';
$_['help_capture_network']   = 'Intercepts and logs failed AJAX / fetch requests.';
$_['help_require_comment']   = 'Forces the user to enter a comment before submitting a report.';
$_['help_max_reports']       = 'Maximum number of reports to keep. Oldest are auto-deleted (0 = unlimited).';
$_['help_severity']          = 'Choose which severity levels appear in the report form dropdown.';

// Buttons
$_['button_save']         = 'Save';
$_['button_cancel']       = 'Cancel';
$_['button_view_reports'] = 'View Reports';
$_['button_clear_all']    = 'Clear All Reports';

// Popup modal (injected via event)
$_['popup_title']          = 'Report a Problem';
$_['popup_btn_trigger']    = 'Report a Problem';
$_['popup_label_page']     = 'Page';
$_['popup_label_severity'] = 'Severity';
$_['popup_label_comment']  = 'Comment';
$_['popup_label_console']  = 'Console Errors';
$_['popup_placeholder']    = 'Describe the issue...';
$_['popup_severity_bug']   = '🐛 Bug';
$_['popup_severity_warn']  = '⚠ Warning';
$_['popup_severity_info']  = 'ℹ Info';
$_['popup_btn_cancel']     = 'Cancel';
$_['popup_btn_save']       = 'Save';
$_['popup_btn_reports']    = 'View Reports';
$_['popup_tip_severity']   = 'Choose the impact level of the issue you observed.';
$_['popup_tip_comment']    = 'Describe what you were doing and what went wrong. Required if "Require Comment" is enabled.';
$_['popup_tip_console']    = 'JavaScript errors automatically captured on this page.';
$_['popup_tip_reports']    = 'Access the reports list (administrators only).';

// Confirm
$_['text_confirm_clear']  = 'Delete ALL reports? This cannot be undone.';

// Error
$_['error_permission']    = 'Warning: You do not have permission to modify Debug Logger!';

// Pro features
$_['text_license']           = 'License';
$_['entry_license_key']      = 'License Key';
$_['help_license_key']       = 'Enter your Pro license key (XXXX-XXXX-XXXX-XXXX) to unlock all features.';
$_['text_free_limit']        = 'Free version: limited to 50 reports. Upgrade to Pro for unlimited reports and advanced features.';
$_['text_disabled']          = 'Disabled';

$_['entry_capture_screenshot'] = 'Capture Screenshot';
$_['help_capture_screenshot']  = 'Automatically takes a screenshot of the page when reporting (uses html2canvas).';
$_['popup_label_screenshot']   = 'Screenshot';

$_['text_email']             = 'Email Notifications';
$_['entry_email_enable']     = 'Enable Email';
$_['entry_email_to']         = 'Recipient';
$_['help_email_to']          = 'Email address to receive bug report notifications.';
$_['text_email_severity']    = 'Notify on';
$_['entry_email_bug']        = 'Bug';
$_['entry_email_warning']    = 'Warning';
$_['entry_email_info']       = 'Info';

$_['text_webhook']           = 'Webhook Notifications';
$_['entry_webhook_type']     = 'Service';
$_['entry_webhook_url']      = 'Webhook URL';
$_['help_webhook_url']       = 'Paste your Slack or Discord webhook URL here.';

$_['text_pro_title']         = 'Pro Features';
$_['text_pro_1']             = 'Unlimited reports';
$_['text_pro_2']             = 'Screenshot capture (html2canvas)';
$_['text_pro_3']             = 'Email notifications on new reports';
$_['text_pro_4']             = 'Slack / Discord webhook integration';
$_['text_pro_5']             = 'Export CSV / JSON + report assignment';
