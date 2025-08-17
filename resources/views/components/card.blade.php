@props([
    'title' => null,
])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
  @if ($title || isset($header))
    <div class="border-b border-gray-200 dark:border-gray-700 p-6">
      @if (isset($header))
        {{ $header }}
      @else
        <h3 class="text-lg font-semibold">{{ $title }}</h3>
      @endif
    </div>
  @endif

  <div class="p-6">
    {{ $slot }}
  </div>
</div>
