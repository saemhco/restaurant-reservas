<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Responses\ApiResponse;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->with(['reservation.customer', 'dishes'])
            ->latest()
            ->paginate(15);

        return ApiResponse::paginated(OrderResource::collection($orders));
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                'reservation_id' => $request->validated('reservation_id'),
                'ordered_at' => $request->validated('ordered_at', now()),
            ]);

            $sync = [];
            foreach ($request->validated('dishes') as $item) {
                $dish = Dish::findOrFail($item['dish_id']);
                $sync[$dish->id] = [
                    'quantity' => $item['quantity'],
                    'unit_price' => $dish->price,
                ];
            }

            $order->dishes()->sync($sync);

            return $order->load(['reservation', 'dishes']);
        });

        return ApiResponse::created(new OrderResource($order));
    }

    public function show(Order $order): JsonResponse
    {
        return ApiResponse::success(
            new OrderResource($order->load(['reservation.customer', 'dishes']))
        );
    }

    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
    {
        DB::transaction(function () use ($request, $order) {
            if ($request->filled('ordered_at')) {
                $order->update(['ordered_at' => $request->validated('ordered_at')]);
            }

            if ($request->filled('dishes')) {
                $sync = [];
                foreach ($request->validated('dishes') as $item) {
                    $dish = Dish::findOrFail($item['dish_id']);
                    $sync[$dish->id] = [
                        'quantity' => $item['quantity'],
                        'unit_price' => $dish->price,
                    ];
                }
                $order->dishes()->sync($sync);
            }
        });

        return ApiResponse::success(
            new OrderResource($order->fresh()->load(['reservation', 'dishes'])),
            'Order updated successfully'
        );
    }

    public function destroy(Order $order): JsonResponse
    {
        if (! request()->user()?->hasPermission('orders.manage')) {
            return ApiResponse::error('Forbidden. Missing permission.', 403);
        }

        $order->delete();

        return ApiResponse::noContent();
    }
}
