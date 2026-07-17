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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_id')->nullable()->unique();
            $table->string('stripe_invoice_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['succeeded', 'failed', 'pending', 'refunded']);
            $table->string('description', 500)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->string('receipt_url', 500)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('paid_at');
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
