<?php

namespace App\Livewire\Units;

use App\Models\Unit;
use App\Models\Province;
use App\Models\UnitType;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UnitTree extends Component
{
    public $province_id = '';
    public $tree = [];

    public function mount()
    {
        $this->buildTree();
    }

    public function updatedProvinceId()
    {
        $this->buildTree();
        
    }

    private function buildTree(): void
    {
        // نوع دانشگاه
        $universityType = UnitType::where('title', 'دانشگاه علوم پزشکی')->first();

        if (!$universityType) {
            $this->tree = [];
            return;
        }

        // واحدهای مجاز بر اساس استان
        $unitsQuery = Unit::with('type', 'city')
            ->where(function ($q) use ($universityType) {
                // همیشه دانشگاه‌ها (ریشه‌های فیلتر)
                $q->where('unit_type_id', $universityType->id);
            });

        if ($this->province_id) {
            $unitsQuery->whereHas('city', function ($q) {
                $q->where('province_id', $this->province_id);
            });
        }

        $universities = $unitsQuery->get();

        // جمع‌آوری همه نودهای موردنیاز (دانشگاه + همه زیرمجموعه‌ها)
        $allowedIds = [];

        foreach ($universities as $uni) {
            $this->collectChildrenIds($uni, $allowedIds);
        }

        // گرفتن کل واحدهای مجاز
        $units = Unit::with('type', 'city')
            ->whereIn('id', $allowedIds)
            ->get();

        // ساخت درخت واقعی
        $this->tree = $this->makeTree($units);
    }

    private function collectChildrenIds(Unit $unit, array &$ids)
    {
        if (in_array($unit->id, $ids)) {
            return;
        }

        $ids[] = $unit->id;

        foreach ($unit->children as $child) {
            $this->collectChildrenIds($child, $ids);
        }
    }

    private function makeTree($units)
    {
        $items = [];
        foreach ($units as $unit) {
            $items[$unit->id] = [
                'model' => $unit,
                'children' => [],
            ];
        }

        $tree = [];

        foreach ($items as $id => &$node) {
            $parentId = $node['model']->parent_id;

            if ($parentId && isset($items[$parentId])) {
                $items[$parentId]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }

        return $tree;
    }


    public function render()
    {
        return view('livewire.units.unit-tree', [
            'provinces' => Province::orderBy('name')->get(),
        ]);
    }
}
