<?php
// Original: warehouse/marketplace/connection.php

$_['heading_title']				          = 'Connecter mes sites de vente';
$_['heading_title_account']         = 'Connecter mes sites de vente';
$_['heading_title_add']			        = 'Ajouter un site de vente';
$_['heading_title_edit']	          = 'Modifier un site de vente';

//text
$_['text_marketplace_account_list']	      = 'Liste des compte des sites de vente';
$_['text_add']					            = 'Connecter un magasin ou un marketplace';
$_['text_account']	   		          = 'Compte';
$_['text_marketplace_account']			        = 'Compte de site de vente';
$_['text_no_results']			          = 'Aucun enregistrement trouvé !';
$_['text_enabled']	   		          = 'Activé';
$_['text_disabled']	   		          = 'Désactivé';
$_['text_confirm']		  	          = 'Êtes-vous sûr?';
$_['text_success_add']			        = 'Succès : les détails du compte du site de vente ont été modifiés avec succès!';
$_['text_success']				          = 'Succès : le compte du site de vente a bien été supprimé !';

//column
$_['column_account_id']		         	= 'Identifiant de compte';
$_['column_marketplace_store_name']      	= 'Nom du magasin';
$_['column_marketplace_user_id']		        = 'Nom d\'utilisateur';
$_['column_marketplace_sites']			= 'Site de vente';
$_['column_action']			          	= 'Action';
$_['column_status']			          	= 'Statut';

//button
$_['button_add_ebay_account']	         	= 'Ajouter un compte Ebay';
$_['button_add_bonanza_account']	         	= 'Ajouter un compte Bonanza';
$_['button_add_opencart_account']	         	= 'Ajouter un compte Opencart';
$_['button_add_account']	         	= 'Ajouter un Account';
$_['button_add_shopify_account']	         	= 'Ajouter un compte Shopify';
$_['button_add_shipstation_account']	         	= 'Ajouter un compte Shipstation';
$_['button_add_etsy_account']	         	= 'Ajouter un compte Etsy';
$_['button_refresh']	         		= 'Rafraîchir';
$_['button_save']			            	= 'Enregistrer le compte';
$_['button_filter']			          	= 'Filtrer les comptes';
$_['button_clear']			          	= 'Effacer le filtre';
$_['button_cancel']                 = 'Annuler';
$_['button_save_token']             = 'Sauvegarder le token';
$_['button_delete']                 = 'Supprimer';

// Gestion des tokens
$_['text_token_status']             = 'Statut Token';
$_['text_token_present']            = 'Token OK';
$_['text_token_truncated']          = 'Token tronqué!';
$_['text_token_absent']             = 'Aucun token';
$_['text_token_valid']              = 'Valide — connexion eBay OK';
$_['text_token_invalid']            = 'Invalide ou expiré — veuillez le renouveler';
$_['text_token_missing']            = 'Refresh token absent';
$_['text_token_too_short']          = 'Token trop court — un refresh token eBay fait 800+ caractères. Vérifiez le copier-coller.';
$_['text_token_saved_valid']        = 'Token sauvegardé et vérifié — connexion eBay OK!';
$_['text_token_saved_invalid']      = 'Token sauvegardé mais INVALIDE — vérifiez que vous avez copié le bon refresh token.';
$_['text_token_empty']              = 'Veuillez coller un refresh token.';
$_['text_test_token']               = 'Tester la connexion eBay';
$_['text_update_token']             = 'Mettre à jour le Refresh Token';
$_['text_testing']                  = 'Test en cours';
$_['text_new_refresh_token']        = 'Nouveau Refresh Token';
$_['text_token_placeholder']        = 'Collez votre nouveau refresh token eBay ici (800+ caractères)...';
$_['text_token_hint']               = 'Obtenez-le sur developer.ebay.com → Votre App → User Tokens → Get a Token from eBay';
$_['text_token_instructions_title'] = 'Comment obtenir un nouveau refresh token :';
$_['text_token_instructions']       = '1. Allez sur <a href="https://developer.ebay.com/my/keys" target="_blank">developer.ebay.com/my/keys</a><br>2. Cliquez <strong>Get a Token from eBay via Your Application</strong> (Production)<br>3. Connectez-vous avec votre compte vendeur eBay<br>4. Copiez le <strong>Refresh Token</strong> (PAS l\'Access Token)<br>5. Collez-le ci-dessous et sauvegardez';


// OAuth2 Flow
$_['text_oauth_success']            = 'eBay OAuth2 : Nouveau refresh token obtenu et sauvegardé avec succès!';
$_['error_oauth_session_expired']   = 'Session OAuth expirée — veuillez réessayer.';
$_['error_oauth_denied']            = 'Autorisation eBay refusée';
$_['error_oauth_no_code']           = 'Aucun code d\'autorisation reçu d\'eBay.';
$_['error_oauth_curl']              = 'Erreur réseau lors du contact avec eBay';
$_['error_oauth_exchange']          = 'Échec de l\'échange du code d\'autorisation';

// Clés manquantes (ajoutées)
$_['button_add_amazon_account']  = 'Ajouter un compte Amazon';
$_['text_success_edit']          = 'Succès : Les détails du compte Marketplace ont été modifiés avec succès !';
$_['text_success_modif']         = 'Succès : Les détails du compte Marketplace ont été modifiés avec succès !';
$_['text_success_remove']        = 'Succès : Le compte Marketplace a été supprimé avec succès !';
