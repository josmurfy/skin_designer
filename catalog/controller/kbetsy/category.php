<?php

include_once(DIR_SYSTEM . 'library/kbetsy/KbOAuth.php');
include_once(DIR_SYSTEM . 'library/kbetsy/EtsyApi.php');
include_once(DIR_SYSTEM . 'library/kbetsy/RequestValidator.php');
include_once(DIR_SYSTEM . 'library/kbetsy/EtsyMain.php');
include_once(DIR_SYSTEM . 'library/kbetsy/oauth_client.php');
include_once(DIR_SYSTEM . 'library/kbetsy/http.php');

class ControllerKbetsyCategory extends Controller {

    private $syncType = 'SyncCategory';

    public function index() {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', -1);
        @set_time_limit(0);
        if ($this->config->get('kbetsy_demo_flag') == 0) {
            $this->load->model('kbetsy/cron');
            $this->model_kbetsy_cron->auditLogEntry("Category Sync Started", $this->syncType);
            //$this->syncOldCategory(0,0);
            $this->syncCategory(0);
            $this->model_kbetsy_cron->auditLogEntry("Category Sync Completed", $this->syncType);
            echo "Success!!! Please refresh the back page to continue.";
        } else {
            echo "Sorry!!! You are not allowed to perform this action the demo version.";
        }
    }

    private function syncCategory($parent_id, $level = false, $data = array()) {
        $this->load->model('kbetsy/cron');
        $this->load->model('kbetsy/category');
        if ($level == false) {
            /* In case of first call, Pick data from the File */
            $category_data = file_get_contents(DIR_SYSTEM . "library/kbetsy/categroy.json");
            $categoryArray = json_decode($category_data, true);
            $data = $categoryArray["results"];
        } else {
            /* Data is being passed from the recursive call to insert sub category (data array variable).  */
        }
        foreach ($data as $category) {
            $category_inserted_id = $this->model_kbetsy_category->updateCategories($category['id'], $category['path'], $category['name'], $parent_id);
            if ($category_inserted_id) {
                if (!empty($category['children'])) {
                    $this->syncCategory($category_inserted_id, true, $category['children']);
                }
            }
        }
    }

    /* If need to update the taxtonomy, Delete the category table data & run this function from URL (Make function public). This will update the category.josn file & then run the syncCategory function to update the taxonomy in the table. */

    private function syncFileUpdate() {
        $this->load->model('kbetsy/cron');
        $this->load->model('kbetsy/category');
        $result = $etsyMain->sendRequest("getSellerTaxonomy");
        file_put_contents(DIR_SYSTEM . "library/kbetsy/categroy.json", json_encode($result));
        die();
    }

    /* OLD Version of Category Sync */

    private function syncOldCategory($category_id, $parent_id = 0, $level = 0, $tag = "", $subtag = "") {
        $this->load->model('kbetsy/cron');
        $this->load->model('kbetsy/category');

        $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
        if ($level == 2 && $category_id != "") {
            $result = $etsyMain->sendRequest("findAllSubCategoryChildren", array("params" => array("tag" => $tag, "subtag" => $subtag)));
        } else if ($level == 1 && $category_id != "") {
            $result = $etsyMain->sendRequest("findAllTopCategoryChildren", array("params" => array("tag" => $tag)));
        } else if ($level == 0) {
            $result = $etsyMain->sendRequest("getSellerTaxonomy");
        }
        if (isset($result['error'])) {
            $this->model_kbetsy_cron->auditLogEntry($result['error'], $this->syncType);
        } elseif (isset($result['results'])) {
            foreach ($result['results'] as $category) {
                $category_inserted_id = $this->model_kbetsy_category->updateCategories($category['category_id'], $category['name'], $category['short_name'], $parent_id);
                if ($category_inserted_id) {
                    if ($level == 0) {
                        $this->syncCategory($category['category_id'], $category_inserted_id, 1, $category['name']);
                    } else if ($level == 1) {
                        $this->syncCategory($category['category_id'], $category_inserted_id, 2, $tag, $category['name']);
                    }
                }
            }
        }
    }

    public function getTaxonomyNodeProperties() {
        if ($this->config->get('kbetsy_demo_flag') == 0) {
            $this->load->model('kbetsy/cron');
            $this->load->model('kbetsy/category');
            $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
            $data = array(
                'taxonomy_id' => $this->request->get['taxonomy_id']
            );
            $result = $etsyMain->sendRequest("getTaxonomyNodeProperties", array("data" => $data, "params" => $data));
            $resonses = array();
            if (isset($result['results'])) {
                foreach ($result['results'] as $results) {
                    if (!empty($results['scales'])) {
                        $resonses[$results['property_id']]['name'] = $results['name'];
                        $scale_data = array();
                        foreach ($results['scales'] as $scale) {
                            $scale_data[] = array("scale_id" => $scale['scale_id'], "scale" => $scale['display_name'], "name" => $scale['name']);
                        }
                        $resonses[$results['property_id']]['scales'] = $scale_data;
                    }
                }
            }
            echo json_encode(array("scales" => $resonses));
        } else {
            echo json_encode(array("scales" => array()));
        }
        die();
    }

}
