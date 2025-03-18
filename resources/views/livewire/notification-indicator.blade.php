<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button 
        class="relative p-1 rounded-full text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        @click="open = !open"
        wire:click="toggleDropdown"
    >
        <span class="sr-only">{{ __('khairat.view_notifications') }}</span>
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-xs font-bold flex items-center justify-center">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100" 
        x-transition:enter-start="transform opacity-0 scale-95" 
        x-transition:enter-end="transform opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-75" 
        x-transition:leave-start="transform opacity-100 scale-100" 
        x-transition:leave-end="transform opacity-0 scale-95" 
        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        style="display: none;"
    >
        <div class="py-1 divide-y divide-gray-100">
            <div class="px-4 py-2 flex justify-between items-center">
                <h3 class="text-sm font-medium text-gray-900">{{ __('khairat.notifications') }}</h3>
                
                @if ($unreadCount > 0)
                    <button 
                        wire:click="markAllAsRead" 
                        class="text-xs text-indigo-600 hover:text-indigo-900"
                    >
                        {{ __('khairat.mark_all_as_read') }}
                    </button>
                @endif
            </div>
            
            <div class="max-h-60 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <div class="px-4 py-2 {{ $notification['read'] ? 'bg-white' : 'bg-blue-50' }} hover:bg-gray-50">
                        <div class="flex justify-between">
                            <p class="text-sm font-medium text-gray-900">
                                {{ __(ucfirst(str_replace('_', ' ', $notification['type']))) }}
                            </p>
                            <span class="text-xs text-gray-500">{{ $notification['time'] }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $notification['message'] }}</p>
                        
                        @if (!$notification['read'])
                            <button 
                                wire:click="markAsRead('{{ $notification['id'] }}')" 
                                class="mt-1 text-xs text-indigo-600 hover:text-indigo-900"
                            >
                                {{ __('khairat.mark_as_read') }}
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-6 text-center">
                        <p class="text-sm text-gray-500">{{ __('khairat.no_notifications') }}</p>
                    </div>
                @endforelse
            </div>
            
            <div class="px-4 py-2 text-center">
                <a href="{{ route('notifications.history') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    {{ __('khairat.view_all_notifications') }}
                </a>
            </div>
        </div>
    </div>
</div>
