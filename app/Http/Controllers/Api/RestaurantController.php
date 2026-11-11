<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Http\Responses\ApiResponse;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;

class RestaurantController extends Controller
{
    public function index(): JsonResponse
    {
        $restaurants = Restaurant::query()
            ->withCount(['tables', 'dishes', 'employees'])
            ->latest()
            ->paginate(15);

        return ApiResponse::paginated(
            RestaurantResource::collection($restaurants)
        );
    }

    public function store(StoreRestaurantRequest $request): JsonResponse
    {
        $restaurant = Restaurant::create($request->validated());

        return ApiResponse::created(new RestaurantResource($restaurant));
    }

    public function show(Restaurant $restaurant): JsonResponse
    {
        $restaurant->load(['tables', 'dishes', 'employees']);

        return ApiResponse::success(new RestaurantResource($restaurant));
    }

    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): JsonResponse
    {
        $restaurant->update($request->validated());

        return ApiResponse::success(
            new RestaurantResource($restaurant->fresh()),
            'Restaurant updated successfully'
        );
    }

    public function destroy(Restaurant $restaurant): JsonResponse
    {
        if (! request()->user()?->hasPermission('restaurants.manage')) {
            return ApiResponse::error('Forbidden. Missing permission.', 403);
        }

        $restaurant->delete();

        return ApiResponse::noContent();
    }
}
