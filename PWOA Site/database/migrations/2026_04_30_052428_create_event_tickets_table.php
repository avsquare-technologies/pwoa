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
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('batch_id')->nullable()->index();
            $table->integer('ticket_seq')->nullable();
            $table->integer('ticket_number')->index();
            $table->string('owner_wallet_address')->nullable();
            $table->string('nft_token_id')->nullable()->index();
            $table->string('tx_hash')->nullable();
            $table->string('status')->default('minting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tickets');
    }
};
