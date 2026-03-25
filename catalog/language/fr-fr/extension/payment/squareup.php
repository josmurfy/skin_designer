<?php
// Text
$_['text_accepted_cards']                       = 'Cartes Acceptée:';
$_['text_card_cvc']                             = 'Code de sécurité de la carte (CVC):';
$_['text_card_details']                         = 'Payer par carte de crédit / débit';
$_['text_card_ends_in']                         = 'Payer avec la carte %s existante qui se termine le XXXX XXXX XXXX %s';
$_['text_card_expiry']                          = 'Expiration de la carte (MM/AA):';
$_['text_card_number']                          = 'Numéro de carte:';
$_['text_card_placeholder']                     = 'XXXX XXXX XXXX XXXX';
$_['text_card_save']                            = 'Enregistrer la carte pour une utilisation future?';
$_['text_card_save_warning']                    = 'Square peut accepter les paiements récurrents uniquement avec des cartes enregistrées.';
$_['text_card_zip']                             = 'Code postal de la carte:';
$_['text_card_zip_placeholder']                 = 'Code postal';
$_['text_cron_expiration_message_expired']      = 'Les transactions non capturées suivantes ont expiré. Ils ont été automatiquement annulés par Square en raison de 6 jours d\'inactivité. Les clients ont été informés.';
$_['text_cron_expiration_message_expiring']     = 'Les transactions non capturées suivantes sont sur le point d`\'expirer. Veuillez agir dès que possible.';
$_['text_cron_expiration_subject']              = 'Transactions Square non capturées';
$_['text_cron_fail_charge']                     = 'Profile <strong>#%s</strong> n\'a pas pu être changé <strong>%s</strong>';
$_['text_cron_inventory_dashboard']             = '<a href="%s" target="_blank">Cliquez ici</a> pour accéder à votre tableau de bord Square afin de définir manuellement vos inventaires.<br /><br />For more detailed instructions, here\'s a helpful <a href="%s" target="_blank">Video Tutorial</a>!';
$_['text_cron_inventory_links_intro']           = 'New items have just been synced. Some of them have been added to Square with empty inventories.';
$_['text_cron_inventory_links_more']            = '... %s other item(s).';
$_['text_cron_message']                         = 'Here is a list of all CRON tasks performed by your Square extension:';
$_['text_cron_subject']                         = 'Square CRON job summary';
$_['text_cron_success_charge']                  = 'Profile <strong>#%s</strong> was charged with <strong>%s</strong>';
$_['text_cron_summary_error_heading']           = 'Transaction Errors:';
$_['text_cron_summary_fail_heading']            = 'Failed Transactions (Profiles Suspended):';
$_['text_cron_summary_fail_sync']               = 'Sync failed. Errors:';
$_['text_cron_summary_success_heading']         = 'Successful Transactions:';
$_['text_cron_summary_success_sync_heading']    = 'Sync between Square and OpenCart:';
$_['text_cron_summary_token_heading']           = 'Refresh of access token:';
$_['text_cron_summary_token_updated']           = 'Access token updated successfully!';
$_['text_cron_tax_rates_intro']                 = 'The last Square sync has resulted in <strong>%s</strong> new Tax Rate(s) in your OpenCart store.<br /><br />Please visit <a href="%s" target="_blank">the admin panel of the Square Payment Extension</a> to configure the appropriate Geo Zone for each new Tax Rate:';
$_['text_cron_tax_rates_subject']               = 'Square - newly created Tax Rates';
$_['text_cron_warnings_intro']                  = 'The last Square sync has resulted in <strong>%s</strong> issues:';
$_['text_cron_warnings_subject']                = 'Square Catalog Sync: A few items to update manually';
$_['text_cvv']                                  = 'CVV';
$_['text_default_squareup_name']                = 'Carte de crédit / Carte débit';
$_['text_expiry']                               = 'MM/AA';
$_['text_length']                               = ' pour %s paiements';
$_['text_loading']                              = 'Chargement... Veuillez patienter...';
$_['text_new_card']                             = '+ Ajouter un nouvelle carte';
$_['text_order_error_mail_intro']               = 'La commande suivante <strong>#%s</strong> a été soumise avec succès; Il a été soumis à Square en tant que transaction "Montant personnalisé" non détaillée en raison de l\'erreur suivante:';
$_['text_order_error_mail_outro']               = 'Because of the order being recorded as "Custom Amount", you may need to manually adjust your accounting and inventory entries in Square to reflect the itemization of the order.<br /><br />To prevent this issue in the future:<br /><br />#1 - Please first ensure you have the latest version of the Square plug-in.<br />#2 - If upgrading does not resolve the issue, please file a support ticket here [ <a href="%s" target="_blank">%s</a> ] with as much information as possible.<br /><br />This is an automated email. Please do not reply.';
$_['text_order_error_mail_subject']             = 'Square Problème de commande';
$_['text_order_id']                             = 'Commande #%s';
$_['text_pay_with_applepay']                    = 'Payer avec Apple Pay';
$_['text_pay_with_wallet']                      = 'Payer avec un portefeuille numérique';
$_['text_recurring']                            = '%s tous les %s %s';
$_['text_saved_card']                           = 'Utiliser la carte enregistrée:';
$_['text_secured']                              = 'Sécurisé par Square';
$_['text_squareup_profile_suspended']           = ' Your recurring payments have been suspended. Please contact us for more details.';
$_['text_squareup_recurring_expired']           = ' Your recurring payments have expired. This was your last payment.';
$_['text_squareup_trial_expired']               = ' Your trial period has expired.';
$_['text_sync_disabled']                        = 'Sync is disabled. No sync has been performed.';
$_['text_token_expired_message']                = "The Square payment extension's access token connecting it to your Square account has expired. You need to verify your application credentials and CRON job in the extension settings and connect again.";
$_['text_token_expired_subject']                = 'Your Square access token has expired!';
$_['text_token_issue_customer_error']           = 'We are experiencing a technical outage in our payment system. Please try again later.';
$_['text_token_revoked_message']                = "The Square payment extension's access to your Square account has been revoked through the Square Dashboard. You need to verify your application credentials in the extension settings and connect again.";
$_['text_token_revoked_subject']                = 'Your Square access token has been revoked!';
$_['text_trial']                                = '%s every %s %s for %s payments then ';
$_['text_view']                                 = 'VUE';
$_['text_wallet_details']                       = 'Payer avec un portefeuille numérique';

