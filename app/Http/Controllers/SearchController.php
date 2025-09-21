<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'nullable|in:beginner,intermediate,advanced',
            'status' => 'nullable|in:draft,published,archived',
            'price_max' => 'nullable|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        try {
            $query = $request->input('q', '');
            
            $builder = Course::query();

            if ($query) {
                $builder->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                });
            }

            if ($request->filled('category_id')) {
                $builder->where('category_id', $request->category_id);
            }

            if ($request->filled('level')) {
                $builder->where('level', $request->level);
            }

            if ($request->filled('status')) {
                $builder->where('status', $request->status);
            }

            if ($request->filled('price_max')) {
                $builder->where('price', '<=', (float) $request->price_max);
            }

            if ($request->filled('rating')) {
                $builder->whereHas('reviews', function ($q) use ($request) {
                    $q->groupBy('course_id')->havingRaw('AVG(rating) >= ?', [(float) $request->rating]);
                });
            }

            $results = $builder->paginate(10);

            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => 'not found ' . $e->getMessage()], 500);
        }
    }
}