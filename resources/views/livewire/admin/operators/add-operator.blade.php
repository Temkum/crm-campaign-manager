<div class="p-4 sm:p-6 lg:p-8">
  <!-- Header -->
  <div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-2">
      <a href="{{ route('operators.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">
        Operators
      </a>
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
      <span>Add Operator</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Operator</h1>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create a new operator with its configuration</p>
  </div>

  <!-- Form Card -->
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
    <form wire:submit="save">
      <div class="p-6 space-y-6">
        <!-- Operator Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Operator Name <span class="text-red-500">*</span>
          </label>
          <input type="text" id="name" wire:model="name" placeholder="Enter operator name..."
            class="w-full px-3 py-2 border @error('name') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          @error('name')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Enter a unique name for the operator (e.g., "BetMGM", "DraftKings")
          </p>
        </div>

        <!-- Website URL -->
        <div>
          <label for="website_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Website URL <span class="text-red-500">*</span>
          </label>
          <input type="url" id="website_url" wire:model="website_url" placeholder="https://example.com"
            class="w-full px-3 py-2 border @error('website_url') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          @error('website_url')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Enter the operator's official website URL (must be unique)
          </p>
        </div>

        <!-- Logo URL -->
        <div>
          <label for="logo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Logo URL <span class="text-gray-400">(Optional)</span>
          </label>
          <input type="url" id="logo_url" wire:model="logo_url" placeholder="https://example.com/logo.png"
            class="w-full px-3 py-2 border @error('logo_url') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          @error('logo_url')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
          @enderror
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Enter the URL for the operator's logo image (optional)
          </p>
        </div>

        <!-- Logo Preview -->
        @if ($logo_url)
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Logo Preview
            </label>
            <div class="flex items-center space-x-3">
              <img src="{{ $logo_url }}" alt="Logo preview"
                class="h-12 w-12 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div
                class="h-12 w-12 rounded-lg bg-red-100 dark:bg-red-900 border border-red-200 dark:border-red-700 flex items-center justify-center"
                style="display: none;">
                <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                  </path>
                </svg>
              </div>
              <span class="text-sm text-gray-500 dark:text-gray-400">
                Logo preview (if URL is valid)
              </span>
            </div>
          </div>
        @endif

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
                <strong>Note:</strong> Both the operator name and website URL must be unique. The logo URL is optional
                but should point to a valid image if provided.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
        <div
          class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
          <a href="{{ route('operators.index') }}"
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
            <span wire:loading.remove>Create Operator</span>
            <span wire:loading>Creating...</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
