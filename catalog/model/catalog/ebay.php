<?
class ModelCatalogEbay extends Model {

  /**
   * [_getUserAccount user account id of seller]
   * @param  [type] $userid [user account name userid]
   * @return [type]           [array]
   */
   public function getAPI() {
     $user = $this->db->query("SELECT * FROM " . DB_PREFIX . "ebay_accounts");
     return $user->row;
   }
}
?>