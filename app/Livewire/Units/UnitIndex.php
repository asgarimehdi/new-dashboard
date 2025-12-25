<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use App\Models\City;
use App\Models\Province;
use App\Models\UnitType;
use App\Models\UnitTypeHierarchy;
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

    /* ===================== Rules ===================== */
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => $this->isNationalUnit()
             ? 'nullable'
             : 'required|exists:cities,id',
            'unit_type_id' => 'required|exists:unit_types,id',
            'parent_id' => 'nullable|exists:units,id',
            'is_active' => 'boolean',
        ];
    }

    /* ===================== Core ===================== */
    public function save()
    {
        $this->validate();
        $this->validateHierarchy();

        Unit::create($this->unitData());

        $this->resetForm();
        session()->flash('success', 'واحد با موفقیت ثبت شد');
    }

    public function edit($id)
    {
        $unit = Unit::with('city.province')->findOrFail($id);

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
        $this->validateHierarchy();

        Unit::findOrFail($this->unitId)->update($this->unitData());

        $this->resetForm();
        session()->flash('success', 'واحد بروزرسانی شد');
    }

    public function delete($id)
    {
        Unit::where('parent_id', $id)->update(['parent_id' => null]);
        Unit::findOrFail($id)->delete();

        session()->flash('success', 'واحد حذف شد');
    }

    /* ===================== Hierarchy Logic ===================== */

    private function requiresParent(): bool
    {
        $type = UnitType::find($this->unit_type_id);

        return $type && $type->title !== 'وزارت بهداشت';
    }

    private function allowedParents()
    {
        if (!$this->unit_type_id) {
            return collect();
        }

        $allowedTypeIds = UnitTypeHierarchy::where(
            'child_unit_type_id',
            $this->unit_type_id
        )->pluck('parent_unit_type_id');

        return Unit::whereIn('unit_type_id', $allowedTypeIds)->get();
    }

    private function validateHierarchy(): void
    {
        if ($this->requiresParent() && !$this->parent_id) {
            $this->addError('parent_id', 'برای این نوع واحد، انتخاب بالادست الزامی است');
            abort(422);
        }

        if ($this->parent_id) {
            $validParentIds = $this->allowedParents()->pluck('id')->toArray();

            if (!in_array($this->parent_id, $validParentIds)) {
                $this->addError('parent_id', 'واحد بالادست انتخاب‌شده مجاز نیست');
                abort(422);
            }
        }
    }

    /* ===================== Helpers ===================== */
    private function unitData(): array
    {
        return [
            'name' => $this->name,
            'city_id' => $this->city_id,
            'unit_type_id' => $this->unit_type_id,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
        ];
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
            'unitId',
        ]);

        $this->is_active = true;
    }

    /* ===================== Render ===================== */
    public function getRequiresParentProperty(): bool
{
    return $this->requiresParent();
}

public function updatedUnitTypeId()
{
    // وقتی نوع واحد عوض شد، parent قبلی پاک شود
    $this->parent_id = null;
}
private function isNationalUnit(): bool
{
    $type = UnitType::find($this->unit_type_id);

    return $type && $type->title === 'وزارت بهداشت';
}

    public function render()
    {
        return view('livewire.units.unit-index', [
            'units' => Unit::with(['type', 'city.province', 'parent'])
                ->where('name', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),

            'provinces' => Province::orderBy('name')->get(),

            'cities' => $this->province_id
                ? City::where('province_id', $this->province_id)->orderBy('name')->get()
                : [],

            'types' => UnitType::orderBy('title')->get(),

            'parents' => $this->allowedParents(),
        ]);
    }
}
