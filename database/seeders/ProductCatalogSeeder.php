<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Amplía el catálogo con al menos 10 productos por categoría, incluyendo
 * especificaciones (material, color, dimensiones) y fotografías de marca
 * generadas automáticamente. No se ejecuta con `db:seed` por defecto:
 *
 *   php artisan db:seed --class=ProductCatalogSeeder
 */
class ProductCatalogSeeder extends Seeder
{
    /** Paleta de marca (Tailwind primary/accent) usada en las fotos placeholder. */
    private const IMAGE_STYLES = [
        ['bg' => '593B21', 'text' => 'F5E8C8'], // primary-700 / accent-100
        ['bg' => '966B24', 'text' => 'FFFFFF'], // accent-600 / white
        ['bg' => 'B47F55', 'text' => '3B2818'], // primary-400 / primary-900
    ];

    public function run(): void
    {
        $categories = [
            'salas' => 'Salas',
            'comedores' => 'Comedores',
            'dormitorios' => 'Dormitorios',
            'oficina' => 'Oficina',
            'exteriores' => 'Exteriores',
        ];

        $categoryModels = [];
        foreach ($categories as $slug => $name) {
            $categoryModels[$slug] = Category::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        foreach ($this->products() as $data) {
            $categorySlug = $data['category'];
            unset($data['category']);

            $product = Product::query()->where('sku', $data['sku'])->first();

            if (! $product) {
                $product = Product::query()->create([
                    ...$data,
                    'category_id' => $categoryModels[$categorySlug]->id,
                    'slug' => Str::slug($data['name'].'-'.$data['sku']),
                ]);
            }

            $this->ensureImages($product);
        }
    }

    /**
     * Completa las fotografías del producto hasta alcanzar IMAGE_STYLES,
     * sin duplicar las que ya tenga (útil al volver a correr el seeder).
     */
    private function ensureImages(Product $product): void
    {
        $existing = $product->images()->count();

        for ($position = $existing; $position < count(self::IMAGE_STYLES); $position++) {
            $path = $this->downloadPlaceholder($product->name, self::IMAGE_STYLES[$position], $position);

            if ($path === null) {
                continue;
            }

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'position' => $position,
            ]);
        }
    }

    private function downloadPlaceholder(string $name, array $style, int $position): ?string
    {
        $text = urlencode(Str::limit($name, 40, ''));
        $url = "https://placehold.co/900x700/{$style['bg']}/{$style['text']}.png?text={$text}&font=montserrat";

        try {
            $response = Http::timeout(10)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $filename = 'products/'.Str::random(20).'-'.$position.'.png';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function products(): array
    {
        return [
            // ---- Salas ----
            ['category' => 'salas', 'sku' => 'SL-01', 'name' => 'Sofá 2 plazas tela beige', 'description' => "Sofá de 2 plazas tapizado en tela beige con estructura de madera de pino.\nCompacto y cómodo para espacios pequeños.", 'price' => 2200, 'stock' => 7, 'material' => 'Tela y madera de pino', 'color' => 'Beige', 'width_cm' => 160, 'height_cm' => 85, 'depth_cm' => 88],
            ['category' => 'salas', 'sku' => 'SL-02', 'name' => 'Sofá seccional en L gris oscuro', 'description' => 'Sofá seccional en L, tapizado en tela gris oscuro resistente al desgaste.', 'price' => 5200, 'stock' => 3, 'material' => 'Tela y madera', 'color' => 'Gris oscuro', 'width_cm' => 280, 'height_cm' => 90, 'depth_cm' => 160, 'featured' => true],
            ['category' => 'salas', 'sku' => 'SL-03', 'name' => 'Love seat terciopelo verde botella', 'description' => 'Love seat de 2 plazas en terciopelo verde botella, patas de madera torneada.', 'price' => 1950, 'stock' => 6, 'material' => 'Terciopelo y madera', 'color' => 'Verde botella', 'width_cm' => 140, 'height_cm' => 85, 'depth_cm' => 88],
            ['category' => 'salas', 'sku' => 'SL-04', 'name' => 'Sillón individual orejero', 'description' => 'Sillón orejero clásico tapizado en tela café, estructura de cedro.', 'price' => 1350, 'stock' => 9, 'material' => 'Tela y madera de cedro', 'color' => 'Café', 'width_cm' => 85, 'height_cm' => 105, 'depth_cm' => 90],
            ['category' => 'salas', 'sku' => 'SL-05', 'name' => 'Mesa de centro madera y vidrio', 'description' => 'Mesa de centro con base de madera de pino y cubierta de vidrio templado.', 'price' => 980, 'stock' => 10, 'material' => 'Madera de pino y vidrio templado', 'color' => 'Café natural', 'width_cm' => 110, 'height_cm' => 45, 'depth_cm' => 60],
            ['category' => 'salas', 'sku' => 'SL-06', 'name' => 'Mueble para TV 1.60m', 'description' => 'Mueble de TV de 1.60m con espacio para consolas y repisas abiertas.', 'price' => 1450, 'stock' => 8, 'material' => 'MDF laminado', 'color' => 'Nogal', 'width_cm' => 160, 'height_cm' => 45, 'depth_cm' => 40],
            ['category' => 'salas', 'sku' => 'SL-07', 'name' => 'Set sala 3 piezas tela gris claro', 'description' => 'Sofá 3 plazas + 2 sillones individuales tapizados en tela gris claro.', 'price' => 4600, 'stock' => 4, 'material' => 'Tela y madera', 'color' => 'Gris claro', 'featured' => true],
            ['category' => 'salas', 'sku' => 'SL-08', 'name' => 'Puf otomana redondo', 'description' => 'Puf otomana tapizado, ideal como reposapiés o asiento extra.', 'price' => 450, 'stock' => 15, 'material' => 'Tela y madera', 'color' => 'Mostaza', 'width_cm' => 60, 'height_cm' => 40, 'depth_cm' => 60],
            ['category' => 'salas', 'sku' => 'SL-09', 'name' => 'Librero decorativo 5 niveles', 'description' => 'Librero de 5 niveles en madera de pino, acabado rústico.', 'price' => 890, 'stock' => 8, 'material' => 'Madera de pino', 'color' => 'Café oscuro', 'width_cm' => 80, 'height_cm' => 180, 'depth_cm' => 30],
            ['category' => 'salas', 'sku' => 'SL-10', 'name' => 'Sofá cama individual', 'description' => 'Sofá cama individual, ideal para visitas o espacios multiusos.', 'price' => 2650, 'stock' => 5, 'material' => 'Tela y madera', 'color' => 'Azul petróleo', 'width_cm' => 90, 'height_cm' => 85, 'depth_cm' => 190],

            // ---- Comedores ----
            ['category' => 'comedores', 'sku' => 'CM-01', 'name' => 'Mesa redonda 4 personas', 'description' => 'Mesa de comedor redonda para 4 personas en madera de pino.', 'price' => 1600, 'stock' => 7, 'material' => 'Madera de pino', 'color' => 'Café natural', 'width_cm' => 120, 'height_cm' => 75, 'depth_cm' => 120],
            ['category' => 'comedores', 'sku' => 'CM-02', 'name' => 'Juego de comedor 4 sillas pino', 'description' => 'Juego de comedor con mesa y 4 sillas en madera de pino, acabado miel.', 'price' => 2900, 'stock' => 5, 'material' => 'Madera de pino', 'color' => 'Miel', 'width_cm' => 140, 'height_cm' => 75, 'depth_cm' => 80],
            ['category' => 'comedores', 'sku' => 'CM-03', 'name' => 'Juego de comedor 8 sillas nogal', 'description' => 'Juego de comedor amplio con 8 sillas, madera de nogal macizo.', 'price' => 6800, 'stock' => 2, 'material' => 'Madera de nogal', 'color' => 'Nogal oscuro', 'width_cm' => 220, 'height_cm' => 75, 'depth_cm' => 100, 'featured' => true],
            ['category' => 'comedores', 'sku' => 'CM-04', 'name' => 'Vitrinero 2 puertas', 'description' => 'Vitrinero de 2 puertas para exhibir vajilla, madera de cedro.', 'price' => 2100, 'stock' => 4, 'material' => 'Madera de cedro', 'color' => 'Café oscuro', 'width_cm' => 100, 'height_cm' => 180, 'depth_cm' => 40],
            ['category' => 'comedores', 'sku' => 'CM-05', 'name' => 'Mesa alta tipo bar con 4 bancos', 'description' => 'Mesa alta estilo bar con 4 bancos incluidos, estructura de metal.', 'price' => 2350, 'stock' => 5, 'material' => 'Madera y metal', 'color' => 'Negro y café', 'width_cm' => 120, 'height_cm' => 110, 'depth_cm' => 60],
            ['category' => 'comedores', 'sku' => 'CM-06', 'name' => 'Set de 6 sillas tapizadas', 'description' => 'Set de 6 sillas de comedor tapizadas en tela beige, patas de haya.', 'price' => 1800, 'stock' => 6, 'material' => 'Tela y madera de haya', 'color' => 'Beige'],
            ['category' => 'comedores', 'sku' => 'CM-07', 'name' => 'Mesa extensible 6-8 personas', 'description' => 'Mesa de comedor extensible, se ajusta de 6 a 8 personas.', 'price' => 4200, 'stock' => 3, 'material' => 'Madera de roble', 'color' => 'Roble claro', 'width_cm' => 220, 'height_cm' => 75, 'depth_cm' => 95],
            ['category' => 'comedores', 'sku' => 'CM-08', 'name' => 'Carrito de servicio con ruedas', 'description' => 'Carrito auxiliar de servicio con ruedas, dos niveles.', 'price' => 650, 'stock' => 10, 'material' => 'Madera y metal', 'color' => 'Café', 'width_cm' => 60, 'height_cm' => 80, 'depth_cm' => 40],
            ['category' => 'comedores', 'sku' => 'CM-09', 'name' => 'Juego de comedor rústico 6 sillas', 'description' => 'Juego de comedor rústico con 6 sillas, madera de pino sin tratar.', 'price' => 3600, 'stock' => 3, 'material' => 'Madera de pino rústico', 'color' => 'Miel rústico', 'width_cm' => 200, 'height_cm' => 75, 'depth_cm' => 90],
            ['category' => 'comedores', 'sku' => 'CM-10', 'name' => 'Mesa cuadrada 4 personas', 'description' => 'Mesa cuadrada compacta para 4 personas, acabado blanco.', 'price' => 1200, 'stock' => 8, 'material' => 'Madera de pino', 'color' => 'Blanco', 'width_cm' => 90, 'height_cm' => 75, 'depth_cm' => 90],

            // ---- Dormitorios ----
            ['category' => 'dormitorios', 'sku' => 'DM-01', 'name' => 'Cama matrimonial base tapizada', 'description' => 'Cama matrimonial con base tapizada en tela gris.', 'price' => 2400, 'stock' => 6, 'material' => 'Tela y madera', 'color' => 'Gris', 'width_cm' => 150, 'height_cm' => 100, 'depth_cm' => 200],
            ['category' => 'dormitorios', 'sku' => 'DM-02', 'name' => 'Cama King con cabecera capitoné', 'description' => 'Cama King con cabecera capitoné en tela beige, base de madera de caoba.', 'price' => 3800, 'stock' => 3, 'material' => 'Tela y madera de caoba', 'color' => 'Beige', 'width_cm' => 200, 'height_cm' => 115, 'depth_cm' => 210, 'featured' => true],
            ['category' => 'dormitorios', 'sku' => 'DM-03', 'name' => 'Cama individual juvenil', 'description' => 'Cama individual juvenil, ideal para recámaras de niños o jóvenes.', 'price' => 1450, 'stock' => 8, 'material' => 'Madera de pino', 'color' => 'Blanco', 'width_cm' => 100, 'height_cm' => 90, 'depth_cm' => 200],
            ['category' => 'dormitorios', 'sku' => 'DM-04', 'name' => 'Ropero 4 puertas', 'description' => 'Ropero de 4 puertas con espacio para colgar y cajones internos.', 'price' => 2900, 'stock' => 5, 'material' => 'Melamina', 'color' => 'Wengue', 'width_cm' => 180, 'height_cm' => 200, 'depth_cm' => 60],
            ['category' => 'dormitorios', 'sku' => 'DM-05', 'name' => 'Cómoda 6 cajones', 'description' => 'Cómoda de 6 cajones con corredera metálica, madera de pino.', 'price' => 1550, 'stock' => 7, 'material' => 'Madera de pino', 'color' => 'Café', 'width_cm' => 120, 'height_cm' => 85, 'depth_cm' => 45],
            ['category' => 'dormitorios', 'sku' => 'DM-06', 'name' => 'Velador con cajón', 'description' => 'Velador compacto con un cajón, madera de pino acabado miel.', 'price' => 450, 'stock' => 14, 'material' => 'Madera de pino', 'color' => 'Miel', 'width_cm' => 45, 'height_cm' => 55, 'depth_cm' => 40],
            ['category' => 'dormitorios', 'sku' => 'DM-07', 'name' => 'Tocador con espejo', 'description' => 'Tocador con espejo incluido y cajones para organización.', 'price' => 1750, 'stock' => 5, 'material' => 'MDF laminado', 'color' => 'Blanco', 'width_cm' => 100, 'height_cm' => 140, 'depth_cm' => 45],
            ['category' => 'dormitorios', 'sku' => 'DM-08', 'name' => 'Litera doble', 'description' => 'Litera doble en madera de pino natural, con escalera integrada.', 'price' => 2600, 'stock' => 4, 'material' => 'Madera de pino', 'color' => 'Natural', 'width_cm' => 100, 'height_cm' => 150, 'depth_cm' => 200],
            ['category' => 'dormitorios', 'sku' => 'DM-09', 'name' => 'Cama Queen con almacenaje', 'description' => 'Cama Queen con cajones de almacenaje bajo el colchón, madera de nogal.', 'price' => 3300, 'stock' => 4, 'material' => 'Madera de nogal', 'color' => 'Nogal', 'width_cm' => 160, 'height_cm' => 100, 'depth_cm' => 210],
            ['category' => 'dormitorios', 'sku' => 'DM-10', 'name' => 'Set de 2 veladores + cómoda', 'description' => 'Set de dormitorio: 2 veladores a juego más una cómoda, madera de cedro.', 'price' => 2950, 'stock' => 3, 'material' => 'Madera de cedro', 'color' => 'Café oscuro'],

            // ---- Oficina ----
            ['category' => 'oficina', 'sku' => 'OF-01', 'name' => 'Escritorio en L ejecutivo', 'description' => 'Escritorio ejecutivo en forma de L con cajonera integrada.', 'price' => 2100, 'stock' => 6, 'material' => 'Melamina y metal', 'color' => 'Negro', 'width_cm' => 160, 'height_cm' => 75, 'depth_cm' => 150],
            ['category' => 'oficina', 'sku' => 'OF-02', 'name' => 'Silla ejecutiva ergonómica', 'description' => 'Silla ejecutiva con soporte lumbar y reposabrazos ajustables.', 'price' => 980, 'stock' => 10, 'material' => 'Cuero sintético y metal', 'color' => 'Negro', 'width_cm' => 65, 'height_cm' => 115, 'depth_cm' => 65, 'featured' => true],
            ['category' => 'oficina', 'sku' => 'OF-03', 'name' => 'Librero de oficina 4 niveles', 'description' => 'Librero de oficina de 4 niveles para documentos y libros.', 'price' => 1150, 'stock' => 7, 'material' => 'Melamina', 'color' => 'Wengue', 'width_cm' => 90, 'height_cm' => 180, 'depth_cm' => 35],
            ['category' => 'oficina', 'sku' => 'OF-04', 'name' => 'Archivador metálico 3 gavetas', 'description' => 'Archivador metálico de 3 gavetas con cerradura de seguridad.', 'price' => 850, 'stock' => 9, 'material' => 'Metal', 'color' => 'Gris', 'width_cm' => 45, 'height_cm' => 100, 'depth_cm' => 60],
            ['category' => 'oficina', 'sku' => 'OF-05', 'name' => 'Escritorio compacto para PC', 'description' => 'Escritorio compacto ideal para espacios reducidos o home office.', 'price' => 750, 'stock' => 12, 'material' => 'MDF', 'color' => 'Blanco', 'width_cm' => 100, 'height_cm' => 75, 'depth_cm' => 55],
            ['category' => 'oficina', 'sku' => 'OF-06', 'name' => 'Silla de visita apilable', 'description' => 'Silla de visita apilable, estructura metálica y asiento tapizado.', 'price' => 380, 'stock' => 20, 'material' => 'Metal y tela', 'color' => 'Azul', 'width_cm' => 55, 'height_cm' => 85, 'depth_cm' => 55],
            ['category' => 'oficina', 'sku' => 'OF-07', 'name' => 'Mesa de juntas 6 personas', 'description' => 'Mesa de juntas para sala de reuniones, capacidad 6 personas.', 'price' => 3200, 'stock' => 3, 'material' => 'Melamina y metal', 'color' => 'Café', 'width_cm' => 200, 'height_cm' => 75, 'depth_cm' => 100],
            ['category' => 'oficina', 'sku' => 'OF-08', 'name' => 'Módulo de recepción', 'description' => 'Módulo de recepción con espacio de trabajo y fachada decorativa.', 'price' => 4200, 'stock' => 2, 'material' => 'Melamina', 'color' => 'Blanco y café', 'width_cm' => 180, 'height_cm' => 110, 'depth_cm' => 70],
            ['category' => 'oficina', 'sku' => 'OF-09', 'name' => 'Panel divisor de oficina', 'description' => 'Panel divisor acústico para separar espacios de trabajo.', 'price' => 650, 'stock' => 10, 'material' => 'Metal y tela', 'color' => 'Gris', 'width_cm' => 120, 'height_cm' => 150, 'depth_cm' => 4],
            ['category' => 'oficina', 'sku' => 'OF-10', 'name' => 'Silla reclinable estilo gamer', 'description' => 'Silla reclinable ergonómica, ideal para largas jornadas de trabajo.', 'price' => 1250, 'stock' => 8, 'material' => 'Cuero sintético y metal', 'color' => 'Rojo y negro', 'width_cm' => 70, 'height_cm' => 130, 'depth_cm' => 70],

            // ---- Exteriores ----
            ['category' => 'exteriores', 'sku' => 'EX-01', 'name' => 'Set de terraza 4 sillas + mesa aluminio', 'description' => 'Set de terraza con mesa y 4 sillas en aluminio y textilene.', 'price' => 3400, 'stock' => 4, 'material' => 'Aluminio y textilene', 'color' => 'Gris', 'width_cm' => 140, 'height_cm' => 75, 'depth_cm' => 80, 'featured' => true],
            ['category' => 'exteriores', 'sku' => 'EX-02', 'name' => 'Hamaca doble con base de madera', 'description' => 'Hamaca doble con base de madera de teca, ideal para jardín.', 'price' => 1900, 'stock' => 5, 'material' => 'Madera de teca y tela', 'color' => 'Natural', 'width_cm' => 200, 'height_cm' => 150, 'depth_cm' => 100],
            ['category' => 'exteriores', 'sku' => 'EX-03', 'name' => 'Juego de jardín 6 piezas ratán', 'description' => 'Juego de jardín de 6 piezas en ratán sintético resistente a la intemperie.', 'price' => 5200, 'stock' => 2, 'material' => 'Ratán sintético', 'color' => 'Café'],
            ['category' => 'exteriores', 'sku' => 'EX-04', 'name' => 'Mesa plegable para exterior', 'description' => 'Mesa plegable ligera en aluminio, fácil de guardar y transportar.', 'price' => 650, 'stock' => 9, 'material' => 'Aluminio', 'color' => 'Blanco', 'width_cm' => 120, 'height_cm' => 72, 'depth_cm' => 70],
            ['category' => 'exteriores', 'sku' => 'EX-05', 'name' => 'Sombrilla de terraza 3m', 'description' => 'Sombrilla de terraza de 3 metros con manivela, protección UV.', 'price' => 950, 'stock' => 6, 'material' => 'Aluminio y poliéster', 'color' => 'Beige', 'width_cm' => 300, 'height_cm' => 250, 'depth_cm' => 300],
            ['category' => 'exteriores', 'sku' => 'EX-06', 'name' => 'Set de 2 mecedoras de exterior', 'description' => 'Par de mecedoras en madera de teca tratada para exteriores.', 'price' => 1600, 'stock' => 5, 'material' => 'Madera de teca', 'color' => 'Natural', 'width_cm' => 70, 'height_cm' => 100, 'depth_cm' => 80],
            ['category' => 'exteriores', 'sku' => 'EX-07', 'name' => 'Banca de jardín 1.5m', 'description' => 'Banca de jardín de 1.5 metros, madera de pino tratada para intemperie.', 'price' => 1250, 'stock' => 6, 'material' => 'Madera de pino tratada', 'color' => 'Café', 'width_cm' => 150, 'height_cm' => 85, 'depth_cm' => 55],
            ['category' => 'exteriores', 'sku' => 'EX-08', 'name' => 'Chaise longue reclinable', 'description' => 'Chaise longue reclinable para piscina o terraza, aluminio y textilene.', 'price' => 1450, 'stock' => 7, 'material' => 'Aluminio y textilene', 'color' => 'Azul', 'width_cm' => 60, 'height_cm' => 40, 'depth_cm' => 190],
            ['category' => 'exteriores', 'sku' => 'EX-09', 'name' => 'Cojines para exterior set x4', 'description' => 'Set de 4 cojines impermeables para muebles de exterior.', 'price' => 480, 'stock' => 15, 'material' => 'Tela impermeable', 'color' => 'Terracota'],
            ['category' => 'exteriores', 'sku' => 'EX-10', 'name' => 'Kiosco desmontable 3x3m', 'description' => 'Kiosco/gazebo desmontable de 3x3 metros, estructura de acero y lona.', 'price' => 6800, 'stock' => 2, 'material' => 'Acero y lona', 'color' => 'Verde', 'width_cm' => 300, 'height_cm' => 300, 'depth_cm' => 250],
        ];
    }
}
