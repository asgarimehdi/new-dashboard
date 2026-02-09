<?php
namespace App\Livewire\UnitTypes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UnitType;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class UnitTypeIndex extends Component
{
    use WithPagination;

    public $title, $description, $unitTypeId;
    public $search = '';
    public $isModalOpen = false;

    protected $updatesQueryString = ['search'];

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|unique:unit_types,title,' . $this->unitTypeId,
            'description' => 'nullable|string|max:500',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        UnitType::updateOrCreate(['id' => $this->unitTypeId], [
            'title' => $this->title,
            'description' => $this->description,
        ]);

        $this->dispatch('swal', [
            'title' => $this->unitTypeId ? 'ویرایش شد!' : 'ثبت شد!',
            'text' => 'عملیات با موفقیت انجام گردید.',
            'icon' => 'success'
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $type = UnitType::findOrFail($id);
        $this->unitTypeId = $type->id;
        $this->title = $type->title;
        $this->description = $type->description;
        $this->isModalOpen = true;
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirmDelete', $id);
    }

    public function delete($id)
    {
        UnitType::findOrFail($id)->delete();
        $this->dispatch('swal', [
            'title' => 'حذف شد!',
            'text' => 'نوع واحد مورد نظر از سیستم حذف گردید.',
            'icon' => 'warning'
        ]);
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'unitTypeId']);
        $this->resetValidation();
    }

    public function render()
    {
        $types = UnitType::where('title', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.unit-types.unit-type-index', compact('types'));
    }
}