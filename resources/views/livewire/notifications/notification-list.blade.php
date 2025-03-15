<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ __('Notifications') }}
                    @if ($unreadCount > 0)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </h2>
                <div class="flex space-x-2">
                    @if ($unreadCount > 0)
                        <button wire:click="markAllAsRead" class="text-sm text-blue-600 hover:text-blue-800">
                            {{ __('Mark all as read') }}
                        </button>
                    @endif
                    <button wire:click="toggleShowAll" class="text-sm text-gray-600 hover:text-gray-800">
                        @if ($showAll)
                            {{ __('Show less') }}
                        @else
                            {{ __('Show all') }}
                        @endif
                    </button>
                </div>
            </div>

            @if ($notifications->isEmpty())
                <div class="text-center py-4 text-gray-500">
                    {{ __('No notifications') }}
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($notifications as $notification)
                        <div class="flex items-start p-3 {{ is_null($notification->read_at) ? 'bg-blue-50' : 'bg-white' }} border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 mr-3">
                                @php
                                    $iconColor = $notification->data['color'] ?? 'gray';
                                    $icon = $notification->data['icon'] ?? 'bell';
                                @endphp
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-{{ $iconColor }}-100 text-{{ $iconColor }}-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if ($icon === 'cash')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                        @elseif ($icon === 'check-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @elseif ($icon === 'x-circle')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @elseif ($icon === 'user-plus')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                        @elseif ($icon === 'users')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        @endif
                                    </svg>
                                </span>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900">
                                            @if (isset($notification->data['subject']))
                                                {{ $notification->data['subject'] }}
                                            @else
                                                {{ __(Str::studly(Str::replace('_', ' ', $notification->data['payment_type'] ?? ''))) }}
                                            @endif
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600">
                                            @if (isset($notification->data['message']))
                                                {{ $notification->data['message'] }}
                                            @elseif (isset($notification->data['amount']))
                                                {{ __('Amount') }}: RM {{ number_format($notification->data['amount'], 2) }}
                                            @endif
                                        </p>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if (is_null($notification->read_at))
                                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-blue-600 hover:text-blue-800">
                                            {{ __('Mark as read') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($showAll && method_exists($notifications, 'links'))
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div> 