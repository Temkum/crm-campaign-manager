<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Livewire component for the dashboard overview.
 */
#[Layout('layouts.admin')]
class DashboardOverview extends Component
{
    public $metrics = [
        'campaigns_per_minute' => 23,
        'campaigns_past_hour' => 1347,
        'failed_campaigns_past_7_days' => 12,
        'status' => 'Active',
        'total_campaigns' => 47521,
        'max_throughput' => 156,
    ];

    public $workload = [
        ['queue' => 'default', 'jobs' => 247, 'processes' => 3, 'wait' => 'A few seconds'],
        ['queue' => 'emails', 'jobs' => 89, 'processes' => 2, 'wait' => '1 minute'],
        ['queue' => 'reports', 'jobs' => 12, 'processes' => 1, 'wait' => 'A few seconds'],
    ];

    public function mount()
    {
        // Initialize component
    }

    public function refreshMetrics()
    {
        // Refresh metrics logic
        $this->dispatch('metrics-refreshed');
    }

    public function render()
    {
        return view('livewire.admin.dashboard-overview');
    }
}
