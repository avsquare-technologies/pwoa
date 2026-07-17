<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->string('order_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->change();
        });
    }
};