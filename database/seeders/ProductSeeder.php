<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories (Bangladesh-friendly)
        $categories = [
            ['name' => 'Laptops', 'description' => 'Laptop computers and notebooks', 'sort_order' => 1],
            ['name' => 'Desktops', 'description' => 'Desktop computers and workstations', 'sort_order' => 2],
            ['name' => 'Smartphones', 'description' => 'Mobile phones and smartphones', 'sort_order' => 3],
            ['name' => 'Tablets', 'description' => 'Tablet devices', 'sort_order' => 4],
            ['name' => 'Accessories', 'description' => 'Computer and phone accessories', 'sort_order' => 5],
            ['name' => 'Monitors', 'description' => 'Display monitors and screens', 'sort_order' => 6],
            ['name' => 'Printers', 'description' => 'Printers and scanners', 'sort_order' => 7],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'sort_order' => $cat['sort_order'],
                'is_active' => true,
            ]);
        }

        // Create Brands (including Bangladesh brands)
        $brands = [
            ['name' => 'HP', 'description' => 'Hewlett Packard', 'sort_order' => 1],
            ['name' => 'Dell', 'description' => 'Dell Technologies', 'sort_order' => 2],
            ['name' => 'Lenovo', 'description' => 'Lenovo Group', 'sort_order' => 3],
            ['name' => 'ASUS', 'description' => 'ASUS Tek Computer', 'sort_order' => 4],
            ['name' => 'Samsung', 'description' => 'Samsung Electronics', 'sort_order' => 5],
            ['name' => 'Walton', 'description' => 'Walton Hi-Tech Industries (Bangladesh)', 'sort_order' => 6],
            ['name' => 'Symphony', 'description' => 'Symphony Bangladesh', 'sort_order' => 7],
            ['name' => 'Canon', 'description' => 'Canon Inc.', 'sort_order' => 8],
        ];

        $brandModels = [];
        foreach ($brands as $brand) {
            $brandModels[$brand['name']] = Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'description' => $brand['description'],
                'sort_order' => $brand['sort_order'],
                'is_active' => true,
            ]);
        }

        // Create Product Models
        $models = [
            ['brand' => 'HP', 'name' => 'Pavilion 15', 'description' => 'HP Pavilion 15 Series'],
            ['brand' => 'HP', 'name' => 'EliteBook 840', 'description' => 'HP EliteBook 840 Series'],
            ['brand' => 'Dell', 'name' => 'Inspiron 15', 'description' => 'Dell Inspiron 15 Series'],
            ['brand' => 'Dell', 'name' => 'XPS 13', 'description' => 'Dell XPS 13 Ultrabook'],
            ['brand' => 'Lenovo', 'name' => 'ThinkPad E14', 'description' => 'Lenovo ThinkPad E14'],
            ['brand' => 'Lenovo', 'name' => 'IdeaPad 3', 'description' => 'Lenovo IdeaPad 3 Series'],
            ['brand' => 'ASUS', 'name' => 'VivoBook 15', 'description' => 'ASUS VivoBook 15'],
            ['brand' => 'ASUS', 'name' => 'ROG Strix', 'description' => 'ASUS ROG Gaming Series'],
            ['brand' => 'Samsung', 'name' => 'Galaxy A54', 'description' => 'Samsung Galaxy A54'],
            ['brand' => 'Samsung', 'name' => 'Galaxy S23', 'description' => 'Samsung Galaxy S23'],
            ['brand' => 'Walton', 'name' => 'Primo S8', 'description' => 'Walton Primo S8 Smartphone'],
            ['brand' => 'Symphony', 'name' => 'Z40', 'description' => 'Symphony Z40 Smartphone'],
        ];

        $modelObjects = [];
        foreach ($models as $model) {
            $modelObjects[] = ProductModel::create([
                'brand_id' => $brandModels[$model['brand']]->id,
                'name' => $model['name'],
                'slug' => Str::slug($model['name']),
                'description' => $model['description'],
                'is_active' => true,
            ]);
        }

        // Get admin user for created_by
        $admin = User::first();

        // Create Products
        $products = [
            // Laptops
            ['name' => 'HP Pavilion 15-eg2000TU', 'category' => 'Laptops', 'brand' => 'HP', 'model' => 'Pavilion 15', 'cost_price' => 55000, 'selling_price' => 65000, 'stock' => 25, 'sku' => 'HP-PAV-15-001'],
            ['name' => 'Dell Inspiron 15 3520', 'category' => 'Laptops', 'brand' => 'Dell', 'model' => 'Inspiron 15', 'cost_price' => 52000, 'selling_price' => 62000, 'stock' => 18, 'sku' => 'DL-INSP-15-001'],
            ['name' => 'Lenovo ThinkPad E14 Gen 4', 'category' => 'Laptops', 'brand' => 'Lenovo', 'model' => 'ThinkPad E14', 'cost_price' => 78000, 'selling_price' => 88000, 'stock' => 12, 'sku' => 'LN-TP-E14-001'],
            ['name' => 'ASUS VivoBook 15 X1504', 'category' => 'Laptops', 'brand' => 'ASUS', 'model' => 'VivoBook 15', 'cost_price' => 58000, 'selling_price' => 68000, 'stock' => 20, 'sku' => 'AS-VB-15-001'],
            ['name' => 'HP EliteBook 840 G9', 'category' => 'Laptops', 'brand' => 'HP', 'model' => 'EliteBook 840', 'cost_price' => 95000, 'selling_price' => 105000, 'stock' => 8, 'sku' => 'HP-EL-840-001'],
            
            // Smartphones
            ['name' => 'Samsung Galaxy A54 5G', 'category' => 'Smartphones', 'brand' => 'Samsung', 'model' => 'Galaxy A54', 'cost_price' => 38000, 'selling_price' => 45000, 'stock' => 35, 'sku' => 'SM-GA54-001'],
            ['name' => 'Samsung Galaxy S23', 'category' => 'Smartphones', 'brand' => 'Samsung', 'model' => 'Galaxy S23', 'cost_price' => 85000, 'selling_price' => 95000, 'stock' => 15, 'sku' => 'SM-GS23-001'],
            
            // Monitors
            ['name' => 'Dell 24 Monitor E2424H', 'category' => 'Monitors', 'brand' => 'Dell', 'model' => null, 'cost_price' => 12000, 'selling_price' => 15000, 'stock' => 40, 'sku' => 'DL-MON-24-001'],
            ['name' => 'ASUS 27 Monitor VZ279HE', 'category' => 'Monitors', 'brand' => 'ASUS', 'model' => null, 'cost_price' => 18000, 'selling_price' => 22000, 'stock' => 25, 'sku' => 'AS-MON-27-001'],
            
            // Printers
            ['name' => 'HP LaserJet Pro M404dn', 'category' => 'Printers', 'brand' => 'HP', 'model' => null, 'cost_price' => 25000, 'selling_price' => 32000, 'stock' => 10, 'sku' => 'HP-PRT-404-001'],
            ['name' => 'Canon PIXMA G3010', 'category' => 'Printers', 'brand' => 'Canon', 'model' => null, 'cost_price' => 15000, 'selling_price' => 20000, 'stock' => 18, 'sku' => 'CN-PIX-G30-001'],
            ['name' => 'Walton Primo S8', 'category' => 'Smartphones', 'brand' => 'Walton', 'model' => 'Primo S8', 'cost_price' => 12000, 'selling_price' => 15000, 'stock' => 30, 'sku' => 'WT-PRIMO-S8-001'],
            ['name' => 'Symphony Z40', 'category' => 'Smartphones', 'brand' => 'Symphony', 'model' => 'Z40', 'cost_price' => 8500, 'selling_price' => 11000, 'stock' => 45, 'sku' => 'SYM-Z40-001'],
            
            // Accessories
            ['name' => 'Wireless Mouse', 'category' => 'Accessories', 'brand' => null, 'model' => null, 'cost_price' => 500, 'selling_price' => 800, 'stock' => 100, 'sku' => 'ACC-MOUSE-001'],
            ['name' => 'USB Keyboard', 'category' => 'Accessories', 'brand' => null, 'model' => null, 'cost_price' => 800, 'selling_price' => 1200, 'stock' => 80, 'sku' => 'ACC-KB-001'],
            ['name' => 'Laptop Bag 15.6"', 'category' => 'Accessories', 'brand' => null, 'model' => null, 'cost_price' => 1200, 'selling_price' => 1800, 'stock' => 60, 'sku' => 'ACC-BAG-001'],
        ];

        foreach ($products as $product) {
            $category = $categoryModels[$product['category']];
            $brand = $product['brand'] ? $brandModels[$product['brand']] : null;
            $model = null;
            if ($product['model']) {
                foreach ($modelObjects as $m) {
                    if ($m->name === $product['model'] && $m->brand_id === $brand->id) {
                        $model = $m;
                        break;
                    }
                }
            }

            // 1 stock = 1 barcode: initial stock 0, barcodes empty; stock comes from purchase receive
            Product::create([
                'name' => $product['name'],
                'sku' => $product['sku'],
                'barcodes' => [],
                'category_id' => $category->id,
                'brand_id' => $brand?->id,
                'product_model_id' => $model?->id,
                'description' => 'High-quality ' . strtolower($product['name']) . ' with excellent features.',
                'specifications' => 'Check product specifications for details.',
                'unit' => 'pcs',
                'cost_price' => $product['cost_price'],
                'selling_price' => $product['selling_price'],
                'stock_quantity' => 0,
                'reorder_level' => 10,
                'min_stock' => 5,
                'max_stock' => 200,
                'is_active' => true,
                'is_featured' => rand(0, 1) === 1,
                'status' => 'out_of_stock',
                'warranty_period' => 365,
                'created_by' => $admin?->id,
            ]);
        }
    }
}
