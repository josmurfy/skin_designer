<?php
namespace Opencart\Admin\Model\Extension\DebugLogger\Module;

class DebugLogger extends \Opencart\System\Engine\Model {

    public function createTable(): void {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "debug_report` (
                `id`          INT(11)       NOT NULL AUTO_INCREMENT,
                `url`         TEXT          NOT NULL,
                `console_log` MEDIUMTEXT    DEFAULT NULL,
                `network_log` MEDIUMTEXT    DEFAULT NULL,
                `screenshot`  MEDIUMTEXT    DEFAULT NULL,
                `comment`     TEXT          DEFAULT NULL,
                `admin_user`  VARCHAR(255)  DEFAULT '',
                `assigned_to` INT(11)       DEFAULT NULL,
                `severity`    VARCHAR(20)   DEFAULT 'bug',
                `source`      VARCHAR(20)   DEFAULT 'admin',
                `status`      TINYINT(1)    DEFAULT 0,
                `date_added`  DATETIME      NOT NULL,
                PRIMARY KEY (`id`),
                KEY `idx_assigned` (`assigned_to`),
                KEY `idx_status` (`status`),
                KEY `idx_severity` (`severity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    /**
     * Upgrade from v1.x — add missing columns if they don't exist.
     */
    public function upgrade(): void {
        $table = DB_PREFIX . 'debug_report';

        // screenshot
        $q = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE 'screenshot'");
        if (!$q->num_rows) {
            $this->db->query("ALTER TABLE `" . $table . "` ADD COLUMN `screenshot` MEDIUMTEXT DEFAULT NULL AFTER `network_log`");
        }

        // assigned_to
        $q = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE 'assigned_to'");
        if (!$q->num_rows) {
            $this->db->query("ALTER TABLE `" . $table . "` ADD COLUMN `assigned_to` INT(11) DEFAULT NULL AFTER `admin_user`");
            $this->db->query("ALTER TABLE `" . $table . "` ADD KEY `idx_assigned` (`assigned_to`)");
        }
    }

    public function dropTable(): void {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "debug_report`");
    }

    public function addReport(array $data): int {
        $this->db->query("
            INSERT INTO `" . DB_PREFIX . "debug_report` SET
                `url`         = '" . $this->db->escape(substr((string)($data['url'] ?? ''), 0, 2048)) . "',
                `console_log` = '" . $this->db->escape(substr((string)($data['console_log'] ?? ''), 0, 65535)) . "',
                `network_log` = '" . $this->db->escape(substr((string)($data['network_log'] ?? ''), 0, 65535)) . "',
                `screenshot`  = '" . $this->db->escape((string)($data['screenshot'] ?? '')) . "',
                `comment`     = '" . $this->db->escape(substr((string)($data['comment'] ?? ''), 0, 4096)) . "',
                `admin_user`  = '" . $this->db->escape(substr((string)($data['admin_user'] ?? ''), 0, 255)) . "',
                `severity`    = '" . $this->db->escape($data['severity'] ?? 'bug') . "',
                `source`      = '" . $this->db->escape($data['source'] ?? 'admin') . "',
                `date_added`  = NOW()
        ");
        return $this->db->getLastId();
    }

    public function assignReport(int $id, int $user_id): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "debug_report` SET `assigned_to` = " . ($user_id ?: 'NULL') . " WHERE `id` = " . (int)$id);
    }

    public function getAdminUsers(): array {
        $result = $this->db->query("SELECT `user_id`, `firstname`, `lastname`, `username` FROM `" . DB_PREFIX . "user` WHERE `status` = 1 ORDER BY `firstname`");
        return $result ? $result->rows : [];
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

    public function getReportsByDay(int $days = 7): array {
        $result = $this->db->query("
            SELECT DATE(`date_added`) AS `day`, `severity`, COUNT(*) AS `cnt`
            FROM `" . DB_PREFIX . "debug_report`
            WHERE `date_added` >= DATE_SUB(NOW(), INTERVAL " . (int)$days . " DAY)
            GROUP BY `day`, `severity`
            ORDER BY `day`
        ");
        return $result ? $result->rows : [];
    }

    public function getTopPages(int $limit = 5): array {
        $result = $this->db->query("
            SELECT `url`, COUNT(*) AS `cnt`
            FROM `" . DB_PREFIX . "debug_report`
            GROUP BY `url`
            ORDER BY `cnt` DESC
            LIMIT " . (int)$limit . "
        ");
        return $result ? $result->rows : [];
    }

    public function getCountBySeverity(): array {
        $result = $this->db->query("
            SELECT `severity`, COUNT(*) AS `cnt`
            FROM `" . DB_PREFIX . "debug_report`
            GROUP BY `severity`
        ");
        return $result ? $result->rows : [];
    }
}
