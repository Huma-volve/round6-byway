<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query =Review::with(['course','user'])->latest();

        if (request()->has('course_id')) {
            # code...

            $query->where('course_id', $request->course_id);
        }

              if ($request->has('instructor_id')) {
        $query->join('courses', 'reviews.course_id', '=', 'courses.id')
              ->where('courses.instructor_id', $request->instructor_id)
              ->select('reviews.*'); 
    }

             if ($request->has('user_id')) {
        $query->where('user_id', $request->user_id);
    }

            $reviews =$query->paginate(10);

        return response()->json($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $query = Review::with(['user', 'course'])->find($id);

        if (!$query) {
            return response()->json([
                'message' => 'Review not found'
            ], 404);
        }

            $review =$query;
        return response()->json($review);
    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //

        $review=Review::findOrFail($id);
            // /** @var \App\Models\User|null $user */


        // $user = auth()->user();

        
        
        // if (!$user) {
        //     # code...
        //     return response()->json([
        //         'message'=>'Unauthorized please Login'
        //     ],401);
        // }
        
        
        // if ($user->role !== 'admin') {
        //     # code...
        //     return response()->json([
        //         'message'=>'Unauthorized '
        //     ],403);
        // }

          $review->delete();

    return response()->json([
        'message' => 'Review deleted successfully'
    ], 200);
    }
}
