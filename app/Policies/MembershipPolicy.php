<?php

namespace App\Policies;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MembershipPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'cashier']);
    }

    public function view(User $user, Membership $membership): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id === $membership->user_id;
    }

    public function create(User $user): bool
    {
        // Admin can do anything
        if ($user->role === 'admin') {
            return true;
        }

        // Cashier can create memberships, but only for regular members
        if ($user->role === 'cashier') {
            $targetUser = User::find(request('user_id'));
            
            // Can't create memberships for admins or other cashiers
            return $targetUser && in_array($targetUser->role, ['member']);
        }

        return false;
    }

    public function update(User $user, Membership $membership): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        // Cashier can only update their own memberships
        return $user->role === 'cashier' && $user->id === $membership->user_id;
    }

    public function delete(User $user, Membership $membership): bool
    {
        return $user->role === 'admin';
    }
}
