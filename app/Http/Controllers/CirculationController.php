<?php

namespace App\Http\Controllers;

use App\Models\Copy;
use App\Models\Loan;
use App\Models\User;
use App\Services\LibraryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CirculationController extends Controller
{
    public function __construct(
        protected LibraryService $libraryService
    ) {
        $this->middleware('auth');
    }

    /**
     * Checkout a book
     */
    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'loan_days' => 'sometimes|integer|min:1|max:90',
        ]);

        try {
            $copy = Copy::where('barcode', $request->barcode)->firstOrFail();
            $user = User::findOrFail($request->user_id);
            $loanDays = $request->input('loan_days', 14);

            $loan = $this->libraryService->checkout($copy, $user, $loanDays);

            return response()->json([
                'success' => true,
                'message' => 'Book checked out successfully',
                'loan' => $loan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Return a book
     */
    public function return(Request $request, Loan $loan): JsonResponse
    {
        try {
            $loan = $this->libraryService->return($loan);

            return response()->json([
                'success' => true,
                'message' => 'Book returned successfully',
                'loan' => $loan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Renew a loan
     */
    public function renew(Request $request, Loan $loan): JsonResponse
    {
        $request->validate([
            'additional_days' => 'sometimes|integer|min:1|max:90',
        ]);

        try {
            $additionalDays = $request->input('additional_days', 14);
            $loan = $this->libraryService->renew($loan, $additionalDays);

            return response()->json([
                'success' => true,
                'message' => 'Loan renewed successfully',
                'loan' => $loan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Mark loan as lost
     */
    public function markAsLost(Loan $loan): JsonResponse
    {
        try {
            $this->libraryService->markAsLost($loan);

            return response()->json([
                'success' => true,
                'message' => 'Loan marked as lost',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
