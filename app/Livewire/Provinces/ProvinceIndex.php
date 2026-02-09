<?php
namespace App\Livewire\Provinces;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Province;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProvinceIndex extends Component
{
    use WithPagination;

    public $name;
    public $provinceId;
    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:2|max:100|unique:provinces,name,' . $this->provinceId,
        ];
    }

    protected $validationAttributes = [
        'name' => 'نام استان',
    ];

    public function save()
    {
        $this->validate();

        Province::updateOrCreate(
            ['id' => $this->provinceId],
            ['name' => $this->name]
        );

        $isEdit = $this->provinceId ? true : false;
        $this->resetForm();
        
        $this->dispatch('swal', [
            'title' => $isEdit ? 'بروزرسانی موفق' : 'ثبت موفق',
            'text' => $isEdit ? 'اطلاعات استان با موفقیت ویرایش شد.' : 'استان جدید با موفقیت اضافه شد.',
            'icon' => 'success'
        ]);
    }

    public function edit($id)
    {
        $province = Province::findOrFail($id);
        $this->provinceId = $province->id;
        $this->name = $province->name;
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($id)
    {
        Province::findOrFail($id)->delete();
        $this->dispatch('swal', [
            'title' => 'حذف شد',
            'text' => 'استان مورد نظر از سیستم حذف گردید.',
            'icon' => 'error'
        ]);
    }

    public function resetForm()
    {
        $this->reset(['name', 'provinceId']);
        $this->resetValidation();
    }

    public function render()
    {
        $provinces = Province::where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.provinces.province-index', compact('provinces'));
    }
}