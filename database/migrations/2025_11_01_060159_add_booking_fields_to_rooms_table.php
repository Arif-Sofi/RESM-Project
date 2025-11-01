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
        Schema::table('rooms', function (Blueprint $table) {
            $table->integer('capacity')->default(20)->after('description');
            $table->string('building')->nullable()->after('capacity');
            $table->integer('floor')->nullable()->after('building');
            $table->boolean('has_projector')->default(false)->after('floor');
            $table->boolean('has_whiteboard')->default(false)->after('has_projector');
            $table->text('equipment')->nullable()->after('has_whiteboard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'capacity',
                'building',
                'floor',
                'has_projector',
                'has_whiteboard',
                'equipment',
            ]);
        });
    }
};
