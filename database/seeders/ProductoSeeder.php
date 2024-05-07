<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Producto;
use App\Models\Categoria;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->getProducts();
        $this->getDetailsProductos();
    }

    private function getProductos()
    {
        // Obtener las categorías que no tienen una categoría padre
        $categorias = Categoria::all();

        foreach ($categorias as $categoria) {
            // Obtener la API ID de la categoría actual
            $apiId = $categoria->api_id;

            // Obtener los datos de la API para la categoría actual
            $response = Http::get("https://tienda.mercadona.es/api/categorias/{$apiId}");

            // Verificar si la solicitud fue exitosa
            if ($response->ok()) {
                $data = $response->json();

                // Iterar sobre los productos de la categoría
                foreach ($data['categorias'] as $categoriaAPI) {
                    $categoria_id = $categoriaAPI['id'];

                    foreach ($categoriaAPI['productos'] as $productoAPI) {
                        // Verificar si la categoría existe
                        $categoria = Categoria::where('api_id', $categoria_id)->first();

                        // Si la categoría no existe, puedes decidir qué hacer, por ejemplo, omitir este producto
                        if (!$categoria) {
                            continue; // Opcional: saltar este producto y continuar con el siguiente
                        }

                        // Crear un nuevo producto en la base de datos
                        Producto::create([
                            'api_id_producto' => $productoAPI['id'],
                            'categoria_id' => $categoria_id,

                            'thumbnail' => $productoAPI['thumbnail'],
                            'display_name' => $productoAPI['display_name'],
                            'iva' => $productoAPI['price_instructions']['iva'],
                            'unit_price' => $productoAPI['price_instructions']['unit_price'],
                        ]);
                    }
                }
            }
        }
    }

    private function getDetailsProductos()
    {
        $productos = Producto::all();

        foreach ($productos as $producto) {
            $apiId = $producto->api_id_producto;

            $response = Http::get("https://tienda.mercadona.es/api/productos/{$apiId}");
            if ($response->ok()) {
                $data = $response->json();
                $product->update([
                    'slug' => $data['slug'],
                    'share_url' => $data['share_url'],
                    'brand' => $data['brand'],
                    'origin' => $data['origin'],
                ]);
            }
        }
    }
}
