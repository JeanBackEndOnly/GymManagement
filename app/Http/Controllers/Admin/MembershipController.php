<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Models\Payment;
use App\Services\MembershipService;
use App\Http\Requests\Admin\MembershipRequest;
use App\Http\Requests\Admin\MembershipUpdateRequest;
use App\Http\Resources\MembershipResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private MembershipService $membershipService
    ) {}

    public function index(Request $request)
    {
        try {
            $this->authorize('viewAny', Membership::class);

            $memberships = Membership::query()
                ->with(['user', 'payment'])
                ->when($request->search, function ($query) use ($request) {
                    $query->whereHas('user', function ($q) use ($request) {
                        $q->where('firstname', 'like', "%{$request->search}%")
                          ->orWhere('lastname', 'like', "%{$request->search}%");
                    });
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->membership_type, function ($query) use ($request) {
                    $query->where('membership_type', $request->membership_type);
                })
                ->latest()
                ->paginate($request->per_page ?? 15);

            $memberships->getCollection()->transform(function ($membership) {
                return new MembershipResource($membership);
            });

            return response()->json([
                'status' => 1,
                'message' => 'Memberships fetched successfully.',
                'data' => $memberships,
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Fetch memberships failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch memberships. Please try again.',
            ], 500);
        }
    }

    public function store(MembershipRequest $request)
    {
        try {
            $this->authorize('create', Membership::class);

            $membership = $this->membershipService->create($request->validated());

            return response()->json([
                'status' => 1,
                'message' => 'Contract created successfully.',
                'data' => new MembershipResource($membership->load(['user', 'payment'])),
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Contract creation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Contract process failed. Please try again.',
            ], 500);
        }
    }

    public function show(Membership $membership)
    {
        try {
            $this->authorize('view', $membership);

            return response()->json([
                'status' => 1,
                'message' => 'Membership fetched successfully.',
                'data' => new MembershipResource($membership->load(['user', 'payment'])),
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Fetch membership failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch membership. Please try again.',
            ], 500);
        }
    }

    public function update(MembershipUpdateRequest $request, Membership $membership)
    {
        try {
            $this->authorize('update', $membership);

            $membership->update($request->validated());

            return response()->json([
                'status' => 1,
                'message' => 'Membership updated successfully.',
                'data' => new MembershipResource($membership->fresh(['user', 'payment'])),
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Membership update failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Membership update failed. Please try again.',
            ], 500);
        }
    }

    public function destroy(Membership $membership)
    {
        try {
            $this->authorize('delete', $membership);

            // Delete payment first
            if ($membership->payment) {
                $membership->payment->delete();
            }

            $memberName = $membership->user->firstname . ' ' . $membership->user->lastname;
            $membership->delete();

            return response()->json([
                'status' => 1,
                'message' => $memberName . '\'s membership deleted successfully.',
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Membership delete failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete membership. Please try again.',
            ], 500);
        }
    }
}