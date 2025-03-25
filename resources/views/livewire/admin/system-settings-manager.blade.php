<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('khairat.system_settings') }}</h2>
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-6">
                    <button wire:click="$set('activeTab', 'organization')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'organization' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ __('khairat.organization') }}
                    </button>
                    <button wire:click="$set('activeTab', 'payment')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'payment' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ __('khairat.payment') }}
                    </button>
                    <button wire:click="$set('activeTab', 'notification')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'notification' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ __('khairat.notification') }}
                    </button>
                    <button wire:click="$set('activeTab', 'system')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'system' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ __('khairat.system') }}
                    </button>
                </nav>
            </div>
            
            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <!-- Organization Settings -->
                    <div x-show="$wire.activeTab === 'organization'" x-cloak>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            @foreach ($settingGroups['organization'] as $setting)
                                @continue(!isset($setting->description))
                                <div wire:key="organization-{{ $setting->key }}" class="sm:col-span-3">
                                    <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                                        {{ __(ucwords(str_replace('_', ' ', $setting->key))) }}
                                    </label>
                                    <div class="mt-1">
                                        <input type="text" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @error('formData.' . $setting->key) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Payment Settings -->
                    <div x-show="$wire.activeTab === 'payment'" x-cloak>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            @foreach ($settingGroups['payment'] as $setting)
                                @continue(!isset($setting->description))
                                <div wire:key="payment-{{ $setting->key }}" class="sm:col-span-3">
                                    <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                                        {{ __(ucwords(str_replace('_', ' ', $setting->key))) }}
                                    </label>
                                    <div class="mt-1">
                                        @if (str_contains($setting->key, 'fee') || str_contains($setting->key, 'amount'))
                                            <div class="relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">RM</span>
                                                </div>
                                                <input type="number" step="0.01" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="pl-12 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                            </div>
                                        @else
                                            <input type="text" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @error('formData.' . $setting->key) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Notification Settings -->
                    <div x-show="$wire.activeTab === 'notification'" x-cloak>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            @foreach ($settingGroups['notification'] as $setting)
                                @continue(!isset($setting->description))
                                <div wire:key="notification-{{ $setting->key }}" class="sm:col-span-3">
                                    <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                                        {{ __(ucwords(str_replace('_', ' ', $setting->key))) }}
                                    </label>
                                    <div class="mt-1">
                                        @if (str_contains($setting->key, 'enabled'))
                                            <div class="flex items-center">
                                                <input type="checkbox" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <label for="{{ $setting->key }}" class="ml-2 block text-sm text-gray-900">
                                                    {{ __('khairat.enable') }}
                                                </label>
                                            </div>
                                        @elseif (str_contains($setting->key, 'email'))
                                            <input type="email" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @else
                                            <input type="text" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @error('formData.' . $setting->key) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- System Settings -->
                    <div x-show="$wire.activeTab === 'system'" x-cloak>
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            @foreach ($settingGroups['system'] as $setting)
                                @continue(!isset($setting->description))
                                <div wire:key="system-{{ $setting->key }}" class="sm:col-span-3">
                                    <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700">
                                        {{ __(ucwords(str_replace('_', ' ', $setting->key))) }}
                                    </label>
                                    <div class="mt-1">
                                        @if ($setting->key === 'maintenance_mode' || $setting->key === 'backup_enabled')
                                            <div class="flex items-center">
                                                <input type="checkbox" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                                <label for="{{ $setting->key }}" class="ml-2 block text-sm text-gray-900">
                                                    {{ __('Enable') }}
                                                </label>
                                            </div>
                                        @elseif ($setting->key === 'backup_frequency')
                                            <select id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                                <option value="daily">{{ __('khairat.daily') }}</option>
                                                <option value="weekly">{{ __('khairat.weekly') }}</option>
                                                <option value="monthly">{{ __('khairat.monthly') }}</option>
                                            </select>
                                        @else
                                            <input type="text" id="{{ $setting->key }}" wire:model.defer="formData.{{ $setting->key }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @endif
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">{{ $setting->description }}</p>
                                    @error('formData.' . $setting->key) <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-between pt-5">
                        <button type="button" wire:click="resetToDefaults" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('khairat.reset_to_defaults') }}
                        </button>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('khairat.save_settings') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> 