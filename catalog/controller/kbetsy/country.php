<?php

include_once(DIR_SYSTEM . 'library/jgetsy/KbOAuth.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyApi.php');
include_once(DIR_SYSTEM . 'library/jgetsy/RequestValidator.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyMain.php');
include_once(DIR_SYSTEM . 'library/jgetsy/oauth_client.php');
include_once(DIR_SYSTEM . 'library/jgetsy/http.php');

class ControllerJgetsyCountry extends Controller {

    private $syncType = 'SyncCountryRegion';
    private $syncMethod = '';
    private $syncError = false;

    public function index() {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', -1);
        @set_time_limit(0);
        if ($this->config->get('jgetsy_demo_flag') == 0) {
            $this->load->model('jgetsy/cron');
            $this->model_jgetsy_cron->auditLogEntry("Syncing Country/Region Started", $this->syncType);
            $this->syncCountries();
            $this->syncRegion();
            $this->model_jgetsy_cron->auditLogEntry("Syncing Country/Region Ended", $this->syncType);
            if ($this->syncError == true) {
                echo "Success with some error(s). Refer to audit log for the details of the error.";
            } else {
                echo "Success";
            }
        } else {
            echo "Sorry!!! You are not allowed to perform this action the demo version.";
        }
    }

    //Function definition to sync countries and regions
    private function syncCountries() {
        $this->load->model('jgetsy/cron');
        $this->syncMethod = 'SyncEtsyCountiresToOC';

        $logEntry = "Countries Sync Started";
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
        $result = $etsyMain->sendRequest("findAllCountry");
        if (isset($result['error'])) {
            $this->syncError = true;
            $this->auditLogEntry($result['error'], $this->syncMethod);
        } elseif (isset($result['results'])) {

            $this->model_jgetsy_cron->insertCountries($result['results']);

            $logEntry = "Inserted " . count($result['results']) . " country successfully";
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
        $logEntry = "Countries Sync Completed";
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
    }

    private function syncRegion() {
        $this->load->model('jgetsy/cron');
        $this->syncMethod = 'SyncEtsyRegionToOC';

        $logEntry = "Region Sync Started";
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        $etsyMain = $this->model_jgetsy_cron->createEtsyObject();
        $result = $etsyMain->sendRequest("findAllRegion");

        if (isset($result['error'])) {
            $this->syncError = true;
            $this->auditLogEntry($result['error'], $this->syncMethod);
        } elseif (isset($result['results'])) {
            $this->model_jgetsy_cron->insertRegions($result['results']);

            $logEntry = "Inserted " . count($result['results']) . " Region successfully";
            $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
        }
    }

    public function syncOc() {
        $this->load->model('jgetsy/cron');
        $this->syncMethod = 'SyncEtsyDataType';

        $logEntry = "Data Type Sync Started";
        $this->model_jgetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        //$etsyMain = $this->model_jgetsy_cron->createEtsyObject();
        //$result = $etsyMain->sendRequest("describeWhoMadeEnum");
        //print_r($result);
        //die();
    }

}
