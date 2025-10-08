<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCustomersUniqueConstraintsWithSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop existing unique constraints
            $table->dropUnique(['mobile']);
            $table->dropUnique(['phone']);
            $table->dropUnique(['national_code']);
            $table->dropUnique(['economic_code']);
        });

        Schema::table('customers', function (Blueprint $table) {
            // Add new composite unique constraints that include deleted_at
            // This allows multiple soft-deleted records with same values
            $table->unique(['mobile', 'deleted_at'], 'customers_mobile_deleted_at_unique');
            $table->unique(['phone', 'deleted_at'], 'customers_phone_deleted_at_unique');
            $table->unique(['national_code', 'deleted_at'], 'customers_national_code_deleted_at_unique');
            $table->unique(['economic_code', 'deleted_at'], 'customers_economic_code_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop the composite unique constraints
            $table->dropUnique('customers_mobile_deleted_at_unique');
            $table->dropUnique('customers_phone_deleted_at_unique');
            $table->dropUnique('customers_national_code_deleted_at_unique');
            $table->dropUnique('customers_economic_code_deleted_at_unique');
        });

        Schema::table('customers', function (Blueprint $table) {
            // Restore original unique constraints
            $table->unique('mobile');
            $table->unique('phone');
            $table->unique('national_code');
            $table->unique('economic_code');
        });
    }
}
