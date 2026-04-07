<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MotorcycleController extends Controller
{
    public function index(Request $request)
    {
        $query = Motorcycle::query();

        if (!Auth::check() || Auth::user()->role !== 'admin') {
            $query->where('status', 'available');
        }

        $motorcycles = $query->get();

        return response()->json([
            'success' => true,
            'data' => $motorcycles
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1900',
            'price_per_day' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'image_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $motorcycle = Motorcycle::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $motorcycle
        ], 201);
    }

    public function show(Motorcycle $motorcycle)
    {
        return response()->json([
            'success' => true,
            'data' => $motorcycle
        ]);
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $this->authorizeAdmin();

        $request->validate([
            'make' => 'string|max:255',
            'model' => 'string|max:255',
            'year' => 'integer|min:1900',
            'price_per_day' => 'numeric|min:0',
            'status' => 'in:available,maintenance, rented',
            'image_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        $motorcycle->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $motorcycle
        ]);
    }

    public function destroy(Motorcycle $motorcycle)
    {
        $this->authorizeAdmin();

        $motorcycle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Motorcycle deleted'
        ]);
    }

    private function authorizeAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }
}
