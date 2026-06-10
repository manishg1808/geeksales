<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

class ProductModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ? AND p.status = "active"'
        );
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        return $product ? $this->formatProduct($product) : null;
    }

    public function getRelated(int $productId, ?int $categoryId, int $limit = 4): array
    {
        if (!$categoryId) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, c.name AS category_name, c.slug AS category_slug
             FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.category_id = ? AND p.id <> ? AND p.status = "active"
             ORDER BY p.id DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(2, $productId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn (array $row): array => $this->formatProduct($row), $stmt->fetchAll());
    }

    public function formatProduct(array $row): array
    {
        $cat = $this->categoryKey((string)($row['category_slug'] ?? ''));
        $brand = (string)($row['brand_name'] ?? '');
        $name = (string)($row['name'] ?? '');
        $price = (float)($row['price'] ?? 0);
        $oldPrice = (float)($row['old_price'] ?? 0);
        $badge = (string)($row['badge'] ?? '');
        $description = (string)($row['short_description'] ?: $row['description'] ?: '');
        $rating = max(0, min(5, (float)($row['rating'] ?? 0)));

        return [
            'id' => (int)$row['id'],
            'category_id' => isset($row['category_id']) ? (int)$row['category_id'] : null,
            'name' => $name,
            'slug' => (string)($row['slug'] ?? ''),
            'brand' => $brand,
            'brand_name' => $brand,
            'brand_slug' => (string)($row['brand_slug'] ?? ''),
            'cat' => $cat,
            'category_name' => (string)($row['category_name'] ?? ''),
            'category_slug' => (string)($row['category_slug'] ?? ''),
            'price' => $price,
            'oldPrice' => $oldPrice,
            'old_price' => $oldPrice,
            'rating' => $rating,
            'reviews' => 120 + ((int)$row['id'] * 17),
            'badge' => $badge,
            'badgeColor' => $this->badgeColor($badge),
            'color' => $this->productColor($brand, $cat),
            'iconColor' => $this->iconColor($brand, $cat),
            'features' => $this->features($description, $cat),
            'desc' => $description,
            'description' => $description,
            'specs' => $this->specs($row, $cat),
            'inbox' => $this->inbox($brand, $cat),
            'newest' => stripos($badge, 'new') !== false || (int)$row['id'] >= 9,
            'discount' => $oldPrice > $price && $oldPrice > 0 ? (int)round((($oldPrice - $price) / $oldPrice) * 100) : 0,
            'stock' => (int)($row['stock'] ?? 0),
            'top_pick' => (int)($row['top_pick'] ?? 0),
            'featured' => (int)($row['featured'] ?? 0),
            'image_url' => (string)($row['image_url'] ?? ''),
        ];
    }

    private function categoryKey(string $slug): string
    {
        return match ($slug) {
            'all-in-one' => 'allinone',
            'ink-toner' => 'ink',
            default => $slug !== '' ? $slug : 'inkjet',
        };
    }

    private function badgeColor(string $badge): string
    {
        $badge = strtolower($badge);
        if (str_contains($badge, 'sale') || str_contains($badge, 'deal')) {
            return 'red';
        }
        if (str_contains($badge, 'new')) {
            return 'green';
        }
        if (str_contains($badge, 'best')) {
            return 'amber';
        }
        return $badge !== '' ? 'navy' : '';
    }

    private function productColor(string $brand, string $cat): string
    {
        if ($cat === 'business') {
            return '#f8fafc';
        }
        return match (strtolower($brand)) {
            'brother' => '#fffbeb',
            'epson' => '#ecfdf5',
            default => '#f1f5f9',
        };
    }

    private function iconColor(string $brand, string $cat): string
    {
        if ($cat === 'ink') {
            return '#1e293b';
        }
        return match (strtolower($brand)) {
            'epson' => '#059669',
            'brother' => '#1d4ed8',
            'canon' => '#475569',
            default => '#1e293b',
        };
    }

    private function features(string $description, string $cat): array
    {
        $text = strtolower($description);
        $features = [];
        if (str_contains($text, 'wireless') || str_contains($text, 'wi-fi')) {
            $features[] = 'wireless';
        }
        if ($cat !== 'laser' && $cat !== 'ink') {
            $features[] = 'color';
        }
        if (str_contains($text, 'duplex')) {
            $features[] = 'duplex';
        }
        if ($cat !== 'ink') {
            $features[] = 'mobile';
        }
        return array_values(array_unique($features));
    }

    private function specs(array $row, string $cat): array
    {
        $raw = trim((string)($row['specifications'] ?? ''));
        if ($raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return array_values(array_map('strval', $decoded));
            }
            return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: [])));
        }

        $type = match ($cat) {
            'laser' => 'Laser',
            'business' => 'Business',
            'allinone' => 'All-in-One',
            'ink' => 'Supply',
            default => 'Inkjet',
        };

        return [
            'Type: ' . $type,
            'Connectivity: Wi-Fi, USB',
            'Paper Capacity: 100+ sheets',
            'Warranty: 2 years included',
        ];
    }

    private function inbox(string $brand, string $cat): array
    {
        if ($cat === 'ink') {
            return [$brand . ' cartridge', 'Installation guide', 'Warranty card'];
        }

        return [$brand . ' printer', 'Starter ink or toner', 'Power cord', 'Quick setup guide'];
    }
}
