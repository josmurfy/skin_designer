<?php
namespace Opencart\Admin\Model\Extension\DebugLogger\Module;

class DebugLogger extends \Opencart\System\Engine\Model {

    public function createTable(): void {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "debug_report` (
                `id`          INT(11)       NOT NULL AUTO_INCREMENT,
                `url`         TEXT          NOT NULL,
                `console_log` MEDIUMTEXT    DEFAULT NULL,
                `comment`     TEXT          DEFAULT NULL,
                `admin_user`  VARCHAR(255)  DEFAULT '',
                `severity`    VARCHAR(20)   DEFAULT 'bug',
                `source`      VARCHAR(20)   DEFAULT 'admin',
                `status`      TINYINT(1)    DEFAULT 0,
                `date_added`  DATETIME      NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function dropTable(): void {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "debug_report`");
    }

    public function addReport(array $data): int {
        $this->db->query("
            INSERT INTO `" . DB_PREFIX . "debug_report` SET
                `url`         = '" . $this->db->escape(substr((string)($data['url'] ?? ''), 0, 2048)) . "',
                `console_log` = '" . $this->db->escape(substr((string)($data['console_log'] ?? ''), 0, 65535)) . "',
                `comment`     = '" . $this->db->escape(substr((string)($data['comment'] ?? ''), 0, 4096)) . "',
                `admin_user`  = '" . $this->db->escape(substr((string)($data['admin_user'] ?? ''), 0, 255)) . "',
                `severity`    = '" . $this->db->escape($data['severity'] ?? 'bug') . "',
                `source`      = '" . $this->db->escape($data['source'] ?? 'admin') . "',
                `date_added`  = NOW()
        ");
        return $this->db->getLastId();
    }

    public function getReports(int $filter_status = -1, string $filter_source = ''): array {
        $sql = "SELECT * FROM `" . DB_PREFIX . "debug_report`";
        $where = [];
        if ($filter_status >= 0) {
            $where[] = "`status` = " . (int)$filter_status;
        }
        if ($filter_source !== '') {
            $where[] = "`source` = '" . $this->db->escape($filter_source) . "'";
        }
        if ($where) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        $sql .= " ORDER BY `id` DESC LIMIT 500";
        $result = $this->db->query($sql);
        return $result ? $result->rows : [];
    }

    public function getTotalReports(): int {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "debug_report`");
        return $result ? (int)($result->row['total'] ?? 0) : 0;
    }

    public function getTotalOpenReports(): int {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "debug_report` WHERE `status` = 0");
        return $result ? (int)($result->row['total'] ?? 0) : 0;
    }

    public function getTotalBySource(string $source): int {
        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "debug_report` WHERE `source` = '" . $this->db->escape($source) . "'");
        return $result ? (int)($result->row['total'] ?? 0) : 0;
    }

    public function setStatus(int $id, int $status): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "debug_report` SET `status` = " . (int)$status . " WHERE `id` = " . (int)$id);
    }

    public function deleteReport(int $id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_report` WHERE `id` = " . (int)$id);
    }

    public function clearAllReports(): void {
        $this->db->query("TRUNCATE `" . DB_PREFIX . "debug_report`");
    }

    public function pruneOldest(int $max): void {
        $total = $this->getTotalReports();
        if ($total > $max) {
            $delete = $total - $max;
            $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_report` ORDER BY `id` ASC LIMIT " . (int)$delete);
        }
    }
}
