<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        if ($request->user()->is_admin) {
            $bookings = Booking::with(['user', 'service'])->get();
        } else {
            $bookings = $request->user()->bookings()->with('service')->get();
        }

        return response()->json($bookings);
    }

    public function store(BookingRequest $request)
    {
        $service = Service::findOrFail($request->service_id);

        $booking = Booking::create([
            'user_id' => $request->user()->id,
            'service_id' => $service->id,
            'booking_date' => $request->booking_date,
            'status' => 'pending',
        ]);

        return response()->json($booking->load('service'), 201);
    }
}
