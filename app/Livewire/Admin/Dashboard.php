<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $metrics;
    public $workload;
    public $processes;

    public function mount()
    {
        $this->loadData();
    }

    public function refreshMetrics()
    {
        $this->loadData();
        $this->dispatch('metrics-refreshed');
    }

    private function loadData()
    {
        // Simulate data loading - replace with real logic
        $this->metrics = [
            [
                'title' => 'Campaigns Per Minute',
                'value' => '23',
                'change' => '12%',
                'trend' => 'up',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'blue'
            ],
            [
                'title' => 'Campaigns Past Hour',
                'value' => '1,347',
                'change' => '8%',
                'trend' => 'up',
                'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
                'color' => 'green'
            ],
            [
                'title' => 'Failed Campaigns Past 7 Days',
                'value' => '12',
                'change' => '3%',
                'trend' => 'up',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z',
                'color' => 'red'
            ],
            [
                'title' => 'Status',
                'value' => 'Active',
                'subtitle' => 'All systems operational',
                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'green',
                'pulse' => true
            ],
            [
                'title' => 'Total Campaigns',
                'value' => '47,521',
                'change' => '23%',
                'trend' => 'up',
                'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
                'color' => 'purple'
            ],
            [
                'title' => 'Max Throughput',
                'value' => '156/min',
                'subtitle' => 'Peak at 2:30 PM',
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                'color' => 'yellow'
            ]
        ];

        $this->workload = [
            ['queue' => 'default', 'jobs' => 247, 'processes' => 3, 'wait' => 'A few seconds', 'status' => 'good'],
            ['queue' => 'emails', 'jobs' => 89, 'processes' => 2, 'wait' => '1 minute', 'status' => 'warning'],
            ['queue' => 'reports', 'jobs' => 12, 'processes' => 1, 'wait' => 'A few seconds', 'status' => 'good'],
        ];

        $this->processes = [
            ['id' => 1, 'queue' => 'default', 'jobs' => 82, 'status' => 'active'],
            ['id' => 2, 'queue' => 'emails', 'jobs' => 45, 'status' => 'active'],
            ['id' => 3, 'queue' => 'default', 'jobs' => 120, 'status' => 'busy'],
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
