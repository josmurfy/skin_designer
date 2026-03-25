<?php

include_once(DIR_SYSTEM . 'library/jgetsy/KbOAuth.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyApi.php');
include_once(DIR_SYSTEM . 'library/jgetsy/RequestValidator.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyMain.php');
include_once(DIR_SYSTEM . 'library/jgetsy/oauth_client.php');
include_once(DIR_SYSTEM . 'library/jgetsy/http.php');

class ControllerJgetsyShopSection extends Controller {

    private $syncType = 'SyncShopSection';
    private $syncMethod = '';
    private $syncError = false;

    public function index() {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', -1);
        @set_time_limit(0);
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');
            $this->load->model('jgetsy/shop_section');

            $this->model_jgetsy_cron->auditLogEntry("Shop section sync started", $this->syncType);

            $settings = $this->config->get('etsy_general_settings');
            if (isset($settings['enable']) && $settings['enable'] == 1) {
                $etsy_access_token = $this->config->get('etsy_access_token');
                $etsy_access_token_secret = $this->config->get('etsy_access_token_secret');
                if ($etsy_access_token && $etsy_access_token_secret) {
                    $this->getShopSectionFromEtsy();
                    $this->createShopSectionRequest();
                    $this->renewShopSectionRequest();
                    $this->deleteShopSectionRequest();
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
            $this->model_jgetsy_cron->auditLogEntry("Shop Section sync completed", $this->syncType);
        } else {
            echo "Sorry!!! You are not allowed to perform this action the demo version.";
        }
        die();
    }

    /* Being used in Admin Add/Update Shop Section */

    public function getEtsyShop() {
        $response = array();
        $this->load->model('jgetsy/cron');
        $this->load->model('jgetsy/shop_section');
        $shopSectionData = array();

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        /* Get Esty User ID To Pass to All User Shops */
        $result = $etsyMain->sendRequest('usersDetail', array());
        $shops = $etsyMain->sendRequest('findAllUserShops', array("params" => array("user_id" => $result['results'][0]['user_id'])));
        if (isset($shops['error'])) {
            $response = array("type" => "error", "message" => $shops['error']);
        } else {
            $response = array("type" => "success", "data" => $shops['results']);
        }
        echo json_encode($response);
        die();
    }

    private function getShopSectionFromEtsy() {
        $this->syncMethod = 'SyncShopSectionEtsyToOC';
        $logEntry = 'Sync shop section from Etsy to OC started';
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        $shopSectionData = array();

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();

        /* Get Esty User ID To Pass to All User Shops */
        $result = $etsyMain->sendRequest('usersDetail', array());
        $shops = $etsyMain->sendRequest('findAllUserShops', array("params" => array("user_id" => $result['results'][0]['user_id'])));

        if (isset($shops['error'])) {
            $this->syncError = true;
            $this->model_jgetsy_cron->auditLogEntry($shops['error'], $this->syncMethod);
            return false;
            /* Mandatory to return false to avoid wrong Shop Section deletion. If not returned, etsy shop data will be empty & system will consider that there is no shop section & sytem will delete the shop section from local (Botton code of this funcation to delete the shop section */
        } elseif (isset($result['results'])) {
            foreach ($shops['results'] as $shop) {
                $shop_id = $shop['shop_id'];
                $shopSectionResult = $etsyMain->sendRequest('findAllShopSections', array("params" => array('shop_id' => $shop_id, 'limit' => 100)));
                if (isset($shopSectionResult['error'])) {
                    $this->syncError = true;
                    $this->model_jgetsy_cron->auditLogEntry($shopSectionResult['error'], $this->syncMethod);
                    return false;
                    /* Mandatory to return false to avoid wrong Shop Section deletion. If not returned, etsy shop data will be empty & system will consider that there is no shop section & sytem will delete the shop section from local (Botton code of this funcation to delete the shop section */
                } else {
                    foreach ($shopSectionResult['results'] as $shop_section) {
                        $shopSectionData[] = array(
                            'etsy_shop_section_id' => $shop_section['shop_section_id'],
                            'title' => $shop_section['title'],
                            'shop_id' => $shop_id
                        );
                    }
                }
            }
        }

        if (!empty($shopSectionData)) {
            foreach ($shopSectionData as $shopSection) {

                /* If shop section exist then update the shop section else create the new shop section */
                $section_exist = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE etsy_shop_section_id = " . $shopSection['etsy_shop_section_id']);
                if ($section_exist->num_rows > 0) {
                    $this->db->query("UPDATE " . DB_PREFIX . "etsy_shop_sections SET "
                            . "shop_id = '" . $shopSection['shop_id'] . "',"
                            . "title = '" . $this->db->escape($shopSection['title']) . "' "
                            . "WHERE etsy_shop_section_id = '" . $shopSection['etsy_shop_section_id'] . "'");
                } else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shop_sections SET "
                            . "shop_id = " . $shopSection['shop_id'] . ","
                            . "title = '" . $this->db->escape($shopSection['title']) . "',"
                            . "etsy_shop_section_id = '" . $shopSection['etsy_shop_section_id'] . "'");
                }
            }
        }

