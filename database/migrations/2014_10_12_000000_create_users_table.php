<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_datetime')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('surname',32);
            $table->string('name_s',64);
            $table->string('username', 32)->unique()->nullable();
            $table->timestamps();  // signup_datetime
            $table->enum('gender',['male','female'])->nullable();
            $table->enum('account_level',['normal','admin','super_admin'])->default('normal');
            $table->enum('account_status',['active','suspended','deactivated','deleted'])->default('active');
            $table->enum('account_type',['normal','store_owner','seller'])->default('normal');
            $table->timestamp('account_verified_datetime')->nullable();
            $table->unsignedTinyInteger('loyalty_points')->default(0);
            $table->string('referral_code', 32)->unique()->nullable();
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
        Schema::dropIfExists('users');
    }
}
