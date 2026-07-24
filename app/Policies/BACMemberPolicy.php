<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\BACMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class BACMemberPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_b_a_c_member');
    }

    public function view(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('view_b_a_c_member');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_b_a_c_member');
    }

    public function update(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('update_b_a_c_member');
    }

    public function delete(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('delete_b_a_c_member');
    }

    public function restore(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('restore_b_a_c_member');
    }

    public function forceDelete(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('force_delete_b_a_c_member');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_b_a_c_member');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_b_a_c_member');
    }

    public function replicate(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('replicate_b_a_c_member');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_b_a_c_member');
    }

}