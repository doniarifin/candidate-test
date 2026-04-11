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
        Schema::create('layers', function (Blueprint $table) {
            $table->id()->primary();;

            $table->foreignId('layup_id')
                ->constrained('layups')
                ->cascadeOnDelete();

            $table->integer('layer_order');

            $table->decimal('thickness', 8, 2);
            $table->decimal('width', 8, 2);
            $table->decimal('angle', 5, 2); 
            $table->string('grade')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layers');
    }
};
