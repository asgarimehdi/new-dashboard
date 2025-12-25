<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use App\Models\City;
use App\Models\Province;
use App\Models\UnitType;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UnitIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $name;
    public $province_id;
    public $city_id;
    public $unit_type_id;
    public $parent_id;
    public $is_active = true;
    public $unitId;

    public $search = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'unit_type_id' => 'required|exists:unit_types,id',
            'parent_id' => 'nullable|exists:units,id',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        Unit::create($this->getUnitData());

        $this->resetForm();
        session()->flash('success', 'واحد ثبت شد');
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);

        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->province_id = $unit->city->province_id;
        $this->city_id = $unit->city_id;
        $this->unit_type_id = $unit->unit_type_id;
        $this->parent_id = $unit->parent_id;
        $this->is_active = $unit->is_active;
    }

    public function update()
    {
        $this->validate();

        Unit::findOrFail($this->unitId)->update($this->getUnitData());

        $this->resetForm();
        session()->flash('success', 'واحد ویرایش شد');
    }

    public function delete($id)
    {
        Unit::where('parent_id', $id)->update(['parent_id' => null]);
        Unit::findOrFail($id)->delete();

        session()->flash('success', 'واحد حذف شد');
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'province_id',
            'city_id',
            'unit_type_id',
            'parent_id',
            'is_active',
            'unitId'
        ]);
        $this->is_active = true;
    }

    private function getUnitData()
    {
        return [
            'name' => $this->name,
            'city_id' => $this->city_id,
            'unit_type_id' => $this->unit_type_id,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
        ];
    }

    public function render()
    {
        $units = Unit::with(['type', 'city.province', 'parent'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.units.unit-index', [
            'units' => $units,
            'provinces' => Province::orderBy('name')->get(),
            'cities' => $this->province_id
                ? City::where('province_id', $this->province_id)->orderBy('name')->get()
                : [],
            'types' => UnitType::orderBy('title')->get(),
            'parents' => Unit::whereNull('parent_id')->orWhere('id', '!=', $this->unitId)->get(),
        ]);
    }
}
