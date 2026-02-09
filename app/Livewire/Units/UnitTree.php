<?php
namespace App\Livewire\Units;

use App\Models\Unit;
use App\Models\Province;
use App\Models\UnitType;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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
        $universityType = UnitType::where('title', 'دانشگاه علوم پزشکی')->first();

        if (!$universityType) {
            $this->tree = [];
            return;
        }

        // ۱. پیدا کردن ریشه‌ها (دانشگاه‌ها) بر اساس فیلتر استان
        $rootQuery = Unit::with(['type', 'city'])
            ->where('unit_type_id', $universityType->id);

        if ($this->province_id) {
            $rootQuery->whereHas('city', function ($q) {
                $q->where('province_id', $this->province_id);
            });
        }

        $roots = $rootQuery->get();
        
        // ۲. دریافت تمام واحدها برای ساخت درخت در حافظه (برای جلوگیری از کوئری‌های مکرر)
        // اگر تعداد کل واحدها خیلی زیاد نیست، همه را بگیرید. در غیر این صورت باید از بازگشتی بهینه استفاده کرد.
        $allUnits = Unit::with(['type', 'city'])->get();

        $this->tree = $this->makeTree($allUnits, $roots->pluck('id')->toArray());
    }

    private function makeTree($allUnits, $rootIds)
    {
        $items = [];
        foreach ($allUnits as $unit) {
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
            }
            
            // فقط واحدهایی که جزو ریشه‌های فیلتر شده هستند را در سطح اول قرار بده
            if (in_array($id, $rootIds)) {
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