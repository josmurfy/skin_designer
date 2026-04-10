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
                `resolution`  TEXT          DEFAULT NULL,
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

        // v3.0.0: resolution
        $q = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE 'resolution'");
        if (!$q->num_rows) {
            $this->db->query("ALTER TABLE `" . $table . "` ADD COLUMN `resolution` TEXT DEFAULT NULL AFTER `comment`");
        }

        // v3.0.0: tags table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "debug_logger_tags` (
                `tag_id`    INT(11)      NOT NULL AUTO_INCREMENT,
                `report_id` INT(11)      NOT NULL,
                `tag_name`  VARCHAR(100) NOT NULL,
                PRIMARY KEY (`tag_id`),
                KEY `idx_report` (`report_id`),
                KEY `idx_tag_name` (`tag_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Deduplicate events — keep only the latest row per code
        foreach (['debug_logger_admin', 'debug_logger_catalog', 'debug_logger_menu'] as $ecode) {
            $rows = $this->db->query("SELECT `event_id` FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($ecode) . "' ORDER BY `event_id` ASC");
            if ($rows->num_rows > 1) {
                $ids = array_column($rows->rows, 'event_id');
                array_pop($ids); // keep the last one
                foreach ($ids as $eid) {
                    $this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `event_id` = '" . (int)$eid . "'");
                }
            }
        }

        // v3.2.0: auto-register column_left menu event for existing installs
        $menu_check = $this->db->query("SELECT `event_id` FROM `" . DB_PREFIX . "event` WHERE `code` = 'debug_logger_menu' LIMIT 1");
        if (!$menu_check->num_rows) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "event` SET
                `code`        = 'debug_logger_menu',
                `description` = 'Debug Logger Admin Menu',
                `trigger`     = 'admin/view/common/column_left/before',
                `action`      = 'extension/debug_logger/event/header.columnLeft',
                `status`      = '1',
                `sort_order`  = '0'
            ");
        }
    }

    public function dropTable(): void {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "debug_logger_tags`");
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

    public function updateComment(int $id, string $comment): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "debug_report` SET `comment` = '" . $this->db->escape(substr($comment, 0, 4096)) . "' WHERE `id` = " . (int)$id);
    }

    public function updateResolution(int $id, string $resolution): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "debug_report` SET `resolution` = '" . $this->db->escape(substr($resolution, 0, 4096)) . "' WHERE `id` = " . (int)$id);
    }

    public function updateSeverity(int $id, string $severity): void {
        if (!in_array($severity, ['bug', 'warning', 'info'])) {
            $severity = 'bug';
        }
        $this->db->query("UPDATE `" . DB_PREFIX . "debug_report` SET `severity` = '" . $this->db->escape($severity) . "' WHERE `id` = " . (int)$id);
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
        $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_logger_tags` WHERE `report_id` = " . (int)$id);
        $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_report` WHERE `id` = " . (int)$id);
    }

    public function clearAllReports(): void {
        $this->db->query("TRUNCATE `" . DB_PREFIX . "debug_logger_tags`");
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

    public function getCountBySource(): array {
        $result = $this->db->query("
            SELECT `source`, COUNT(*) AS `cnt`
            FROM `" . DB_PREFIX . "debug_report`
            GROUP BY `source`
        ");
        return $result ? $result->rows : [];
    }

    public function getReportsByHour(): array {
        $result = $this->db->query("
            SELECT HOUR(`date_added`) AS `hour`, COUNT(*) AS `cnt`
            FROM `" . DB_PREFIX . "debug_report`
            WHERE `date_added` >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY `hour`
            ORDER BY `hour`
        ");
        return $result ? $result->rows : [];
    }

    public function getAvgResolutionTime(): ?float {
        $result = $this->db->query("
            SELECT AVG(TIMESTAMPDIFF(HOUR, `date_added`, NOW())) AS `avg_hours`
            FROM `" . DB_PREFIX . "debug_report`
            WHERE `status` = 1
            AND `date_added` >= DATE_SUB(NOW(), INTERVAL 90 DAY)
        ");
        return $result && $result->row['avg_hours'] !== null ? round((float)$result->row['avg_hours'], 1) : null;
    }

    public function getGroupedReports(int $limit = 10): array {
        $result = $this->db->query("
            SELECT `url`, `severity`,
                   COUNT(*) AS `cnt`,
                   MAX(`date_added`) AS `last_seen`,
                   MIN(`date_added`) AS `first_seen`,
                   GROUP_CONCAT(DISTINCT `id` ORDER BY `id` DESC SEPARATOR ',') AS `report_ids`
            FROM `" . DB_PREFIX . "debug_report`
            GROUP BY `url`, `severity`
            HAVING `cnt` > 1
            ORDER BY `cnt` DESC
            LIMIT " . (int)$limit . "
        ");
        return $result ? $result->rows : [];
    }

    public function getRecentActivity(int $limit = 10): array {
        $result = $this->db->query("
            SELECT `id`, `severity`, `source`, `url`, `comment`, `status`, `date_added`, `admin_user`
            FROM `" . DB_PREFIX . "debug_report`
            ORDER BY `id` DESC
            LIMIT " . (int)$limit . "
        ");
        return $result ? $result->rows : [];
    }

    // --- Tags ---

    public function addTag(int $report_id, string $tag_name): int {
        $tag_name = trim(substr($tag_name, 0, 100));
        if (!$tag_name || !$report_id) return 0;
        // Prevent duplicate tag on same report
        $exists = $this->db->query("SELECT `tag_id` FROM `" . DB_PREFIX . "debug_logger_tags` WHERE `report_id` = " . (int)$report_id . " AND `tag_name` = '" . $this->db->escape($tag_name) . "'");
        if ($exists->num_rows) return (int)$exists->row['tag_id'];
        $this->db->query("INSERT INTO `" . DB_PREFIX . "debug_logger_tags` SET `report_id` = " . (int)$report_id . ", `tag_name` = '" . $this->db->escape($tag_name) . "'");
        return $this->db->getLastId();
    }

    public function removeTag(int $tag_id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_logger_tags` WHERE `tag_id` = " . (int)$tag_id);
    }

    public function getTagsByReport(int $report_id): array {
        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "debug_logger_tags` WHERE `report_id` = " . (int)$report_id . " ORDER BY `tag_name`");
        return $result ? $result->rows : [];
    }

    public function getTagsByReportIds(array $report_ids): array {
        if (empty($report_ids)) return [];
        $ids = implode(',', array_map('intval', $report_ids));
        $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "debug_logger_tags` WHERE `report_id` IN (" . $ids . ") ORDER BY `tag_name`");
        $grouped = [];
        if ($result) {
            foreach ($result->rows as $row) {
                $grouped[(int)$row['report_id']][] = $row;
            }
        }
        return $grouped;
    }

    public function getAllTagNames(): array {
        $result = $this->db->query("SELECT DISTINCT `tag_name` FROM `" . DB_PREFIX . "debug_logger_tags` ORDER BY `tag_name`");
        return $result ? array_column($result->rows, 'tag_name') : [];
    }

    public function deleteTagsByReport(int $report_id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "debug_logger_tags` WHERE `report_id` = " . (int)$report_id);
    }
}
