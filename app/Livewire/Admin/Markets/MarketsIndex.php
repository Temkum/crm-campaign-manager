<?php

namespace App\Livewire\Admin\Markets;

use App\Models\Market;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MarketsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showDeleteModal = false;
    public $marketToDelete = null;

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

    public function confirmDelete($marketId)
    {
        $this->marketToDelete = Market::findOrFail($marketId);
        $this->showDeleteModal = true;
    }

    public function deleteMarket()
    {
        if ($this->marketToDelete) {
            $this->marketToDelete->delete();
            $this->showDeleteModal = false;
            $this->marketToDelete = null;

            session()->flash('success', 'Market deleted successfully.');
            $this->dispatch('market-deleted');
        }
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->marketToDelete = null;
    }

    #[On('market-created')]
    #[On('market-updated')]
    public function refreshList()
    {
        // This will refresh the component
    }

    public function render()
    {
        $markets = Market::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('iso_code', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.markets.markets-index', [
            'markets' => $markets
        ]);
    }
}
