<?php

use App\Http\Controllers\CollabathonTxController;
use App\Http\Controllers\EscrowController;
use App\Http\Controllers\NftController;
use App\Http\Controllers\PrivateSignController;
use App\Http\Controllers\TokenWorkflowController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test-private', function (Request $request) {
    return response()->json([
        'status' => 'private-ok',
        'time' => now()->toDateTimeString(),
    ]);
});

// Route::middleware('private.access')->group(function () {
    Route::get('/test', [PrivateSignController::class, 'test']);
    Route::post('/sign', [PrivateSignController::class, 'sign']);

    Route::prefix('wallet')->group(function () {
        Route::post('/create', [WalletController::class, 'create']);
        Route::post('/balance', [WalletController::class, 'balance']);
        Route::post('/transactions', [WalletController::class, 'transactions']);
        Route::post('/send', [WalletController::class, 'sendPayment']);
        Route::post('/add-fund', [WalletController::class, 'addFund']);
        Route::post('/send-xrp', [WalletController::class, 'sendXRP']);
        Route::post('/withdraw', [WalletController::class, 'withdraw']);
        Route::post('/sweep-wallet', [WalletController::class, 'sweepWallet']);

        Route::post('/system-wallet/{type}', [WalletController::class, 'getSystemWalletAddress']);
        Route::post('/stream-payout', [WalletController::class, 'streamPayout']);

        Route::post('/send-user-external', [WalletController::class, 'sendUserExternal']);
    });

    Route::prefix('escrow')->group(function () {
        Route::post('/create', [EscrowController::class, 'create']);
        Route::post('/finish', [EscrowController::class, 'finish']);
        Route::post('/cancel', [EscrowController::class, 'cancel']);
    });

    Route::prefix('nft')->group(function () {
        Route::post('/mint', [NftController::class, 'mint']);
        Route::post('/buy', [NftController::class, 'buy']);
        Route::post('/batch-mint', [NftController::class, 'batchMint']);
        Route::get('/batch-status/{address}', [NftController::class, 'getBatchStatus']);
        Route::post('/sync-ids', [NftController::class, 'syncIds']);
        Route::post('sell-offer', [NftController::class, 'createSellOffer']);
        Route::post('burn', [NftController::class, 'burn']);
    });

    Route::prefix('collabathon')->group(function () {
        Route::post('/payment', [CollabathonTxController::class, 'payment']);
        Route::post('/payout-payment', [CollabathonTxController::class, 'payoutPayment']);
        Route::post('/buy-ticket', [CollabathonTxController::class, 'buyTicket']);
    });

    // Route::post('/issue-token', [TokenIssueController::class, 'issue']);
    Route::post('/issue-token', [TokenWorkflowController::class, 'runFullFlow']);
// });
