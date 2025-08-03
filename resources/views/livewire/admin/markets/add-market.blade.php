<div class="p-4 sm:p-6 lg:p-8">
  <!-- Header -->
  <div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
      <a href="{{ route('markets.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">
        Markets
      </a>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
      <span>Add Market</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Market</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create a new market with its configuration</p>
  </div>

  <!-- Form Card -->
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <form wire:submit="save">
      <div class="p-6 space-y-6">
        <!-- Market Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Market Name <span class="text-red-500">*</span>
          </label>
          <input type="text" id="name" wire:model="name" placeholder="Enter market name..."
            class="w-full px-3 py-2 border @error('name') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Enter a unique name for the market (e.g., "United States", "United Kingdom")
          </p>
        </div>

        <!-- ISO Code -->
        <div>
          <label for="iso_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            ISO Code <span class="text-red-500">*</span>
          </label>
          <input type="text" id="iso_code" wire:model="iso_code" placeholder="Enter ISO code..." maxlength="2"
            class="w-full px-3 py-2 border @error('iso_code') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent uppercase">
          @error('iso_code')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Enter a unique 2-letter ISO country code (e.g., "US", "UK", "DE")
          </p>
        </div>

        <!-- Form Info -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm text-blue-800 dark:text-blue-200">
                <strong>Note:</strong> Both the market name and ISO code must be unique. The ISO code will be
                automatically converted to uppercase.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
        <div
          class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
          <a href="{{ route('markets.index') }}"
            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
            Cancel
          </a>
          <button type="submit"
            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            wire:loading.attr="disabled">
            <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
              </circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            <span wire:loading.remove>Create Market</span>
            <span wire:loading>Creating...</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
