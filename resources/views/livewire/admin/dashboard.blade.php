<div wire:poll.30s="refreshMetrics">
  {{-- overview section --}}
  <section class="mb-8">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold">{{ __('Overview') }}</h2>
      <div class="flex items-center space-x-2">
        <div class="flex items-center space-x-2 text-sm">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <span class="text-green-600 dark:text-green-400 font-medium">{{ __('Active') }}</span>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
      @foreach ($metrics as $metric)
        @php
          $colorClasses = [
              'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-600 dark:text-blue-400'],
              'green' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'text' => 'text-green-600 dark:text-green-400'],
              'red' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-600 dark:text-red-400'],
              'purple' => [
                  'bg' => 'bg-purple-50 dark:bg-purple-900/20',
                  'text' => 'text-purple-600 dark:text-purple-400',
              ],
              'yellow' => [
                  'bg' => 'bg-yellow-50 dark:bg-yellow-900/20',
                  'text' => 'text-yellow-600 dark:text-yellow-400',
              ],
          ];
          $colors = $colorClasses[$metric['color']] ?? $colorClasses['blue'];
        @endphp

        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ $metric['title'] }}</div>
              <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metric['value'] }}</div>
            </div>
            <div class="w-12 h-12 {{ $colors['bg'] }} rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $metric['icon'] }}"></path>
              </svg>
            </div>
          </div>

          <div class="flex items-center mt-2 text-xs">
            @if (isset($metric['change']))
              <span
                class="{{ $metric['trend'] === 'up' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $metric['trend'] === 'up' ? '↗' : '↘' }} {{ $metric['change'] }}
              </span>
              <span class="text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
            @elseif(isset($metric['subtitle']))
              @if (isset($metric['pulse']))
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
              @endif
              <span class="text-gray-500 dark:text-gray-400">{{ $metric['subtitle'] }}</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  </section>

  {{-- current workload --}}
  {{--  <section class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold">Current Workload</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Queue</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jobs</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Processes</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Wait</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($workload as $queue)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $queue['queue'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $queue['jobs'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $queue['processes'] }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="{{ $queue['status'] === 'good' ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                        {{ $queue['wait'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section> --}}

  {{-- supervisor status --}}
  {{-- <section>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Supervisor Status</h3>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-green-600 dark:text-green-400">tem-XgH6</span>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
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
                        <div class="font-medium text-blue-600 dark:text-blue-400">Auto</div>
                    </div>
                </div>
                
                <!-- Process Details -->
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-4">Process Details</h4>
                    <div class="space-y-3">
                        @foreach ($processes as $process)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-2 h-2 {{ $process['status'] === 'active' ? 'bg-green-500' : 'bg-yellow-500' }} rounded-full"></div>
                                    <span class="text-sm font-medium">Process #{{ $process['id'] }}</span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300">
                                    <span class="mr-4">Queue: {{ $process['queue'] }}</span>
                                    <span>Jobs: {{ $process['jobs'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
</div>
