<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('khairat.notification_preferences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:notification-preferences />
            
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('khairat.notification_channels') }}</h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700">{{ __('khairat.email_notifications') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('khairat.email_notifications_description') }}
                                {{ __('khairat.current_email') }} <strong>{{ Auth::user()->email }}</strong>
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('khairat.update_email') }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700">{{ __('khairat.inapp_notifications') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('khairat.inapp_notifications_description') }}
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('notifications.history') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('khairat.view_notification_history') }}
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-700">{{ __('khairat.sms_notifications') }}</h4>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('khairat.sms_notifications_description') }}
                                @if (Auth::user()->phone)
                                    {{ __('khairat.current_phone') }} <strong>{{ Auth::user()->phone }}</strong>
                                @else
                                    <span class="text-red-500">{{ __('khairat.no_phone_registered') }}</span>
                                @endif
                            </p>
                            <div class="mt-2">
                                <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                                    {{ __('khairat.update_phone') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 