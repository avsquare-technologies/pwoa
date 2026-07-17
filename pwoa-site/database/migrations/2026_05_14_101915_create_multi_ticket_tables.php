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
        // 1. Create Ticket Orders Table
        Schema::create('ticket_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->decimal('total_amount', 12, 2);
            $table->string('currency')->default('WASH');
            $table->string('status')->default('pending'); 
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 2. Create Ticket Transfers Table
        Schema::create('ticket_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('order_id')->constrained('ticket_orders')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained('event_tickets');
            $table->string('nft_token_id')->nullable();
            $table->string('tx_hash')->nullable()->index();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // 3. Add reservation columns to event_tickets
        Schema::table('event_tickets', function (Blueprint $table) {
            $table->uuid('order_id')->nullable()->index()->after('batch_id');
            $table->timestamp('reserved_at')->nullable()->after('status');
            $table->foreignId('reserved_by_user_id')->nullable()->constrained('users')->after('reserved_at');
            $table->foreignId('user_id')->nullable()->change(); // Ensure user_id can be null if not already
        });

        // 4. Add order_id to event_attendees for tracking
        Schema::table('event_attendees', function (Blueprint $table) {
            $table->uuid('order_id')->nullable()->index()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_attendees', function (Blueprint $table) {
            $table->dropColumn('order_id');
        });

        Schema::table('event_tickets', function (Blueprint $table) {
            $table->dropForeign(['reserved_by_user_id']);
            $table->dropColumn(['order_id', 'reserved_at', 'reserved_by_user_id']);
        });

        Schema::dropIfExists('ticket_transfers');
        Schema::dropIfExists('ticket_orders');
    }
};
