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
        Schema::create('collabathon_nfts', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->index();
            $table->string('creator_address');
            $table->integer('ticket_index'); // 1, 2, 3...
            $table->string('ticket_sequence'); // The Ledger Ticket Seq
            $table->string('tx_hash')->nullable();
            $table->string('nft_token_id')->nullable(); // Filled later via sync
            $table->string('status')->default('submitted'); // submitted, confirmed, failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collabathon_nfts');
    }
};
