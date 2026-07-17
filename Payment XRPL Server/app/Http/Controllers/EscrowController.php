<?php

namespace App\Http\Controllers;


use App\Services\EscrowService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EscrowController extends Controller
{
    public function create(Request $request, EscrowService $escrowService)
    {
        try {
            $data = $request->validate([
                'order_id' => 'required',
                'source' => 'required|string',
                'destination' => 'required|string',
                'amount' => 'required|numeric|min:0.000001',
                'amount_usd' => 'required|numeric|min:0.01',
                'expires_at' => 'required|date',
            ]);
            Log::info('create', $data);


            $result = $escrowService->createEscrow(
                $data['order_id'],
                $data['source'],
                $data['destination'],
                $data['amount'],
                (float) $data['amount_usd'],
                $data['expires_at']
            );

            return response()->json($result, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Escrow creation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    public function finish(Request $request, EscrowService $escrowService)
    {
        $data = $request->validate([
            'order_id' => 'required',
        ]);

        Log::info('finish', $data);

        $orderId = $data['order_id'];

        try {
            $result = $escrowService->finishEscrow($orderId);

            return response()->json($result, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Escrow not found',
            ], 404);

        } catch (\RuntimeException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (\Throwable $e) {
            Log::error('Escrow finish failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to finish escrow',
            ], 500);
        }
    }



    public function cancel(Request $request, EscrowService $escrowService)
    {
        $data = $request->validate([
            'order_id' => 'required',
        ]);

        Log::info('cancel', $data);

        $orderId = $data['order_id'];
        try {
            $result = $escrowService->cancelEscrow($orderId);

            return response()->json($result, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Escrow not found',
            ], 404);

        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);

        } catch (\Throwable $e) {
            Log::error('Escrow cancel failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel escrow',
            ], 500);
        }
    }


}
