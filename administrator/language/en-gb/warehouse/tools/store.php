<?php
// Original: shopmanager/opencart.php

$_['text_title']			 = '<p>Your OpenCart store can be connected in just minutes. Please follow the instructions below.</p>
                                <ol>
                                <li>Download <a href="https:download/opencart.zip" rel="noreferrer" target="_blank">this file</a> and extract the contents to your OpenCart folder</li>
                                <li>Modify the chmod on the opencart/config.php file to 777.</li>
                                <li>In your OpenCart Admin site, go to <b>Extensions &gt; Modules</b>.</li>
                                <li>Click <b>Install</b> next to the <b>CanUShip Config</b> module.</li>
                                <li>Click <b>Edit</b> next to the <b>CanUShip Config</b> module.</li>
                                <li>Enter the <b>CanUShip Key</b> and <b>CanUShip Verification Key</b> in the fields below</li>
                                <li>Enter your web site\'s URL below. If OpenCart is installed in a subfolder, include the folder in the path
                                (e.g. https://www.opencart.com/ocart)</li><li>Click <b>Test Connection</b> to see if the steps you followed worked correctly</li>
                                <li>Click on <b>Connect</b> to make this store active!</li>
                                </ol>';

//entry
$_['entry_version']	      = 'Version';
$_['entry_userkey']	   	  = 'CanUShip Key';
$_['entry_regkey']		  = 'CanUShip Verification Key';
$_['entry_url']			  = 'Enter your OpenCart WebSite';

//text
$_['text_select']			= 'Select version';
$_['text_ver1']		        = 'Version Less Than 2.0';
$_['text_ver2']      	    = 'Version 2.0 - 2.2';
$_['text_ver23']		    = 'Version 2.3';
$_['text_ver3']			    = 'Version 3.0 Or Greater';

//error
$_['error_version']			= 'You must select a version of your Opencart';
$_['error_userkey']			= 'You must specify the Key';
$_['error_regkey']	        = 'You must specify the Verification Key';
$_['error_url']	         	= 'You must specify the url of your Opencart Website (https://)';
$_['error_connection']	    = 'Connection failed: ';

$_['success_connection']	= 'Connection SUCCESS';


$_['button_back']	        = 'Back';
$_['button_testconnection']	= 'Test Connection';
$_['button_cancel']	        = 'Cancel';
$_['button_connect']	    = 'Connect';


