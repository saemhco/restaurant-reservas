<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Http\Responses\ApiResponse;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Reservation::class);

        $query = Reservation::query()
            ->with(['customer', 'table.restaurant'])
            ->latest();

        // Customers solo ven sus reservas
        if (! $request->user()->hasPermission('reservations.manage')) {
            $customerId = $request->user()->customer?->id;
            $query->where('customer_id', $customerId ?? 0);
        }

        return ApiResponse::paginated(
            ReservationResource::collection($query->paginate(15))
        );
    }

    public function store(StoreReservationRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (! $request->user()->hasPermission('reservations.manage')) {
            $data['customer_id'] = $request->user()->customer?->id;

            if (! $data['customer_id']) {
                return ApiResponse::error('Customer profile not found for this user.', 422);
            }
        }

        $reservation = Reservation::create([
            ...$data,
            'status' => $data['status'] ?? 'pending',
        ]);

        $reservation->load(['customer', 'table.restaurant']);

        return ApiResponse::created(new ReservationResource($reservation));
    }

    public function show(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $reservation->load(['customer', 'table.restaurant', 'order.dishes']);

        return ApiResponse::success(new ReservationResource($reservation));
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation): JsonResponse
    {
        $reservation->update($request->validated());

        return ApiResponse::success(
            new ReservationResource($reservation->fresh()->load(['customer', 'table.restaurant'])),
            'Reservation updated successfully'
        );
    }

    public function destroy(Reservation $reservation): JsonResponse
    {
        $this->authorize('delete', $reservation);

        $reservation->delete();

        return ApiResponse::noContent();
    }

    public function total(Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $reservation->load(['order.dishes', 'customer', 'table']);

        if (! $reservation->order) {
            return ApiResponse::error('This reservation has no associated order.', 404, [
                'reservation_id' => $reservation->id,
                'total' => 0,
            ]);
        }

        $total = $reservation->order->calculateTotal();

        return ApiResponse::success([
            'reservation_id' => $reservation->id,
            'order_id' => $reservation->order->id,
            'customer' => $reservation->customer->name,
            'table' => $reservation->table->label,
            'total' => round($total, 2),
            'formatted_total' => '$'.number_format($total, 2, '.', ','),
            'items' => $reservation->order->dishes->map(fn ($dish) => [
                'dish' => $dish->name,
                'quantity' => $dish->pivot->quantity,
                'unit_price' => (float) $dish->pivot->unit_price,
                'subtotal' => round($dish->pivot->quantity * $dish->pivot->unit_price, 2),
            ]),
        ]);
    }
}
