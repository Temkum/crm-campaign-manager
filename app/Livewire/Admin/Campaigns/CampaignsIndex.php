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
        $campaigns = Campaign::where('name', 'like', '%' . $this->search . '%')
            ->orderByDesc('updated_at')
            ->paginate(15);
        return view('livewire.admin.campaigns.campaigns-index', [
            'campaigns' => $campaigns,
        ]);
    }
}
