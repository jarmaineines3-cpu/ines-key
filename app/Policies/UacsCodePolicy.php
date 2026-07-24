<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UacsCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class UacsCodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_uacs_code');
    }

    public function view(AuthUser $authUser, UacsCode $uacsCode): bool
    {
        return $authUser->can('view_uacs_code');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_uacs_code');
    }

    public function update(AuthUser $authUser, UacsCode $uacsCode): bool
    {
        return $authUser->can('update_uacs_code');
    }

    public function delete(AuthUser $authUser, UacsCode $uacsCode): bool
    {
        return $authUser->can('delete_uacs_code');
    }

    public function restore(AuthUser $authUser, UacsCode $uacsCode): bool
    {
        return $authUser->can('restore_uacs_code');
    }

    public function forceDelete(AuthUser $authUser, UacsCode $uacsCode): bool
    {
        return $authUser->can('force_delete_uacs_code');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_uacs_code');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_uacs_code');
    }

    public function replicate(AuthUser $authUser, UacsCode $uacsCode): bool
    {
        return $authUser->can('replicate_uacs_code');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_uacs_code');
    }

}