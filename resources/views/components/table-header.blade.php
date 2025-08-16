@props([
    'sortable' => false,
    'direction' => null,
])

<th
  {{ $attributes->merge([
      'class' => 'text-left px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider',
  ]) }}>
  @if ($sortable)
    <button class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
      <span>{{ $slot }}</span>
      @if ($direction === 'asc')
        <x-heroicon-m-chevron-up class="w-4 h-4" />
      @elseif($direction === 'desc')
        <x-heroicon-m-chevron-down class="w-4 h-4" />
      @else
        <x-heroicon-m-chevron-up-down class="w-4 h-4 opacity-50" />
      @endif
    </button>
  @else
    {{ $slot }}
  @endif
</th>
