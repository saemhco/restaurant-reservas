<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Http\Resources\DishResource;
use App\Http\Responses\ApiResponse;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DishController extends Controller
{
    public function index(): JsonResponse
    {
        $dishes = Dish::query()->with('restaurant')->latest()->paginate(15);

        return ApiResponse::paginated(DishResource::collection($dishes));
    }

    public function mostOrdered(Request $request): JsonResponse
    {
        $limit = (int) $request->integer('limit', 10);

        $dishes = Dish::query()->mostOrdered(max(1, min($limit, 50)))->get();

        return ApiResponse::success(
            DishResource::collection($dishes),
            'Most ordered dishes'
        );
    }

    public function store(StoreDishRequest $request): JsonResponse
    {
        $dish = Dish::create($request->validated());

        return ApiResponse::created(new DishResource($dish->load('restaurant')));
    }

    public function show(Dish $dish): JsonResponse
    {
        return ApiResponse::success(new DishResource($dish->load('restaurant')));
    }

    public function update(UpdateDishRequest $request, Dish $dish): JsonResponse
    {
        $dish->update($request->validated());

        return ApiResponse::success(
            new DishResource($dish->fresh()->load('restaurant')),
            'Dish updated successfully'
        );
    }

    public function destroy(Dish $dish): JsonResponse
    {
        if (! request()->user()?->hasPermission('dishes.manage')) {
            return ApiResponse::error('Forbidden. Missing permission.', 403);
        }

        $dish->delete();

        return ApiResponse::noContent();
    }
}
