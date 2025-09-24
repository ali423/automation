<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('status');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->string('vehicle_type')->nullable()->after('driver_phone');
            $table->string('plate_serial')->nullable()->after('vehicle_type');
            $table->string('plate_number')->nullable()->after('plate_serial');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('withdrawal_request_id')->nullable()->after('status');
            $table->foreign('withdrawal_request_id')
                ->references('id')
                ->on('withdrawal_requests')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['withdrawal_request_id']);
            $table->dropColumn('withdrawal_request_id');
        });

        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['driver_name', 'driver_phone', 'vehicle_type', 'plate_serial', 'plate_number']);
        });
    }
};


