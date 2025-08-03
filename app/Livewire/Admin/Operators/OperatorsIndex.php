<?php

namespace App\Livewire\Admin\Operators;

use App\Models\Operator;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class OperatorsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showDeleteModal = false;
    public $operatorToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function confirmDelete($operatorId)
    {
        $this->operatorToDelete = Operator::findOrFail($operatorId);
        $this->showDeleteModal = true;
    }

    public function deleteOperator()
    {
        if ($this->operatorToDelete) {
            $this->operatorToDelete->delete();
            $this->showDeleteModal = false;
            $this->operatorToDelete = null;

            session()->flash('success', 'Operator deleted successfully.');
            $this->dispatch('operator-deleted');
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->operatorToDelete = null;
    }

    #[On('operator-created')]
    #[On('operator-updated')]
    public function refreshList()
    {
        // This will refresh the component
    }

    public function render()
    {
        $operators = Operator::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('website_url', 'like', '%' . $this->search . '%');
            })
            ->withCount('campaigns')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.operators.operators-index', [
            'operators' => $operators
        ]);
    }
}
