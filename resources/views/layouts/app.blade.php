<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }" :class="{ 'dark': darkMode }"
  x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Campaign Manager') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
  <!-- Mobile backdrop -->
  <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 lg:hidden">
    <div @click="sidebarOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
  </div>

  <!-- Sidebar -->
  <nav
    class="fixed top-0 left-0 z-50 h-full w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200 lg:translate-x-0"
    :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center space-x-2">
        <div
          class="w-8 h-8 bg-gradient-to-r from-primary-500 to-purple-600 rounded-lg flex items-center justify-center">
          <x-heroicon-s-bolt class="w-5 h-5 text-white" />
        </div>
        <span class="text-lg font-semibold">Campaign Manager</span>
      </div>
      <button @click="sidebarOpen = false" class="lg:hidden p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
        <x-heroicon-o-x-mark class="w-6 h-6" />
      </button>
    </div>

    <!-- Navigation -->
    <div class="p-4 space-y-2">
      <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" wire:navigate>
        <x-heroicon-o-squares-2x2 class="w-5 h-5" />
        <span class="font-medium">Dashboard</span>
      </x-nav-link>

      <x-nav-link href="#" :active="request()->routeIs('monitoring')" wire:navigate>
        <x-heroicon-o-chart-bar class="w-5 h-5" />
        <span>Monitoring</span>
      </x-nav-link>

      <x-nav-link href="#" :active="request()->routeIs('metrics')" wire:navigate>
        <x-heroicon-o-chart-bar-square class="w-5 h-5" />
        <span>Metrics</span>
      </x-nav-link>

      <x-nav-link href="{{ route('campaigns.index') }}" :active="request()->routeIs('campaigns')" wire:navigate>
        <x-heroicon-o-inbox-stack class="w-5 h-5" />
        <span>Campaigns</span>
      </x-nav-link>

      <x-nav-link href="#" :active="request()->routeIs('jobs.pending')" wire:navigate>
        <x-heroicon-o-clock class="w-5 h-5" />
        <span>Pending Jobs</span>
      </x-nav-link>

      <x-nav-link href="#" :active="request()->routeIs('jobs.completed')" wire:navigate>
        <x-heroicon-o-check-circle class="w-5 h-5" />
        <span>Completed Jobs</span>
      </x-nav-link>

      <x-nav-link href="#" :active="request()->routeIs('jobs.failed')" wire:navigate>
        <x-heroicon-o-x-circle class="w-5 h-5" />
        <span>Failed Jobs</span>
      </x-nav-link>
    </div>
  </nav>

  <!-- Main content -->
  <div class="lg:ml-64 min-h-screen">
    <!-- Top bar -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
      <div class="flex items-center justify-between px-4 py-3">
        <div class="flex items-center space-x-4">
          <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            <x-heroicon-o-bars-3 class="w-6 h-6" />
          </button>
          <h1 class="text-xl font-semibold">{{ config('app.name', 'Campaign Manager') }}</h1>
        </div>

        <div class="flex items-center space-x-4">
          <!-- Theme toggle -->
          <button @click="darkMode = !darkMode"
            class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <x-heroicon-o-moon x-show="!darkMode" class="w-5 h-5" />
            <x-heroicon-o-sun x-show="darkMode" class="w-5 h-5" />
          </button>

          <!-- Profile dropdown -->
          <x-dropdown>
            <x-slot name="trigger">
              <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                <div
                  class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                  <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name ?? 'JD', 0, 2) }}</span>
                </div>
              </button>
            </x-slot>

            <x-dropdown-link href="#">
              Profile
            </x-dropdown-link>
            <x-dropdown-link href="#">
              Settings
            </x-dropdown-link>
            {{-- <x-dropdown-divider /> --}}
            <form method="POST" action="#">
              @csrf
              <x-dropdown-link href="#" onclick="event.preventDefault(); this.closest('form').submit();"
                class="text-red-600 dark:text-red-400">
                Sign out
              </x-dropdown-link>
            </form>
          </x-dropdown>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="p-6">
      {{ $slot }}
    </main>
  </div>

  @stack('scripts')
  @yield('scripts')
  @livewireScripts
</body>

</html>
