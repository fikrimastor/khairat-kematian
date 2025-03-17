<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification Preferences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:notification-preferences />
            
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Notification Channels') }}</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700">{{ __('Email Notifications') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Email notifications are sent to your registered email address.') }}
                                {{ __('Your current email address is:') }} <strong>{{ Auth::user()->email }}</strong>
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('Update your email address') }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700">{{ __('In-App Notifications') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('In-app notifications appear in your notification center within the application.') }}
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('notifications.history') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('View your notification history') }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700">{{ __('SMS Notifications') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('SMS notifications are sent to your registered phone number.') }}
                                @if (Auth::user()->phone)
                                    {{ __('Your current phone number is:') }} <strong>{{ Auth::user()->phone }}</strong>
                                @else
                                    <span class="text-red-500">{{ __('You have not registered a phone number yet.') }}</span>
                                @endif
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('Update your phone number') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 