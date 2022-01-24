<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('reference',16)->unique();
            $table->string('name',64);
            $table->unsignedDecimal('price', $precision = 11, $scale = 2);
            $table->text('details');
            $table->string('commune',32);
            $table->string('wilaya',32);
            $table->unsignedTinyInteger('stock_available')->nullable();
            $table->foreignId('category_id')
                    ->nullable()
                    ->constrained('product_categories')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->enum('seller_table',['users','stores']);
            $table->unsignedBigInteger('seller_id');
            $table->enum('entry_type',['product','service','product_and_or_service'])->default('product_and_or_service');
            $table->boolean('is_seller_pinned')->default(false);
            $table->timestamps(); // added_datetime
            $table->foreignId('adder_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->string('condition',32);
            $table->enum('status',['pending_confirmation','available','unavailable','suspended']);
            $table->foreignId('intermediary_admin_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            $table->timestamp('confirmation_datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
