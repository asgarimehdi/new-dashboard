<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // ۱. پاک کردن کش Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ۲. ایجاد تمام مجوزها (Permissions)
        $permissions = [
            'manage-users', 'manage-units', 'manage-roles-permissions',
            'manage-unit-types', 'view-tree-structure', 'manage-locations',
            'create-tickets', 'view-tickets-list', 'view-tickets-stats',
            'view-profile', 'view-dashboard', 'view-reports'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ۳. ایجاد نقش‌ها و اختصاص دسترسی‌ها

        // مدیر سامانه (همه دسترسی‌ها)
        $adminSystem = Role::findOrCreate('مدیر سامانه', 'web');
        $adminSystem->syncPermissions(Permission::all());

        // مدیر کل
        $generalManager = Role::findOrCreate('مدیر کل', 'web');
        $generalManager->syncPermissions([
            'view-tree-structure', 'create-tickets', 'view-tickets-list', 
            'view-tickets-stats', 'view-profile', 'view-dashboard', 'view-reports'
        ]);

        // مدیر واحد
        $unitManager = Role::findOrCreate('مدیر واحد', 'web');
        $unitManager->syncPermissions([
            'create-tickets', 'view-tickets-list', 'view-tickets-stats', 
            'view-profile', 'view-dashboard'
        ]);

        // کارشناس
        $expert = Role::findOrCreate('کارشناس', 'web');
        $expert->syncPermissions([
            'create-tickets', 'view-tickets-list', 'view-profile', 'view-dashboard'
        ]);

        // کاربر عادی
        $normalUser = Role::findOrCreate('کاربر عادی', 'web');
        $normalUser->syncPermissions([
            'create-tickets', 'view-tickets-list', 'view-profile'
        ]);

        // ۴. اختصاص نقش به کاربر شماره ۱ (اگر وجود داشته باشد)
        $user = User::find(1);
        if ($user) {
            $user->assignRole('مدیر سامانه');
        }
    }
}