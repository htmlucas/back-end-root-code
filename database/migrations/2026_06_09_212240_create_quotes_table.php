<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('destino');
            $table->integer('dias_cobrados')->default(0);
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->json('viajantes');
            $table->json('avisos')->nullable();
            $table->decimal('desconto_grupo_percentual', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
