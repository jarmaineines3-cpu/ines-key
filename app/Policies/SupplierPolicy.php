<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:supplier');
    }

    public function view(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('view:supplier');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:supplier');
    }

    public function update(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('update:supplier');
    }

    public function delete(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('delete:supplier');
    }

    public function restore(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('restore:supplier');
    }

    public function forceDelete(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('force_delete:supplier');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any:supplier');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any:supplier');
    }

    public function replicate(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('replicate:supplier');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder:supplier');
    }

}