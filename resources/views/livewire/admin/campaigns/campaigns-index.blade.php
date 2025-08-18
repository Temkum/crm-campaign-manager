<div class="space-y-6">
  <!-- Breadcrumbs -->
  <nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2 text-sm">
      <li class="flex items-center">
        <a href="{{ route('dashboard') }}"
          class="ml-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">Dashboard</a>
      </li>
      <li class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd"
            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
            clip-rule="evenodd"></path>
        </svg>
        <span class="ml-2 text-gray-900 dark:text-white font-medium">Campaigns</span>
      </li>
    </ol>
  </nav>

  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Campaigns</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your marketing campaigns and track their performance</p>
    </div>
    <div class="mt-4 sm:mt-0">
      <a href="{{ route('campaigns.create') }}"
        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Create Campaign
      </a>
    </div>
  </div>

  <!-- Table Container -->
  <div
    class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th scope="col"
              class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
              Name
            </th>
            <th scope="col"
              class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
              Market
            </th>
            <th scope="col"
              class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
              Operator
            </th>
            <th scope="col"
              class="px-6 py-4 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
              Status
            </th>
            <th scope="col"
              class="px-6 py-4 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
          @forelse ($campaigns as $campaign)
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
              {{ $campaign->name }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
              {{ $campaign->market->name }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
              {{ $campaign->operator->name }}
            </td>
            <td class="px-6 py-4">
              @php
              $statusClasses = [
              'active' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
              'paused' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
              'inactive' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300',
              'draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
              ];
              $statusClass = $statusClasses[strtolower($campaign->status)] ?? 'bg-gray-100 dark:bg-gray-700
              text-gray-800 dark:text-gray-300';
              @endphp
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                {{ ucfirst($campaign->status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <a href="{{ route('campaigns.edit', $campaign->id) }}"
                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                  </path>
                </svg>
                Edit
              </a>
              <button wire:click="confirmDelete({{ $campaign->id }})"
                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 dark:text-red-300 bg-red-100 dark:bg-red-900 hover:bg-red-200 dark:hover:bg-red-800 rounded-md transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-red-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                  </path>
                </svg>
                Delete
              </button>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                  </path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No campaigns yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Get started by creating your first campaign.</p>
                <a href="{{ route('campaigns.create') }}"
                  class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  Create your first campaign
                </a>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($campaigns->hasPages())
    <div class="bg-white dark:bg-gray-900 px-6 py-3 border-t border-gray-200 dark:border-gray-800">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700 dark:text-gray-300">
          Showing {{ $campaigns->firstItem() ?? 0 }} to {{ $campaigns->lastItem() ?? 0 }} of {{ $campaigns->total() }}
          results
        </div>
        <div class="pagination-wrapper">
          {{ $campaigns->links() }}
        </div>
      </div>
    </div>
    @endif
  </div>

  <!-- Delete Confirmation Modal -->
  <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-cloak
    class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
    <div
      class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700">
      <div class="mt-3 text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900">
          <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
            </path>
          </svg>
        </div>
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Delete Campaign</h3>
        <div class="mt-2 px-7 py-3">
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Are you sure you want to delete this campaign? This action cannot be undone.
          </p>
        </div>
        <div class="items-center px-4 py-3 space-x-2">
          <button wire:click="deleteCampaign"
            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-auto hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors duration-150">
            Delete
          </button>
          <button wire:click="$set('showDeleteModal', false)"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-base font-medium rounded-md w-auto hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-150">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</div>