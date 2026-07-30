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
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('source')->nullable();
            $table->string('external_event_id')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->decimal('magnitude', 4, 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('depth', 10, 3)->nullable();

            $table->unique(['source', 'external_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropUnique(['source', 'external_event_id']);
            $table->dropColumn([
                'source',
                'external_event_id',
                'external_url',
                'magnitude',
                'latitude',
                'longitude',
                'depth',
            ]);
        });
    }
};
