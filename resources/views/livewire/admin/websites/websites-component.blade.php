<div class="space-y-6">
  <nav class="flex" aria-label="Breadcrumb">
    <ol role="list" class="flex items-center space-x-2">
      <li>
        <div class="flex items-center">
          <a href="{{ route('dashboard') }}"
            class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">{{
            __('Dashboard') }}</a>
        </div>
      </li>
      <li>
        <div class="flex items-center">
          <svg class="h-5 w-5 flex-shrink-0 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
              d="M8.22 5.22a.75.75 0 011.06 0l4.25 4.25a.75.75 0 010 1.06l-4.25 4.25a.75.75 0 01-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 010-1.06z"
              clip-rule="evenodd" />
          </svg>
          <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-100" aria-current="page">{{
            __('Websites') }}</span>
        </div>
      </li>
    </ol>
  </nav>
  <div class="px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumb -->

    <div class="mt-6">
      <!-- Header -->
      <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Websites') }}</h1>
          <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
            {{ __('Manage all websites in your account including their API configurations and authentication settings.')
            }}
          </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
          <a href="{{ route('websites.create') }}"
            class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 dark:bg-indigo-700 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors sm:w-auto">
            <svg class="-ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ __('Add Website') }}
          </a>
        </div>
      </div>

      <!-- Filters -->
      <div class="mt-6 bg-white dark:bg-gray-900 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <!-- Search -->
            <div class="sm:col-span-3">
              <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{{
                __('Search') }}</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" id="search"
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md leading-5 bg-white dark:bg-gray-800 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 dark:focus:placeholder-gray-500 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm text-gray-900 dark:text-gray-100"
                  placeholder="{{ __('Search by URL, API URL, or domain...') }}">
                @if ($search)
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                  <button type="button" wire:click="$set('search', '')"
                    class="text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:text-gray-500 dark:focus:text-gray-300 transition-colors">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
                @endif
              </div>
            </div>

            <!-- Type Filter -->
            <div class="sm:col-span-2">
              <label for="type_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{{
                __('Website Type') }}</label>
              <select wire:model.live="typeFilter" id="type_filter"
                class="block w-full rounded-md border-gray-300 dark:border-gray-700 py-2 pl-3 pr-10 text-base focus:border-indigo-500 dark:focus:border-indigo-400 focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                <option value="">{{ __('All Types') }}</option>
                @foreach ($websiteTypes as $type)
                <option value="{{ $type->value }}">{{ $type->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- Auth Type Filter -->
            <div class="sm:col-span-1">
              <label for="auth_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">{{
                __('Auth Type') }}</label>
              <select wire:model.live="authFilter" id="auth_filter"
                class="block w-full rounded-md border-gray-300 dark:border-gray-700 py-2 pl-3 pr-10 text-base focus:border-indigo-500 dark:focus:border-indigo-400 focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                <option value="">{{ __('All') }}</option>
                <option value="NONE">{{ __('None') }}</option>
                <option value="TOKEN">{{ __('Token') }}</option>
                <option value="BASIC">{{ __('Basic') }}</option>
              </select>
            </div>
          </div>

          <!-- Results Summary -->
          @if ($search || $typeFilter || $authFilter)
          <div class="mt-4 text-sm text-gray-600 dark:text-gray-300">
            <span class="font-medium">{{ $websites->total() }}</span>
            {{ __('websites found') }}
            @if ($search)
            {{ __('matching') }} "<span class="font-medium">{{ $search }}</span>"
            @endif
            <button wire:click="resetFilters"
              class="btn-sm ml-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-medium">
              {{ __('Clear all filters') }}
            </button>
          </div>
          @endif
        </div>
      </div>

      <!-- Website Table -->
      <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
            <div
              class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg bg-white dark:bg-gray-900">
              <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th scope="col"
                      class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">
                      <a href="#" wire:click.prevent="sortBy('url')"
                        class="group inline-flex items-center hover:text-gray-700 dark:hover:text-gray-300">
                        {{ __('Website') }}
                        <span
                          class="ml-2 flex-none rounded text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300">
                          @if ($sortField === 'url' && $sortDirection === 'asc')
                          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @elseif($sortField === 'url' && $sortDirection === 'desc')
                          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @else
                          <svg class="h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @endif
                        </span>
                      </a>
                    </th>
                    <th scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                      <a href="#" wire:click.prevent="sortBy('type')"
                        class="group inline-flex items-center hover:text-gray-700 dark:hover:text-gray-300">
                        {{ __('Type') }}
                        <span
                          class="ml-2 flex-none rounded text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300">
                          @if ($sortField === 'type' && $sortDirection === 'asc')
                          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @elseif($sortField === 'type' && $sortDirection === 'desc')
                          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @else
                          <svg class="h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @endif
                        </span>
                      </a>
                    </th>
                    <th scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                      {{ __('API URL') }}
                    </th>
                    <th scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                      {{ __('Authentication') }}
                    </th>
                    <th scope="col"
                      class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                      <a href="#" wire:click.prevent="sortBy('created_at')"
                        class="group inline-flex items-center hover:text-gray-700 dark:hover:text-gray-300">
                        {{ __('Created') }}
                        <span
                          class="ml-2 flex-none rounded text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300">
                          @if ($sortField === 'created_at' && $sortDirection === 'asc')
                          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 01-1.08 0l4.25-4.5a.75.75 0 01-.02 1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @elseif($sortField === 'created_at' && $sortDirection === 'desc')
                          <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @else
                          <svg class="h-5 w-5 opacity-0 group-hover:opacity-100 transition-opacity"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                              d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z"
                              clip-rule="evenodd" />
                          </svg>
                          @endif
                        </span>
                      </a>
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                      <span class="sr-only">{{ __('Actions') }}</span>
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                  @forelse($websites as $website)
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                      <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0">
                          <div
                            class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <img class="h-6 w-6 rounded"
                              src="https://www.google.com/s2/favicons?domain={{ parse_url($website->url, PHP_URL_HOST) }}&sz=32"
                              alt="{{ $website->url }} favicon"
                              onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500 hidden" fill="none"
                              stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                            </svg>
                          </div>
                        </div>
                        <div class="ml-4">
                          <div class="font-medium text-gray-900 dark:text-gray-100">
                            <a href="{{ $website->url }}" target="_blank" rel="noopener noreferrer"
                              class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                              {{ $website->url }}
                              <svg class="inline h-3 w-3 ml-1 text-gray-400 dark:text-gray-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                </path>
                              </svg>
                            </a>
                          </div>
                          @if ($website->api_url)
                          <div class="text-gray-500 dark:text-gray-400 text-xs mt-1">
                            API: <span class="font-mono">{{ Str::limit($website->api_url, 50) }}</span>
                          </div>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                      @if ($website->type)
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                        {{ $website->type->name }}
                      </span>
                      @else
                      <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200">
                        {{ __('Not Set') }}
                      </span>
                      @endif
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                      @if ($website->api_url)
                      <div class="max-w-xs truncate">
                        <a href="{{ $website->api_url }}" target="_blank" rel="noopener noreferrer"
                          class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-mono text-xs">
                          {{ $website->api_url }}
                          <svg class="inline h-3 w-3 ml-1 text-indigo-400 dark:text-indigo-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                            </path>
                          </svg>
                        </a>
                      </div>
                      @else
                      <span class="text-gray-400 dark:text-gray-500 text-xs">{{ __('No API URL') }}</span>
                      @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                      @php
                      $authConfig = [
                      'NONE' => [
                      'label' => 'None',
                      'color' => 'gray',
                      'darkColor' => 'gray',
                      'icon' =>
                      'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636',
                      ],
                      'TOKEN' => [
                      'label' => 'Token',
                      'color' => 'green',
                      'darkColor' => 'green',
                      'icon' =>
                      'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0
                      01.293-.707l5.964-5.964A6 6 0 1121 9z',
                      ],
                      'BASIC' => [
                      'label' => 'Basic Auth',
                      'color' => 'blue',
                      'darkColor' => 'blue',
                      'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                      ],
                      ];
                      $auth = $authConfig[$website->auth_type] ?? $authConfig['NONE'];
                      @endphp
                      <span
                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-{{ $auth['color'] }}-100 dark:bg-{{ $auth['darkColor'] }}-900 text-{{ $auth['color'] }}-800 dark:text-{{ $auth['darkColor'] }}-200">
                        <svg class="mr-1.5 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $auth['icon'] }}">
                          </path>
                        </svg>
                        {{ $auth['label'] }}
                      </span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                      <div class="flex flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $website->created_at->format('M d,
                          Y') }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $website->created_at->format('g:i A')
                          }}</span>
                      </div>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                      <div class="flex items-center justify-end space-x-2">
                        <!-- Fixed Edit Button -->
                        <a href="{{ route('websites.edit', $website) }}" wire:navigate
                          class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 font-medium transition-colors">
                          {{ __('Edit') }}
                        </a>

                        <!-- Delete Button -->
                        <button wire:click="deleteWebsite({{ $website->id }})"
                          wire:confirm="Are you sure you want to delete this website? This action cannot be undone."
                          class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 font-medium transition-colors">
                          {{ __('Delete') }}
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                      <div class="flex flex-col items-center">
                        <svg class="h-12 w-12 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">{{ __('No websites found')
                          }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                          @if ($search || $typeFilter || $authFilter)
                          {{ __('Try adjusting your search or filter criteria.') }}
                          @else
                          {{ __('Get started by adding your first website.') }}
                          @endif
                        </p>
                        @if (!$search && !$typeFilter && !$authFilter)
                        <a href="{{ route('websites.create') }}"
                          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 transition-colors">
                          {{ __('Add Website') }}
                        </a>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>

              <!-- Pagination -->
              @if ($websites->hasPages())
              <div class="bg-white dark:bg-gray-900 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                {{ $websites->links() }}
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>