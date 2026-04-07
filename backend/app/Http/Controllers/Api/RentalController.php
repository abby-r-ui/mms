<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with(['user', 'motorcycle']);

        if ($request->user()->role === 'customer') {
            $query->where('user_id', $request->user()->id);
        }

        $rentals = $query->get();

        return response()->json([
            'success' => true,
            'data' => $rentals
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'motorcycle_id' => 'required|exists:motorcycles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $motorcycle = Motorcycle::findOrFail($request->motorcycle_id);

        if ($motorcycle->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Motorcycle not available'
            ], 400);
        }

        $days = Carbon::parse($request->end_date)->diffInDays(Carbon::parse($request->start_date)) + 1;
        $total_price = $days * $motorcycle->price_per_day;

        $rental = Rental::create([
            'user_id' => $request->user()->id,
            'motorcycle_id' => $request->motorcycle_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total_price,
            'status' => 'pending',
        ]);

        // Update motorcycle status
        $motorcycle->update(['status' => 'rented']);

        $rental->load(['user', 'motorcycle']);

        return response()->json([
            'success' => true,
            'data' => $rental
        ], 201);
    }

    public function show(Rental $rental)
    {
        $rental->load(['user', 'motorcycle']);

        return response()->json([
            'success' => true,
            'data' => $rental
        ]);
    }

    public function update(Request $request, Rental $rental)
    {
        if ($rental->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'status' => 'in:pending,confirmed,completed,cancelled',
        ]);

        $rental->update($request->only('status'));

        if ($request->status === 'cancelled') {
            $rental->motorcycle->update(['status' => 'available']);
        }

        $rental->load(['user', 'motorcycle']);

        return response()->json([
            'success' => true,
            'data' => $rental
        ]);
    }

    public function destroy(Rental $rental)
    {
        if ($rental->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        $rental->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rental deleted'
        ]);
    }
}
