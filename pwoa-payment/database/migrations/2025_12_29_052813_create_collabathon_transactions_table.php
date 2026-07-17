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
        Schema::create('collabathon_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('collabathon_id')->nullable(); // Link to Collabathon ID
            $table->string('type'); // 'FUND', 'PRIZE', 'BUY_TICKET'
            $table->string('tx_hash')->nullable();
            $table->string('source_address');
            $table->string('destination_address');
            $table->decimal('amount', 20, 6); // XRP Amount
            $table->string('nft_token_id')->nullable(); // Only for Buy Ticket
            $table->string('status')->default('pending'); // pending, success, failed
            $table->json('response')->nullable(); // Full Ledger Response
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collabathon_transactions');
    }
};
