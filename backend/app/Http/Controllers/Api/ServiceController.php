<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function index()
    {
        $services = Service::where('status', true)->get();
        return ServiceResource::collection($services);
    }

    public function store(ServiceRequest $request)
    {
        $this->authorize('create', Service::class);

        $service = Service::create($request->validated());
        return new ServiceResource($service);
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $this->authorize('update', $service);

        $service->update($request->validated());
        return new S($service);
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        $service->delete();
        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }
}
