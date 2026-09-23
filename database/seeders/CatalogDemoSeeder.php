<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogDemoSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Salas' => 'salas',
            'Comedores' => 'comedores',
            'Dormitorios' => 'dormitorios',
            'Oficina' => 'oficina',
            'Exteriores' => 'exteriores',
        ];

        $categoryModels = [];
        foreach ($categories as $name => $slug) {
            $categoryModels[$slug] = Category::query()->create(['name' => $name, 'slug' => $slug]);
        }

        $products = [
            [
                'category' => 'salas',
                'sku' => 'SOFA-3P-GRIS',
                'name' => 'Sofá 3 plazas tela gris',
                'description' => "Sofá de 3 plazas tapizado en tela gris, estructura de madera de pino.\nIdeal para salas modernas.",
                'price' => 2800,
                'stock' => 8,
                'material' => 'Tela y madera de pino',
                'color' => 'Gris',
                'width_cm' => 210,
                'height_cm' => 85,
                'depth_cm' => 90,
                'featured' => true,
            ],
            [
                'category' => 'comedores',
                'sku' => 'COM-6P-ROBLE',
                'name' => 'Juego de comedor 6 sillas roble',
                'description' => 'Mesa de comedor con 6 sillas, acabado en madera de roble macizo.',
                'price' => 4500,
                'stock' => 4,
                'material' => 'Madera de roble',
                'color' => 'Café natural',
                'width_cm' => 180,
                'height_cm' => 75,
                'depth_cm' => 95,
                'featured' => true,
            ],
            [
                'category' => 'dormitorios',
                'sku' => 'CAMA-Q-CAOBA',
                'name' => 'Cama Queen con cabecera tapizada',
                'description' => 'Cama tamaño Queen con cabecera tapizada en tela beige y base de madera de caoba.',
                'price' => 3200,
                'stock' => 6,
                'material' => 'Madera de caoba y tela',
                'color' => 'Beige',
                'width_cm' => 160,
                'height_cm' => 110,
                'depth_cm' => 200,
                'featured' => true,
            ],
            [
                'category' => 'oficina',
                'sku' => 'ESC-1P-NEGRO',
                'name' => 'Escritorio ejecutivo 1.40m',
                'description' => 'Escritorio ejecutivo con cajonera, acabado negro mate.',
                'price' => 1450,
                'stock' => 10,
                'material' => 'Melamina',
                'color' => 'Negro',
                'width_cm' => 140,
                'height_cm' => 75,
                'depth_cm' => 60,
            ],
            [
                'category' => 'exteriores',
                'sku' => 'SET-JARDIN-4',
                'name' => 'Set de jardín 4 piezas ratán sintético',
                'description' => 'Set de terraza con sofá, dos sillones y mesa de centro en ratán sintético resistente a la intemperie.',
                'price' => 3900,
                'stock' => 3,
                'material' => 'Ratán sintético y aluminio',
                'color' => 'Café oscuro',
                'featured' => true,
            ],
            [
                'category' => 'salas',
                'sku' => 'SILLON-1P-CUERO',
                'name' => 'Sillón reclinable de cuero',
                'description' => 'Sillón individual reclinable, tapizado en cuero sintético color café.',
                'price' => 1650,
                'stock' => 12,
                'material' => 'Cuero sintético',
                'color' => 'Café',
                'width_cm' => 90,
                'height_cm' => 100,
                'depth_cm' => 95,
            ],
        ];

        foreach ($products as $data) {
            $categorySlug = $data['category'];
            unset($data['category']);

            Product::query()->create([
                ...$data,
                'category_id' => $categoryModels[$categorySlug]->id,
                'slug' => Str::slug($data['name']),
            ]);
        }
    }
}
