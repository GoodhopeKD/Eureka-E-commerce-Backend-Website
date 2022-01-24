<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connect_instances', function (Blueprint $table) {
            $table->id();
            $table->string('app_access_token',64)->unique();
            $table->foreignId('user_id')
                    ->nullable()
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->timestamp('signin_datetime')->nullable();
            $table->timestamp('last_active_datetime')->useCurrent();
            $table->timestamp('signout_datetime')->nullable();
            $table->timestamps();
            $table->string('utc_offset',8)->nullable();
            $table->enum('status',['empty','active','ended'])->default('empty');
            $table->text('device_info');
            $table->text('agent_app_info');
            $table->text('request_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connect_instances');
    }
}
