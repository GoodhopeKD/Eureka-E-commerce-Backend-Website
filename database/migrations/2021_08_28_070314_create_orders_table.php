<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('reference',16)->unique();
            $table->foreignId('placer_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->timestamps();
            $table->enum('seller_table',['users','stores']);
            $table->unsignedBigInteger('seller_id');
            $table->foreignId('product_id')
                    ->nullable()
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('set null'); 
            $table->unsignedTinyInteger('product_count');
            $table->unsignedDecimal('delivery_fee', $precision = 10, $scale = 2);
            $table->timestamp('delivery_fee_set_datetime')->nullable();
            $table->timestamp('estimated_delivery_datetime')->nullable();
            $table->unsignedDecimal('amount_due_provisional', $precision = 11, $scale = 2);
            $table->string('discount_code',32)->nullable();
            $table->unsignedDecimal('discount_amount', $precision = 10, $scale = 2)->nullable();
            $table->unsignedDecimal('amount_due_final', $precision = 11, $scale = 2)->nullable();
            $table->enum('payment_method',['cash','post_cheque','post_transfer']);
            $table->timestamp('payment_made_datetime')->nullable();
            $table->timestamp('payment_confirmation_datetime')->nullable();
            $table->foreignId('intermediary_admin_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->enum('status',['placed','delivery_fee_set','payment_made','payment_confirmed','delivered','completed','cancelled'])->default('placed');
            $table->timestamp('delivered_datetime')->nullable();
            $table->timestamp('completed_datetime')->nullable();
            $table->boolean('visible_to_seller')->default(true);
            $table->boolean('visible_to_placer')->default(true);
            $table->boolean('visible_to_admin')->default(true);
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
        Schema::dropIfExists('orders');
    }
}
