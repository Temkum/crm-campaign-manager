<div class="p-6 space-y-6">
  <h1 class="text-3xl font-semibold text-gray-100">Campaigns</h1>

  <!-- Tableau des markets -->
  <div class="overflow-x-auto rounded-lg shadow border border-gray-700">
    <table class="min-w-full text-sm bg-gray-900 text-white">
      <thead class="bg-gray-800 text-gray-300 uppercase tracking-wide text-xs">
        <tr>
          <th class="px-6 py-3 text-left">Name</th>
          <th class="px-6 py-3 text-left">Market</th>
          <th class="px-6 py-3 text-left">Operator</th>
          <th class="px-6 py-3 text-left">Status</th>
          <th class="px-6 py-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-800">
        @foreach ($campaigns as $campaign)
          <tr>
            <td class="px-6 py-2">{{ $campaign->name }}</td>
            <td class="px-6 py-2">{{ $campaign->market->name }}</td>
            <td class="px-6 py-2">{{ $campaign->operator->name }}</td>
            <td class="px-6 py-2">{{ $campaign->status }}</td>
            <td class="px-6 py-2 text-right space-x-2">
              <a href="{{ route('campaigns.edit', $campaign->id) }}" class="secondary-btn btn-sm text-xs">Edit</a>
              <button wire:click="confirmDelete({{ $campaign->id }})" class="danger-btn btn-sm text-xs">Delete</button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="mt-4">
    {{ $campaigns->links() }}
  </div>
</div>
