<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kaijus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category', 20);
            $table->smallInteger('threat_level');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        DB::statement(
            "ALTER TABLE kaijus ADD CONSTRAINT kaijus_category_check
            CHECK (category IN ('aquatic', 'terrestrial', 'aerial', 'amphibious', 'unknown'))"
        );

        DB::statement(
            'ALTER TABLE kaijus ADD CONSTRAINT kaijus_threat_level_check
            CHECK (threat_level BETWEEN 1 AND 5)'
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kaijus');
    }
};
