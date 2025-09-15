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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->string('provider', 50)->default('stripe'); // stripe, paypal, paymob...
            $table->string('method', 50)->default('card');   // card, wallet, kiosk

            $table->integer('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['initiated', 'requires_action', 'succeeded', 'failed', 'canceled', 'refunded'])->default('initiated');

            $table->string('external_id', 191)->index(); // gateway payment/intent id
            $table->string('error_code', 100)->nullable();
            $table->text('error_message')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
