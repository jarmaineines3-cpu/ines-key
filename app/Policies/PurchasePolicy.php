<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Purchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_purchase');
    }

    public function view(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('view_purchase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_purchase');
    }

    public function update(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('update_purchase');
    }

    public function delete(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('delete_purchase');
    }

    public function restore(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('restore_purchase');
    }

    public function forceDelete(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('force_delete_purchase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_purchase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_purchase');
    }

    public function replicate(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('replicate_purchase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_purchase');
    }

}