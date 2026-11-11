<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTableRequest;
use App\Http\Requests\UpdateTableRequest;
use App\Http\Resources\TableResource;
use App\Http\Responses\ApiResponse;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(): JsonResponse
    {
        $tables = Table::query()->with('restaurant')->latest()->paginate(15);

        return ApiResponse::paginated(TableResource::collection($tables));
    }

    public function available(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'restaurant_id' => ['nullable', 'exists:restaurants,id'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Table::query()
            ->with('restaurant')
            ->available($validated['date'], $validated['time']);

        if (! empty($validated['restaurant_id'])) {
            $query->where('restaurant_id', $validated['restaurant_id']);
        }

        if (! empty($validated['min_capacity'])) {
            $query->where('capacity', '>=', $validated['min_capacity']);
        }

        return ApiResponse::success(
            TableResource::collection($query->orderBy('number')->get()),
            'Available tables'
        );
    }

    public function store(StoreTableRequest $request): JsonResponse
    {
        $table = Table::create($request->validated());

        return ApiResponse::created(new TableResource($table->load('restaurant')));
    }

    public function show(Table $table): JsonResponse
    {
        return ApiResponse::success(new TableResource($table->load('restaurant')));
    }

    public function update(UpdateTableRequest $request, Table $table): JsonResponse
    {
        $table->update($request->validated());

        return ApiResponse::success(
            new TableResource($table->fresh()->load('restaurant')),
            'Table updated successfully'
        );
    }

    public function destroy(Table $table): JsonResponse
    {
        if (! request()->user()?->hasPermission('tables.manage')) {
            return ApiResponse::error('Forbidden. Missing permission.', 403);
        }

        $table->delete();

        return ApiResponse::noContent();
    }
}
