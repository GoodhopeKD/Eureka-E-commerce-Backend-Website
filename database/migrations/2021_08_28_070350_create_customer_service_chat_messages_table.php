<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerServiceChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_service_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->text('message_body');
            $table->foreignId('chat_id')
                    ->constrained('customer_service_chats')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreignId('sender_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->timestamps(); // sent_datetime
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_service_chat_messages');
    }
}
