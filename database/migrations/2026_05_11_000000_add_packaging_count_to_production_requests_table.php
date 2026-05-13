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
        Schema::table('production_requests', function (Blueprint $table) {
            $table->unsignedInteger('packaging_count')->nullable()->after('production_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            $table->dropColumn('packaging_count');
        });
    }
};
