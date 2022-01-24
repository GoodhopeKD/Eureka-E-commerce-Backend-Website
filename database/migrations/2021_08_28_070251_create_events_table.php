<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('reference',16)->unique();
            $table->string('title',64);
            $table->text('description');
            $table->string('venue',64);
            $table->text('contact_details')->nullable();
            $table->text('other_details')->nullable();
            $table->timestamp('event_datetime')->nullable();
            $table->string('utc_offset',8)->nullable();
            $table->foreignId('adder_admin_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->timestamps(); // added_datetime
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
