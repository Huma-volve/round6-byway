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
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_profile_id')->constrained()->onDelete('cascade');
            $table->enum('provider',['Paypal','Bank Transfer','Fawry'])->default('Bank Transfer'); // bank_transfer
            $table->string('account_name');
            $table->string('account_number');
            $table->enum('bank_name',['CIB','Al-Ahly','Bank of Egypt']);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payout_methods');
    }
};
