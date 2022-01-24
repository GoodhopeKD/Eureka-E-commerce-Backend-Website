<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_user_id')
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('followed_store_id')
                    ->constrained('stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->timestamps(); // followed_datetime
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow_instances');
    }
}
