<?php
// Original: shopmanager/catalog/category_ebay.php
namespace Opencart\Admin\Model\Shopmanager\Catalog;


class CategoryEbay extends \Opencart\System\Engine\Model {
    public function addCategoryEbay(array $data): int {
        // $this->db->query("INSERT ...");
        // return (int)$this->db->getLastId();
        return 0;
    }

    public function editCategoryEbay(int $category_id, array $data): void {
    }

    public function deleteCategoryEbay(int $category_id): void {
    }

    public function repairCategories(): void {
    }

    public function getTotalCategories(array $data = []): int {
        return 0;
    }

    public function getCategories(array $data = []): array {
        // Doit retourner un tableau d’entrées avec clés utilisées dans le contrôleur:
        // category_id, name, leaf, specifics, specifics_error, sort_order, status
        return [];
    }

    public function getCategoryEbay(int $category_id): array {
        return [];
    }

    public function getCategoryEbayPath(int $category_id): array {
        return [];
    }

    public function getCategoryEbayDescriptions(int $category_id): array {
        return [];
    }

    public function getCategoryEbayFilters(int $category_id): array {
        return [];
    }

    public function getCategoryEbayStores(int $category_id): array {
        return [0];
    }

    public function getCategoryEbayLayouts(int $category_id): array {
        return [];
    }

    public function uploadImageFromLink(int $category_id, string $url): array {
        // Exemple minimal de téléchargement distant vers image/catalog/...
        try {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return ['success' => false, 'error' => 'URL invalide'];
            }

            $image_data = @file_get_contents($url);
            if ($image_data === false) {
                return ['success' => false, 'error' => 'Téléchargement impossible'];
            }

            $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'category_ebay/' . $category_id . '_' . time() . '.' . preg_replace('/[^a-z0-9]+/i', '', $ext);

            $fullpath = rtrim(DIR_IMAGE, '/\\') . '/' . $filename;
            $dir = dirname($fullpath);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            if (file_put_contents($fullpath, $image_data) === false) {
                return ['success' => false, 'error' => 'Écriture fichier échouée'];
            }

            // $this->db->query("UPDATE ... SET image = '" . $this->db->escape($filename) . "' WHERE category_id=" . (int)$category_id);

            return ['success' => true, 'image_url' => 'image/' . $filename];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
