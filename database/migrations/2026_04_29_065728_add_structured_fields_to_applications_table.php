<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('has_other_pets')->nullable()->after('adopter_message');
            $table->string('housing_type')->nullable()->after('has_other_pets');
            $table->string('landlord_allows_pets')->nullable()->after('housing_type');
            $table->string('hours_alone')->nullable()->after('landlord_allows_pets');
            $table->string('has_outdoor_space')->nullable()->after('hours_alone');
            $table->string('previous_pet_experience')->nullable()->after('has_outdoor_space');
            $table->text('why_this_pet')->nullable()->after('previous_pet_experience');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'has_other_pets',
                'housing_type',
                'landlord_allows_pets',
                'hours_alone',
                'has_outdoor_space',
                'previous_pet_experience',
                'why_this_pet',
            ]);
        });
    }
};