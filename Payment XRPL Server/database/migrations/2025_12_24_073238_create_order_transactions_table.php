<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');
            $table->string('tx_hash')->nullable()->index();
            $table->string('source');
            $table->string('destination');
            $table->decimal('amount', 20, 6);
            $table->string('currency')->default('FEE');
            $table->string('type')->default('payment');
            $table->string('status')->default('pending');
            $table->json('response')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_transactions');
    }
};
