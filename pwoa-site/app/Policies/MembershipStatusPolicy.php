<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MembershipStatus;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MembershipStatusPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MembershipStatus');
    }

    public function view(AuthUser $authUser, MembershipStatus $membershipStatus): bool
    {
        return $authUser->can('View:MembershipStatus');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MembershipStatus');
    }

    public function update(AuthUser $authUser, MembershipStatus $membershipStatus): bool
    {
        return $authUser->can('Update:MembershipStatus');
    }

    public function delete(AuthUser $authUser, MembershipStatus $membershipStatus): bool
    {
        return $authUser->can('Delete:MembershipStatus');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MembershipStatus');
    }

    public function restore(AuthUser $authUser, MembershipStatus $membershipStatus): bool
    {
        return $authUser->can('Restore:MembershipStatus');
    }

    public function forceDelete(AuthUser $authUser, MembershipStatus $membershipStatus): bool
    {
        return $authUser->can('ForceDelete:MembershipStatus');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MembershipStatus');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MembershipStatus');
    }

    public function replicate(AuthUser $authUser, MembershipStatus $membershipStatus): bool
    {
        return $authUser->can('Replicate:MembershipStatus');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MembershipStatus');
    }
}
