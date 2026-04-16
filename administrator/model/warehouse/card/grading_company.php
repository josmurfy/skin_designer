<?php
// Original: warehouse/card/grading_company.php
namespace Opencart\Admin\Model\Warehouse\Card;

class GradingCompany extends \Opencart\System\Engine\Model {

    private array $defaultCompanies = [
        'PSA', 'BGS', 'BGSX', 'SGC', 'CSA', 'HGA', 'GAI', 'ACE', 'CGC', 'KSA'
    ];

    public function getActiveCodes(): array {
        try {
            $query = $this->db->query("SELECT `code`
                FROM `" . DB_PREFIX . "card_grading_company`
                WHERE `status` = 1
                ORDER BY `sort_order` ASC, `code` ASC");
        } catch (\Throwable $e) {
            return $this->defaultCompanies;
        }

        $codes = [];

        foreach ($query->rows as $row) {
            $code = strtoupper(trim((string)($row['code'] ?? '')));

            if ($code === '') {
                continue;
            }

            $codes[] = $code;
        }

        return !empty($codes) ? $codes : $this->defaultCompanies;
    }
}
