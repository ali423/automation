<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('previous_activity_id')->nullable();
            $table->integer('record_change_id');
            $table->string('record_change_type');
            $table->string('relation_name')->nullable();
            $table->string('relation_model')->nullable();
            $table->enum('action',['create','update','delete','sync','attach','detach','pivot_update']);
            $table->json('data')->nullable();
            $table->json('pivot_data')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
