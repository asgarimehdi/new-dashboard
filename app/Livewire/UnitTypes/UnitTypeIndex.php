<?php

namespace App\Livewire\UnitTypes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UnitType;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UnitTypeIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $title;
    public $description;
    public $unitTypeId;
    public $search = '';

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|unique:unit_types,title,' . $this->unitTypeId,
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        $this->validate();

        UnitType::create([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->resetForm();
        session()->flash('success', 'نوع واحد ثبت شد');
    }

    public function edit($id)
    {
        $type = UnitType::findOrFail($id);
        $this->unitTypeId = $type->id;
        $this->title = $type->title;
        $this->description = $type->description;
    }

    public function update()
    {
        $this->validate();

        UnitType::findOrFail($this->unitTypeId)->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->resetForm();
        session()->flash('success', 'نوع واحد ویرایش شد');
    }

    public function delete($id)
    {
        UnitType::findOrFail($id)->delete();
        session()->flash('success', 'نوع واحد حذف شد');
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'unitTypeId']);
    }

    public function render()
    {
        $types = UnitType::where('title', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.unit-types.unit-type-index', compact('types'));
    }
}
