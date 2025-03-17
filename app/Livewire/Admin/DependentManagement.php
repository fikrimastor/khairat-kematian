<?php

namespace App\Livewire\Admin;

use App\Models\Dependent;
use App\Models\User;
use App\Services\DependentManagementServiceFactory;
use Livewire\Attributes\Computed;
use Livewire\Component;

class DependentManagement extends Component
{
    public User $member;

    // For dependent form
    public $dependentId = null;

    public $name = '';

    public $identification_number = '';

    public $birth_date = '';

    public $relationship = '';

    // For confirmation modal
    public $confirmingDependentDeletion = false;

    public $dependentToDelete = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'identification_number' => 'required|string|max:20',
        'birth_date' => 'nullable|date',
        'relationship' => 'required|string|max:50',
    ];

    public function mount(User $member)
    {
        $this->member = $member;
    }

    public function addDependent()
    {
        $this->resetForm();
        $this->dependentId = null;
    }

    public function editDependent($dependentId)
    {
        $dependent = Dependent::findOrFail($dependentId);
        $this->dependentId = $dependent->id;
        $this->name = $dependent->name;
        $this->identification_number = $dependent->identification_number;
        $this->birth_date = $dependent->birth_date ? $dependent->birth_date->format('Y-m-d') : null;
        $this->relationship = $dependent->relationship;
    }

    public function saveDependent()
    {
        $this->validate();

        $service = DependentManagementServiceFactory::create();
        $data = [
            'name' => $this->name,
            'identification_number' => $this->identification_number,
            'birth_date' => $this->birth_date,
            'relationship' => $this->relationship,
        ];

        if ($this->dependentId) {
            // Update existing dependent
            $dependent = Dependent::find($this->dependentId);
            $service->updateDependent($dependent, $data);

            session()->flash('message', 'Dependent updated successfully.');
        } else {
            // Create new dependent
            $service->createDependent($this->member, $data);

            session()->flash('message', 'Dependent added successfully.');
        }

        $this->resetForm();
        $this->dispatch('dependentSaved');
    }

    public function confirmDependentDeletion(Dependent $dependent)
    {
        $this->dependentToDelete = $dependent;
        $this->confirmingDependentDeletion = true;
    }

    public function deleteDependent()
    {
        if ($this->dependentToDelete) {
            $service = DependentManagementServiceFactory::create();
            $service->deleteDependent($this->dependentToDelete);

            $this->confirmingDependentDeletion = false;
            $this->dependentToDelete = null;
            session()->flash('message', 'Dependent deleted successfully.');
        }
    }

    public function cancelDelete()
    {
        $this->confirmingDependentDeletion = false;
        $this->dependentToDelete = null;
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->dispatch('dependentFormCancelled');
    }

    public function resetForm()
    {
        $this->dependentId = null;
        $this->name = '';
        $this->identification_number = '';
        $this->birth_date = '';
        $this->relationship = '';
        $this->resetValidation();
    }

    #[Computed]
    public function dependents()
    {
        $service = DependentManagementServiceFactory::create();
        return $service->getDependentsForMember($this->member);
    }

    public function render()
    {


        return view('livewire.admin.dependent-management');
    }
}
