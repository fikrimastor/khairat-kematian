<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Family Members') }}</h3>
        <button 
            wire:click="showAddForm" 
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            {{ __('Add Family Member') }}
        </button>
    </div>

    <!-- List of Dependents -->
    @if($dependents->isEmpty() && !$showForm)
        <div class="bg-gray-50 p-4 rounded-md">
            <p class="text-sm text-gray-700">{{ __('You have not added any family members yet.') }}</p>
        </div>
    @endif

    @if(!$dependents->isEmpty())
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                @foreach($dependents as $dependent)
                    <li class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $dependent->name }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Relationship') }}: {{ $dependent->relationship }}</p>
                                @if($dependent->identification_number)
                                    <p class="mt-1 text-xs text-gray-500">{{ __('ID') }}: {{ $dependent->identification_number }}</p>
                                @endif
                                @if($dependent->birth_date)
                                    <p class="mt-1 text-xs text-gray-500">{{ __('Birth Date') }}: {{ $dependent->birth_date->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button 
                                    wire:click="showEditForm({{ $dependent->id }})" 
                                    class="inline-flex items-center px-3 py-1 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    {{ __('Edit') }}
                                </button>
                                <button 
                                    wire:click="delete({{ $dependent->id }})" 
                                    wire:confirm="{{ __('Are you sure you want to remove this family member?') }}"
                                    class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Add/Edit Form -->
    @if($showForm)
        <div class="mt-5 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium leading-6 text-gray-900">
                    {{ $isEditing ? __('Edit Family Member') : __('Add Family Member') }}
                </h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>{{ __('Please provide the details of your family member.') }}</p>
                </div>
                <form wire:submit.prevent="save" class="mt-5 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                        <div class="mt-1">
                            <input type="text" wire:model="name" id="name" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="relationship" class="block text-sm font-medium text-gray-700">{{ __('Relationship') }}</label>
                        <div class="mt-1">
                            <select wire:model="relationship" id="relationship" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                                <option value="">{{ __('Select Relationship') }}</option>
                                <option value="spouse">{{ __('Spouse') }}</option>
                                <option value="child">{{ __('Child') }}</option>
                                <option value="parent">{{ __('Parent') }}</option>
                                <option value="sibling">{{ __('Sibling') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </select>
                        </div>
                        @error('relationship') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="identification_number" class="block text-sm font-medium text-gray-700">{{ __('Identification Number') }}</label>
                        <div class="mt-1">
                            <input type="text" wire:model="identification_number" id="identification_number" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('identification_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">{{ __('Birth Date') }}</label>
                        <div class="mt-1">
                            <input type="date" wire:model="birth_date" id="birth_date" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        @error('birth_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="cancelForm" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ $isEditing ? __('Update') : __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div> 