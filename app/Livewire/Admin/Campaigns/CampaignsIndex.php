<?php

namespace App\Livewire\Admin\Campaigns;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Campaign;

#[Layout('layouts.app')]
class CampaignsIndex extends Component
{
    public $search = '';
    public function render()
    {
        // You can load any necessary data here, for example:
        $campaigns = Campaign::where('name', 'like', '%' . $this->search . '%')->paginate(10);
        return view('livewire.admin.campaigns.campaigns-index', compact('campaigns'));
    }
}