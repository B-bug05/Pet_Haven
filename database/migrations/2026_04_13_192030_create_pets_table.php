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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); 
            $table->string('breed')->nullable();
            $table->string('age')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            
            // UPDATED: New Pet Status Wording
            $table->enum('status', [
                'Ready for Adoption', 
                'Under Review', 
                'Found a Home', 
                'No Longer Available'
            ])->default('Ready for Adoption');
            
            $table->text('health_summary')->nullable(); // Our "Other Medical Issues"
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
