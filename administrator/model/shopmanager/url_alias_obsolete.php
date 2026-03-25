<?php
namespace Opencart\Admin\Model\Shopmanager;

class UrlAlias extends \Opencart\System\Engine\Model {
    public function getUrlAlias(string $keyword): array {
        $language_id = $this->config->get('config_language_id');
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($keyword) . "' AND language_id = '" . (int)$language_id . "' LIMIT 1");
        return $query->row;
    }
}