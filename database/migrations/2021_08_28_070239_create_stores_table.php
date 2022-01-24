<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name',64);
            $table->string('username', 32)->unique()->nullable();
            $table->enum('status',['active','suspended','deactivated','deleted']);
            $table->string('description');
            $table->string('commune',32);
            $table->string('wilaya',32);
            $table->foreignId('owner_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->foreignId('creator_admin_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0); // Deleted with timeout
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
