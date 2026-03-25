#!/bin/bash

# Définition du chemin du répertoire OpenCart
OPENCART_DIR="/home/n7f9655/public_html/phoenixsupplies/admin"

# Création du fichier modèle (COMPLET)
cat > "$OPENCART_DIR/model/shopmanager/marketplace_product.php" <<EOF
<?php
class ModelShopmanagerMarketplaceProduct extends Model {
    public function addProduct(\$data) {
        \$this->db->query("INSERT INTO " . DB_PREFIX . "marketplace_product SET 
            name = '" . \$this->db->escape(\$data['name']) . "',
            marketplace_id = '" . (int)\$data['marketplace_id'] . "',
            price = '" . (float)\$data['price'] . "',
            quantity = '" . (int)\$data['quantity'] . "',
            date_added = NOW(),
            date_modified = NOW()");
        
        return \$this->db->getLastId();
    }

    public function editProduct(\$product_id, \$data) {
        \$this->db->query("UPDATE " . DB_PREFIX . "marketplace_product SET 
            name = '" . \$this->db->escape(\$data['name']) . "',
            marketplace_id = '" . (int)\$data['marketplace_id'] . "',
            price = '" . (float)\$data['price'] . "',
            quantity = '" . (int)\$data['quantity'] . "',
            date_modified = NOW()
            WHERE product_id = " . (int)\$product_id);
    }

    public function deleteProduct(\$product_id) {
        \$this->db->query("DELETE FROM " . DB_PREFIX . "marketplace_product WHERE product_id = " . (int)\$product_id);
    }

    public function getProduct(\$product_id) {
        \$query = \$this->db->query("SELECT * FROM " . DB_PREFIX . "marketplace_product WHERE product_id = " . (int)\$product_id);
        return \$query->row;
    }

    public function getProducts(\$data = array()) {
        \$sql = "SELECT mp.*, ma.name as marketplace_name FROM " . DB_PREFIX . "marketplace_product mp 
                LEFT JOIN " . DB_PREFIX . "marketplace_account ma ON (mp.marketplace_id = ma.marketplace_id)";

        \$sql .= " WHERE 1";

        if (!empty(\$data['filter_name'])) {
            \$sql .= " AND mp.name LIKE '%" . \$this->db->escape(\$data['filter_name']) . "%'";
        }

        if (isset(\$data['filter_marketplace_id']) && \$data['filter_marketplace_id'] !== '') {
            \$sql .= " AND mp.marketplace_id = " . (int)\$data['filter_marketplace_id'];
        }

        if (isset(\$data['filter_price'])) {
            \$sql .= " AND mp.price = " . (float)\$data['filter_price'];
        }

        if (isset(\$data['filter_quantity'])) {
            \$sql .= " AND mp.quantity = " . (int)\$data['filter_quantity'];
        }

        \$sort_data = array('mp.name', 'mp.price', 'mp.quantity', 'mp.date_added');

        if (isset(\$data['sort']) && in_array(\$data['sort'], \$sort_data)) {
            \$sql .= " ORDER BY " . \$data['sort'];
        } else {
            \$sql .= " ORDER BY mp.date_added";
        }

        if (isset(\$data['order']) && (\$data['order'] == 'DESC' || \$data['order'] == 'ASC')) {
            \$sql .= " " . \$data['order'];
        } else {
            \$sql .= " DESC";
        }

        if (isset(\$data['start']) || isset(\$data['limit'])) {
            if (\$data['start'] < 0) {
                \$data['start'] = 0;
            }

            if (\$data['limit'] < 1) {
                \$data['limit'] = 20;
            }

            \$sql .= " LIMIT " . (int)\$data['start'] . "," . (int)\$data['limit'];
        }

        \$query = \$this->db->query(\$sql);
        return \$query->rows;
    }

    public function getTotalProducts(\$data = array()) {
        \$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "marketplace_product WHERE 1";

        if (!empty(\$data['filter_name'])) {
            \$sql .= " AND name LIKE '%" . \$this->db->escape(\$data['filter_name']) . "%'";
        }

        if (isset(\$data['filter_marketplace_id']) && \$data['filter_marketplace_id'] !== '') {
            \$sql .= " AND marketplace_id = " . (int)\$data['filter_marketplace_id'];
        }

        if (isset(\$data['filter_price'])) {
            \$sql .= " AND price = " . (float)\$data['filter_price'];
        }

        if (isset(\$data['filter_quantity'])) {
            \$sql .= " AND quantity = " . (int)\$data['filter_quantity'];
        }

        \$query = \$this->db->query(\$sql);
        return \$query->row['total'];
    }

    public function getMarketplaceAccounts() {
        \$query = \$this->db->query("SELECT * FROM " . DB_PREFIX . "marketplace_account ORDER BY name ASC");
        return \$query->rows;
    }
}
EOF

echo "Le modèle COMPLET marketplace_product.php a été généré dans $OPENCART_DIR"
