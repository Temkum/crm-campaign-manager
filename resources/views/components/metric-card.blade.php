@props([
    'title',
    'value',
    'change' => null,
    'trend' => null,
    'subtitle' => null,
    'icon' => 'heroicon-o-chart-bar',
    'color' => 'blue',
    'showPulse' => false,
])

@php
  $colorClasses = [
      'blue' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
      'green' => 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400',
      'red' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
      'purple' => 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400',
      'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400',
  ];

  $trendColors = [
      'up' => 'text-green-600 dark:text-green-400',
      'down' => 'text-red-600 dark:text-red-400',
  ];
@endphp

<div
  class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ $title }}</div>
      <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $value }}</div>
    </div>
    <div class="w-12 h-12 {{ $colorClasses[$color] }} rounded-lg flex items-center justify-center">
      <x-dynamic-component :component="$icon" class="w-6 h-6" />
    </div>
  </div>

  @if ($change || $subtitle)
    <div class="flex items-center mt-2 text-xs">
      @if ($change)
        <span class="{{ $trendColors[$trend] ?? 'text-gray-500 dark:text-gray-400' }}">
          {{ $trend === 'up' ? '↗' : '↘' }} {{ $change }}
        </span>
        <span class="text-gray-500 dark:text-gray-400 ml-1">vs last period</span>
      @elseif($subtitle)
        @if ($showPulse)
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></div>
        @endif
        <span class="text-gray-500 dark:text-gray-400">{{ $subtitle }}</span>
      @endif
    </div>
  @endif
</div>
