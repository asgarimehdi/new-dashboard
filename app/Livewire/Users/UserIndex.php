<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $userId = null;

    public $full_name = '';
    public $national_code = '';
    public $unit_id = '';
    public $is_active = true;

    public $search = '';

    protected function rules()
    {
        return [
            'full_name' => 'required|string|min:3',
            'national_code' => 'required|digits:10|unique:users,national_code,' . $this->userId,
            'unit_id' => 'required|exists:units,id',
            'is_active' => 'boolean',
        ];
    }

    /* ---------- CRUD ---------- */

    public function save()
    {
        $this->validate();

        User::updateOrCreate(
            ['id' => $this->userId],
            [
                'full_name' => $this->full_name,
                'national_code' => $this->national_code,
                'unit_id' => $this->unit_id,
                'is_active' => $this->is_active,
            ]
        );

        $this->resetForm();
        session()->flash('success', 'کاربر ذخیره شد');
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->full_name = $user->full_name;
        $this->national_code = $user->national_code;
        $this->unit_id = $user->unit_id;
        $this->is_active = $user->is_active;
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('success', 'کاربر حذف شد');
    }

    public function resetForm()
    {
        $this->reset([
            'userId',
            'full_name',
            'national_code',
            'unit_id',
            'is_active',
        ]);

        $this->is_active = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /* ---------- Render ---------- */

    public function render()
    {
        $users = User::with('unit')
            ->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('national_code', 'like', '%' . $this->search . '%')
                  ->orWhereHas('unit', fn ($u) =>
                      $u->where('name', 'like', '%' . $this->search . '%')
                  );
            })
            ->latest()
            ->paginate(10);

        return view('livewire.users.user-index', [
            'users' => $users,
            'units' => Unit::orderBy('name')->get(),
        ]);
    }
}
