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
        Schema::create("ticket_batches", function (Blueprint $table) {
            $table->id();
            $table->foreignId("event_id")->constrained()->cascadeOnDelete();
            $table->bigInteger("creator_id")->nullable()->index();
            $table->uuid("batch_id")->unique();
            $table->integer("total")->default(0);
            $table->integer("minted")->default(0);
            $table->integer("failed")->default(0);
            $table->integer("next_index")->default(1);
            $table->string("metadata_uri")->nullable();
            $table->float("price")->default(0);
            $table->string("status")->default("queued")->index();
            $table->text("error")->nullable();
            $table->timestamp("last_heartbeat")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_batches');
    }
};
