<div wire:poll.30s="refreshMetrics">
  {{-- overview section --}}
  <section class="mb-8">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold">Overview</h2>
      <div class="flex items-center space-x-2">
        <div class="flex items-center space-x-2 text-sm">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-green-600 dark:text-green-400 font-medium">{{ $metrics['status'] }}</span>
        </div>
      </div>
    </div>

    {{-- metrics grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
      <x-metric-card title="Campaigns Per Minute" value="{{ $metrics['campaigns_per_minute'] }}" change="12%"
        trend="up" icon="heroicon-o-clock" color="blue" />

      <x-metric-card title="Campaigns Past Hour" value="{{ number_format($metrics['campaigns_past_hour']) }}"
        change="8%" trend="up" icon="heroicon-o-arrow-trending-up" color="green" />

      <x-metric-card title="Failed Campaigns Past 7 Days" value="{{ $metrics['failed_campaigns_past_7_days'] }}"
        change="3%" trend="up" icon="heroicon-o-exclamation-triangle" color="red" />

      <x-metric-card title="Status" value="{{ $metrics['status'] }}" subtitle="All systems operational"
        icon="heroicon-o-check-circle" color="green" :show-pulse="true" />

      <x-metric-card title="Total Campaigns" value="{{ number_format($metrics['total_campaigns']) }}" change="23%"
        trend="up" icon="heroicon-o-inbox-stack" color="purple" />

      <x-metric-card title="Max Throughput" value="{{ $metrics['max_throughput'] }}/min" subtitle="Peak at 2:30 PM"
        icon="heroicon-o-bolt" color="yellow" />
    </div>
  </section>

  {{-- current workload --}}
  {{-- <section class="mb-8">
    <x-card title="Current Workload">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <x-table-header>Queue</x-table-header>
              <x-table-header>Jobs</x-table-header>
              <x-table-header>Processes</x-table-header>
              <x-table-header>Wait</x-table-header>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($workload as $queue)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <x-table-cell class="font-medium">{{ $queue['queue'] }}</x-table-cell>
                <x-table-cell>{{ $queue['jobs'] }}</x-table-cell>
                <x-table-cell>{{ $queue['processes'] }}</x-table-cell>
                <x-table-cell>
                  <span
                    class="{{ $queue['wait'] === 'A few seconds' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                    {{ $queue['wait'] }}
                  </span>
                </x-table-cell>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </x-card>
  </section> --}}

  {{-- supervisor status --}}
  {{-- <section>
    <x-card>
      <x-slot name="header">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold">Supervisor Status</h3>
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-sm text-green-600 dark:text-green-400">tem-XgH6</span>
          </div>
        </div>
      </x-slot>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Supervisor</div>
          <div class="font-medium">supervisor-1</div>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Queues</div>
          <div class="font-medium">default, emails, reports</div>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Processes</div>
          <div class="font-medium">6</div>
        </div>
        <div>
          <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Balancing</div>
          <div class="font-medium text-primary-600 dark:text-primary-400">Auto</div>
        </div>
      </div>
    </x-card>
  </section> --}}
</div>
