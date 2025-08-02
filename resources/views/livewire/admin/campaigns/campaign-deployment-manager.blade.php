<div>
  <div class="max-w-7xl mx-auto p-6 dark:bg-gray-800 dark:text-gray-200">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-200">Campaign Deployment</h1>
      <p class="text-gray-600 mt-2 dark:text-gray-200">Manage and deploy campaigns to websites</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
      <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
        <p class="text-green-800">{{ session('success') }}</p>
      </div>
    @endif

    @if (session()->has('error'))
      <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
        <p class="text-red-800">{{ session('error') }}</p>
      </div>
    @endif

    <!-- Deployment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-blue-100 text-blue-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Today's Deployments</p>
            <p class="text-2xl font-bold text-gray-900">{{ $deploymentStats['today_deployments'] ?? 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Queue Jobs</p>
            <p class="text-2xl font-bold text-gray-900">{{ $deploymentStats['pending_queue_jobs'] ?? 0 }}
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-green-100 text-green-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Active Campaigns</p>
            <p class="text-2xl font-bold text-gray-900">{{ $deploymentStats['active_campaigns'] ?? 0 }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-3 rounded-full bg-red-100 text-red-600">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Recent Failures</p>
            <p class="text-2xl font-bold text-gray-900">{{ $deploymentStats['recent_failures'] ?? 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow mb-8">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
      </div>
      <div class="p-6">
        <div class="flex flex-wrap gap-4">
          <!-- Deploy All Ready Button -->
          <button wire:click="deployAllReady" wire:loading.attr="disabled"
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50">
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
            class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 disabled:opacity-50">
            <span wire:loading.remove>Deploy Selected ({{ count($selectedCampaigns) }})</span>
            <span wire:loading>Deploying...</span>
          </button>

          <!-- Validate Selected Button -->
          <button wire:click="validateSelected"
            class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors duration-200">
            Validate Selected
          </button>

          <!-- Refresh Stats Button -->
          <button wire:click="loadDeploymentStats"
            class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Refresh Stats
          </button>
        </div>
      </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Campaigns</h2>
        <p class="text-sm text-gray-600 mt-1">Select campaigns to deploy or manage</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                <input type="checkbox" wire:model.live="selectAll"
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Campaign</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Priority</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Dates
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Websites</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Triggers</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ready
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($allCampaigns as $campaign)
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <input type="checkbox" wire:model.live="selectedCampaigns" value="{{ $campaign->id }}"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                  <div class="text-sm text-gray-500">ID: {{ $campaign->id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-md
                                @if ($campaign->status === 'active') bg-green-100 text-green-800
                                @elseif($campaign->status === 'scheduled') bg-yellow-100 text-yellow-800
                                @elseif($campaign->status === 'disabled') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($campaign->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $campaign->priority }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div>Start: {{ $campaign->start_at?->format('M j, Y H:i') }}</div>
                  <div>End: {{ $campaign->end_at?->format('M j, Y H:i') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $campaign->campaignWebsites->count() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $campaign->campaignTriggers->count() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @php
                    $isReady = collect($deployableCampaigns)->contains('id', $campaign->id);
                  @endphp
                  @if ($isReady)
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                      Ready
                    </span>
                  @else
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
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
    <div class="mt-8 bg-white rounded-lg shadow" wire:poll.60s="loadDeployments">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Recent Deployments</h2>
        <p class="text-sm text-gray-600 mt-1">Latest 20 deployments, auto-refreshing every 60 seconds</p>
      </div>
      <div class="p-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Campaign</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deployed At</th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Verification
              </th>
              <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($deployments as $deployment)
              <tr>
                <td class="px-4 py-2 text-sm text-gray-900">{{ $deployment->id }}</td>
                <td class="px-4 py-2 text-sm text-gray-900">{{ $deployment->campaign->name ?? 'N/A' }}</td>
                <td class="px-4 py-2">
                  <span
                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-md
                                    @if ($deployment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($deployment->status === 'queued') bg-yellow-100 text-yellow-800
                                    @elseif($deployment->status === 'scheduled') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                    @if ($deployment->status === 'completed')
                      <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    @elseif($deployment->status === 'queued')
                      <svg class="w-4 h-4 mr-1 text-yellow-500 animate-spin" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 2v4m0 0a8 8 0 018 8h-4" class="opacity-75" />
                      </svg>
                    @elseif($deployment->status === 'scheduled')
                      <svg class="w-4 h-4 mr-1 text-yellow-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                      </svg>
                    @else
                      <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    @endif
                    {{ ucfirst($deployment->status) }}
                  </span>
                </td>
                <td class="px-4 py-2 text-sm text-gray-500">
                  {{ $deployment->deployed_at?->format('M j, Y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    H:i') }}
                </td>
                <td class="px-4 py-2 text-sm">
                  @if (isset($deployment->metadata['verification']))
                    @if ($deployment->metadata['verification']['success'])
                      <span class="text-green-700">✔ Success</span>
                    @else
                      <span class="text-red-700">✖ Failed</span>
                    @endif
                    <div class="text-xs text-gray-500">
                      {{ $deployment->metadata['verification']['message'] ?? '' }}
                    </div>
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-2">
                  @if (in_array($deployment->status, ['failed', 'scheduled']))
                    <button wire:click="redeploy({{ $deployment->id }})"
                      class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors duration-200"
                      title="Retry deployment for this campaign">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <td colspan="6" class="px-4 py-4 text-center text-gray-400">No deployments found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Deployment Results -->
    @if ($lastDeploymentResult)
      <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Last Deployment Result</h2>
        </div>
        <div class="p-6">
          <pre class="bg-gray-100 p-4 rounded-lg overflow-auto text-sm">{{ json_encode($lastDeploymentResult, JSON_PRETTY_PRINT) }}</pre>
        </div>
      </div>
    @endif
  </div>

  <!-- Loading Overlay -->
  <div wire:loading.flex class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm mx-auto">
      <div class="flex items-center space-x-4">
        <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
          viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
          </circle>
          <path class="opacity-75" fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
          </path>
        </svg>
        <div>
          <p class="text-lg font-medium text-gray-900">Processing Deployment</p>
          <p class="text-sm text-gray-500">Please wait...</p>
        </div>
      </div>
    </div>
  </div>
</div>
