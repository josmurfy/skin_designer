<?php

include_once(DIR_SYSTEM . 'library/jgetsy/KbOAuth.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyApi.php');
include_once(DIR_SYSTEM . 'library/jgetsy/RequestValidator.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyMain.php');
include_once(DIR_SYSTEM . 'library/jgetsy/oauth_client.php');
include_once(DIR_SYSTEM . 'library/jgetsy/http.php');

class ControllerJgetsyTemplate extends Controller {

    private $syncType = 'SyncShippingTemplates';
    private $syncMethod = '';
    private $syncError = false;

    public function index() {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', -1);
        @set_time_limit(0);
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');
            $this->load->model('jgetsy/shipping_profile');

            $this->model_jgetsy_cron->auditLogEntry("Shipping template sync started", $this->syncType);

            $settings = $this->config->get('etsy_general_settings');
            if (isset($settings['enable']) && $settings['enable'] == 1) {
                $etsy_access_token = $this->config->get('etsy_access_token');
                $etsy_access_token_secret = $this->config->get('etsy_access_token_secret');
                if ($etsy_access_token && $etsy_access_token_secret) {
                    $this->getShippingTemplateFromEtsy();
                    $this->createShippingTemplateRequest();
                    $this->renewShippingTemplateRequest();
                    $this->deleteShippingTemplateRequest();
                    $this->createShippingTemplateEntryRequest();
                    $this->updateShippingTemplateEntryRequest();
                    $this->deleteShippingTemplateEntryRequest();
                    if ($this->syncError == true) {
                        echo "Success with some error(s). Refer to audit log for the details of the error.";
                    } else {
                        echo "Success";
                    }
                } else {
                    $logEntry = "Please connect you store to etsy from general settings page!";
                    $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncType);
                    echo $logEntry;
                }
            } else {
                $logEntry = "Module is not enabled. Kindly go to general settings page to enable the module.";
                $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncType);
                echo $logEntry;
            }
            $this->model_jgetsy_cron->auditLogEntry("Shipping template syncing completed", $this->syncType);
        }
        die();
    }

    private function getShippingTemplateFromEtsy() {
        $this->syncMethod = 'SyncTemplateEtsyToOC';
        $logEntry = 'Sync templates from Etsy to OC started';
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        $templateData = array();

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        /* Get Esty User ID To pass in findAllUserShippingProfiles */
        $result = $etsyMain->sendRequest('usersDetail', array());
        if (isset($result['error'])) {
            $this->syncError = true;
            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
            return false;
            /* Mandatory to return false to avoid wrong template deletion. If not returned, etsy templates data will be empty & system will consider that there is not template & sytem will delete the template from local (Botton code of this funcation to delete the template */
        } elseif (isset($result['results'])) {
            foreach ($result['results'] as $users) {
                $user_id = $users['user_id'];
                $templateResult = $etsyMain->sendRequest('findAllUserShippingProfiles', array("params" => array('user_id' => $user_id, 'limit' => 100)));
                if (isset($templateResult['error'])) {
                    $this->syncError = true;
                    $this->model_jgetsy_cron->auditLogEntry($templateResult['error'], $this->syncMethod);
                    return false;
                    /* Mandatory to return false to avoid wrong template deletion. If not returned, etsy templates data will be empty & system will consider that there is not template & sytem will delete the template from local (Botton code of this funcation to delete the template */
                } else {
                    foreach ($templateResult['results'] as $template) {
                        $templateData[] = array(
                            'template_id' => $template['shipping_profile_id'],
                            'title' => $template['title'],
                            'min_processing_days' => $template['min_processing_days'],
                            'max_processing_days' => $template['max_processing_days'],
                            'processing_days_display_label' => $template['processing_days_display_label'],
                            'origin_country_id' => $template['origin_country_id']
                        );
                    }
                }
            }
        }
        if (!empty($templateData)) {
            foreach ($templateData as $template) {

                /* If there is an error in fetching shipping template entry for any template then don't add/update that template */
                $templateEntryResult = $etsyMain->sendRequest('findAllShippingTemplateEntries', array("params" => array('shipping_profile_id' => $template['template_id'], 'limit' => 100)));
                if (isset($templateEntryResult['error'])) {
                    $this->syncError = true;
                    $this->model_jgetsy_cron->auditLogEntry($templateEntryResult['error'], $this->syncMethod);
                } else {

                    $country_data = $this->model_jgetsy_cron->geyEtsyCountry($template['origin_country_id']);

                    /* If template exist then update the template else create the new template */
                    $template_exist = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE shipping_profile_id = " . $template['template_id']);
                    if ($template_exist->num_rows > 0) {
                        $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_profiles SET "
                                . "shipping_profile_title = '" . $template['title'] . "',"
                                . "shipping_origin_country_id = " . $template['origin_country_id'] . ","
                                . "shipping_origin_country = '" . $this->db->escape($country_data['country_name']) . "',"
                                . "shipping_min_process_days = " . $template['min_processing_days'] . ","
                                . "shipping_max_process_days = " . $template['max_processing_days'] . " "
                                . "WHERE shipping_profile_id = " . $template['template_id']);
                        $template_id = $template_exist->row['id_etsy_shipping_profiles'];
                    } else {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shipping_profiles SET "
                                . "shipping_profile_id = " . $template['template_id'] . ","
                                . "shipping_profile_title = '" . $template['title'] . "',"
                                . "shipping_origin_country_id = " . $template['origin_country_id'] . ","
                                . "shipping_origin_country = '" . $this->db->escape($country_data['country_name']) . "',"
                                . "shipping_min_process_days = " . $template['min_processing_days'] . ","
                                . "shipping_max_process_days = " . $template['max_processing_days'] . ","
                                . "shipping_date_added = '" . date("Y-m-d H:i:s") . "'");
                        $template_id = $this->db->getLastId();
                    }

                    /* Delete All Shipping Tempalte Entries & add again */
                    $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = " . $template_id);

                    foreach ($templateEntryResult['results'] as $templateEntry) {
                        if ($templateEntry['destination_region_id'] != "") {
                            $destination_country_id = '';
                            $destination_country = '';
                            $destination_region_id = $templateEntry['destination_region_id'];

                            /* If Region ID etnry for shipping template exist, then don't add more entry because others are duplicate */
                            $template_entry_region_check = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = " . $template_id . " AND shipping_entry_destination_region_id = " . $destination_region_id);
                            if ($template_entry_region_check->num_rows > 0) {
                                continue;
                            }
                            $destination_region_data = $this->model_jgetsy_cron->geyEtsyRegion($templateEntry['destination_region_id']);
                            $destination_region = $destination_region_data['region_name'];
                        } else if ($templateEntry['destination_country_id'] != "") {
                            $destination_region_id = '';
                            $destination_region = '';
                            $destination_country_id = $templateEntry['destination_country_id'];
                            $destination_country_data = $this->model_jgetsy_cron->geyEtsyCountry($templateEntry['destination_country_id']);
                            $destination_country = $destination_country_data['country_name'];
                        } else {
                            $destination_region_id = '';
                            $destination_region = '';
                            $destination_country_id = '';
                            $destination_country = '';
                        }
                        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shipping_profiles_entries SET "
                                . "id_etsy_shipping_profiles = " . $template_id . ","
                                . "shipping_profile_entry_id = " . $templateEntry['shipping_profile_entry_id'] . ","
                                . "shipping_entry_destination_country_id = '" . $destination_country_id . "',"
                                . "shipping_entry_destination_country = '" . $this->db->escape($destination_country) . "',"
                                . "shipping_entry_destination_region_id = '" . $destination_region_id . "',"
                                . "shipping_entry_destination_region = '" . $destination_region . "',"
                                . "shipping_entry_primary_cost = " . $this->db->escape($templateEntry['primary_cost']) . ","
                                . "shipping_entry_secondary_cost = " . $this->db->escape($templateEntry['secondary_cost']) . ","
                                . "shipping_entry_date_added = '" . date("Y-m-d H:i:s") . "'");
                    }
                }
            }
        }

        /* Delete Shipping Profile from local which has been deleted from the Etsy */
        $shipping_profile_data = $this->model_jgetsy_shipping_profile->getAllShippingTemplates();
        if ($shipping_profile_data) {
            foreach ($shipping_profile_data as $shipping_profile) {
                /* Delete only when shipping_profile_id is not blank. In case shipping_profile_id is blank that means tempalte has been added into local & sync to etsy is pending for the template */
                if ($shipping_profile['shipping_profile_id'] != "") {
                    $delete_flag = true;
                    if (!empty($templateData)) {
                        foreach ($templateData as $template) {
                            if ($template['template_id'] == $shipping_profile['shipping_profile_id']) {
                                /* Template exist on the etsy so not to be delete from the local */
                                $delete_flag = false;
                                break;
                            }
                        }
                    } else {
                        /* Template doesn't exist on the Etsy (As there is no template returned from the etsy */
                        $delete_flag = true;
                    }
                    if ($delete_flag == true) {
                        $this->model_jgetsy_shipping_profile->deleteShippingTemplate($shipping_profile['id_etsy_shipping_profiles']);
                        $logEntry = "Shipping Template: " . $shipping_profile['shipping_profile_id'] . " (" . $shipping_profile['shipping_profile_title'] . ") has been deleted from the Etsy. Removed the same from local";
                        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
                    }
                }
            }
        }

        $logEntry = 'Shipping templates Sync ended.';
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        return true;
    }

    public function createShippingTemplateRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');

            $this->syncMethod = 'CreateTemplate';
            $logEntry = 'Create shipping templates on Etsy started';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $getShippingTemplates = $this->model_jgetsy_cron->getShippingTemplatesToAdd();
            $shippingTemplatesCreated = 0;
            if ($getShippingTemplates) {

                $logEntry = "Found " . count($getShippingTemplates) . " to add";
                $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

                $data = array();
                foreach ($getShippingTemplates as $shippingTemplate) {
                    $data = array(
                        'title' => $shippingTemplate['shipping_profile_title'],
                        'origin_country_id' => $shippingTemplate['shipping_origin_country_id'],
                        'destination_country_id' => $shippingTemplate['shipping_origin_country_id'],
                        'primary_cost' => (float) $shippingTemplate['shipping_primary_cost'],
                        'secondary_cost' => (float) $shippingTemplate['shipping_secondary_cost'],
                        'min_processing_days' => $shippingTemplate['shipping_min_process_days'],
                        'max_processing_days' => $shippingTemplate['shipping_max_process_days'],
                        'origin_postal_code' => '12919',
                        'max_delivery_time'=>$shippingTemplate['shipping_max_process_days'],
                        'min_delivery_time' =>$shippingTemplate['shipping_min_process_days']
                    );
                    
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest('createShippingTemplate', array('data' => $data, 'params' => $data));
                        
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shippingTemplatesCreated++;
                            $this->model_jgetsy_cron->updateShippingTemplates($shippingTemplate['id_etsy_shipping_profiles'], $result['results']);
                        }
                    }
                }
            }
            $logEntry = 'Create shipping templates on Etsy ended. <br>Total templates created: ' . $shippingTemplatesCreated;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function renewShippingTemplateRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');

            $this->syncMethod = 'UpdateTemplate';

            $logEntry = 'Update shipping templates on Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $shippingTemplatesRenewed = 0;
            $getShippingTemplates = $this->model_jgetsy_cron->getShippingTemplatesToRenew();

            if ($getShippingTemplates) {
                $data = array();
                foreach ($getShippingTemplates as $shippingTemplate) {
                    $data = array(
                        'shipping_profile_id' => $shippingTemplate['shipping_profile_id'],
                        'title' => $shippingTemplate['shipping_profile_title'],
                        'origin_country_id' => $shippingTemplate['shipping_origin_country_id'],
                        'min_processing_days' => $shippingTemplate['shipping_min_process_days'],
                        'max_processing_days' => $shippingTemplate['shipping_max_process_days'],
                        'origin_postal_code' => '12919',
                        'max_delivery_time'=>$shippingTemplate['shipping_max_process_days'],
                        'min_delivery_time' =>$shippingTemplate['shipping_min_process_days']
                    );
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest('updateShippingTemplate', array('params' => $data, 'data' => $data));
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shippingTemplatesRenewed++;
                            $this->model_jgetsy_cron->updateShippingTemplates($shippingTemplate['id_etsy_shipping_profiles'], $result['results']);
                        }
                    }
                }
            }

            $logEntry = 'Update shipping templates on Etsy ended. <br>Total Shipping Templates Updated: ' . $shippingTemplatesRenewed;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function deleteShippingTemplateRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');

            $this->syncMethod = 'DeleteTemplate';

            $logEntry = 'Delete shipping templates from Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $shippingTemplatesDeleted = 0;
            $getShippingTemplates = $this->model_jgetsy_cron->getShippingTemplatesToDelete();
            if (!empty($getShippingTemplates)) {
                $data = array();
                foreach ($getShippingTemplates as $shippingTemplate) {
                    $data = array(
                        'shipping_profile_id' => $shippingTemplate['shipping_profile_id'],
                    );
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest('deleteShippingTemplate', array('params' => $data));
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shippingTemplatesDeleted++;
                            $this->model_jgetsy_cron->deleteShippingTemplate($shippingTemplate['id_etsy_shipping_profiles'], $shippingTemplate['shipping_profile_id']);
                        }
                    }
                }
            }

            $logEntry = 'Delete shipping templates from Etsy ended. <br>Total Shipping Templates Deleted: ' . $shippingTemplatesDeleted;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function createShippingTemplateEntryRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');

            $this->syncMethod = 'CreateTemplateEntry';

            $logEntry = 'Create shipping templates entries on Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $shippingTemplatesEntriesCreated = 0;
            $getShippingTemplates = $this->model_jgetsy_cron->getAllShippingTemplates();
            if (!empty($getShippingTemplates)) {
                foreach ($getShippingTemplates as $shippingTemplate) {
                    $getShippingTemplateEntries = $this->model_jgetsy_cron->getShippingTemplateEntriesToAdd($shippingTemplate['id_etsy_shipping_profiles']);
                    if (!empty($getShippingTemplateEntries)) {
                        foreach ($getShippingTemplateEntries as $shippingTemplateEntry) {
                            $data = array();
                            $data['data'] = array(
                                'shipping_profile_id' => $shippingTemplate['shipping_profile_id'],
                                'primary_cost' => (float) $shippingTemplateEntry['shipping_entry_primary_cost'],
                                'secondary_cost' => (float) $shippingTemplateEntry['shipping_entry_secondary_cost']
                            );

                            if ($shippingTemplateEntry['shipping_entry_destination_region_id'] != null && $shippingTemplateEntry['shipping_entry_destination_region_id'] != '0') {
                                $data['data']['destination_region_id'] = $shippingTemplateEntry['shipping_entry_destination_region_id'];
                            } else {
                                $data['data']['destination_country_id'] = $shippingTemplateEntry['shipping_entry_destination_country_id'];
                            }
                            if ($data) {
                                $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                                $result = $etsyMain->sendRequest('createShippingTemplateEntry', $data);
                                if (isset($result['error'])) {
                                    $this->syncError = true;
                                    $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                                } elseif (isset($result['results'])) {
                                    $shippingTemplatesEntriesCreated++;
                                    $this->model_jgetsy_cron->updateShippingTemplateEntryStatus($result['results'][0]['shipping_profile_entry_id'], $shippingTemplateEntry['id_etsy_shipping_profiles_entries']);
                                }
                            }
                        }
                    }
                }
            }
            $logEntry = 'Create shipping templates entries on Etsy ended. <br>Total Shipping Templates Entries Created: ' . $shippingTemplatesEntriesCreated;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function updateShippingTemplateEntryRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');

            $this->syncMethod = 'UpdateTemplateEntry';

            $logEntry = 'Update shipping templates entries on Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
            $shippingTemplatesEntriesRenewed = 0;
            $getShippingTemplateEntries = $this->model_jgetsy_cron->getShippingTemplateEntriesToUpdate();
            if (!empty($getShippingTemplateEntries)) {
                foreach ($getShippingTemplateEntries as $shippingTemplateEntry) {
                    $data = array();
                    $data['data'] = array(
                        'shipping_profile_entry_id' => $shippingTemplateEntry['shipping_profile_entry_id'],
                        'primary_cost' => (float) $shippingTemplateEntry['shipping_entry_primary_cost'],
                        'secondary_cost' => (float) $shippingTemplateEntry['shipping_entry_secondary_cost']
                    );
                    $data['params'] = array(
                        'shipping_profile_entry_id' => $shippingTemplateEntry['shipping_profile_entry_id'],
                        'primary_cost' => (float) $shippingTemplateEntry['shipping_entry_primary_cost'],
                        'secondary_cost' => (float) $shippingTemplateEntry['shipping_entry_secondary_cost']
                    );

                    if ($shippingTemplateEntry['shipping_entry_destination_region_id'] != null && $shippingTemplateEntry['shipping_entry_destination_region_id'] != '0') {
                        $data['data']['destination_region_id'] = $shippingTemplateEntry['shipping_entry_destination_region_id'];
                    } else {
                        $data['data']['destination_country_id'] = $shippingTemplateEntry['shipping_entry_destination_country_id'];
                    }
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest("updateShippingTemplateEntry", $data);
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shippingTemplatesEntriesRenewed++;
                            $this->model_jgetsy_cron->updateShippingTemplateEntryStatus($result['results'][0]['shipping_profile_entry_id'], $shippingTemplateEntry['id_etsy_shipping_profiles_entries']);
                        }
                    }
                }
            }
            $logEntry = 'Update shipping templates entries on Etsy ended. <br>Total Shipping Templates Entries Renewal: ' . $shippingTemplatesEntriesRenewed;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function deleteShippingTemplateEntryRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');

            $this->syncMethod = 'DeleteTemplateEntry';

            //Audit Log Entry
            $logEntry = 'Delete shipping templates entries on Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $getShippingTemplateEntries = $this->model_jgetsy_cron->getShippingTemplateEntriesToDelete();
            $shippingTemplatesEntriesDeleted = 0;
            if (!empty($getShippingTemplateEntries)) {
                foreach ($getShippingTemplateEntries as $shippingTemplateEntry) {
                    $data = array(
                        'shipping_profile_entry_id' => $shippingTemplateEntry['shipping_profile_entry_id']
                    );
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest("deleteShippingTemplateEntry", array('params' => $data));
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shippingTemplatesEntriesDeleted++;
                            $this->model_jgetsy_cron->deleteShippingTemplateEntry($shippingTemplateEntry['id_etsy_shipping_profiles_entries']);
                        }
                    }
                }
            }
            $logEntry = 'Delete shipping templates entries on Etsy ended. <br>Total Shipping Templates Entries Deleted: ' . $shippingTemplatesEntriesDeleted;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

}
