<div class="p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        {{ __('Filter Payments') }}
    </h3>
    
    <form wire:submit="apply">
        <div class="space-y-4">
            <!-- Payment Status -->
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" wire:model="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statuses as $statusOption)
                        <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Payment Type -->
            <div>
                <x-input-label for="type" :value="__('Type')" />
                <select id="type" wire:model="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach($types as $typeOption)
                        <option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="dateFrom" :value="__('From Date')" />
                    <x-text-input id="dateFrom" type="date" class="mt-1 block w-full" wire:model="dateFrom" />
                </div>
                
                <div>
                    <x-input-label for="dateTo" :value="__('To Date')" />
                    <x-text-input id="dateTo" type="date" class="mt-1 block w-full" wire:model="dateTo" />
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end space-x-3">
            <x-secondary-button type="button" wire:click="resetFilters">
                {{ __('Reset') }}
            </x-secondary-button>
            
            <x-primary-button type="submit">
                {{ __('Apply Filters') }}
            </x-primary-button>
        </div>
    </form>
</div> 