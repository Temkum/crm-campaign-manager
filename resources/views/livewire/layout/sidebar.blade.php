<nav
  class="fixed top-0 left-0 z-50 h-full w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200 lg:translate-x-0"
  :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">

  {{-- logo --}}
  <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center space-x-2">
      <img src="{{ asset('assets/images/logo-dark.png') }}" alt="{{ config('app.name') }}"
        class="w-10 h-10 object-contain">

      <span class="text-lg font-semibold text-gray-900 dark:text-gray-400">{{ config('app.name') }}</span>
    </div>
    <button @click="sidebarOpen = false"
      class="lg:hidden p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </button>
  </div>

  {{-- navigation --}}
  <div class="p-4 space-y-2">
    @php
    $navItems = [
    [
    'route' => 'dashboard',
    'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z M7 7 5 5 5 5',
    'label' => 'Dashboard',
    ],
    [
    'route' => 'campaigns.index',
    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6
    0a2 2 0 002
    2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'label' => 'Campaigns',
    ],
    [
    'route' => 'campaigns.deployments',
    'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0
    012-2m0
    0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    'label' => 'Deployments',
    ],
    [
    'route' => 'websites.index',
    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    'label' => 'Websites',
    ],
    [
    'route' => 'markets.index',
    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'label' => 'Markets',
    ],
    [
    'route' => 'operators.index',
    'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L12 12m6.364 6.364L12 12m0
    0L5.636
    5.636M12 12l6.364-6.364M12 12l-6.364 6.364',
    'label' => 'Operators',
    ],
    ];
    @endphp

    @foreach ($navItems as $item)
    @php
    $isActive = request()->routeIs($item['route']);
    $activeClasses = 'flex items-center space-x-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600
    dark:text-blue-400 border
    border-blue-200 dark:border-blue-800';
    $inactiveClasses = 'flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700
    transition-colors text-gray-700
    dark:text-gray-200 hover:text-gray-900 dark:hover:text-gray-100';
    @endphp

    <a href="{{ route($item['route']) }}" wire:navigate class="{{ $isActive ? $activeClasses : $inactiveClasses }}">
      <svg class="w-5 h-5 {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}"
        fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path>
      </svg>
      <span class="{{ $isActive ? 'font-medium' : '' }}">{{ $item['label'] }}</span>
    </a>
    @endforeach
  </div>

  <div class="absolute bottom-4 left-4 right-4">
    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
      <div class="flex items-center space-x-2">
        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
        <span class="text-xs text-gray-600 dark:text-gray-400">System Online</span>
      </div>
    </div>
  </div>
</nav>