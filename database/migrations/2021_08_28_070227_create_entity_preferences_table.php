<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntityPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entity_preferences', function (Blueprint $table) {
            $table->id();
            $table->enum('entity_table',['users','admins','stores']);
            $table->unsignedBigInteger('entity_id');
            $table->string('key',16);
            $table->string('value',32);
            $table->timestamps(); // updated_datetime
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_preferences');
    }
}
