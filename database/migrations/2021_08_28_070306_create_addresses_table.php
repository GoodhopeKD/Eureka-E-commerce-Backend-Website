<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('surname',32);
            $table->string('name_s',64);
            $table->tinyText('address_line_one');
            $table->tinyText('address_line_two')->nullable();
            $table->string('postal_code',6)->nullable();
            $table->string('commune',32);
            $table->string('wilaya',32);
            $table->enum('owner_table',['users','orders']);
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
        Schema::dropIfExists('addresses');
    }
}
