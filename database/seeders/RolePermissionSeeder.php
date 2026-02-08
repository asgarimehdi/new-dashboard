<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // ۱. پاک کردن کش Spatie (بسیار مهم برای جلوگیری از خطاهای کش)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ۲. ایجاد مجوزها (Permissions)
        // حتماً ابتدا لیست تمام مجوزهایی که نیاز دارید را بسازید
        $permissions = [
            'manage-users',
            'manage-units',
            'view-reports',
            'edit-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ۳. ایجاد نقش‌ها و اختصاص مجوزها
        
        // نقش ادمین کل (همه دسترسی‌ها)
        $adminRole = Role::findOrCreate('Admin', 'web');
        $adminRole->givePermissionTo(Permission::all());

        // نقش مدیر واحد (فقط دسترسی مدیریت کاربران)
        $managerRole = Role::findOrCreate('Manager', 'web');
        $managerRole->givePermissionTo('manage-users');

        // ۴. اختصاص نقش به کاربر ارشد (اختیاری)
        // $user = \App\Models\User::find(1);
        // $user->assignRole('Admin');
    }
}