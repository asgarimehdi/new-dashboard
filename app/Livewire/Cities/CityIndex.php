<?php

namespace App\Livewire\Cities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\City;
use App\Models\Province;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CityIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name;
    public $province_id;
    public $cityId;
    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|min:2',
            'province_id' => 'required|exists:provinces,id',
        ];
    }

    public function save()
    {
        $this->validate();

        City::create([
            'name' => $this->name,
            'province_id' => $this->province_id,
        ]);

        $this->resetForm();
        session()->flash('success', 'شهر ثبت شد');
    }

    public function edit($id)
    {
        $city = City::findOrFail($id);
        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->province_id = $city->province_id;
    }

    public function update()
    {
        $this->validate();

        City::findOrFail($this->cityId)->update([
            'name' => $this->name,
            'province_id' => $this->province_id,
        ]);

        $this->resetForm();
        session()->flash('success', 'شهر ویرایش شد');
    }

    public function delete($id)
    {
        City::findOrFail($id)->delete();
        session()->flash('success', 'شهر حذف شد');
    }

    public function resetForm()
    {
        $this->reset(['name', 'province_id', 'cityId']);
    }

    public function render()
    {
        $cities = City::with('province')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhereHas('province', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.cities.city-index', [
            'cities' => $cities,
            'provinces' => Province::orderBy('name')->get(),
        ]);
    }
}
