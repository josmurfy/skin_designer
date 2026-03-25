-- Initial SQL (module install)
-- Grading companies table used by administrator/model/shopmanager/card/card_grading_company.php

CREATE TABLE IF NOT EXISTS `oc_card_grading_company` (
  `card_grading_company_id` INT(11) NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT(11) NOT NULL DEFAULT 0,
  `date_added` DATETIME NOT NULL,
  PRIMARY KEY (`card_grading_company_id`),
  UNIQUE KEY `code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `oc_card_grading_company` (`code`, `status`, `sort_order`, `date_added`) VALUES
('PSA', 1, 0, NOW()),
('BGS', 1, 1, NOW()),
('BGSX', 1, 2, NOW()),
('SGC', 1, 3, NOW()),
('CSA', 1, 4, NOW()),
('HGA', 1, 5, NOW()),
('GAI', 1, 6, NOW()),
('ACE', 1, 7, NOW()),
('CGC', 1, 8, NOW()),
('KSA', 1, 9, NOW());
