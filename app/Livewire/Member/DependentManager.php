<?php

namespace App\Livewire\Member;

use App\Actions\Members\AddDependentAction;
use App\Actions\Members\UpdateDependentAction;
use App\Models\Dependent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DependentManager extends Component
{
    public $dependents = [];
    public $showForm = false;
    public $isEditing = false;
    public $editingDependentId = null;
    
    // Form fields
    public $name = '';
    public $identification_number = '';
    public $birth_date = '';
    public $relationship = '';
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'identification_number' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date',
        'relationship' => 'required|string|max:50',
    ];
    
    public function mount()
    {
        $this->loadDependents();
    }
    
    public function loadDependents()
    {
        $this->dependents = Auth::user()->dependents;
    }
    
    public function showAddForm()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }
    
    public function showEditForm(Dependent $dependent)
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = true;
        $this->editingDependentId = $dependent->id;
        
        $this->name = $dependent->name;
        $this->identification_number = $dependent->identification_number;
        $this->birth_date = optional($dependent->birth_date)->format('Y-m-d');
        $this->relationship = $dependent->relationship;
    }
    
    public function resetForm()
    {
        $this->name = '';
        $this->identification_number = '';
        $this->birth_date = '';
        $this->relationship = '';
        $this->editingDependentId = null;
    }
    
    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }
    
    public function save(AddDependentAction $addDependent, UpdateDependentAction $updateDependent)
    {
        $this->validate();
        
        $data = [
            'name' => $this->name,
            'identification_number' => $this->identification_number,
            'birth_date' => $this->birth_date,
            'relationship' => $this->relationship,
        ];
        
        if ($this->isEditing && $this->editingDependentId) {
            $updateDependent->execute($this->editingDependentId, $data);
            $this->dispatch('notify', ['message' => __('Dependent updated successfully'), 'type' => 'success']);
        } else {
            $addDependent->execute(Auth::id(), $data);
            $this->dispatch('notify', ['message' => __('Dependent added successfully'), 'type' => 'success']);
        }
        
        $this->loadDependents();
        $this->showForm = false;
        $this->resetForm();
    }
    
    public function delete(Dependent $dependent)
    {
        // Check if the dependent belongs to the authenticated user
        if ($dependent->user_id !== Auth::id()) {
            abort(403);
        }
        
        $dependent->delete();
        $this->loadDependents();
        $this->dispatch('notify', ['message' => __('Dependent removed successfully'), 'type' => 'success']);
    }
    
    public function render()
    {
        return view('livewire.member.dependent-manager');
    }
} 