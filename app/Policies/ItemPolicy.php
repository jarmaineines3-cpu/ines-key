<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Item;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_item');
    }

    public function view(AuthUser $authUser, Item $item): bool
    {
        return $authUser->can('view_item');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_item');
    }

    public function update(AuthUser $authUser, Item $item): bool
    {
        return $authUser->can('update_item');
    }

    public function delete(AuthUser $authUser, Item $item): bool
    {
        return $authUser->can('delete_item');
    }

    public function restore(AuthUser $authUser, Item $item): bool
    {
        return $authUser->can('restore_item');
    }

    public function forceDelete(AuthUser $authUser, Item $item): bool
    {
        return $authUser->can('force_delete_item');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_item');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_item');
    }

    public function replicate(AuthUser $authUser, Item $item): bool
    {
        return $authUser->can('replicate_item');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_item');
    }

}