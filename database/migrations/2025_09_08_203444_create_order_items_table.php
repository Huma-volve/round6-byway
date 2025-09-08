<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('purchasable_type'); // e.g. Course
            $table->unsignedBigInteger('purchasable_id');

            $table->integer('unit_price_cents');
            $table->integer('quantity')->default(1);
            $table->integer('total_cents');

            $table->timestamps();

            $table->unique(['order_id', 'purchasable_type', 'purchasable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
