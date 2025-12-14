<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Hold;
use App\Services\LibraryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HoldController extends Controller
{
    public function __construct(
        protected LibraryService $libraryService
    ) {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Hold::with(['book', 'user']);

        if ($request->user()->isPatron()) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        try {
            $book = Book::findOrFail($request->book_id);
            $user = $request->user()->isLibrarian() && $request->has('user_id')
                ? \App\Models\User::findOrFail($request->user_id)
                : $request->user();

            $hold = $this->libraryService->placeHold($book, $user);

            return response()->json([
                'success' => true,
                'message' => 'Hold placed successfully',
                'hold' => $hold,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hold $hold)
    {
        return response()->json($hold->load(['book', 'user']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hold $hold): JsonResponse
    {
        try {
            $this->libraryService->cancelHold($hold);

            return response()->json([
                'success' => true,
                'message' => 'Hold cancelled successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
