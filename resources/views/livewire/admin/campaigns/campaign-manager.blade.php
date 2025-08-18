<div class="space-y-6">
  {{-- breadcrumbs --}}
  <nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm">
      <li class="flex items-center">
        <a href="{{ route('dashboard') }}"
          class="ml-2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition-colors">
          {{ __('Dashboard') }}
        </a>
      </li>
      <li class="flex items-center">
        <svg class="w-4 h-4 text-gray-400 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"></path>
        </svg>
        <span class="ml-2 text-gray-900 dark:text-gray-100 font-medium">Campaigns</span>
      </li>
    </ol>
  </nav>

  <div class="max-w-6xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    {{-- debug info section --}}
    @if (app()->environment('local'))
    <div
      class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-700 rounded-md text-sm text-yellow-800 dark:text-yellow-200">
      <strong>Debug Info:</strong>
      Mode: {{ $isEdit ? 'Edit' : 'Create' }} |
      Campaign ID: {{ $campaign_id ?? 'New' }} |
      Websites: {{ count($websites) }} |
      Groups: {{ count($groups) }}
    </div>
    @endif

    {{-- header section --}}
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
        {{ $isEdit ? 'Edit Campaign' : 'Create New Campaign' }}
      </h1>
      <p class="text-gray-600 dark:text-gray-300 mt-2">
        {{ $isEdit
        ? 'Update campaign details, websites, and trigger groups'
        : 'Set up a new campaign with websites and trigger groups' }}
      </p>
    </div>

    {{-- global error messages --}}
    @if (session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
              clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm text-red-700 dark:text-red-200">{{ session('error') }}</p>
        </div>
      </div>
    </div>
    @endif

    <form wire:submit="submit" class="campaign-form space-y-8">
      {{-- campaign core information section --}}
      <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">{{ __('Campaign Information') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          {{-- campaign name input --}}
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Campaign Name *
            </label>
            <input type="text" id="name" wire:model.live="name"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('name') border-red-500 dark:border-red-400 @enderror"
              placeholder="Enter campaign name">
            @error('name')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- operator select --}}
          <div>
            <label for="operator_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              {{ __('Operator') }} *
            </label>
            <select id="operator_id" wire:model.live="operator_id"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('operator_id') border-red-500 dark:border-red-400 @enderror">
              <option value="">{{ __('Select Operator') }}</option>
              @foreach ($operators as $operator)
              <option value="{{ $operator['id'] }}" {{ $operator_id==$operator['id'] ? 'selected' : '' }}>
                {{ $operator['name'] }}
              </option>
              @endforeach
            </select>
            @error('operator_id')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- market select --}}
          <div>
            <label for="market_id" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              {{ __('Market') }} *
            </label>
            <select id="market_id" wire:model.live="market_id"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('market_id') border-red-500 dark:border-red-400 @enderror">
              <option value="">{{ __('Select Market') }}</option>
              @foreach ($markets as $market)
              <option value="{{ $market['id'] }}" {{ $market_id==$market['id'] ? 'selected' : '' }}>
                {{ $market['name'] }}
              </option>
              @endforeach
            </select>
            @error('market_id')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- status select --}}
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Status *
            </label>
            <select id="status" wire:model.live="status"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('status') border-red-500 dark:border-red-400 @enderror">
              @foreach ($status_options as $key => $option)
              <option value="{{ $key }}" {{ $status==$key ? 'selected' : '' }}>
                {{ ucfirst($option) }}
              </option>
              @endforeach
            </select>
            @error('status')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- start date input --}}
          <div>
            <label for="start_at" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Start Date *
            </label>
            <input type="datetime-local" id="start_at" wire:model.live="start_at" value="{{ $start_at }}"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('start_at') border-red-500 dark:border-red-400 @enderror">
            @error('start_at')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- end date input --}}
          <div>
            <label for="end_at" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              End Date *
            </label>
            <input type="datetime-local" id="end_at" wire:model.live="end_at" value="{{ $end_at }}"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('end_at') border-red-500 dark:border-red-400 @enderror">
            @error('end_at')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- priority select --}}
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Priority *
            </label>
            <select id="priority" wire:model.live="priority"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('priority') border-red-500 dark:border-red-400 @enderror">
              @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}" {{ $priority==$i ? 'selected' : '' }}>{{ $i }}
                </option>
                @endfor
            </select>
            @error('priority')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- duration input --}}
          <div>
            <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Duration (seconds)
            </label>
            <input type="number" id="duration" wire:model.live="duration" value="{{ $duration }}"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('duration') border-red-500 dark:border-red-400 @enderror"
              placeholder="Optional duration in seconds" min="1">
            @error('duration')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- rotation delay input --}}
          <div>
            <label for="rotation_delay" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Rotation Delay (seconds)
            </label>
            <input type="number" id="rotation_delay" wire:model.live="rotation_delay" value="{{ $rotation_delay }}"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('rotation_delay') border-red-500 dark:border-red-400 @enderror"
              placeholder="Optional rotation delay" min="0">
            @error('rotation_delay')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- dom selector input --}}
          <div class="md:col-span-2">
            <label for="dom_selector" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              DOM Selector
            </label>
            <input type="text" id="dom_selector" wire:model.live="dom_selector" value="{{ $dom_selector }}"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('dom_selector') border-red-500 dark:border-red-400 @enderror"
              placeholder="CSS selector (e.g., .banner, #popup)">
            @error('dom_selector')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>

      {{-- campaign websites section --}}
      <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Campaign Websites *</h2>
          <button type="button" wire:click="addWebsite"
            class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white text-sm font-medium rounded-md transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
              </path>
            </svg>
            Add Website
          </button>
        </div>

        @error('websites')
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md">
          <p class="text-red-600 dark:text-red-300 text-sm">{{ $message }}</p>
        </div>
        @endif

        <div class="space-y-6">
          @foreach ($websites as $index => $website)
          <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Website #{{ $index + 1 }}</h3>
              @if (count($websites) > 1)
              <button type="button" wire:click="removeWebsite({{ $index }})"
                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                  </path>
                </svg>
              </button>
              @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              {{-- website selection --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Website *</label>
                <select wire:model.live="websites.{{ $index }}.website_id"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('websites.' . $index . '.website_id') border-red-500 dark:border-red-400 @enderror">
                  <option value="">Select Website</option>
                  @foreach ($availableWebsites as $availableWebsite)
                  <option value="{{ $availableWebsite['id'] }}">
                    {{ $availableWebsite['url'] }}
                  </option>
                  @endforeach
                </select>
                @error('websites.' . $index . '.website_id')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- website priority --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Priority *</label>
                <select wire:model.live="websites.{{ $index }}.priority"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('websites.' . $index . '.priority') border-red-500 dark:border-red-400 @enderror">
                  @for ($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                @error('websites.' . $index . '.priority')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- website dom selector --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">DOM Selector</label>
                <input type="text" wire:model.live="websites.{{ $index }}.dom_selector"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('websites.' . $index . '.dom_selector') border-red-500 dark:border-red-400 @enderror"
                  placeholder="CSS selector">
                @error('websites.' . $index . '.dom_selector')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- timer offset --}}
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Timer Offset
                  (seconds)</label>
                <input type="number" wire:model.live="websites.{{ $index }}.timer_offset"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('websites.' . $index . '.timer_offset') border-red-500 dark:border-red-400 @enderror"
                  placeholder="Offset in seconds" min="0">
                @error('websites.' . $index . '.timer_offset')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>

              {{-- custom affiliate url --}}
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Custom Affiliate
                  URL</label>
                <input type="url" wire:model.live="websites.{{ $index }}.custom_affiliate_url"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('websites.' . $index . '.custom_affiliate_url') border-red-500 dark:border-red-400 @enderror"
                  placeholder="https://example.com/affiliate">
                @error('websites.' . $index . '.custom_affiliate_url')
                <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- campaign trigger groups section --}}
      <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
        <div class="mb-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Campaign Trigger Groups *</h2>
            <button type="button" wire:click="addGroup"
              class="inline-flex items-center px-4 py-2 bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white text-sm font-medium rounded-md transition-colors duration-200">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
              </svg>
              Add Group
            </button>
          </div>

          {{-- global logic radio buttons --}}
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
              Logic Between Groups *
            </label>
            <div class="flex gap-4">
              <label class="flex items-center">
                <input type="radio" wire:model.live="globalLogic" value="AND"
                  class="mr-2 text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                <span class="text-sm text-gray-700 dark:text-gray-200">AND (All groups must match)</span>
              </label>
              <label class="flex items-center">
                <input type="radio" wire:model.live="globalLogic" value="OR"
                  class="mr-2 text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400">
                <span class="text-sm text-gray-700 dark:text-gray-200">OR (Any group can match)</span>
              </label>
            </div>
            @error('globalLogic')
            <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        @error('groups')
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md">
          <p class="text-red-600 dark:text-red-300 text-sm">{{ $message }}</p>
        </div>
        @endif

        <div class="space-y-6">
          @foreach ($groups as $groupIndex => $group)
          <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-6 bg-white dark:bg-gray-800 shadow-sm">
            {{-- group header --}}
            <div class="flex justify-between items-start mb-6">
              <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Group #{{ $groupIndex + 1 }}
                  </h3>

                  {{-- group reorder buttons --}}
                  <div class="flex gap-1">
                    @if ($groupIndex > 0)
                    <button type="button" wire:click="moveGroupUp({{ $groupIndex }})"
                      class="p-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                      title="Move Up">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                        </path>
                      </svg>
                    </button>
                    @endif
                    @if ($groupIndex < count($groups) - 1) <button type="button"
                      wire:click="moveGroupDown({{ $groupIndex }})"
                      class="p-1 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                      title="Move Down">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                      </svg>
                      </button>
                      @endif
                  </div>
                </div>

                {{-- group name and logic inputs --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Group Name *</label>
                    <input type="text" wire:model.live="groups.{{ $groupIndex }}.name"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('groups.' . $groupIndex . '.name') border-red-500 dark:border-red-400 @enderror"
                      placeholder="Enter group name">
                    @error('groups.' . $groupIndex . '.name')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Logic Within Group
                      *</label>
                    <select wire:model.live="groups.{{ $groupIndex }}.logic"
                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('groups.' . $groupIndex . '.logic') border-red-500 dark:border-red-400 @enderror">
                      <option value="AND">AND (All triggers must match)</option>
                      <option value="OR">OR (Any trigger can match)</option>
                    </select>
                    @error('groups.' . $groupIndex . '.logic')
                    <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>

              {{-- group remove button --}}
              @if (count($groups) > 1)
              <button type="button" wire:click="removeGroup({{ $groupIndex }})"
                class="ml-4 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors duration-200"
                title="Remove Group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                  </path>
                </svg>
              </button>
              @endif
            </div>

            {{-- triggers section --}}
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div class="flex justify-between items-center mb-4">
                <h4 class="text-md font-medium text-gray-800 dark:text-gray-200">Triggers</h4>
                <button type="button" wire:click="addTrigger({{ $groupIndex }})"
                  class="inline-flex items-center px-3 py-1.5 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-200">
                  <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                  </svg>
                  Add Trigger
                </button>
              </div>

              @error('groups.' . $groupIndex . '.triggers')
              <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 rounded-md">
                <p class="text-red-600 dark:text-red-300 text-sm">{{ $message }}</p>
              </div>
              @endif

              <div class="space-y-4">
                @foreach ($group['triggers'] as $triggerIndex => $trigger)
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-white dark:bg-gray-800">
                  <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-2">
                      <h5 class="text-sm font-medium text-gray-700 dark:text-gray-200">Trigger #{{ $triggerIndex + 1 }}
                      </h5>

                      {{-- trigger reorder buttons --}}
                      <div class="flex gap-1">
                        @if ($triggerIndex > 0)
                        <button type="button" wire:click="moveTriggerUp({{ $groupIndex }}, {{ $triggerIndex }})"
                          class="p-1 text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100 transition-colors"
                          title="Move Up">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                            </path>
                          </svg>
                        </button>
                        @endif
                        @if ($triggerIndex < count($group['triggers']) - 1) <button type="button"
                          wire:click="moveTriggerDown({{ $groupIndex }}, {{ $triggerIndex }})"
                          class="p-1 text-gray-400 dark:text-gray-300 hover:text-gray-600 dark:hover:text-gray-100 transition-colors"
                          title="Move Down">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                          </svg>
                          </button>
                          @endif
                      </div>
                    </div>

                    @if (count($group['triggers']) > 1)
                    <button type="button" wire:click="removeTrigger({{ $groupIndex }}, {{ $triggerIndex }})"
                      class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors duration-200"
                      title="Remove Trigger">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                      </svg>
                    </button>
                    @endif
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- trigger type select --}}
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Type *</label>
                      <select wire:model.live="groups.{{ $groupIndex }}.triggers.{{ $triggerIndex }}.type"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.type') border-red-500 dark:border-red-400 @enderror">
                        <option value="">Select Type</option>
                        @foreach ($triggerTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                      </select>
                      @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.type')
                      <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    {{-- trigger operator select --}}
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Operator *</label>
                      <select wire:model.live="groups.{{ $groupIndex }}.triggers.{{ $triggerIndex }}.operator"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.operator') border-red-500 dark:border-red-400 @enderror">
                        <option value="">Select Operator</option>
                        @foreach ($triggerOperators as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                      </select>
                      @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.operator')
                      <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    {{-- trigger value input --}}
                    <div>
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Value *</label>
                      <input type="text" wire:model.live="groups.{{ $groupIndex }}.triggers.{{ $triggerIndex }}.value"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.value') border-red-500 dark:border-red-400 @enderror"
                        placeholder="Trigger value">
                      @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.value')
                      <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    {{-- trigger description input --}}
                    <div class="md:col-span-3">
                      <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Description</label>
                      <input type="text"
                        wire:model.live="groups.{{ $groupIndex }}.triggers.{{ $triggerIndex }}.description"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.description') border-red-500 dark:border-red-400 @enderror"
                        placeholder="Optional description for this trigger">
                      @error('groups.' . $groupIndex . '.triggers.' . $triggerIndex . '.description')
                      <p class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                      @enderror
                    </div>
                  </div>

                  {{-- trigger help text --}}
                  @if (isset($trigger['type']) && $trigger['type'])
                  <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/50 rounded-md">
                    <p class="text-sm text-blue-700 dark:text-blue-200">
                      @switch($trigger['type'])
                      @case('url')
                      <strong>URL Trigger:</strong> Matches against the current page URL
                      @break
                      @case('referrer')
                      <strong>Referrer Trigger:</strong> Matches against the referring page URL
                      @break
                      @case('device')
                      <strong>Device Trigger:</strong> Matches device type (mobile, tablet, desktop)
                      @break
                      @case('country')
                      <strong>Country Trigger:</strong> Matches visitor's country code (e.g., US, UK)
                      @break
                      @case('pageViews')
                      <strong>Page Views Trigger:</strong> Number of pages viewed in session
                      @break
                      @case('timeOnSite')
                      <strong>Time on Site:</strong> Total time spent on site in seconds
                      @break
                      @case('timeOnPage')
                      <strong>Time on Page:</strong> Time spent on current page in seconds
                      @break
                      @case('scroll')
                      <strong>Scroll Trigger:</strong> Percentage of page scrolled (0-100)
                      @break
                      @case('exitIntent')
                      <strong>Exit Intent:</strong> Triggered when user moves cursor toward browser controls
                      @break
                      @case('newVisitor')
                      <strong>New Visitor:</strong> Use "true" or "false" to match new vs returning visitors
                      @break
                      @case('dayOfWeek')
                      <strong>Day of Week:</strong> Day name (Monday, Tuesday, etc.) or number (1-7)
                      @break
                      @case('hour')
                      <strong>Hour Trigger:</strong> Hour of day in 24-hour format (0-23)
                      @break
                      @endswitch
                    </p>
                  </div>
                  @endif
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>

      {{-- form actions --}}
      <div class="flex justify-between items-center pt-6 border-t border-gray-200 dark:border-gray-600">
        <a href="{{ route('campaigns.index') }}"
          class="inline-flex items-center px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
          Cancel
        </a>

        <button type="submit"
          class="inline-flex items-center px-6 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-200 disabled:opacity-50"
          wire:loading.attr="disabled">
          <svg wire:loading class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
            fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
            </circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
            </path>
          </svg>
          <span wire:loading.remove>{{ $isEdit ? 'Update Campaign' : 'Create Campaign' }}</span>
          <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
        </button>
      </div>
    </form>

    {{-- auto-scroll and dropdown fix script --}}
    <script>
      document.addEventListener('livewire:init', () => {
        Livewire.on('scrollToFirstError', () => {
          setTimeout(() => {
            const firstErrorInput = document.querySelector('input.border-red-500, select.border-red-500, textarea.border-red-500');
            const firstErrorMessage = document.querySelector('.text-red-500');
            const firstError = firstErrorInput || firstErrorMessage;
            
            if (firstError) {
              firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
              });
              if (firstErrorInput && typeof firstErrorInput.focus === 'function') {
                firstErrorInput.focus();
              }
            }
          }, 150);
        });
      });

      document.addEventListener('DOMContentLoaded', function() {
        let errorCheckTimeout;
        
        const fixDropdownPositioning = () => {
          const selects = document.querySelectorAll('.campaign-form select');
          selects.forEach(select => {
            if (getComputedStyle(select.parentElement).position === 'static') {
              select.parentElement.style.position = 'relative';
            }
            
            select.addEventListener('mousedown', function(e) {
              this.style.zIndex = '9999';
              setTimeout(() => {
                this.style.zIndex = '15';
              }, 100);
            });
            
            select.addEventListener('focus', function(e) {
              this.style.zIndex = '9999';
            });
            
            select.addEventListener('blur', function(e) {
              this.style.zIndex = '15';
            });
          });
        };
        
        const scrollToFirstError = () => {
          const firstErrorInput = document.querySelector('input.border-red-500, select.border-red-500, textarea.border-red-500');
          const firstErrorMessage = document.querySelector('.text-red-500');
          const firstError = firstErrorInput || firstErrorMessage;
          
          if (firstError) {
            firstError.scrollIntoView({
              behavior: 'smooth',
              block: 'center',
              inline: 'nearest'
            });
            if (firstErrorInput && typeof firstErrorInput.focus === 'function') {
              setTimeout(() => firstErrorInput.focus(), 100);
            }
          }
        };

        fixDropdownPositioning();

        const observer = new MutationObserver(function(mutations) {
          let hasErrorChanges = false;
          let hasNewSelects = false;
          
          mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
              if (mutation.target.classList && 
                  (mutation.target.classList.contains('border-red-500') || 
                   mutation.target.classList.contains('text-red-500'))) {
                hasErrorChanges = true;
              }
              if (mutation.addedNodes) {
                mutation.addedNodes.forEach(node => {
                  if (node.nodeType === 1) {
                    if (node.classList && 
                        (node.classList.contains('border-red-500') || 
                         node.classList.contains('text-red-500'))) {
                      hasErrorChanges = true;
                    }
                    if (node.querySelector && 
                        node.querySelector('.border-red-500, .text-red-500')) {
                      hasErrorChanges = true;
                    }
                    if (node.tagName === 'SELECT' || node.querySelector('select')) {
                      hasNewSelects = true;
                    }
                  }
                });
              }
            }
          });
          
          if (hasErrorChanges) {
            clearTimeout(errorCheckTimeout);
            errorCheckTimeout = setTimeout(scrollToFirstError, 200);
          }
          
          if (hasNewSelects) {
            setTimeout(fixDropdownPositioning, 50);
          }
        });

        observer.observe(document.body, {
          childList: true,
          subtree: true,
          attributes: true,
          attributeFilter: ['class']
        });
      });
    </script>
  </div>
</div>