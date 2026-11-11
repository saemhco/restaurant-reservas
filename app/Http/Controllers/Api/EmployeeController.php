<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Responses\ApiResponse;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        $employees = Employee::query()->with('restaurant')->latest()->paginate(15);

        return ApiResponse::paginated(EmployeeResource::collection($employees));
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        return ApiResponse::created(new EmployeeResource($employee->load('restaurant')));
    }

    public function show(Employee $employee): JsonResponse
    {
        return ApiResponse::success(new EmployeeResource($employee->load('restaurant')));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        $employee->update($request->validated());

        return ApiResponse::success(
            new EmployeeResource($employee->fresh()->load('restaurant')),
            'Employee updated successfully'
        );
    }

    public function destroy(Employee $employee): JsonResponse
    {
        if (! request()->user()?->hasPermission('employees.manage')) {
            return ApiResponse::error('Forbidden. Missing permission.', 403);
        }

        $employee->delete();

        return ApiResponse::noContent();
    }
}
