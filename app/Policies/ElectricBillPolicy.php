<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ElectricBill;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectricBillPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ElectricBill');
    }

    public function view(AuthUser $authUser, ElectricBill $electricBill): bool
    {
        return $authUser->can('View:ElectricBill');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ElectricBill');
    }

    public function update(AuthUser $authUser, ElectricBill $electricBill): bool
    {
        return $authUser->can('Update:ElectricBill');
    }

    public function delete(AuthUser $authUser, ElectricBill $electricBill): bool
    {
        return $authUser->can('Delete:ElectricBill');
    }

}