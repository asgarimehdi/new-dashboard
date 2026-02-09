<?php

namespace App\Livewire\Units;

use App\Models\{Unit, City, Province, UnitType, UnitTypeHierarchy};
use Livewire\{Component, WithPagination, Attributes\Layout};

#[Layout('components.layouts.app')]
class UnitIndex extends Component
{
    use WithPagination;

    // Properties
    public $name, $province_id, $city_id, $unit_type_id, $parent_id, $unitId;
    public $is_active = true;
    public $search = '';

    protected $updatesQueryString = ['search'];

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'province_id' => $this->isNationalUnit() ? 'nullable' : 'required|exists:provinces,id',
            'city_id' => $this->isNationalUnit() ? 'nullable' : 'required|exists:cities,id',
            'unit_type_id' => 'required|exists:unit_types,id',
            'parent_id' => [
                $this->requiresParent() ? 'required' : 'nullable',
                'exists:units,id',
                function ($attribute, $value, $fail) {
                    if ($this->unitId && $value == $this->unitId) {
                        $fail('یک واحد نمی‌تواند زیرمجموعه خودش باشد.');
                    }
                },
            ],
            'is_active' => 'boolean',
        ];
    }

    /* -------------------- Actions -------------------- */
    public function save()
    {
        $this->validate();
        $this->validateHierarchy();

        Unit::create($this->unitData());
        
        $this->dispatch('notify', ['type' => 'success', 'message' => 'واحد جدید با موفقیت ثبت شد']);
        $this->resetForm();
    }

    public function edit($id)
    {
        $unit = Unit::with('city.province')->findOrFail($id);
        
        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->unit_type_id = $unit->unit_type_id;
        $this->parent_id = $unit->parent_id;
        $this->is_active = $unit->is_active;

        if ($unit->city) {
            $this->province_id = $unit->city->province_id;
            $this->city_id = $unit->city_id;
        }
    }

    public function update()
    {
        $this->validate();
        $this->validateHierarchy();

        Unit::findOrFail($this->unitId)->update($this->unitData());

        $this->dispatch('notify', ['type' => 'success', 'message' => 'تغییرات با موفقیت اعمال شد']);
        $this->resetForm();
    }

    public function delete($id)
    {
        $unit = Unit::findOrFail($id);
        // انتقال فرزندان به سطح بالاتر یا حذف منطقی (وابسته به بیزنس شما)
        Unit::where('parent_id', $id)->update(['parent_id' => $unit->parent_id]);
        $unit->delete();

        $this->dispatch('notify', ['type' => 'warning', 'message' => 'واحد مورد نظر حذف شد']);
    }

    /* -------------------- Helpers -------------------- */
    private function unitData(): array {
        return [
            'name' => $this->name,
            'city_id' => $this->isNationalUnit() ? null : $this->city_id,
            'unit_type_id' => $this->unit_type_id,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
        ];
    }

    public function isNationalUnit(): bool {
        $type = UnitType::find($this->unit_type_id);
        return $type && $type->title === 'وزارت بهداشت';
    }

    public function requiresParent(): bool {
        return !$this->isNationalUnit() && $this->unit_type_id;
    }

    public function updatedProvinceId() { $this->city_id = null; }

    public function updatedUnitTypeId() { $this->parent_id = null; }

    public function resetForm() {
        $this->reset(['name', 'province_id', 'city_id', 'unit_type_id', 'parent_id', 'unitId']);
        $this->is_active = true;
        $this->resetErrorBag();
    }

    private function validateHierarchy() {
        if ($this->parent_id) {
            $allowedTypeIds = UnitTypeHierarchy::where('child_unit_type_id', $this->unit_type_id)
                ->pluck('parent_unit_type_id')->toArray();
            
            $parentUnit = Unit::find($this->parent_id);
            if (!$parentUnit || !in_array($parentUnit->unit_type_id, $allowedTypeIds)) {
                $this->addError('parent_id', 'انتخاب این واحد به عنوان بالادست مجاز نیست.');
                abort(422);
            }
        }
    }

    public function render()
    {
        $allowedTypeIds = UnitTypeHierarchy::where('child_unit_type_id', $this->unit_type_id)
            ->pluck('parent_unit_type_id');

        return view('livewire.units.unit-index', [
            'units' => Unit::with(['type', 'city.province', 'parent'])
                ->where('name', 'like', "%{$this->search}%")
                ->latest()->paginate(10),
            'provinces' => Province::all(),
            'cities' => $this->province_id ? City::where('province_id', $this->province_id)->get() : [],
            'types' => UnitType::all(),
            'parents' => Unit::whereIn('unit_type_id', $allowedTypeIds)
                ->when($this->unitId, fn($q) => $q->where('id', '!=', $this->unitId))
                ->get(),
        ]);
    }
}