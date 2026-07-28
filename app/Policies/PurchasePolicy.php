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
        return $authUser->can('view_any:purchase');
    }

    public function view(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('view:purchase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:purchase');
    }

    public function update(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('update:purchase');
    }

    public function delete(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('delete:purchase');
    }

    public function restore(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('restore:purchase');
    }

    public function forceDelete(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('force_delete:purchase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any:purchase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any:purchase');
    }

    public function replicate(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('replicate:purchase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder:purchase');
    }

}