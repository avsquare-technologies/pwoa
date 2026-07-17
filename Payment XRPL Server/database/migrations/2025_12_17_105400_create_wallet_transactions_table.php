<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'from_user_id')->nullable();
            $table->string(column: 'to_user_id')->nullable();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('tx_hash')->nullable()->index();
            $table->string('source')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('amount', 20, 6)->default(0);
            $table->string('type', 20)->nullable();
            $table->string('status', 20)->default('pending');
            $table->json('response')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
