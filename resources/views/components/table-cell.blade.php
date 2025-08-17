@props([
    'class' => '',
])

<td {{ $attributes->merge([
    'class' => 'px-6 py-4 text-sm text-gray-600 dark:text-gray-300 ' . $class,
]) }}>
  {{ $slot }}
</td>
