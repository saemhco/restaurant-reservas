<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Responses\ApiResponse;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $customers = Customer::query()->latest()->paginate(15);

        return ApiResponse::paginated(CustomerResource::collection($customers));
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return ApiResponse::created(new CustomerResource($customer));
    }

    public function show(Customer $customer): JsonResponse
    {
        $customer->load('reservations');

        return ApiResponse::success(new CustomerResource($customer));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return ApiResponse::success(
            new CustomerResource($customer->fresh()),
            'Customer updated successfully'
        );
    }

    public function destroy(Customer $customer): JsonResponse
    {
        if (! request()->user()?->hasPermission('customers.manage')) {
            return ApiResponse::error('Forbidden. Missing permission.', 403);
        }

        $customer->delete();

        return ApiResponse::noContent();
    }
}
