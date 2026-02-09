<?php

namespace App\Livewire\UnitTypes;

use Livewire\Component;
use App\Models\UnitType;
use App\Models\UnitTypeHierarchy;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UnitTypeHierarchyManager extends Component
{
    public $selectedParentId;
    public $selectedChildrenIds = [];

    public function selectParent($id)
    {
        $this->selectedParentId = $id;
        // لود کردن فرزندان فعلی از جدول واسط
        $this->selectedChildrenIds = UnitTypeHierarchy::where('parent_unit_type_id', $id)
            ->pluck('child_unit_type_id')
            ->map(fn($id) => (string) $id) // برای هماهنگی با چک‌باکس‌ها
            ->toArray();
    }

    public function save()
    {
        if (!$this->selectedParentId) return;

        DB::transaction(function () {
            // حذف روابط قبلی
            UnitTypeHierarchy::where('parent_unit_type_id', $this->selectedParentId)->delete();

            // ثبت روابط جدید
            foreach ($this->selectedChildrenIds as $childId) {
                UnitTypeHierarchy::create([
                    'parent_unit_type_id' => $this->selectedParentId,
                    'child_unit_type_id' => $childId,
                ]);
            }
        });

        $this->dispatch('swal', [
            'title' => 'عملیات موفق',
            'text' => 'سلسله مراتب با موفقیت به‌روزرسانی شد.',
            'icon' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.unit-types.unit-type-hierarchy-manager', [
            'unitTypes' => UnitType::orderBy('title')->get(),
        ]);
    }
}