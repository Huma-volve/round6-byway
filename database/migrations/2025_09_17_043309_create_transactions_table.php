<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // صاحب العملية (instructor غالبًا)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // نوع العملية: student payment OR instructor withdrawal
            $table->enum('type', ['payment', 'withdrawal']);

            // المبلغ
            $table->decimal('amount', 10, 2);

            // الحالة
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');

            // ربط بطريقة الدفع
            $table->foreignId('payment_method_id')
                  ->nullable()
                  ->constrained('payment_methods')
                  ->nullOnDelete();

            // بيانات إضافية (course_id, paid_by, bank_info ...)
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
