<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Campaign Manager') }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Google+Sans+Code:ital,wght@0,300..800;1,300..800&family=Quicksand:wght@300..700&display=swap"
    rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body x-cloak x-data="{ darkMode: $persist(false), sidebarOpen: false }" :class="{ 'dark': darkMode === true }"
  class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-400 transition-colors duration-200 antialiased">

  <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 lg:hidden">
    <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
  </div>

  <livewire:layout.sidebar />

  <div class="lg:ml-64 min-h-screen">
    <livewire:layout.header />

    <main class="p-6 bg-gray-50 dark:bg-gray-800 dark:text-gray-300 text-gray-800 min-h-screen">
      {{ $slot }}
    </main>
  </div>

  @stack('scripts')
  @yield('scripts')
  @livewireScripts
</body>

</html>