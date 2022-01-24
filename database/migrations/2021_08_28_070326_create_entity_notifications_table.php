<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('message_title',32);
            $table->string('message_body',128);
            $table->timestamps(); // created_datetime
            $table->timestamp('opened_datetime')->nullable();
            $table->enum('entity_table',['users','admins','stores']);
            $table->unsignedBigInteger('entity_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_notifications');
    }
}
