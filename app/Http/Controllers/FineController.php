<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Fine::with(['user', 'loan']);

        if ($request->user()->isPatron()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * Display the specified resource.
     */
    public function show(Fine $fine)
    {
        return response()->json($fine->load(['user', 'loan']));
    }

    /**
     * Mark fine as paid
     */
    public function markPaid(Request $request, Fine $fine): JsonResponse
    {
        $request->validate([
            'paid_date' => 'sometimes|date',
        ]);

        $fine->update([
            'status' => 'paid',
            'paid_date' => $request->input('paid_date', now()),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fine marked as paid',
            'fine' => $fine,
        ]);
    }

    /**
     * Waive a fine
     */
    public function waive(Fine $fine): JsonResponse
    {
        $fine->update([
            'status' => 'waived',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fine waived',
            'fine' => $fine,
        ]);
    }
}
