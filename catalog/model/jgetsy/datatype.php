<?php

class ModelJgetsyDatatype extends Model {

    public function getDataType($type) {
        $data = array();
        $typeSql = "SELECT * FROM " . DB_PREFIX . "etsy_data_types WHERE type = '" . $type . "'";
        $result = $this->db->query($typeSql);
        if ($result->num_rows > 0) {
            foreach ($result->rows as $dataType) {
                $data[$dataType['language']][$dataType['data_key']] = $dataType['data'];
            }
        }
        return $data;
    }

}
