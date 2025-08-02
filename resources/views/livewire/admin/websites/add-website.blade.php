<div class="py-6">
  <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mb-6">{{ __('Add Website') }}</h1>

    @if (session()->has('message'))
      <div class="mb-4 p-4 rounded-md bg-green-100 text-green-700 border border-green-200" role="alert">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.53 10.5a.75.75 0 00-1.06 1.061l1.5 1.5a.75.75 0 001.137-.089l4-5.5z"
                clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium">{{ session('message') }}</p>
          </div>
        </div>
      </div>
    @endif

    @if (session()->has('error'))
      <div class="mb-4 p-4 rounded-md bg-red-100 text-red-700 border border-red-200" role="alert">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium">{{ session('error') }}</p>
          </div>
        </div>
      </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6" novalidate>
      <!-- URL -->
      <div>
        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Website URL') }} <span class="text-red-500" aria-label="required">*</span>
        </label>
        <input wire:model.live.debounce.500ms="url" type="url" id="url" name="url" required
          placeholder="https://example.com" aria-describedby="url-error url-help"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('url') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
        <p id="url-help" class="mt-1 text-xs text-gray-500">
          {{ __('Enter the main website URL (e.g., https://example.com)') }}</p>
        @error('url')
          <p id="url-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <!-- API URL -->
      <div>
        <label for="api_url" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('API URL') }}
        </label>
        <input wire:model.live.debounce.500ms="api_url" type="url" id="api_url" name="api_url"
          placeholder="https://api.example.com" aria-describedby="api-url-error api-url-help"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('api_url') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
        <p id="api-url-help" class="mt-1 text-xs text-gray-500">
          {{ __('Optional: API endpoint URL for data integration') }}</p>
        @error('api_url')
          <p id="api-url-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <!-- Type -->
      <div>
        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Website Type') }} <span class="text-red-500" aria-label="required">*</span>
        </label>
        <select wire:model.live="type" id="type" name="type" required aria-describedby="type-error type-help"
          class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm @error('type') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
          <option value="" disabled>{{ __('Select a website type') }}</option>
          @foreach ($websiteTypes as $typeOption)
            <option value="{{ $typeOption->value }}">{{ $typeOption->name }}</option>
          @endforeach
        </select>
        <p id="type-help" class="mt-1 text-xs text-gray-500">
          {{ __('Choose the category that best describes this website') }}</p>
        @error('type')
          <p id="type-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <!-- Auth Type -->
      <div>
        <label for="auth_type" class="block text-sm font-medium text-gray-700 mb-1">
          {{ __('Authentication Type') }}
        </label>
        <select wire:model.live="auth_type" id="auth_type" name="auth_type"
          aria-describedby="auth-type-error auth-type-help"
          class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm @error('auth_type') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror">
          <option value="NONE">{{ __('None') }}</option>
          <option value="TOKEN">{{ __('Token Authentication') }}</option>
          <option value="BASIC">{{ __('Basic Authentication') }}</option>
        </select>
        <p id="auth-type-help" class="mt-1 text-xs text-gray-500">
          {{ __('Select the authentication method required for API access') }}</p>
        @error('auth_type')
          <p id="auth-type-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror
      </div>

      <!-- Token Authentication Fields -->
      @if ($auth_type === 'TOKEN')
        <div class="bg-gray-50 p-4 rounded-md border border-gray-200" wire:transition>
          <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('Token Authentication') }}</h3>
          <div>
            <label for="auth_token" class="block text-sm font-medium text-gray-700 mb-1">
              {{ __('Authentication Token') }} <span class="text-red-500" aria-label="required">*</span>
            </label>
            <input wire:model.defer="auth_token" type="password" id="auth_token" name="auth_token" required
              autocomplete="new-password" placeholder="Enter your API token"
              aria-describedby="auth-token-error auth-token-help"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('auth_token') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
            <p id="auth-token-help" class="mt-1 text-xs text-gray-500">
              {{ __('Your API token will be encrypted and stored securely') }}</p>
            @error('auth_token')
              <p id="auth-token-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
          </div>
        </div>
      @endif

      <!-- Basic Authentication Fields -->
      @if ($auth_type === 'BASIC')
        <div class="bg-gray-50 p-4 rounded-md border border-gray-200" wire:transition>
          <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('Basic Authentication') }}</h3>
          <div class="space-y-4">
            <div>
              <label for="auth_user" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Username') }} <span class="text-red-500" aria-label="required">*</span>
              </label>
              <input wire:model.defer="auth_user" type="text" id="auth_user" name="auth_user" required
                autocomplete="username" placeholder="Enter username" aria-describedby="auth-user-error"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('auth_user') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
              @error('auth_user')
                <p id="auth-user-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="auth_pass" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Password') }} <span class="text-red-500" aria-label="required">*</span>
              </label>
              <input wire:model.defer="auth_pass" type="password" id="auth_pass" name="auth_pass" required
                autocomplete="new-password" placeholder="Enter password"
                aria-describedby="auth-pass-error auth-pass-help"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('auth_pass') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
              <p id="auth-pass-help" class="mt-1 text-xs text-gray-500">
                {{ __('Your credentials will be encrypted and stored securely') }}</p>
              @error('auth_pass')
                <p id="auth-pass-error" class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>
      @endif

      <!-- Form Actions -->
      <div class="flex justify-between pt-6 border-t border-gray-200">
        <a href="{{ route('websites.index') }}"
          class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
          {{ __('Cancel') }}
        </a>
        <button type="submit" wire:loading.attr="disabled" wire:target="save"
          class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
          <span wire:loading.remove wire:target="save">{{ __('Save Website') }}</span>
          <span wire:loading wire:target="save" class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            {{ __('Saving...') }}
          </span>
        </button>
      </div>
    </form>
  </div>
</div>
