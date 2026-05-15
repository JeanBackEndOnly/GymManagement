<?php

namespace App\Services;

use App\Models\Membership;
use App\Http\Requests\Admin\MembershipUpdateRequest;
use App\Http\Resources\MembershipResource;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    public function create(array $data): Membership
    {
        return DB::transaction(function () use ($data) {
            $membership = Membership::create([
                'user_id'         => $data['user_id'],
                'membership_type' => $data['membership_type'],
                'start_date'      => $data['start_date'],
                'end_date'        => $data['end_date'],
                'status'          => $data['status'] ?? 'active',
            ]);

            $membership->payment()->create([
                'user_id'          => $data['user_id'],
                'payment_type'     => $data['payment_type'],
                'payment_amount'   => $data['payment_amount'],
                'or_number'        => $data['or_number'] ?? null,
                'transaction_id'   => $data['transaction_id'] ?? null,
                'payment_status'   => $data['payment_status'] ?? 'pending',
                'contract_status'  => $data['contract_status'] ?? 'active',
            ]);

            return $membership->load('payment', 'user');
        });
    }
    public function update(MembershipUpdateRequest $request, Membership $membership)
    {
        try {
            $this->authorize('update', $membership);

            $membership->update($request->validated());

            return response()->json([
                'status' => 1,
                'message' => 'Membership updated successfully.',
                'data' => new MembershipResource($membership->fresh()),
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Membership update failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Membership update failed. Please try again.',
            ], 500);
        }
    }
}