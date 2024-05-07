<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // URL del endpoint
        $url = 'https://tienda.mercadona.es/api/categorias/';

        // Realizar la solicitud HTTP para obtener los datos del endpoint
        $response = Http::get($url);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            $categoriasData = $response->json()['results'];

            // Recorrer los datos de las categorías y guardarlos en la base de datos
            foreach ($categoriasData as $categoriaData) {
                $this->saveCategory($categoriaData);
            }

            $this->command->info('Se importaron correctamente las categorías.');
        } else {
            $this->command->error('Error al importar las categorías.');
        }
    }

    private function saveCategory($categoriaData, $parentId = null)
    {
        // Crear una nueva categoría en la base de datos
        $categoria = new Category();
        $categoria->name = $categoriaData['name'];
        // Añadir el ID de la categoría en la API si está disponible
        if (isset($categoriaData['id'])) {
            $categoria->api_id = $categoriaData['id'];
        }
        // Establecer el ID del padre si está disponible
        if ($parentId !== null) {
            $categoria->parent_id = $parentId;
        }
        $categoria->save();

        // Recorrer las subcategorías si están presentes
        if (isset($categoriaData['categorias'])) {
            foreach ($categoriaData['categorias'] as $subCategoriaData) {
                $this->saveCategory($subCategoriaData, $categoria->id);
            }
        }
    }
}
