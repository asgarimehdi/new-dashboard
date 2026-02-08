<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Unit;
use App\Models\City;
use App\Models\Province;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // فیلدهای مدل User
    public $full_name;
    public $national_code;
    public $province_id;
    public $city_id;
    public $unit_id;
    public $is_active = true;
    public $userId;

    // فیلد مربوط به Spatie
    public $selectedRoles = [];

    public $search = '';

    /* ===================== Rules ===================== */
    protected function rules()
    {
        return [
            'full_name'     => 'required|string|min:3',
            'national_code' => 'required|digits:10|unique:users,national_code,' . $this->userId,
            'province_id'   => 'required|exists:provinces,id',
            'city_id'       => 'required|exists:cities,id',
            'unit_id'       => 'required|exists:units,id',
            'is_active'     => 'boolean',
            'selectedRoles' => 'required|array|min:1',
        ];
    }

    /* ===================== Core Actions ===================== */
    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $user = User::updateOrCreate(
                ['id' => $this->userId],
                $this->userData()
            );

            // همگام‌سازی نقش‌های Spatie
            $user->syncRoles($this->selectedRoles);
        });

        $message = $this->userId ? 'اطلاعات کاربر بروزرسانی شد' : 'کاربر با موفقیت تعریف شد';
        $this->resetForm();
        $this->dispatch('swal', ['title' => $message, 'icon' => 'success']);
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $user = User::with(['roles', 'unit.city.province'])->findOrFail($id);

        $this->userId        = $user->id;
        $this->full_name     = $user->full_name;
        $this->national_code = $user->national_code;
        $this->province_id   = $user->province_id ?? ($user->unit?->city?->province_id);
        $this->city_id       = $user->city_id;
        $this->unit_id       = $user->unit_id;
        $this->is_active     = $user->is_active;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        $this->dispatch('swal', ['title' => 'کاربر از سیستم حذف شد', 'icon' => 'warning']);
    }

    /* ===================== Helpers ===================== */
    private function userData(): array
    {
        $data = [
            'full_name'     => $this->full_name,
            'national_code' => $this->national_code,
            'province_id'   => $this->province_id,
            'city_id'       => $this->city_id,
            'unit_id'       => $this->unit_id,
            'is_active'     => $this->is_active,
        ];

        // اگر کاربر جدید است، رمز عبور پیش‌فرض کد ملی باشد
        if (!$this->userId) {
            $data['password'] = bcrypt($this->national_code);
        }

        return $data;
    }

    public function resetForm()
    {
        $this->reset([
            'full_name', 'national_code', 'province_id', 'city_id', 
            'unit_id', 'is_active', 'userId', 'selectedRoles'
        ]);
        $this->is_active = true;
    }

    // وقتی استان تغییر کرد، شهر و واحد باید ریست شوند
    public function updatedProvinceId()
    {
        $this->city_id = null;
        $this->unit_id = null;
    }

    public function updatedCityId()
    {
        $this->unit_id = null;
    }

    /* ===================== Render ===================== */
    public function render()
    {
        return view('livewire.users.user-index', [
            'users' => User::with(['unit', 'roles', 'province', 'city'])
                ->where('full_name', 'like', '%' . $this->search . '%')
                ->orWhere('national_code', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),

            'provinces' => Province::orderBy('name')->get(),

            'cities' => $this->province_id
                ? City::where('province_id', $this->province_id)->orderBy('name')->get()
                : [],

            'units' => $this->city_id
                ? Unit::where('city_id', $this->city_id)->orderBy('name')->get()
                : [],

            'allRoles' => Role::all(),
        ]);
    }
}