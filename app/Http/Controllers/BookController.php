<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Copy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
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
        $query = Book::withCount(['copies', 'availableCopies']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        return response()->json($query->orderBy('title')->paginate(20));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'description' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'language' => 'nullable|string|max:10',
            'pages' => 'nullable|integer|min:1',
            'category' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:255',
            'copies' => 'nullable|integer|min:1',
            'barcode_prefix' => 'nullable|string|max:10',
        ]);

        $book = Book::create($validated);

        // Create copies if specified
        if ($request->has('copies') && $request->copies > 0) {
            $prefix = $request->input('barcode_prefix', 'BK');
            for ($i = 1; $i <= $request->copies; $i++) {
                Copy::create([
                    'book_id' => $book->id,
                    'barcode' => $prefix . str_pad($book->id, 6, '0', STR_PAD_LEFT) . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'status' => 'available',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Book created successfully',
            'book' => $book->loadCount(['copies', 'availableCopies']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        return response()->json($book->load(['copies', 'holds.user'])->loadCount(['copies', 'availableCopies']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'isbn' => 'sometimes|nullable|string|unique:books,isbn,' . $book->id,
            'description' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'language' => 'nullable|string|max:10',
            'pages' => 'nullable|integer|min:1',
            'category' => 'nullable|string|max:255',
            'cover_image' => 'nullable|string|max:255',
        ]);

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book updated successfully',
            'book' => $book,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): JsonResponse
    {
        // Check if book has active loans
        if ($book->copies()->whereHas('activeLoan')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete book with active loans',
            ], 422);
        }

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully',
        ]);
    }
}
