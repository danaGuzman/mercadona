<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('api_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            // Llave foránea para la relación de auto-referencia
            $table->foreign('parent_id')->references('id')->on('categorias')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categorias');
    }
};