// Error
$_['error_browser_not_supported']               = 'Error: The payment system no longer supports your web browser. Please update or use a different one.';
$_['error_card_invalid']                        = 'Erreur: la carte n\'est pas valide!';
$_['error_currency_invalid']                    = 'The expected currency is not supported on this store.';
$_['error_generic']                             = 'Unexpected website error. Please contact the store owner on <strong>%s</strong> or e-mail <strong>%s</strong>. Note that your transaction may be processed.';
$_['error_price_invalid_negative']              = 'The recurring price is negative. This amount cannot be charged by Square.';
$_['error_squareup_cron_token']                 = 'Error: Access token could not get refreshed. Please connect your Square Payment extension via the OpenCart admin panel.';
$_['error_currency_mismatch']                   = 'Your default store currency is different than your Square location currency, therefore the catalog was not synced. In order for Catalog Sync to work, your default OpenCart currency must be %s.';

// Warning
$_['warning_currency_converted']                = 'Attention: le montant total payé sera converti en <strong>%s</strong> à un taux de conversion de <strong>%s</strong>. Le montant attendu de la transaction sera <strong>%s</strong>.';

// Statuses
$_['squareup_status_comment_authorized']        = 'La transaction par carte a été autorisée mais pas encore capturée.';
$_['squareup_status_comment_captured']          = 'La transaction par carte a été autorisée puis capturée (c.-à-d. Terminée).';
$_['squareup_status_comment_failed']            = 'La transaction par carte a échoué.';
$_['squareup_status_comment_voided']            = 'La transaction par carte a été autorisée puis annulée (c\'est-à-dire annulée).';

// Override errors
$_['squareup_error_field']                                  = ' Champ: %s';
$_['squareup_override_error_billing_address.country']       = 'Le pays de l\'adresse de paiement n\'est pas valide. S\'il vous plaît modifiez le et essayez de nouveau.';
$_['squareup_override_error_email_address']                 = 'L\'adresse e-mail de votre client n\'est pas valide. S\'il vous plaît modifiez le et essayez de nouveau.';
$_['squareup_override_error_phone_number']                  = 'Votre numéro de téléphone client n\'est pas valide. S\'il vous plaît modifiez le et essayez de nouveau.';
$_['squareup_override_error_shipping_address.country']      = 'Le pays de l\'adresse de livraison n\'est pas valide. S\'il vous plaît modifiez le et essayez de nouveau.';
