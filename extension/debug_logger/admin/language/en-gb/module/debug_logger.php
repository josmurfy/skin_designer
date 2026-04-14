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
$_['text_info_2']         = 'After saving, the Debug button appears on enabled areas.';
$_['text_info_3']         = 'Click the button to submit a report with console errors and a comment.';
$_['text_active_title']   = 'Debug Logger is Active';
$_['text_not_enabled']    = 'Enable the module and save to start capturing debug reports.';
$_['text_notifications_pro_only'] = 'Notifications require a Pro license. Enter your key in the License tab.';
$_['text_license_active'] = 'Pro license active — all features unlocked.';

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
$_['help_admin_enable']      = 'Shows the Debug button in the admin navbar for logged-in users.';
$_['help_catalog_enable']    = 'Shows a floating Debug button on all storefront pages.';
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
$_['popup_severity_bug']   = 'Bug';
$_['popup_severity_warn']  = 'Warning';
$_['popup_severity_info']  = 'Info';
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
$_['popup_label_files']        = 'Loaded Files';
$_['popup_tip_files']          = 'PHP, Twig, JS, and CSS files loaded on this page.';

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
$_['text_test_email_title']  = 'Test Connection';
$_['button_test_email']      = 'Send Test Email';
$_['help_test_email']        = 'Sends a test email to the recipient address above using the store mail settings.';
$_['text_test_email_sent']   = 'Test email sent to %s — check your inbox (and spam folder).';
$_['text_test_email_failed'] = 'Failed to send test email: %s';
$_['text_test_email_invalid'] = 'Please enter a valid recipient email address above.';

$_['text_pro_title']         = 'Pro Features';
$_['text_pro_1']             = 'Unlimited Reports';
$_['text_pro_1_desc']        = 'No 50-report cap. Keep your full history.';
$_['text_pro_2']             = 'Screenshot Capture';
$_['text_pro_2_desc']        = 'Auto-capture with built-in annotation editor.';
$_['text_pro_3']             = 'Email Notifications';
$_['text_pro_3_desc']        = 'Get notified on new reports with screenshot attached.';
$_['text_pro_4']             = 'Webhook Integration';
$_['text_pro_4_desc']        = 'Send reports to Slack or Discord instantly.';
$_['text_pro_5']             = 'Export Reports';
$_['text_pro_5_desc']        = 'Download reports as CSV or JSON.';
$_['text_pro_6']             = 'Report Assignment';
$_['text_pro_6_desc']        = 'Assign reports to team members for follow-up.';

// Tabs
$_['tab_general']            = 'General';
$_['tab_capture']            = 'Capture';
$_['tab_notifications']      = 'Notifications';
$_['tab_appearance']         = 'Appearance';
$_['tab_updates']            = 'Updates';
$_['tab_permissions']        = 'Permissions';
$_['tab_license']            = 'License & Pro';

// Updates tab
$_['text_current_version']   = 'Current Version';
$_['text_latest_version']    = 'Latest Version';
$_['text_update_available']  = 'A new version is available!';
$_['text_up_to_date']        = 'You are running the latest version.';
$_['text_checking_update']   = 'Checking for updates...';
$_['text_update_error']      = 'Unable to check for updates. Please try again later.';
$_['text_changelog']         = 'Changelog';
$_['text_update_source']     = 'Updates are checked from GitHub:';
$_['button_check_update']    = 'Check for Updates';
$_['button_download_update'] = 'Download';
$_['button_view_release']    = 'View Release';
$_['button_install_update']  = 'Install Update';
$_['text_installing']        = 'Downloading and installing update...';
$_['text_install_success']   = 'Update installed successfully! Version %s is now active. Please refresh the page.';
$_['text_install_download_error'] = 'Failed to download the update file.';
$_['text_install_extract_error']  = 'Failed to extract the update archive.';
$_['text_version_history']       = 'Version History';
$_['text_version_history_hint']  = 'Click the Updates tab to load version history.';
$_['text_version_installed']     = 'INSTALLED';
$_['text_version_newer']         = 'NEW';
$_['text_version_downgrade']     = 'Install this version';
$_['text_confirm_downgrade']     = 'Are you sure you want to install an older version? This will overwrite the current version.';
$_['button_refresh']             = 'Refresh';

