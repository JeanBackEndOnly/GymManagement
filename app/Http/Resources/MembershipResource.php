<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'member'          => $this->user->firstname . ' ' . $this->user->lastname,
            'membership_type' => $this->membership_type,
            'start_date'      => $this->start_date,
            'end_date'        => $this->end_date,
            'status'          => $this->status,
            // 'payment'         => $this->whenLoaded('payment', function () {
            //     return [
            //         'type'            => $this->payment->payment_type,
            //         'amount'          => number_format($this->payment->payment_amount, 2),
            //         'or_number'       => $this->payment->or_number,
            //         'transaction_id'  => $this->payment->transaction_id,
            //         'status'          => $this->payment->payment_status,
            //     ];
            // }),
            'created_at'      => $this->created_at->format('M d, Y'),
        ];
    }
}