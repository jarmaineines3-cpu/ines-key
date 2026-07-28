<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Employee;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:employee');
    }

    public function view(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('view:employee');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:employee');
    }

    public function update(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('update:employee');
    }

    public function delete(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('delete:employee');
    }

    public function restore(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('restore:employee');
    }

    public function forceDelete(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('force_delete:employee');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any:employee');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any:employee');
    }

    public function replicate(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('replicate:employee');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder:employee');
    }

}