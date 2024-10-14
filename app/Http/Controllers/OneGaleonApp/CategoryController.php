<?php

namespace App\Http\Controllers\OneGaleonApp;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $categories = Category::where('user_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'results' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        $category = Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'descripcion' => $request->descripcion,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada.',
            'results' => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $category = Category::where('user_id', $user->id)->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'results' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user();
        $category = Category::where('user_id', $user->id)->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada.',
            ], 404);
        }

        $category->update([
            'name' => $request->name,
            'descripcion' => $request->descripcion,
            'icon' => $request->icon,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Categoría actualizada.',
            'results' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $category = Category::where('user_id', $user->id)->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada.',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoría eliminada.'
        ]);
    }
}
