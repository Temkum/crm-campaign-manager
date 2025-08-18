<div class="mx-auto px-4 py-6">
  <nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm">
      <li class="flex items-center">
        <a href="{{ route('dashboard') }}"
          class="ml-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Dashboard</a>
      </li>
      <li class="flex items-center">
        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"></path>
        </svg>
        <span class="ml-2 text-gray-900 dark:text-white font-medium">Campaigns</span>
      </li>
    </ol>
  </nav>

  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-200">Campaign Deployment</h1>
      <p class="text-gray-600 mt-2 dark:text-gray-400">Manage and deploy campaigns to websites</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-md">
      <p class="text-green-800 dark:text-green-200">{{ session('success') }}</p>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-md">
      <p class="text-red-800 dark:text-red-200">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Deployment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-800">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Today's Deployments</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $deploymentStats['today_deployments'] ?? 0
              }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-800">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Queue Jobs</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $deploymentStats['pending_queue_jobs'] ??
              0 }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-800">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Campaigns</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $deploymentStats['active_campaigns'] ?? 0
              }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6 border border-gray-100 dark:border-gray-800">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Recent Failures</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $deploymentStats['recent_failures'] ?? 0
              }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow mb-8 border border-gray-200 dark:border-gray-800">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Quick Actions</h2>
      </div>
      <div class="p-6">
        <div class="flex flex-wrap gap-4">
          <!-- Deploy All Ready Button -->
          <button wire:click="deployAllReady" wire:loading.attr="disabled"
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50">
            <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
              fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
              </circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            <span wire:loading.remove>Deploy All Ready ({{ count($deployableCampaigns) }})</span>
            <span wire:loading>Deploying...</span>
          </button>

          <!-- Deploy Selected Button -->
          <button wire:click="deploySelected" wire:loading.attr="disabled"
            class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50">
            <span wire:loading.remove>Deploy Selected ({{ count($selectedCampaigns) }})</span>
            <span wire:loading>Deploying...</span>
          </button>

          <!-- Validate Selected Button -->
          <button wire:click="validateSelected"
            class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-700 dark:hover:bg-yellow-800 text-white font-medium rounded-lg transition-colors duration-200">
            Validate Selected
          </button>

          <!-- Refresh Stats Button -->
          <button wire:click="loadDeploymentStats"
            class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-800 text-white font-medium rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh Stats
          </button>
        </div>
      </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-800">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Campaigns</h2>
        <p class="text-sm text-gray-600 mt-1 dark:text-gray-400">Select campaigns to deploy or manage</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <input type="checkbox" wire:model.live="selectAll"
                  class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Campaign</th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Status</th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Priority</th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Dates
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Websites</th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Triggers</th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Ready
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($allCampaigns as $campaign)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
              <td class="px-6 py-4 whitespace-nowrap">
                <input type="checkbox" wire:model.live="selectedCampaigns" value="{{ $campaign->id }}"
                  class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <a href="{{ route('campaigns.edit', $campaign->id) }}">
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $campaign->name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $campaign->id }}</div>
                </a>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-md
                                @if ($campaign->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                @elseif($campaign->status === 'scheduled') bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                @elseif($campaign->status === 'disabled') bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200
                                @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                  {{ ucfirst($campaign->status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ $campaign->priority }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                <div>Start: {{ $campaign->start_at?->format('M j, Y H:i') }}</div>
                <div>End: {{ $campaign->end_at?->format('M j, Y H:i') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ $campaign->campaignWebsites->count() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                {{ $campaign->campaignTriggers->count() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @php
                $isReady = collect($deployableCampaigns)->contains('id', $campaign->id);
                @endphp
                @if ($isReady)
                <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                  Ready
                </span>
                @else
                <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                  Not Ready
                </span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Deployments Table (Auto-refresh) -->
    <div class="mt-8 bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-800"
      wire:poll.60s="loadDeployments">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Recent Deployments</h2>
        <p class="text-sm text-gray-600 mt-1 dark:text-gray-400">Latest 20 deployments, auto-refreshing every 60 seconds
        </p>
      </div>
      <div class="p-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">ID</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Campaign
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Deployed At
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                Verification
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($deployments as $deployment)
            <tr>
              <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $deployment->id }}</td>
              <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">{{ $deployment->campaign->name ?? 'N/A' }}
              </td>
              <td class="px-4 py-2">
                @php
                $status = $deployment->status ?? 'unknown';
                $statusClass = match($status) {
                'successful', 'completed' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                'in_progress', 'queued', 'scheduled', 'partial' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800
                dark:text-yellow-200',
                'failed' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200',
                default => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200'
                };
                @endphp
                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md {{ $statusClass }}">
                  @if (in_array($status, ['successful','completed']))
                  <svg class="w-4 h-4 mr-1 text-green-500 dark:text-green-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  @elseif (in_array($status, ['in_progress','queued','scheduled','partial']))
                  <svg class="w-4 h-4 mr-1 text-yellow-500 dark:text-yellow-300 animate-spin" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 0a8 8 0 018 8h-4"
                      class="opacity-75" />
                  </svg>
                  @elseif ($status === 'failed')
                  <svg class="w-4 h-4 mr-1 text-red-500 dark:text-red-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  @else
                  <svg class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  @endif
                  {{ ucwords(str_replace('_', ' ', $status)) }}
                </span>
              </td>
              <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $deployment->deployed_at?->format('M j, Y H:i') }}
              </td>
              <td class="px-4 py-2 text-sm">
                @php
                $verification = data_get($deployment, 'metadata.verification');
                @endphp
                @if (is_array($verification))
                @if (data_get($verification, 'success') === true)
                <span class="text-green-700 dark:text-green-300">✔ Success</span>
                @elseif (data_get($verification, 'success') === false)
                <span class="text-red-700 dark:text-red-300">✖ Failed</span>
                @else
                <span class="text-gray-400 dark:text-gray-300">-</span>
                @endif
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  {{ data_get($verification, 'message', '') }}
                </div>
                @else
                <span class="text-gray-400 dark:text-gray-300">-</span>
                @endif
              </td>
              <td class="px-4 py-2">
                @if (in_array($deployment->status, ['failed', 'scheduled', 'partial']))
                <button wire:click="redeploy({{ $deployment->id }})"
                  class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white text-xs font-medium rounded transition-colors duration-200"
                  title="Retry deployment for this campaign">
                  <svg class="w-4 h-4 mr-1 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582M20 20v-5h-.581M5.419 19A9 9 0 1021 12.003M19.999 15v2a2 2 0 01-2 2h-2" />
                  </svg>
                  Redeploy
                </button>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="px-4 py-4 text-center text-gray-400 dark:text-gray-300">No deployments found.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Deployment Results -->
    @if ($lastDeploymentResult)
    <div class="mt-8 bg-white dark:bg-gray-900 rounded-lg shadow border border-gray-200 dark:border-gray-800">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Last Deployment Result</h2>
      </div>
      <div class="p-6">
        <pre
          class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg overflow-auto text-sm text-gray-900 dark:text-gray-100">{{ json_encode($lastDeploymentResult, JSON_PRETTY_PRINT) }}</pre>
      </div>
    </div>
    @endif
  </div>

  <!-- Loading Overlay -->
  <div wire:loading.flex
    class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-80 z-50 items-center justify-center">
    <div class="bg-white dark:bg-gray-900 rounded-lg p-6 max-w-sm mx-auto border border-gray-200 dark:border-gray-800">
      <div class="flex items-center space-x-4">
        <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-300" xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
          </circle>
          <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
          </path>
        </svg>
        <div>
          <p class="text-lg font-medium text-gray-900 dark:text-gray-100">Processing Deployment</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">Please wait...</p>
        </div>
      </div>
    </div>
  </div>
</div>