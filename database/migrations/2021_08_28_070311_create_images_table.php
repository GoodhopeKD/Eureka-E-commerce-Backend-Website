<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('name',32);
            $table->string('type',12);
            $table->string('uri',255);
            $table->unsignedSmallInteger('height')->nullable();
            $table->unsignedSmallInteger('width')->nullable();
            $table->string('title',64);
            $table->string('alt',255);
            $table->enum('tag',['profile_image','store_banner','product_image','event_poster','post_receipt']);
            $table->enum('owner_table',['users','stores','products','events']);
            $table->unsignedBigInteger('owner_id');
            $table->foreignId('adder_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
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
        Schema::dropIfExists('images');
    }
}
