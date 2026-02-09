<?php
namespace App\Livewire\Cities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\City;
use App\Models\Province;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CityIndex extends Component
{
    use WithPagination;

    public $name;
    public $province_id;
    public $cityId;
    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|min:2|max:100',
            'province_id' => 'required|exists:provinces,id',
        ];
    }

    protected $validationAttributes = [
        'name' => 'نام شهر',
        'province_id' => 'استان',
    ];

    public function save()
    {
        $this->validate();

        City::updateOrCreate(
            ['id' => $this->cityId],
            [
                'name' => $this->name,
                'province_id' => $this->province_id
            ]
        );

        $isEdit = $this->cityId ? true : false;
        $this->resetForm();
        
        $this->dispatch('swal', [
            'title' => $isEdit ? 'بروزرسانی موفق' : 'ثبت موفق',
            'text' => $isEdit ? 'اطلاعات شهر با موفقیت ویرایش شد.' : 'شهر جدید به لیست اضافه شد.',
            'icon' => 'success'
        ]);
    }

    public function edit($id)
    {
        $city = City::findOrFail($id);
        $this->cityId = $city->id;
        $this->name = $city->name;
        $this->province_id = $city->province_id;
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('show-delete-confirmation', id: $id);
    }

    public function delete($id)
    {
        City::findOrFail($id)->delete();
        $this->dispatch('swal', [
            'title' => 'حذف شد',
            'text' => 'شهر مورد نظر از سیستم حذف گردید.',
            'icon' => 'warning'
        ]);
    }

    public function resetForm()
    {
        $this->reset(['name', 'province_id', 'cityId']);
        $this->resetValidation();
    }

    public function render()
    {
        $cities = City::with('province')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhereHas('province', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(12);

        return view('livewire.cities.city-index', [
            'cities' => $cities,
            'provinces' => Province::orderBy('name')->get(),
        ]);
    }
}