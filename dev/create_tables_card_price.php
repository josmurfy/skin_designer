<?php
require_once __DIR__ . '/../administrator/config.php';
$db = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($db->connect_error) { die('Connect error: ' . $db->connect_error); }

// Table temporaire: raw eBay listings avant matching
$sql_raw = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "card_price_raw` (
  `raw_id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ebay_item_id`   VARCHAR(64)  NOT NULL DEFAULT '',
  `keyword`        VARCHAR(512) NOT NULL DEFAULT '',
  `title`          VARCHAR(512) NOT NULL DEFAULT '',
  `url`            TEXT,
  `picture`        TEXT,
  `price`          DECIMAL(10,2) NOT NULL DEFAULT 0,
  `currency`       VARCHAR(8)   NOT NULL DEFAULT 'USD',
  `condition_type` VARCHAR(64)  NOT NULL DEFAULT '',
  `date_sold`      VARCHAR(64)  NOT NULL DEFAULT '',
  `grade`          VARCHAR(32)  NOT NULL DEFAULT '',
  `grader`         VARCHAR(64)  NOT NULL DEFAULT '',
  `grade_score`    VARCHAR(16)  NOT NULL DEFAULT '',
  `is_graded`      TINYINT(1)   NOT NULL DEFAULT 0,
  `card_raw_id`    INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK oc_card_set',
  `status`         TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '0=pending,1=matched,2=rejected',
  `date_added`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`raw_id`),
  UNIQUE KEY `ebay_item_id` (`ebay_item_id`),
  KEY `card_raw_id` (`card_raw_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$db->query($sql_raw);
if ($db->error) { echo 'ERROR raw: ' . $db->error . PHP_EOL; } else { echo 'oc_card_price_raw OK' . PHP_EOL; }

// Table finale: prix actifs confirmés
$sql_active = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "card_price_active` (
  `active_id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `card_raw_id`    INT UNSIGNED NOT NULL COMMENT 'FK oc_card_set',
  `ebay_item_id`   VARCHAR(64)  NOT NULL DEFAULT '',
  `title`          VARCHAR(512) NOT NULL DEFAULT '',
  `url`            TEXT,
  `picture`        TEXT,
  `price_usd`      DECIMAL(10,2) NOT NULL DEFAULT 0,
  `price_cad`      DECIMAL(10,2) NOT NULL DEFAULT 0,
  `condition_type` VARCHAR(64)  NOT NULL DEFAULT '',
  `date_sold`      VARCHAR(64)  NOT NULL DEFAULT '',
  `grade`          VARCHAR(32)  NOT NULL DEFAULT '',
  `grader`         VARCHAR(64)  NOT NULL DEFAULT '',
  `grade_score`    VARCHAR(16)  NOT NULL DEFAULT '',
  `is_graded`      TINYINT(1)   NOT NULL DEFAULT 0,
  `keyword`        VARCHAR(512) NOT NULL DEFAULT '',
  `status`         TINYINT(1)   NOT NULL DEFAULT 1,
  `date_added`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`active_id`),
  UNIQUE KEY `ebay_item_id` (`ebay_item_id`),
  KEY `card_raw_id` (`card_raw_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$db->query($sql_active);
if ($db->error) { echo 'ERROR active: ' . $db->error . PHP_EOL; } else { echo 'oc_card_price_active OK' . PHP_EOL; }

$db->close();
echo 'Done.' . PHP_EOL;
