<?php
namespace Opencart\Catalog\Model\Extension\DebugLogger\Module;

class DebugLogger extends \Opencart\System\Engine\Model {

    public function addReport(array $data): int {
        $this->db->query("
            INSERT INTO `" . DB_PREFIX . "debug_report` SET
                `url`         = '" . $this->db->escape(substr((string)($data['url'] ?? ''), 0, 2048)) . "',
                `console_log` = '" . $this->db->escape(substr((string)($data['console_log'] ?? ''), 0, 65535)) . "',
                `comment`     = '" . $this->db->escape(substr((string)($data['comment'] ?? ''), 0, 4096)) . "',
                `admin_user`  = '',
                `severity`    = '" . $this->db->escape($data['severity'] ?? 'bug') . "',
                `source`      = 'catalog',
                `date_added`  = NOW()
        ");
        return $this->db->getLastId();
    }

    public function getTotalReports(): int {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "debug_report`");
        return $result ? (int)($result->row['total'] ?? 0) : 0;
    }

    public function pruneOldest(int $max): void {
        $total = $this->getTotalReports();
        if ($total > $max) {
            $delete = $total - $max;
            $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_report` ORDER BY `id` ASC LIMIT " . (int)$delete);
        }
    }
}
