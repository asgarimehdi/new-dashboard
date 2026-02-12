<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public $userId = null;
    public $full_name = '';
    public $national_code = '';
    public $unit_id = '';
    public $selected_unit_name = ''; // برای نمایش نام در اینپوت
    public $is_active = true;
    public $selected_roles = [];

    public $search = ''; // جستجوی جدول
    public $unit_search = ''; // جستجوی اینپوت واحد
    public $show_dropdown = false; // کنترل نمایش لیست

    protected function rules()
    {
        return [
            'full_name'      => 'required|string|min:3',
            'national_code'  => 'required|digits:10|unique:users,national_code,' . ($this->userId ?? 'NULL'),
            'unit_id'        => 'required|exists:units,id',
            'is_active'      => 'boolean',
            'selected_roles' => 'required|array|min:1',
        ];
    }

    // متد انتخاب واحد از لیست
    public function selectUnit($id, $name)
    {
        $this->unit_id = $id;
        $this->selected_unit_name = $name;
        $this->unit_search = '';
        $this->show_dropdown = false;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'full_name'     => $this->full_name,
            'national_code' => $this->national_code,
            'unit_id'       => $this->unit_id,
            'is_active'     => $this->is_active,
        ];

        if (!$this->userId) {
            $data['password'] = Hash::make($this->national_code);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);
        $user->syncRoles($this->selected_roles);

        $this->resetForm();
        session()->flash('success', 'کاربر با موفقیت ذخیره شد.');
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $user = User::with('unit')->findOrFail($id);

        $this->userId        = $user->id;
        $this->full_name     = $user->full_name;
        $this->national_code = $user->national_code;
        $this->unit_id       = $user->unit_id;
        $this->selected_unit_name = $user->unit?->name;
        $this->is_active     = (bool) $user->is_active;
        $this->selected_roles = $user->getRoleNames()->toArray();
    }

    public function resetForm()
    {
        $this->reset(['userId', 'full_name', 'national_code', 'unit_id', 'selected_unit_name', 'is_active', 'selected_roles', 'unit_search']);
        $this->is_active = true;
    }
public $filter_role = ''; // برای ذخیره نقش انتخاب شده جهت فیلتر

public function updatingFilterRole()
{
    $this->resetPage(); // با تغییر فیلتر، صفحه به یک برمی‌گردد
}

public function render()
{
    // فیلتر واحدها برای اینپوت هوشمند
    $units = [];
    if (strlen($this->unit_search) >= 2) {
        $units = Unit::where('name', 'like', '%' . $this->unit_search . '%')->take(5)->get();
        $this->show_dropdown = true;
    }

    // کوئری اصلی کاربران
    $users = User::with(['unit', 'roles'])
        ->where(function ($q) {
            $q->where('full_name', 'like', '%' . $this->search . '%')
              ->orWhere('national_code', 'like', '%' . $this->search . '%');
        })
        // فیلتر بر اساس نقش (در صورت انتخاب)
        ->when($this->filter_role, function ($q) {
            $q->role($this->filter_role); 
        })
        ->latest()->paginate(10);

    return view('livewire.users.user-index', [
        'users' => $users,
        'units' => $units,
        'roles' => Role::all(),
    ]);
}
}