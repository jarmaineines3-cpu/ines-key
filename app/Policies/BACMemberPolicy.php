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
        return $authUser->can('view_any:b_a_c_member');
    }

    public function view(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('view:b_a_c_member');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:b_a_c_member');
    }

    public function update(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('update:b_a_c_member');
    }

    public function delete(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('delete:b_a_c_member');
    }

    public function restore(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('restore:b_a_c_member');
    }

    public function forceDelete(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('force_delete:b_a_c_member');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any:b_a_c_member');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any:b_a_c_member');
    }

    public function replicate(AuthUser $authUser, BACMember $bACMember): bool
    {
        return $authUser->can('replicate:b_a_c_member');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder:b_a_c_member');
    }

}