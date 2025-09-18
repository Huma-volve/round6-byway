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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('payout_method_id')->constrained()->onDelete('cascade');
            $table->bigInteger('amount_cents'); // المبلغ بالسنتس
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('external_id')->nullable(); // ID جاي من البنك أو Stripe
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
