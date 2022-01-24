<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_items', function (Blueprint $table) {
            $table->id();
            $table->string('action',64);
            $table->foreignId('action_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->foreignId('connect_instance_id')
                    ->nullable()
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->string('thing_table',64);
            $table->unsignedBigInteger('thing_id');
            $table->string('thing_column',64)->nullable();
            $table->string('update_initial_value')->nullable();
            $table->string('update_final_value')->nullable();
            $table->string('multistep_operation_hash',64)->nullable();
            $table->text('request_location')->nullable();
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
        Schema::dropIfExists('log_items');
    }
}
