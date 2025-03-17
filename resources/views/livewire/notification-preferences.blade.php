<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h2 class="text-lg font-semibold mb-4">{{ __('Notification Preferences') }}</h2>
            <p class="mb-4">{{ __('Manage how you receive notifications from the system.') }}</p>
            
            @if ($saveSuccess)
            <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md">
                {{ __('Your notification preferences have been saved successfully.') }}
            </div>
            @endif
            
            <div class="space-y-6">
                @foreach ($preferences as $key => $preference)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-md font-medium text-gray-700">{{ $preference['display_name'] }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ $preference['description'] }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center">
                            <input 
                                id="email-{{ $key }}" 
                                type="checkbox" 
                                wire:model.live="preferences.{{ $key }}.email"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                            <label for="email-{{ $key }}" class="ml-2 block text-sm text-gray-700">
                                {{ __('Email') }}
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                id="in-app-{{ $key }}" 
                                type="checkbox" 
                                wire:model.live="preferences.{{ $key }}.in_app"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                            <label for="in-app-{{ $key }}" class="ml-2 block text-sm text-gray-700">
                                {{ __('In-App') }}
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                id="sms-{{ $key }}" 
                                type="checkbox" 
                                wire:model.live="preferences.{{ $key }}.sms"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                            <label for="sms-{{ $key }}" class="ml-2 block text-sm text-gray-700">
                                {{ __('SMS') }}
                            </label>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                <x-primary-button wire:click="savePreferences" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="savePreferences">{{ __('Save Preferences') }}</span>
                    <span wire:loading wire:target="savePreferences">{{ __('Saving...') }}</span>
                </x-primary-button>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('preferences-saved', () => {
                setTimeout(() => {
                    @this.set('saveSuccess', false);
                }, 3000);
            });
        });
    </script>
</div>
