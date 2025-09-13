<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CashSessionController extends Controller
{
    /**
     * Open a new cash session
     */
    public function open(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'opening_cash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // Check if user has an open session
        $existingSession = CashSession::where('cashier_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if ($existingSession) {
            return response()->json([
                'error' => 'You already have an open cash session'
            ], 400);
        }

        $session = CashSession::create([
            'outlet_id' => $user->outlet_id,
            'cashier_id' => $user->id,
            'opened_at' => Carbon::now(),
            'opening_cash' => $request->opening_cash,
            'status' => 'OPEN',
        ]);

        return response()->json([
            'session_id' => $session->id,
            'opened_at' => $session->opened_at,
            'opening_cash' => $session->opening_cash,
        ], 201);
    }

    /**
     * Close a cash session
     */
    public function close(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'closing_cash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $session = CashSession::where('cashier_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if (!$session) {
            return response()->json([
                'error' => 'No open cash session found'
            ], 400);
        }

        // Calculate expected cash from CASH payments in this session
        $cashPayments = Payment::whereHas('sale', function ($query) use ($session, $user) {
                $query->where('cashier_id', $user->id)
                      ->where('sold_at', '>=', $session->opened_at);
            })
            ->where('method', 'CASH')
            ->sum('amount');

        $expectedCash = $session->opening_cash + $cashPayments;
        $closingCash = $request->closing_cash;
        $variance = $closingCash - $expectedCash;

        $session->update([
            'closed_at' => Carbon::now(),
            'closing_cash' => $closingCash,
            'expected_cash' => $expectedCash,
            'variance' => $variance,
            'status' => 'CLOSED',
        ]);

        return response()->json([
            'session_id' => $session->id,
            'closed_at' => $session->closed_at,
            'opening_cash' => $session->opening_cash,
            'closing_cash' => $session->closing_cash,
            'expected_cash' => $session->expected_cash,
            'variance' => $session->variance,
        ]);
    }

    /**
     * Get active cash session
     */
    public function active(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $session = CashSession::where('cashier_id', $user->id)
            ->where('status', 'OPEN')
            ->first();

        if (!$session) {
            return response()->json([
                'error' => 'No active cash session found'
            ], 404);
        }

        return response()->json([
            'session_id' => $session->id,
            'opened_at' => $session->opened_at,
            'opening_cash' => $session->opening_cash,
            'status' => $session->status,
        ]);
    }
}
