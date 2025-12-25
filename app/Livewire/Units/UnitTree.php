<?php


namespace App\Livewire\Units;

use App\Models\Unit;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UnitTree extends Component
{
    public function render()
    {
        // فقط ریشه‌ها (وزارت)
        $roots = Unit::with([
                'children.children.children.children', // عمق مناسب
                'type'
            ])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('livewire.units.unit-tree', compact('roots'));
    }
}
