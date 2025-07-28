<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function index()
    {
        $services = Service::where('status', true)->get();
        return response()->json($services);
    }

    public function store(ServiceRequest $request)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service = Service::create($request->validated());
        return response()->json($service, 201);
    }

    public function update(ServiceRequest $request, Service $service)
    {
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service->update($request->validated());
        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        if (!auth()->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service->delete();
        return response()->json(['message' => 'Service deleted']);
    }

}
