<?php
// Original: shopmanager/card/card.php
namespace Opencart\Admin\Model\Shopmanager\Card;

/**
 * Class Card
 *
 * Main card model in card/ subdirectory
 *
 * @package Opencart\Admin\Model\Shopmanager\Card
 */
class Card extends \Opencart\System\Engine\Model {
    /**
     * Add Card
     *
     * @param array<string, mixed> $data
     *
     * @return int
     */
    public function addCard(array $data): int {
        // Handle listing creation/updating
        $listing_id = $this->getOrCreateListingId($data);

        // Generate SKU from card_number and player_name
        $card_number = intval($data['card_number'] ?? 0);
        $player_name = trim($data['name'] ?? '');
        $player_name_clean = str_replace(' ', '_', $player_name);
        $player_name_clean = preg_replace('/[^a-zA-Z0-9_]/', '', $player_name_clean);
        $sku = $card_number . '_' . $player_name_clean;

        $this->db->query("INSERT INTO `" . DB_PREFIX . "card` SET
            `listing_id` = '" . (int)$listing_id . "',
            `sku` = '" . $this->db->escape($sku) . "',
            `title` = '" . $this->db->escape($data['name'] ?? '') . "',
            `card_number` = '" . $this->db->escape($data['card_number'] ?? '') . "',
            `player_name` = '" . $this->db->escape($data['name']) . "',
            `team_name` = '" . $this->db->escape($data['team_name'] ?? '') . "',
            `year` = '" . (int)$data['year'] . "',
            `brand` = '" . $this->db->escape($data['set_name'] ?? '') . "',
            `condition_name` = 'Near Mint or Better',
            `price` = '" . (float)($data['price'] ?? 0) . "',
            `quantity` = '" . (int)($data['quantity'] ?? 1) . "',
            `status` = '" . (int)$data['status'] . "',
            `merge` = " . (isset($data['merge']) ? (int)$data['merge'] : (((float)($data['price'] ?? 0) < 10.00) ? 1 : 0)) . ",
            `date_added` = NOW()");

        $card_id = $this->db->getLastId();

        return $card_id;
    }

    /**
     * Edit Card
     *
     * @param int $card_id
     * @param array<string, mixed> $data
     *
     * @return void
     */
    public function editCard(int $card_id, array $data): void {
        // Handle listing creation/updating
        $listing_id = $this->getOrCreateListingId($data);

        // Generate SKU from card_number and player_name
        $card_number = intval($data['card_number'] ?? 0);
        $player_name = trim($data['name'] ?? '');
        $player_name_clean = str_replace(' ', '_', $player_name);
        $player_name_clean = preg_replace('/[^a-zA-Z0-9_]/', '', $player_name_clean);
        $sku = $card_number . '_' . $player_name_clean;

        $this->db->query("UPDATE `" . DB_PREFIX . "card` SET
            `listing_id` = '" . (int)$listing_id . "',
            `sku` = '" . $this->db->escape($sku) . "',
            `title` = '" . $this->db->escape($data['name'] ?? '') . "',
            `card_number` = '" . $this->db->escape($data['card_number'] ?? '') . "',
            `player_name` = '" . $this->db->escape($data['name']) . "',
            `team_name` = '" . $this->db->escape($data['team_name'] ?? '') . "',
            `year` = '" . (int)$data['year'] . "',
            `brand` = '" . $this->db->escape($data['set_name'] ?? '') . "',
            `condition_name` = 'Near Mint or Better',
            `price` = '" . (float)($data['price'] ?? 0) . "',
            `quantity` = '" . (int)($data['quantity'] ?? 1) . "',
            `status` = '" . (int)$data['status'] . "',
            `merge` = " . (isset($data['merge']) ? (int)$data['merge'] : (((float)($data['price'] ?? 0) < 10.00) ? 1 : 0)) . ",
            `date_modified` = NOW()
            WHERE `card_id` = '" . (int)$card_id . "'");
    }

    /**
     * Get or create listing ID based on set_name
     *
     * @param array<string, mixed> $data
     *
     * @return int
     */
    private function getOrCreateListingId(array $data): int {
        $set_name = trim($data['set_name'] ?? '');

        if (empty($set_name)) {
            // Create a default listing if no set_name provided
            $this->db->query("INSERT INTO `" . DB_PREFIX . "card_listing` SET
                `set_name` = 'Default Set',
                `sport` = 'Basketball',
                `location` = 'Unknown',
                `date_added` = NOW()");
            return $this->db->getLastId();
        }

        // Check if listing already exists
        $query = $this->db->query("SELECT listing_id FROM `" . DB_PREFIX . "card_listing` WHERE set_name = '" . $this->db->escape($set_name) . "' LIMIT 1");

        if ($query->num_rows > 0) {
            return (int)$query->row['listing_id'];
        }

        // Create new listing
        $this->db->query("INSERT INTO `" . DB_PREFIX . "card_listing` SET
            `set_name` = '" . $this->db->escape($set_name) . "',
            `sport` = 'Basketball',
            `location` = 'Unknown',
            `year` = '" . (int)($data['year'] ?? 0) . "',
            `brand` = '" . $this->db->escape($this->extractBrandFromSetName($set_name)) . "',
            `date_added` = NOW()");

        return $this->db->getLastId();
    }

    /**
     * Extract brand from set name (e.g., "1990-91 Fleer" -> "Fleer")
     *
     * @param string $set_name
     *
     * @return string
     */
    private function extractBrandFromSetName(string $set_name): string {
        // Simple extraction - take the last word as brand
        $parts = explode(' ', $set_name);
        return end($parts);
    }

    /**
     * Get Card
     *
     * @param int $card_id
     *
     * @return array<string, mixed>
     */
    public function getCard(int $card_id): array {
        $query = $this->db->query("SELECT c.*, cl.set_name, cl.location, ci.image_url FROM `" . DB_PREFIX . "card` c LEFT JOIN `" . DB_PREFIX . "card_listing` cl ON c.listing_id = cl.listing_id LEFT JOIN `" . DB_PREFIX . "card_image` ci ON ci.card_id = c.card_id WHERE c.card_id = '" . (int)$card_id . "' ORDER BY ci.sort_order ASC LIMIT 1");

        return $query->row ?? [];
    }

    /**
     * Get Cards
     *
     * @param array<string, mixed> $data
     *
     * @return array<int, array<string, mixed>>
     */
    public function getCards(array $data = []): array {
        // Taux USD→CAD via OpenCart (oc_card_price est en USD)
        $usd_to_cad = (float)$this->currency->convert(1, 'USD', 'CAD');
        $raw_expr = "CAST(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(market_data_json, '{}'), '$.prices.raw')) AS DECIMAL(15,2))";

        $sql = "SELECT c.*,
                       COALESCE(c.raw_price, cp.ungraded_cad)                                       AS display_raw_price,
                       (c.raw_price IS NULL AND cp.ungraded_cad IS NOT NULL AND cp.ungraded_cad > 0) AS raw_price_is_ref,
                       cl.set_name, cl.card_type_id, ct.name as card_type_name,
                       COALESCE(clb.batch_name, 0) as batch_name
                FROM `" . DB_PREFIX . "card` c
        LEFT JOIN `" . DB_PREFIX . "card_listing` cl ON c.listing_id = cl.listing_id
        LEFT JOIN `" . DB_PREFIX . "card_type` ct ON cl.card_type_id = ct.card_type_id
        LEFT JOIN `" . DB_PREFIX . "card_listing_batch` clb ON clb.batch_id = c.batch_id
        LEFT JOIN (
            SELECT player COLLATE utf8mb4_unicode_ci      AS player,
                   card_number COLLATE utf8mb4_unicode_ci  AS card_number,
                   year,
                   brand COLLATE utf8mb4_unicode_ci        AS brand,
                   MAX(" . $raw_expr . ") * " . $usd_to_cad . "    AS ungraded_cad
            FROM `" . DB_PREFIX . "card_price`
            WHERE " . $raw_expr . " > 0
            GROUP BY player, card_number, year, brand
        ) cp ON cp.player      = c.player_name  COLLATE utf8mb4_unicode_ci
             AND cp.card_number = c.card_number  COLLATE utf8mb4_unicode_ci
             AND cp.year        = CAST(c.year AS CHAR) COLLATE utf8mb4_unicode_ci
             AND cp.brand       = c.brand        COLLATE utf8mb4_unicode_ci";

        $where = [];

        if (!empty($data['filter_card_id'])) {
            $where[] = "c.card_id = '" . (int)$data['filter_card_id'] . "'";
        }

        if (!empty($data['filter_name'])) {
            // Search in both player_name and title
            $where[] = "(c.player_name LIKE '" . $this->db->escape($data['filter_name']) . "%' OR c.title LIKE '" . $this->db->escape($data['filter_name']) . "%')";
        }

        if (!empty($data['filter_set_name'])) {
            $where[] = "cl.set_name LIKE '" . $this->db->escape($data['filter_set_name']) . "%'";
        }

        if (!empty($data['filter_card_type_id'])) {
            $where[] = "cl.card_type_id = '" . (int)$data['filter_card_type_id'] . "'";
        }

        if (!empty($data['filter_year'])) {
            $where[] = "c.year = '" . (int)$data['filter_year'] . "'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $where[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }

        if (!empty($data['filter_listing_id'])) {
            $where[] = "c.listing_id = '" . (int)$data['filter_listing_id'] . "'";
        }

        if (isset($data['filter_batch_name']) && $data['filter_batch_name'] !== '') {
            $where[] = "c.batch_id = '" . (int)$data['filter_batch_name'] . "'";
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // Sort map: key = value sent by client, value = SQL expression
        $sort_map = [
            'c.card_id'     => 'c.card_id',
            'c.player_name' => 'c.player_name',
            'c.card_number' => 'CAST(c.card_number AS UNSIGNED)',
            'c.price'       => 'c.price',
            'c.raw_price'   => 'COALESCE(c.raw_price, cp.ungraded_cad)',
            'c.quantity'    => 'c.quantity',
            'c.batch_id'    => 'c.batch_id',
            'c.batch_name'  => 'clb.batch_name',
            'c.title'       => 'c.title',
            'cl.set_name'   => 'cl.set_name',
            'ct.name'       => 'ct.name',
            'c.year'        => 'c.year',
            'c.status'      => 'c.status',
            'c.date_added'  => 'c.date_added',
            'c.price_sold'  => 'c.price_sold',
            'c.price_list'  => 'c.price_list',
        ];

        if (isset($data['sort']) && isset($sort_map[$data['sort']])) {
            $sql .= " ORDER BY " . $sort_map[$data['sort']];
        } else {
            $sql .= " ORDER BY CAST(c.card_number AS UNSIGNED)";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        // Map the results to interface format and load images per card
        $cards = [];
        foreach ($query->rows as $row) {
            $card             = $row;
            $card['name']     = $row['player_name'] ?: $row['title'];
            $card['set_name'] = $row['set_name'];
            $card['raw_price']        = $row['raw_price'];
            $card['raw_price_is_ref'] = false;
            $card['images']           = $this->getCardImageUrls((int)$row['card_id']);
            $cards[] = $card;
        }

        return $cards;
    }

    /**
     * Bulk-fill oc_card.raw_price pour toutes les cartes NULL d'un listing.
     * Source : oc_card_price.ungraded (USD) converti en CAD. Plancher 0.01 si pas de match.
     *
     * @return int Nombre de lignes mises à jour
     */
    public function backfillRawPrices(int $listing_id): int {
        $usd_to_cad = (float)$this->currency->convert(1, 'USD', 'CAD');
        $raw_expr = "CAST(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(market_data_json, '{}'), '$.prices.raw')) AS DECIMAL(15,2))";

        // 1. Cartes avec match dans oc_card_price
        $this->db->query("
            UPDATE `" . DB_PREFIX . "card` c
            JOIN (
                SELECT player, card_number, year, brand,
                       MAX(" . $raw_expr . ") * " . $usd_to_cad . " AS ungraded_cad
                FROM `" . DB_PREFIX . "card_price`
                WHERE " . $raw_expr . " > 0
                GROUP BY player, card_number, year, brand
            ) cp ON cp.player      = c.player_name
                 AND cp.card_number = c.card_number
                 AND cp.year        = c.year
                 AND cp.brand       = c.brand
            SET c.raw_price = ROUND(cp.ungraded_cad, 4)
            WHERE c.listing_id = " . (int)$listing_id . "
              AND c.status = 1
              AND c.raw_price IS NULL
        ");

        // 2. Cartes sans match → plancher 0.01
        $this->db->query("
            UPDATE `" . DB_PREFIX . "card`
            SET raw_price = 0.01
            WHERE listing_id = " . (int)$listing_id . "
              AND status = 1
              AND raw_price IS NULL
        ");

        return (int)$this->db->countAffected();
    }

    /**
     * Get Total Cards
     *
     * @param array<string, mixed> $data
     *
     * @return int
     */
    public function getTotalCards(array $data = []): int {
        $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "card` c LEFT JOIN `" . DB_PREFIX . "card_listing` cl ON c.listing_id = cl.listing_id LEFT JOIN `" . DB_PREFIX . "card_type` ct ON cl.card_type_id = ct.card_type_id";

        $where = [];

        if (!empty($data['filter_card_id'])) {
            $where[] = "c.card_id = '" . (int)$data['filter_card_id'] . "'";
        }

        if (!empty($data['filter_name'])) {
            // Search in both player_name and title
            $where[] = "(c.player_name LIKE '" . $this->db->escape($data['filter_name']) . "%' OR c.title LIKE '" . $this->db->escape($data['filter_name']) . "%')";
        }

        if (!empty($data['filter_set_name'])) {
            $where[] = "cl.set_name LIKE '" . $this->db->escape($data['filter_set_name']) . "%'";
        }

        if (!empty($data['filter_card_type_id'])) {
            $where[] = "cl.card_type_id = '" . (int)$data['filter_card_type_id'] . "'";
        }

        if (!empty($data['filter_year'])) {
            $where[] = "c.year = '" . (int)$data['filter_year'] . "'";
        }

        // filter_location now supported
        if (!empty($data['filter_location'])) {
            $where[] = "c.location LIKE '" . $this->db->escape($data['filter_location']) . "%'";
        }

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $where[] = "c.status = '" . (int)$data['filter_status'] . "'";
        }

        if (!empty($data['filter_listing_id'])) {
            $where[] = "c.listing_id = '" . (int)$data['filter_listing_id'] . "'";
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $query = $this->db->query($sql);

        return (int)$query->row['total'];
    }

    /**
     * Update Card Location
     *
     * @param int $card_id
     * @param string $location
     *
     * @return void
     */
    public function updateCardLocation(int $card_id, string $location): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "card` SET
            `location` = '" . $this->db->escape($location) . "',
            `date_modified` = NOW()
            WHERE `card_id` = '" . (int)$card_id . "'");
    }

    /**
     * Update card quantity
     *
     * @param int $card_id
     * @param int $quantity
     *
     * @return void
     */
    public function updateCardQuantity(int $card_id, int $quantity): void {
        // 1. Update the card quantity and flag it for eBay sync
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card` SET
                `quantity`      = '" . (int)$quantity . "',
                `to_sync`       = 0,
                `date_modified` = NOW()
             WHERE `card_id` = '" . (int)$card_id . "'"
        );

        // 2. Recalculate oc_card_listing.total_quantity = SUM of all card quantities for this listing
        $row = $this->db->query(
            "SELECT `listing_id` FROM `" . DB_PREFIX . "card`
              WHERE `card_id` = '" . (int)$card_id . "' LIMIT 1"
        )->row;

        if (!empty($row['listing_id'])) {
            $listing_id = (int)$row['listing_id'];
            $this->db->query(
                "UPDATE `" . DB_PREFIX . "card_listing` cl
                    SET cl.`total_quantity` = (
                        SELECT COALESCE(SUM(c.`quantity`), 0)
                          FROM `" . DB_PREFIX . "card` c
                         WHERE c.`listing_id` = " . $listing_id . "
                    ),
                    cl.`date_modified` = NOW()
                  WHERE cl.`listing_id` = " . $listing_id
            );
        }
    }

    /**
     * Get distinct values for autocomplete
     *
     * @param string $field
     * @param string $filter_value
     * @param int $limit
     *
     * @return array
     */
    public function getDistinctValues(string $field, string $filter_value = '', int $limit = 5): array {
        $sql = "SELECT DISTINCT " . $field . " as value FROM " . DB_PREFIX . "card WHERE " . $field . " != '' AND " . $field . " IS NOT NULL";

        if (!empty($filter_value)) {
            $sql .= " AND " . $field . " LIKE '" . $this->db->escape($filter_value) . "%'";
        }

        $sql .= " ORDER BY " . $field . " ASC LIMIT " . (int)$limit;

        $query = $this->db->query($sql);

        return $query->rows;
    }

    /**
     * Retourne toutes les image_url d'une card triées par sort_order.
     * Utilisé pour mettre à jour product.imageUrls sur eBay (inventory_item).
     *
     * @param int $card_id
     * @return string[]  Tableau d'URLs (peut être vide)
     */
    public function getCardImageUrls(int $card_id): array {
        $query = $this->db->query(
            "SELECT `image_url` FROM `" . DB_PREFIX . "card_image`
             WHERE `card_id` = '" . (int)$card_id . "'
             ORDER BY `sort_order` ASC"
        );
        return array_column($query->rows, 'image_url');
    }

    /**
     * Update card image URL (migrate external URL to eBay URL)
     * 
     * @param int $card_id Card ID
     * @param string $old_url Current external URL
     * @param string $new_url New eBay URL
     * 
     * @return bool Success
     */
    public function updateCardImageUrl(int $card_id, string $old_url, string $new_url): bool {
        // Only update if the old URL exists (prevents double updates)
        $this->db->query("UPDATE `" . DB_PREFIX . "card_image` 
            SET `image_url` = '" . $this->db->escape($new_url) . "' 
            WHERE `card_id` = " . (int)$card_id . " 
            AND `image_url` = '" . $this->db->escape($old_url) . "'");
        
        return $this->db->countAffected() > 0;
    }

    /**
     * Save eBay offer_id, published status and error to oc_card by card_id.
     * If offer_id is empty or published is falsy, resets offer_id to NULL and published to 0.
     */
    public function updateCardOffer(int $card_id, string $offer_id = '', int $published = 0, string $error = ''): void {
        $offer_id_sql = !empty($offer_id) ? "'" . $this->db->escape($offer_id) . "'" : 'NULL';

        if (empty($offer_id) || !$published) {
            $offer_id_sql = 'NULL';
            $published    = 0;
        }

        // When offer is successfully published, clear the sync flag
        $to_sync_sql = $published ? ", `to_sync` = 0" : '';

        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card`
             SET `offer_id`  = " . $offer_id_sql . ",
                 `published` = '" . (int)$published . "'" . $to_sync_sql . ",
                 `error`     = '" . $this->db->escape($error) . "'
             WHERE `card_id` = '" . (int)$card_id . "'"
        );
    }

    /**
     * Get a single card by ID (with joined listing data)
     */
    public function getCardById(int $card_id): array {
        $query = $this->db->query(
            "SELECT c.*, cl.set_name, cl.card_type_id, cl.year AS listing_year, cl.brand AS listing_brand,
                    cld.ebay_item_id
             FROM `" . DB_PREFIX . "card` c
             LEFT JOIN `" . DB_PREFIX . "card_listing` cl ON c.listing_id = cl.listing_id
             LEFT JOIN `" . DB_PREFIX . "card_listing_description` cld ON cl.listing_id = cld.listing_id 
                AND cld.language_id = 1 AND cld.batch_id = c.batch_id
             WHERE c.card_id = '" . (int)$card_id . "'
             LIMIT 1"
        );
        return $query->row ?? [];
    }

    /**
     * Save market price check results for a card (prices in CAD)
     */
    public function updateCardMarketPrices(int $card_id, ?float $price_sold, ?float $price_list): void {
        $price_sold_sql = $price_sold !== null ? "'" . number_format($price_sold, 2, '.', '') . "'" : 'NULL';
        $price_list_sql = $price_list !== null ? "'" . number_format($price_list, 2, '.', '') . "'" : 'NULL';
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card`
             SET `price_sold` = " . $price_sold_sql . ",
                 `price_list` = " . $price_list_sql . ",
                 `date_price_check` = NOW()
             WHERE `card_id` = '" . (int)$card_id . "'"
        );
    }

    /**
     * Reset to_sync flag for a single card after a successful eBay offer update.
     */
    public function clearCardSyncFlag(int $card_id): void {
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card`
             SET `to_sync` = 0, `date_modified` = NOW()
             WHERE `card_id` = '" . (int)$card_id . "'"
        );
    }

    /**
     * Merge multiple cards into one keeper.
     * All selected cards must share the same card_number.
     * Keeper = longest player_name (ties: lowest card_id).
     * Quantities are summed; card_image rows are reassigned to keeper.
     *
     * @param array<mixed> $ids
     * @return array<string,mixed>
     */
    public function mergeCards(array $ids): array {
        // Sanitize & deduplicate
        $clean = array_unique(array_filter(array_map('intval', $ids), fn(int $i): bool => $i > 0));

        if (count($clean) < 2) {
            return ['error' => 'Sélectionnez au moins 2 cartes valides.'];
        }

        $in    = implode(',', $clean);
        $query = $this->db->query(
            "SELECT card_id, card_number, player_name, quantity
             FROM `" . DB_PREFIX . "card`
             WHERE card_id IN (" . $in . ")"
        );

        if ((int)$query->num_rows !== count($clean)) {
            return ['error' => 'Certaines cartes sélectionnées sont introuvables.'];
        }

        $cards = $query->rows;

        // All must share the same card_number
        $numbers = array_unique(array_map(fn(array $c): string => strtolower(trim((string)$c['card_number'])), $cards));
        if (count($numbers) > 1) {
            return ['error' => 'Toutes les cartes doivent avoir le même numéro de carte (trouvé: ' . implode(', ', $numbers) . ').'];
        }

        // Keeper = longest player_name; ties broken by lowest card_id
        usort($cards, function(array $a, array $b): int {
            $diff = mb_strlen((string)$b['player_name']) - mb_strlen((string)$a['player_name']);
            return $diff !== 0 ? $diff : (int)$a['card_id'] - (int)$b['card_id'];
        });

        $keeper_id   = (int)$cards[0]['card_id'];
        $player_name = (string)$cards[0]['player_name'];
        $card_number = trim((string)$cards[0]['card_number']);
        $total_qty   = (int)array_sum(array_column($cards, 'quantity'));

        $to_delete = array_values(array_filter($clean, fn(int $id): bool => $id !== $keeper_id));

        if (!empty($to_delete)) {
            $del_in = implode(',', $to_delete);
            // Reassign images to keeper before deleting
            $this->db->query(
                "UPDATE `" . DB_PREFIX . "card_image`
                 SET `card_id` = '" . $keeper_id . "'
                 WHERE `card_id` IN (" . $del_in . ")"
            );
            // Delete merged cards
            $this->db->query(
                "DELETE FROM `" . DB_PREFIX . "card` WHERE `card_id` IN (" . $del_in . ")"
            );
        }

        // Rebuild SKU
        $name_clean = preg_replace('/[^a-zA-Z0-9_]/', '', str_replace(' ', '_', $player_name));
        $sku        = $card_number . '_' . $name_clean;

        // Update keeper
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card`
             SET `player_name`   = '" . $this->db->escape($player_name) . "',
                 `title`         = '" . $this->db->escape($player_name) . "',
                 `sku`           = '" . $this->db->escape($sku) . "',
                 `quantity`      = '" . (int)$total_qty . "',
                 `date_modified` = NOW()
             WHERE `card_id` = '" . $keeper_id . "'"
        );

        return [
            'success'      => true,
            'keeper_id'    => $keeper_id,
            'merged_count' => count($to_delete),
            'player_name'  => $player_name,
            'quantity'     => $total_qty,
        ];
    }

    /**
     * Match an eBay sold item against oc_card_set records.
     *
     * $sale must contain:
     *   - 'title'     : eBay listing title (raw string)
     *   - 'cardNum'   : extracted card number (e.g. "123"), may be empty
     *   - 'titleYear' : extracted year from title (e.g. "1991-92"), may be empty
     *
     * Returns the best matching oc_card_set row, or null.
     */
    public function matchSale(array $sale, array $scpCards): ?array {
        $t       = strtolower($sale['title'] ?? '');
        $saleNum = strtoupper($sale['cardNum'] ?? '');
        $saleYr  = $sale['titleYear'] ?? '';

        $yearVariants = [];
        if ($saleYr) {
            $yearVariants[] = strtolower($saleYr);
            if (preg_match('/^(19|20)(\d{2})(?:-(\d{2,4}))?/', $saleYr, $m)) {
                $yearVariants[] = $m[2];
                if (!empty($m[3])) $yearVariants[] = substr($m[3], -2);
            }
            $yearVariants = array_unique($yearVariants);
        }

        $normBrand = function(string $b): string {
            $b = strtolower($b);
            $b = preg_replace('/o[\.\-]?p[\.\-]?e[\.\-]?e[\.\-]?c[\.\-]?h[\.\-]?e/i', 'opc', $b);
            $b = preg_replace('/o[\.\-]?p[\.\-]?c/i', 'opc', $b);
            return preg_replace('/[^a-z0-9]/', '', $b);
        };

        $candidates = array_filter($scpCards, function($c) use ($saleNum, $saleYr, $yearVariants, $t, $normBrand) {
            $cNum = strtoupper($c['card_number'] ?? '');
            if (!$cNum || !$saleNum || $cNum !== $saleNum) return false;
            if ($c['year'] && $saleYr) {
                $cy    = strtolower($c['year']);
                $match = false;
                foreach ($yearVariants as $v) {
                    if (str_starts_with($v, $cy) || str_starts_with($cy, $v) || str_contains($v, $cy)) {
                        $match = true; break;
                    }
                }
                if (!$match) return false;
            }
            if ($c['brand']) {
                $cb = $normBrand($c['brand']);
                $tb = $normBrand($t);
                if ($cb && !str_contains($tb, $cb)) {
                    if (!($cb === 'opc' && str_contains($tb, 'opc'))) return false;
                }
            }
            return true;
        });

        $candidates = array_values($candidates);
        if (empty($candidates)) return null;

        // Priority 1: card with no player name (generic match)
        foreach ($candidates as $c) {
            if (empty($c['player'])) return $c;
        }
        // Priority 2: all significant words in player match title
        foreach ($candidates as $c) {
            $words = array_filter(explode(' ', strtolower($c['player'])), fn($w) => strlen($w) > 2);
            if (!empty($words) && array_reduce($words, fn($carry, $w) => $carry && str_contains($t, $w), true)) {
                return $c;
            }
        }
        // Priority 3: any significant word in player matches title
        foreach ($candidates as $c) {
            $words = array_filter(explode(' ', strtolower($c['player'])), fn($w) => strlen($w) > 2);
            if (!empty($words) && array_reduce($words, fn($carry, $w) => $carry || str_contains($t, $w), false)) {
                return $c;
            }
        }
        return null;
    }
}