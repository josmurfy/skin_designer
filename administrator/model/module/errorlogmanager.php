<?php 
class ModelModuleErrorlogManager extends Model {
    public function install() {
        $res = $this->db->query("SHOW TABLES LIKE '%errorlog_manager%'");
        if (!$res->num_rows) {
            $this->db->query("CREATE TABLE " . DB_PREFIX . "errorlog_manager (
                `id` INT NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `filename` CHAR(32) NOT NULL,
                `row_hash` VARCHAR(32) NOT NULL,
                `message_hash` CHAR(32) NOT NULL,
                `message` TEXT NOT NULL,
                `timestamp` INT,
                INDEX `filename` (`filename`),
                INDEX `message_hash` (`message_hash`),
                INDEX `timestamp` (`timestamp`)
            )");
        }
    }

    public function uninstall() {
        $res = $this->db->query("SHOW TABLES LIKE '%errorlog_manager%'");
        if ($res->num_rows) {
            $this->db->query("DROP TABLE " . DB_PREFIX . "errorlog_manager");
        }
    }

    public function truncate($file) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "errorlog_manager WHERE `filename`='" . $this->db->escape(md5($file)) . "'");
    }

    public function get_last_error($file) {
        $db_last_error = $this->db->query("SELECT * FROM " . DB_PREFIX . "errorlog_manager WHERE filename='".$this->db->escape(md5($file))."' ORDER BY id DESC LIMIT 1");
        return $db_last_error->row;
    }

    public function get_pages_count($filters) {
        $limit = 10;
        $messages_count = $this->get_messages_count($filters);

        return ceil($messages_count / $limit);
    }

    public function get_messages_count($filters) {
        $table = DB_PREFIX . "errorlog_manager";

        if (!empty($filters['from']) || !empty($filters['to'])) {
            $conditions = array();

            if (!empty($filters['from']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['from'])) {
                $conditions[] = "timestamp > '" . strtotime($filters['from']) . "'";
            }

            if (!empty($filters['to']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['to'])) {
                $conditions[] = "timestamp < '" . strtotime($filters['to']) . "'";
            }

            $table = "(SELECT * FROM " . DB_PREFIX . "errorlog_manager WHERE " . implode(' AND ', $conditions) . ") AS tmp";
        }
        $conditions = array();

        if (!empty($filters['file'])) {
            $conditions[] = "filename = '" . $this->db->escape(md5($filters['file'])) . "'";
        }

        if (!empty($filters['extension'])) {
            $conditions[] = "message LIKE '%" . $filters['extension'] . "%'";
        }

        if (!empty($filters['search'])) {
            $conditions[] = "message LIKE '%" . $filters['search'] . "%'";
        }

        $result = $this->db->query("SELECT COUNT(*) AS rows_total FROM (SELECT * FROM $table WHERE " . implode(' AND ', $conditions) . " GROUP BY message_hash) as tmp_2");

        return (int)$result->row['rows_total'];
    }

    public function get_errors($filters = array(), $page = 1) {
        $limit = 10;
        $start = ($page-1) * $limit;
        $table = DB_PREFIX . "errorlog_manager";

        if (!empty($filters['from']) || !empty($filters['to'])) {
            $conditions = array();

            if (!empty($filters['from']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['from'])) {
                $conditions[] = "timestamp > '" . strtotime($filters['from']) . "'";
            }

            if (!empty($filters['to']) && preg_match('/\d{4}-\d{2}-\d{2}/', $filters['to'])) {
                $conditions[] = "timestamp < '" . strtotime($filters['to']) . "'";
            }

            $table = "(SELECT * FROM " . DB_PREFIX . "errorlog_manager WHERE " . implode(' AND ', $conditions) . ") AS tmp";

        }
        $conditions = array();

        if (!empty($filters['file'])) {
            $conditions[] = "filename = '" . $this->db->escape(md5($filters['file'])) . "'";
        }

        if (!empty($filters['extension'])) {
            $conditions[] = "message LIKE '%" . $filters['extension'] . "%'";
        }

        if (!empty($filters['search'])) {
            $conditions[] = "message LIKE '%" . $filters['search'] . "%'";
        }

        $order = !empty($filters['sort_order']) ? $filters['sort_order'] : 'popularity desc';

        $result = $this->db->query("SELECT *, COUNT(*) AS popularity, MIN(timestamp) AS first_appeared, MAX(timestamp) AS last_appeared FROM $table WHERE " . implode(' AND ', $conditions) . " GROUP BY message_hash ORDER BY $order LIMIT $start, $limit");

        return $result->rows;
    }

    public function get_first_timestamp($file, $hash) {
        $result = $this->db->query("SELECT MIN(timestamp) as timestamp FROM " . DB_PREFIX . "errorlog_manager WHERE filename='".$this->db->escape(md5($file))."' AND message_hash='$hash' LIMIT 1");
        return $result->row['timestamp'];
    }

    public function get_error_message($file, $hash) {
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "errorlog_manager WHERE filename='".$this->db->escape(md5($file))."' AND message_hash='$hash' LIMIT 1");
        return $result->row['message'];
    }

    public function clear_error($filename, $message_hash) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "errorlog_manager WHERE `filename`='" . $this->db->escape(md5($filename)) . "' AND `message_hash`='" . $this->db->escape($message_hash) . "'");
    }
}
