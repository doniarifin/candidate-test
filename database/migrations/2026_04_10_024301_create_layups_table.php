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
        Schema::create('layups', function (Blueprint $table) {
            $table->id()->primary();;

            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();

            $table->string('code'); 
            $table->string('name');

            $table->string('grade')->nullable(); 
            $table->string('revision')->nullable();

            $table->enum('status', ['active', 'draft', 'archived'])
                ->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layups');
    }
};
