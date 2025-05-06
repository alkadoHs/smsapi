<?php

use Livewire\Volt\Component;
use App\Models\Sms;
use Livewire\WithPagination;

new class extends Component {
  use WithPagination;

    public string $search = '+255';

    protected $listeners = [
        'sms-sent' => '$refresh',
    ];

    public function with(): array
    {
      return [
        "smses" => Sms::with('user')->where('phone_number', 'LIKE', "%{$this->search}%")->latest()->paginate(25),
      ];
    }
}; ?>

<section>
    
    <div class="flex items-center justify-between my-4">
      <h2 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">SMS List</h2>
    </div>

    <div class="flex flex-col">
      <flux:input
        wire:model.live.debounce.1800ms="search"
        type="text"
        placeholder="Search phone..."
        class="mb-4"
        />

      <div class="-m-1.5 overflow-x-auto">
        <div class="p-1.5 min-w-full inline-block align-middle">
          <div class="border border-gray-200 overflow-hidden dark:border-neutral-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
              <thead>
                <tr>
                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">S/N</th>
                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Phone Number</th>
                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Message</th>
                  <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Date</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                @foreach ($smses as $sms)
                    <tr wire:key="sms-{{ $sms->id }}" class="hover:bg-gray-50 dark:hover:bg-neutral-800">
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loop->iteration }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $sms->phone_number }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ $sms->message }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">{{ $sms->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach

              </tbody>
            </table>
          </div>

          <div class="px-6 py-3">
            {{ $smses->links() }}
          </div>
        </div>
      </div>
    </div>
</section>
