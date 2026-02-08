<?php

namespace App\Livewire\Roles;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class RoleManager extends Component
{
    public $roleId = null;
    public $name = '';
    public $selected_permissions = []; // دسترسی‌های انتخاب شده برای این نقش

    protected $rules = [
        'name' => 'required|string|unique:roles,name',
        'selected_permissions' => 'required|array|min:1',
    ];

    public function save()
    {
        // اگر در حال ویرایش هستیم، اعتبار سنجی یکتا بودن نام را برای آیدی فعلی استثنا می‌کنیم
        $this->validate([
            'name' => 'required|string|unique:roles,name,' . ($this->roleId ?? 'NULL'),
            'selected_permissions' => 'required|array',
        ]);

        $role = Role::updateOrCreate(['id' => $this->roleId], ['name' => $this->name]);
        
        // همگام‌سازی دسترسی‌های این نقش
        $role->syncPermissions($this->selected_permissions);

        $this->resetForm();
        session()->flash('success', 'نقش و دسترسی‌ها با موفقیت بروزرسانی شدند.');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->name = $role->name;
        // دریافت لیست دسترسی‌هایی که این نقش قبلاً داشته است
        $this->selected_permissions = $role->permissions->pluck('name')->toArray();
    }

    public function delete($id)
    {
        Role::findOrFail($id)->delete();
        session()->flash('success', 'نقش مورد نظر حذف شد.');
    }

    public function resetForm()
    {
        $this->reset(['roleId', 'name', 'selected_permissions']);
    }

    public function render()
    {
        return view('livewire.roles.role-manager', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(), // لیست تمام دسترسی‌های موجود در سیستم
        ]);
    }
}