// Appearance tab (Pro)
$_['text_appearance_pro_only'] = 'Appearance customization requires a Pro license.';
$_['text_appearance_colors'] = 'Colors';
$_['text_appearance_layout'] = 'Button Layout';
$_['text_appearance_preview'] = 'Preview';
$_['entry_btn_color']        = 'Button Color';
$_['help_btn_color']         = 'Background color of the report trigger button.';
$_['entry_header_color']     = 'Modal Header Color';
$_['help_header_color']      = 'Background color of the report popup header.';
$_['entry_accent_color']     = 'Accent Color';
$_['help_accent_color']      = 'Color used for borders, links and focus highlights.';
$_['entry_btn_position']     = 'Button Position';
$_['help_btn_position']      = 'Where the report button appears on the page.';
$_['entry_btn_size']         = 'Button Size';
$_['text_pos_navbar']        = 'Navbar (default)';
$_['text_pos_bottom_right']  = 'Bottom Right';
$_['text_pos_bottom_left']   = 'Bottom Left';
$_['text_pos_top_right']     = 'Top Right';
$_['text_pos_top_left']      = 'Top Left';
$_['text_size_small']        = 'Small';
$_['text_size_medium']       = 'Medium';
$_['text_size_large']        = 'Large';
$_['button_reset_defaults']  = 'Reset to Defaults';

// Permissions tab
$_['text_permissions_info']  = 'Select which user groups can see and use the Debug Logger button. If none are selected, all groups have access.';
$_['text_allowed_groups']    = 'Allowed User Groups';
$_['help_allowed_groups']    = 'Check the groups that should see the Debug Logger report button in the admin panel.';
$_['text_group_name']        = 'Group Name';

// v3.0.0 — Tags, Resolution, Bulk Actions
$_['text_tags']              = 'Tags';
$_['text_add_tag']           = 'Add tag';
$_['text_resolution']        = 'Resolution';
$_['text_resolution_hint']   = 'Document the fix, workaround, or root cause here.';
$_['text_bulk_selected']     = '%d selected';
$_['text_bulk_close']        = 'Close Selected';
$_['text_bulk_open']         = 'Reopen Selected';
$_['text_bulk_delete']       = 'Delete Selected';
$_['text_bulk_confirm_delete'] = 'Delete selected reports?';
$_['text_assignment_email']  = 'Assignment notification emailed to %s.';
$_['text_filter_tag']        = 'Filter by Tag';

// v3.1.0 — Analytics & Dark Mode
$_['text_analytics']         = 'Analytics';
$_['text_analytics_title']   = 'Analytics Dashboard';
$_['text_total_reports_stat'] = 'Total Reports';
$_['text_open_stat']         = 'Open';
$_['text_closed_stat']       = 'Closed';
$_['text_avg_resolution']    = 'Avg Resolution Time';
$_['text_reports_per_day']   = 'Reports / Day (Last 30 Days)';
$_['text_severity_dist']     = 'Severity Distribution';
$_['text_activity_by_hour']  = 'Activity by Hour (Last 30 Days)';
$_['text_source_dist']       = 'Source Distribution';
$_['text_top_pages']         = 'Top Error Pages';
$_['text_recurring_issues']  = 'Recurring Issues (Same URL + Severity)';
$_['text_recent_activity']   = 'Recent Activity';
$_['text_no_data']           = 'No data yet.';
$_['text_no_recurring']      = 'No recurring patterns detected.';
$_['button_dark_mode']       = 'Dark Mode';
$_['button_light_mode']      = 'Light Mode';
$_['text_settings']          = 'Settings';
$_['text_reports']           = 'Reports';

// v3.2.0 — Admin Menu (column_left)
$_['text_menu_title']        = 'Debug Logger';
$_['text_menu_dashboard']    = 'Dashboard';
$_['text_menu_reports']      = 'Log Reports';
$_['text_menu_settings']     = 'Settings';

// v3.3.2 — toast messages
$_['popup_toast_saved']      = 'Report #%s saved';
$_['popup_toast_error']      = 'Error';

// v3.3.2 — screenshot editor
$_['popup_ss_edit']          = 'Edit Screenshot';
$_['popup_ss_done']          = 'Done';
$_['popup_ss_cancel']        = 'Cancel';
$_['popup_ss_draw']          = 'Draw';
$_['popup_ss_arrow']         = 'Arrow';
$_['popup_ss_rect']          = 'Rect';
$_['popup_ss_text']          = 'Text';
$_['popup_ss_undo']          = 'Undo';
$_['popup_ss_reset']         = 'Reset';
$_['popup_ss_thin']          = 'Thin';
$_['popup_ss_normal']        = 'Normal';
$_['popup_ss_thick']         = 'Thick';
$_['popup_ss_prompt']        = 'Text:';
