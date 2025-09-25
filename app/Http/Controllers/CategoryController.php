<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //

    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'data' => $categories
        ]);
    }



    public function show($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'data' => $category
        ]);
    }


    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }


    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->update($request->validated());

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }





}
