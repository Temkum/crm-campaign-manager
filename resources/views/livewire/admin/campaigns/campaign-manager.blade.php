<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $isEdit ? 'Edit Campaign' : 'Create New Campaign' }}
        </h1>
        <p class="text-gray-600 mt-2">
            {{ $isEdit ? 'Update campaign details, websites, and triggers' : 'Set up a new campaign with websites and
            triggers' }}
        </p>
    </div>

    <form wire:submit="submit" class="space-y-8">
        <!-- Campaign Core Information -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Campaign Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Campaign Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Campaign Name *
                    </label>
                    <input type="text" id="name" wire:model.live="name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Enter campaign name">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Operator -->
                <div>
                    <label for="operator_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Operator *
                    </label>
                    <select id="operator_id" wire:model.live="operator_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('operator_id') border-red-500 @enderror">
                        <option value="">Select Operator</option>
                        @foreach($operators as $operator)
                        <option value="{{ $operator['id'] }}">{{ $operator['name'] }}</option>
                        @endforeach
                    </select>
                    @error('operator_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Market -->
                <div>
                    <label for="market_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Market *
                    </label>
                    <select id="market_id" wire:model.live="market_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('market_id') border-red-500 @enderror">
                        <option value="">Select Market</option>
                        @foreach($markets as $market)
                        <option value="{{ $market['id'] }}">{{ $market['name'] }}</option>
                        @endforeach
                    </select>
                    @error('market_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status *
                    </label>
                    <select id="status" wire:model.live="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="paused">Paused</option>
                        <option value="completed">Completed</option>
                    </select>
                    @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Date *
                    </label>
                    <input type="datetime-local" id="start_at" wire:model.live="start_at"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('start_at') border-red-500 @enderror">
                    @error('start_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_at" class="block text-sm font-medium text-gray-700 mb-2">
                        End Date *
                    </label>
                    <input type="datetime-local" id="end_at" wire:model.live="end_at"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('end_at') border-red-500 @enderror">
                    @error('end_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority *
                    </label>
                    <select id="priority" wire:model.live="priority"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror">
                        @for($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                    @error('priority')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Duration (seconds)
                    </label>
                    <input type="number" id="duration" wire:model.live="duration"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration') border-red-500 @enderror"
                        placeholder="Optional duration in seconds" min="1">
                    @error('duration')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Rotation Delay -->
                <div>
                    <label for="rotation_delay" class="block text-sm font-medium text-gray-700 mb-2">
                        Rotation Delay (seconds)
                    </label>
                    <input type="number" id="rotation_delay" wire:model.live="rotation_delay"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('rotation_delay') border-red-500 @enderror"
                        placeholder="Optional rotation delay" min="0">
                    @error('rotation_delay')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- DOM Selector -->
                <div class="md:col-span-2">
                    <label for="dom_selector" class="block text-sm font-medium text-gray-700 mb-2">
                        DOM Selector
                    </label>
                    <input type="text" id="dom_selector" wire:model.live="dom_selector"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('dom_selector') border-red-500 @enderror"
                        placeholder="CSS selector (e.g., .banner, #popup)">
                    @error('dom_selector')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Campaign Websites -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Campaign Websites *</h2>
                <button type="button" wire:click="addWebsite"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Website
                </button>
            </div>

            @error('websites')
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-600 text-sm">{{ $message }}</p>
            </div>
            @enderror

            <div class="space-y-6">
                @foreach($websites as $index => $website)
                <div class="border border-gray-200 rounded-lg p-4 bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Website #{{ $index + 1 }}</h3>
                        @if(count($websites) > 1)
                        <button type="button" wire:click="removeWebsite({{ $index }})"
                            class="text-red-600 hover:text-red-800 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Website Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Website *</label>
                            <select wire:model.live="websites.{{ $index }}.website_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('websites.' . $index . '.website_id') border-red-500 @enderror">
                                <option value="">Select Website</option>
                                @foreach($availableWebsites as $availableWebsite)
                                <option value="{{ $availableWebsite['id'] }}">
                                    {{ $availableWebsite['url'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('websites.' . $index . '.website_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                            <select wire:model.live="websites.{{ $index }}.priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('websites.' . $index . '.priority') border-red-500 @enderror">
                                @for($i = 1; $i <= 10; $i++) <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                            </select>
                            @error('websites.' . $index . '.priority')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- DOM Selector -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">DOM Selector</label>
                            <input type="text" wire:model.live="websites.{{ $index }}.dom_selector"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('websites.' . $index . '.dom_selector') border-red-500 @enderror"
                                placeholder="CSS selector">
                            @error('websites.' . $index . '.dom_selector')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Timer Offset -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Timer Offset (seconds)</label>
                            <input type="number" wire:model.live="websites.{{ $index }}.timer_offset"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('websites.' . $index . '.timer_offset') border-red-500 @enderror"
                                placeholder="Offset in seconds" min="0">
                            @error('websites.' . $index . '.timer_offset')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Custom Affiliate URL -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Custom Affiliate URL</label>
                            <input type="url" wire:model.live="websites.{{ $index }}.custom_affiliate_url"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('websites.' . $index . '.custom_affiliate_url') border-red-500 @enderror"
                                placeholder="https://example.com/affiliate">
                            @error('websites.' . $index . '.custom_affiliate_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Campaign Triggers -->
        <div class="bg-gray-50 p-6 rounded-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Campaign Triggers *</h2>
                <button type="button" wire:click="addTrigger"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Trigger
                </button>
            </div>

            @error('triggers')
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <p class="text-red-600 text-sm">{{ $message }}</p>
            </div>
            @enderror

            <div class="space-y-6">
                @foreach($triggers as $index => $trigger)
                <div class="border border-gray-200 rounded-lg p-4 bg-white">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Trigger #{{ $index + 1 }}</h3>
                        @if(count($triggers) > 1)
                        <button type="button" wire:click="removeTrigger({{ $index }})"
                            class="text-red-600 hover:text-red-800 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Trigger Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                            <select wire:model.live="triggers.{{ $index }}.type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('triggers.' . $index . '.type') border-red-500 @enderror">
                                <option value="">Select Type</option>
                                <option value="time">Time</option>
                                <option value="scroll">Scroll</option>
                                <option value="click">Click</option>
                                <option value="exit_intent">Exit Intent</option>
                                <option value="page_load">Page Load</option>
                            </select>
                            @error('triggers.' . $index . '.type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Trigger Operator -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Operator *</label>
                            <select wire:model.live="triggers.{{ $index }}.operator"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('triggers.' . $index . '.operator') border-red-500 @enderror">
                                <option value="">Select Operator</option>
                                <option value="equals">Equals</option>
                                <option value="greater_than">Greater Than</option>
                                <option value="less_than">Less Than</option>
                                <option value="contains">Contains</option>
                            </select>
                            @error('triggers.' . $index . '.operator')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Trigger Value -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Value *</label>
                            <input type="text" wire:model.live="triggers.{{ $index }}.value"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('triggers.' . $index . '.value') border-red-500 @enderror"
                                placeholder="Trigger value">
                            @error('triggers.' . $index . '.value')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Trigger Help Text -->
                    @if(isset($triggers[$index]['type']) && $triggers[$index]['type'])
                    <div class="mt-3 p-3 bg-blue-50 rounded-md">
                        <p class="text-sm text-blue-700">
                            @switch($triggers[$index]['type'])
                            @case('time')
                            <strong>Time Trigger:</strong> Value should be in seconds (e.g., 30 for 30 seconds after
                            page load)
                            @break
                            @case('scroll')
                            <strong>Scroll Trigger:</strong> Value should be percentage (e.g., 50 for 50% page scroll)
                            @break
                            @case('click')
                            <strong>Click Trigger:</strong> Value should be CSS selector (e.g., .button, #submit)
                            @break
                            @case('exit_intent')
                            <strong>Exit Intent:</strong> Triggers when user moves cursor toward browser close button
                            @break
                            @case('page_load')
                            <strong>Page Load:</strong> Triggers immediately when page loads
                            @break
                            @endswitch
                        </p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
            <a href="{{ route('campaigns.index') }}"
                class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                Cancel
            </a>

            <button type="submit"
                class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200 disabled:opacity-50"
                wire:loading.attr="disabled">
                <svg wire:loading class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span wire:loading.remove>{{ $isEdit ? 'Update Campaign' : 'Create Campaign' }}</span>
                <span wire:loading>{{ $isEdit ? 'Updating...' : 'Creating...' }}</span>
            </button>
        </div>
    </form>

    <!-- Auto-scroll to first error script -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('scrollToFirstError', () => {
                setTimeout(() => {
                    const firstError = document.querySelector('.border-red-500, .text-red-500');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                        firstError.focus();
                    }
                }, 100);
            });
        });

        // Auto-scroll on validation errors
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        const errorElements = document.querySelectorAll('.text-red-500');
                        if (errorElements.length > 0) {
                            setTimeout(() => {
                                errorElements[0].scrollIntoView({ 
                                    behavior: 'smooth', 
                                    block: 'center' 
                                });
                            }, 100);
                        }
                    }
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
</div>