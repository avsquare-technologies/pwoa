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
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('nft_status')->default('pending')->after('issued_at');
            $table->string('nft_token_id')->nullable()->after('nft_status');
            $table->string('nft_tx_hash')->nullable()->after('nft_token_id');
            $table->string('ipfs_image_hash')->nullable()->after('nft_tx_hash');
            $table->string('ipfs_metadata_hash')->nullable()->after('ipfs_image_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn([
                'nft_status',
                'nft_token_id',
                'nft_tx_hash',
                'ipfs_image_hash',
                'ipfs_metadata_hash'
            ]);
        });
    }
};
