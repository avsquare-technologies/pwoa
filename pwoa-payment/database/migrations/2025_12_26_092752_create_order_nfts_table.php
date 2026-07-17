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
        Schema::create('order_nfts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('nft_db_id');
            $table->string('nft_token_id', 64);
            $table->bigInteger('order_id');
            $table->string('buyer_address');
            $table->bigInteger('buyer_id')->nullable();
            $table->string('seller_address');
            $table->bigInteger('seller_id')->nullable();
            $table->string('amount');
            $table->string('tx_hash');
            $table->string('status')->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_nfts');
    }
};
