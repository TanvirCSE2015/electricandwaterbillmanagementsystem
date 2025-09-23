<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ElectricBillSetting;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectricBillSettingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ElectricBillSetting');
    }

    public function view(AuthUser $authUser, ElectricBillSetting $electricBillSetting): bool
    {
        return $authUser->can('View:ElectricBillSetting');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ElectricBillSetting');
    }

    public function update(AuthUser $authUser, ElectricBillSetting $electricBillSetting): bool
    {
        return $authUser->can('Update:ElectricBillSetting');
    }

    public function delete(AuthUser $authUser, ElectricBillSetting $electricBillSetting): bool
    {
        return $authUser->can('Delete:ElectricBillSetting');
    }

    public function restore(AuthUser $authUser, ElectricBillSetting $electricBillSetting): bool
    {
        return $authUser->can('Restore:ElectricBillSetting');
    }

    public function forceDelete(AuthUser $authUser, ElectricBillSetting $electricBillSetting): bool
    {
        return $authUser->can('ForceDelete:ElectricBillSetting');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ElectricBillSetting');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ElectricBillSetting');
    }

    public function replicate(AuthUser $authUser, ElectricBillSetting $electricBillSetting): bool
    {
        return $authUser->can('Replicate:ElectricBillSetting');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ElectricBillSetting');
    }

}