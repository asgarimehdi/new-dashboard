<?php

namespace App\Livewire\Provinces;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Province;
use Livewire\Attributes\Layout;
#[Layout('components.layouts.app')]
class ProvinceIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name;
    public $provinceId;
    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|unique:provinces,name,' . $this->provinceId,
        ];
    }

    public function save()
    {
        $this->validate();

        Province::create([
            'name' => $this->name,
        ]);

        $this->resetForm();
        session()->flash('success', 'استان با موفقیت ثبت شد');
    }

    public function edit($id)
    {
        $province = Province::findOrFail($id);
        $this->provinceId = $province->id;
        $this->name = $province->name;
    }

    public function update()
    {
        $this->validate();

        Province::findOrFail($this->provinceId)->update([
            'name' => $this->name,
        ]);

        $this->resetForm();
        session()->flash('success', 'استان بروزرسانی شد');
    }

    public function delete($id)
    {
        Province::findOrFail($id)->delete();
        session()->flash('success', 'استان حذف شد');
    }

    public function resetForm()
    {
        $this->reset(['name', 'provinceId']);
    }

    public function render()
    {
        $provinces = Province::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.provinces.province-index', compact('provinces'));
    }
}
