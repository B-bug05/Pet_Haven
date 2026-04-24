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
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('pet_id')->constrained()->onDelete('cascade');
                
                // UPDATED: New Application Status Wording
                $table->enum('status', [
                    'Under Review', 
                    'Approved for Adoption', 
                    'Application Declined'
                ])->default('Under Review');
                
                // Adopter provides this when they apply
                $table->text('adopter_address'); 
                $table->string('contact_number'); 
                $table->text('adopter_message')->nullable();
                
                $table->foreignId('reviewed_by')->nullable()->constrained('users');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('applications');
        }
    };
