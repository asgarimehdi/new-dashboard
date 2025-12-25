<?php

namespace App\Livewire\Provinces;

use Livewire\Component;
use App\Models\Province;

class ProvinceIndex extends Component
{
    public $name;
    public $provinceId;

    protected $rules = [
        'name' => 'required|string|min:3|unique:provinces,name',
    ];

    public function save()
    {
        $this->validate();

        Province::create([
            'name' => $this->name,
        ]);

        $this->reset('name');
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
        $this->validate([
            'name' => 'required|string|min:3|unique:provinces,name,' . $this->provinceId,
        ]);

        Province::findOrFail($this->provinceId)->update([
            'name' => $this->name,
        ]);

        $this->reset(['name', 'provinceId']);
        session()->flash('success', 'استان بروزرسانی شد');
    }

    public function delete($id)
    {
        Province::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.provinces.province-index', [
            'provinces' => Province::latest()->get()
        ]);
    }
}
