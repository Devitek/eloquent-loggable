<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLogEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ext_log_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action', 8);
            $table->dateTime('logged_at');
            $table->integer('loggable_id');
            $table->string('loggable_type');
            $table->integer('version');
            $table->string('reason')->nullable(true);
            $table->json('data')->nullable(true);
            $table->string('user_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ext_log_entries');
    }
}
