<?php

use App\Models\Kaiju;
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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('location');
            $table->string('status', 20);
            $table->timestamp('occurred_at');
            $table->foreignIdFor(Kaiju::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        DB::statement(
            "ALTER TABLE incidents ADD CONSTRAINT incidents_status_check
            CHECK (status IN ('open', 'contained', 'closed'))"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
