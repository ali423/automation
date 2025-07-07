<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdToCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->foreignId('unit_id')
            ->nullable()  // Crucial for set null behavior
            ->after('warning_limit')  // Optional: position in table
            ->constrained('units')  // References units table
            ->nullOnDelete();  // Use nullOnDelete() instead of onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
}
