<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;

class CertificateService
{
    /**
     * Generate a certificate for a user who has completed a course, mint NFT, and notify.
     *
     * @param User $user
     * @param Course $course
     * @param int|float $score
     * @param int|null $quizResultId
     * @return Certificate
     */
    public function generateForCourse(User $user, Course $course, $score = 100, $quizResultId = null): Certificate
    {
        // Prevent duplicate certificates for same user & course
        $certificate = Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($certificate) {
            // Update quiz result if provided
            if ($quizResultId) {
                $certificate->update([
                    'quiz_result_id' => $quizResultId,
                    'score' => $score,
                    'issued_at' => now(),
                ]);
            }
        } else {
            // Generate a new certificate record
            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'quiz_result_id' => $quizResultId,
                'certificate_number' => Certificate::generateCertificateNumber(),
                'issued_at' => now(),
                'score' => $score,
            ]);
        }

        // Check if NFT minting is needed, but we defer it to frontend now.
        // The frontend will capture the HTML design and call mintNftFromImage.
        
        return $certificate;
    }

    /**
     * Mint the NFT using an externally generated base64 image (from frontend).
     */
    public function mintNftFromImage(Certificate $certificate, string $base64Image)
    {
        if ($certificate->nft_status !== 'pending') {
            throw new \Exception('Certificate NFT is already minted or in progress.');
        }

        try {
            $pinata = app(\App\Services\PinataService::class);
            $walletService = app(\App\Services\PublicWalletService::class);
            $user = $certificate->user;
            $course = $certificate->course;

            // 1. Decode base64 image and save temporarily
            $imageParts = explode(";base64,", $base64Image);
            $imageTypeAux = explode("image/", $imageParts[0]);
            $imageType = $imageTypeAux[1] ?? 'png';
            $imageBase64 = base64_decode($imageParts[1]);

            $dir = storage_path('app/public/certificates');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $fileName = 'certificate_' . $certificate->id . '_' . time() . '.' . $imageType;
            $tempPath = $dir . '/' . $fileName;
            file_put_contents($tempPath, $imageBase64);

            // 2. Upload Image to Pinata
            $imageIpfsUrl = $pinata->uploadFile($tempPath); // returns ipfs://Qm...
            $imageIpfsHash = str_replace('ipfs://', '', $imageIpfsUrl); // Save bare CID to DB

            // 3. Create and Upload Metadata to Pinata
            $metadata = [
                'name' => 'Certificate: ' . $course->title,
                'description' => 'Certificate of Completion for ' . $user->name . ' - ' . $course->title,
                'image' => $imageIpfsUrl,
                'attributes' => [
                    ['trait_type' => 'Course', 'value' => $course->title],
                    ['trait_type' => 'Certificate Number', 'value' => $certificate->certificate_number],
                    ['trait_type' => 'Score', 'value' => $certificate->score],
                    ['trait_type' => 'Issued At', 'value' => $certificate->issued_at->toDateTimeString()],
                ]
            ];
            $metadataIpfsUrl = $pinata->uploadJson($metadata);
            $metadataIpfsHash = str_replace('ipfs://', '', $metadataIpfsUrl); // Save bare CID to DB

            // 4. Mint the NFT
            $response = $walletService->mintBatchNft($user->id, $metadataIpfsUrl, 1, 0, 0);

            if (isset($response['success']) && !$response['success']) {
                throw new \Exception($response['error'] ?? 'Minting failed');
            }

            // Parse tx hash depending on the API response structure
            $txHash = null;
            $tokenId = null;

            if (isset($response['tickets']) && is_array($response['tickets']) && count($response['tickets']) > 0) {
                $ticket = $response['tickets'][0];
                $txHash = $ticket['tx_hash'] ?? null;
                $tokenId = $ticket['nft_token_id'] ?? null;
            } else {
                $resData = $response['result'] ?? ($response['data'] ?? $response);
                $txHash = $resData['hash'] ?? ($resData['tx_json']['hash'] ?? null);
                $tokenId = $resData['nftoken_id'] ?? ($resData['meta']['nftoken_id'] ?? null);
                if (!$tokenId && isset($resData['nftoken_ids']) && is_array($resData['nftoken_ids'])) {
                    $tokenId = $resData['nftoken_ids'][0] ?? null;
                }
            }

            // 5. Update the Database
            $certificate->update([
                'nft_status' => 'minted',
                'ipfs_image_hash' => $imageIpfsHash,
                'ipfs_metadata_hash' => $metadataIpfsHash,
                'nft_tx_hash' => $txHash,
                'nft_token_id' => $tokenId,
            ]);

            // Cleanup local file
            @unlink($tempPath);

            // 6. Send Email Notification
            $this->sendCertificateEmail($user, $certificate, $course, $txHash, $tokenId);

            return $certificate;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Certificate NFT Minting Failed: ' . $e->getMessage());
            $certificate->update(['nft_status' => 'failed']);
            throw $e;
        }
    }

    protected function sendCertificateEmail(User $user, Certificate $certificate, Course $course, $txHash = null, $tokenId = null)
    {
        $template = \App\Models\EmailTemplate::where('type', 'certificate_issued')->where('is_active', true)->first();
        
        if ($template) {
            $ipfsGatewayUrl = $certificate->ipfs_image_url;
            
            $explorerBaseUrl = str_replace('/account', '', rtrim(config('services.xrpl.explorer_url', 'https://testnet.xrpl.org'), '/'));
            $explorerUrl = $tokenId ? "{$explorerBaseUrl}/nft/{$tokenId}" : '';

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\DynamicEmail($template, [
                'user_name' => $user->name,
                'course_name' => $course->title,
                'certificate_number' => $certificate->certificate_number,
                'score' => $certificate->score,
                'certificate_image_url' => $ipfsGatewayUrl,
                'nft_tx_hash' => $txHash ?? 'N/A',
                'nft_token_id' => $tokenId ?? 'N/A',
                'xrpl_explorer_link' => $explorerUrl,
            ]));
        }
    }
}
