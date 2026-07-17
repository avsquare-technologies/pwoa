<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_escrows', function (Blueprint $table) {
            $table->id();

            $table->string('buyer_address');
            $table->string('seller_address');

            $table->decimal('amount', 20, 6);
            $table->bigInteger('order_id');

            // XRPL-related
            $table->string('xrpl_account')->nullable();
            $table->string('condition')->nullable();
            $table->string('condition_secret')->nullable();
            $table->unsignedBigInteger('escrow_sequence')->nullable();
            $table->string('tx_hash')->nullable();

            // State
            $table->string('status')->index();
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index(['buyer_address', 'seller_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_escrows');
    }
};
