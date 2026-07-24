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
        return $authUser->can('view_any_employee');
    }

    public function view(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('view_employee');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_employee');
    }

    public function update(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('update_employee');
    }

    public function delete(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('delete_employee');
    }

    public function restore(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('restore_employee');
    }

    public function forceDelete(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('force_delete_employee');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_employee');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_employee');
    }

    public function replicate(AuthUser $authUser, Employee $employee): bool
    {
        return $authUser->can('replicate_employee');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_employee');
    }

}