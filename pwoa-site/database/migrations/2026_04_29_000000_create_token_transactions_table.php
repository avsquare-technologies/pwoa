<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 20, 8);
            $table->string('currency')->default('WASH');
            $table->string('destination_address')->index();
            $table->string('tx_hash')->nullable()->index();
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_transactions');
    }
};
