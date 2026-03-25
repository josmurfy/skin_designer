<?php

include_once(DIR_SYSTEM . 'library/jgetsy/KbOAuth.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyApi.php');
include_once(DIR_SYSTEM . 'library/jgetsy/RequestValidator.php');
include_once(DIR_SYSTEM . 'library/jgetsy/EtsyMain.php');
include_once(DIR_SYSTEM . 'library/jgetsy/oauth_client.php');
include_once(DIR_SYSTEM . 'library/jgetsy/http.php');

class ControllerJgetsyCron extends Controller {

    public function on_order_history_add($order_id) {
        if (!empty($order_id)) {
            $updateSQL = "UPDATE " . DB_PREFIX . "etsy_orders_list SET is_status_updated = '1' WHERE id_order = '" . (int) $order_id . "'";
            $this->db->query($updateSQL);
        }
    }

}
