<?php

namespace App\Livewire\Admin\Websites;

use App\Models\Website;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class WebsitesComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $authFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'authFilter' => ['except' => ''],
        'sortField',
        'sortDirection',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingAuthFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->authFilter = '';
        $this->resetPage();
    }

    public function deleteWebsite(int $websiteId): void
    {
        $website = Website::findOrFail($websiteId);
        $website->delete();

        $this->resetPage();
        session()->flash('message', __('Website deleted successfully.'));
    }

    public function render()
    {
        $websites = Website::query()
            ->when($this->search, function ($query) {
                $query->where('url', 'like', '%' . $this->search . '%')
                    ->orWhere('api_url', 'like', '%' . $this->search . '%')
                    ->orWhereRaw('LOWER(url) like ?', ['%' . strtolower($this->search) . '%']);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->authFilter, function ($query) {
                $query->where('auth_type', $this->authFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $websiteTypes = \App\Enums\WebsiteTypeEnum::cases();

        return view('livewire.admin.websites.websites-component', [
            'websites' => $websites,
            'websiteTypes' => $websiteTypes,
        ]);
    }
}
