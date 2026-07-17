<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'quiz_result_id',
        'certificate_number',
        'score',
        'issued_at',
        'nft_status',
        'nft_token_id',
        'nft_tx_hash',
        'ipfs_image_hash',
        'ipfs_metadata_hash',
    ];
    

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quizResult(): BelongsTo
    {
        return $this->belongsTo(QuizResult::class);
    }

    /**
     * Get the IPFS Image URL.
     */
    protected function ipfsImageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->ipfs_image_hash) {
                    return null;
                }
                
                $hash = str_replace('ipfs://', '', $this->ipfs_image_hash);
                
                return 'https://gateway.pinata.cloud/ipfs/' . $hash;
            }
        );
    }

    /**
     * Get the IPFS Metadata URL.
     */
    protected function ipfsMetadataUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->ipfs_metadata_hash) {
                    return null;
                }
                
                $hash = str_replace('ipfs://', '', $this->ipfs_metadata_hash);
                
                return 'https://gateway.pinata.cloud/ipfs/' . $hash;
            }
        );
    }

    /**
     * Generate a unique certificate number.
     */
    public static function generateCertificateNumber(): string
    {
        return 'PWOA-CERT-' . strtoupper(bin2hex(random_bytes(6)));
    }
}