        /* Delete Shop Section from local which has been deleted from the Etsy */
        $shop_sections_data = $this->model_jgetsy_shop_section->getAllShopSection();

        if ($shop_sections_data) {
            foreach ($shop_sections_data as $local_shop_section) {
                /* Delete only when shop section is not blank. In case shipping_profile_id is blank that means tempalte has been added into local & sync to etsy is pending for the template */
                if ($local_shop_section['etsy_shop_section_id'] != "") {
                    $delete_flag = true;
                    if (!empty($shopSectionData)) {
                        foreach ($shopSectionData as $shopSection) {
                            if ($shopSection['etsy_shop_section_id'] == $local_shop_section['etsy_shop_section_id']) {
                                /* Shop Section exist on the etsy so not to be delete from the local */
                                $delete_flag = false;
                                break;
                            }
                        }
                    } else {
                        /* Shop Section doesn't exist on the Etsy (As there is no shop returned from the etsy */
                        $delete_flag = true;
                    }
                    if ($delete_flag == true) {
                        $this->model_jgetsy_shop_section->deleteShopSection($local_shop_section['shop_section_id']);
                        $logEntry = "Shop Section: " . $local_shop_section['etsy_shop_section_id'] . " (" . $local_shop_section['title'] . ") has been deleted from the Etsy. Removed the same from local";
                        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
                    }
                }
            }
        }

        $logEntry = 'Shop Section Sync ended.';
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        return true;
    }

    public function createShopSectionRequest() {
        $this->load->model('jgetsy/cron');
        $this->load->model('jgetsy/shop_section');

        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->syncMethod = 'CreateShopSection';
            $logEntry = 'Create shop section on Etsy started';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $shopSections = $this->model_jgetsy_shop_section->getShopSectionToAdd();
            $shopSectionCreated = 0;
            if ($shopSections) {

                $logEntry = "Found " . count($shopSections) . " to add";
                $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

                $data = array();
                foreach ($shopSections as $shopSection) {
                    $data = array(
                        'title' => (string) $shopSection['title'],
                        'shop_id' => $shopSection['shop_id']
                    );
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest('createShopSection', array('data' => $data, 'params' => $data));
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shopSectionCreated++;
                            $this->model_jgetsy_shop_section->updateShopSection($shopSection['shop_section_id'], $result['results'][0]['shop_section_id']);
                        }
                    }
                }
            }
            $logEntry = 'Create shop section on Etsy ended. <br>Total shop section created: ' . $shopSectionCreated;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function renewShopSectionRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');
            $this->load->model('jgetsy/shop_section');

            $this->syncMethod = 'UpdateSection';

            $logEntry = 'Update shop section on Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $shopSectionRenewed = 0;
            $shopSections = $this->model_jgetsy_shop_section->getShopSectionToRenew();

            if ($shopSections) {
                $data = array();
                foreach ($shopSections as $shopSection) {
                    $data = array(
                        'title' => (string) $shopSection['title'],
                        'shop_id' => $shopSection['shop_id'],
                        'shop_section_id' => $shopSection['etsy_shop_section_id'],
                    );
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest('updateShopSection', array('params' => $data, 'data' => $data));
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shopSectionRenewed++;
                            $this->model_jgetsy_shop_section->updateShopSection($shopSection['shop_section_id'], $result['results'][0]['shop_section_id']);
                        }
                    }
                }
            }

            $logEntry = 'Update shop section on Etsy completed. <br>Total shop section updated: ' . $shopSectionRenewed;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

    public function deleteShopSectionRequest() {
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');
            $this->load->model('jgetsy/shop_section');

            $this->syncMethod = 'DeleteShopSection';

            $logEntry = 'Delete shop section from Etsy started.';
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

            $shopSectionDeleted = 0;
            $shopSections = $this->model_jgetsy_shop_section->getShopSectionToDelete();
            if ($shopSections) {
                $data = array();
                foreach ($shopSections as $shopSection) {
                    $data = array(
                        'shop_id' => $shopSection['shop_id'],
                        'shop_section_id' => $shopSection['etsy_shop_section_id']
                    );
                    if ($data) {
                        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
                        $result = $etsyMain->sendRequest('deleteShopSection', array('params' => $data));
                        if (isset($result['error'])) {
                            $this->syncError = true;
                            $this->model_jgetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                        } elseif (isset($result['results'])) {
                            $shopSectionDeleted++;
                            $this->model_jgetsy_shop_section->deleteShopSection($shopSection['shop_section_id']);
                        }
                    }
                }
            }

            $logEntry = 'Delete shop section templates from Etsy completed. <br>Total shop section deleted: ' . $shopSectionDeleted;
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        return true;
    }

}
