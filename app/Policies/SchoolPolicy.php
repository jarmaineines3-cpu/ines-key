<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\School;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:school');
    }

    public function view(AuthUser $authUser, School $school): bool
    {
        return $authUser->can('view:school');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:school');
    }

    public function update(AuthUser $authUser, School $school): bool
    {
        return $authUser->can('update:school');
    }

    public function delete(AuthUser $authUser, School $school): bool
    {
        return $authUser->can('delete:school');
    }

    public function restore(AuthUser $authUser, School $school): bool
    {
        return $authUser->can('restore:school');
    }

    public function forceDelete(AuthUser $authUser, School $school): bool
    {
        return $authUser->can('force_delete:school');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any:school');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any:school');
    }

    public function replicate(AuthUser $authUser, School $school): bool
    {
        return $authUser->can('replicate:school');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder:school');
    }

}