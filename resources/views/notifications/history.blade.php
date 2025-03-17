<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notification History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">{{ __('Your Notifications') }}</h3>
                        
                        <div class="flex space-x-2">
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                @csrf
                                <x-secondary-button type="submit">
                                    {{ __('Mark All as Read') }}
                                </x-secondary-button>
                            </form>
                            
                            <form action="{{ route('notifications.delete-all') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete all notifications?') }}')">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit">
                                    {{ __('Delete All') }}
                                </x-danger-button>
                            </form>
                        </div>
                    </div>
                    
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-md">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    @if ($notifications->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">{{ __('You have no notifications.') }}</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($notifications as $notification)
                                <div class="border rounded-lg p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50 border-blue-200' }}">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium {{ $notification->read_at ? 'text-gray-700' : 'text-blue-700' }}">
                                                {{ $notification->data['type'] ?? 'Notification' }}
                                            </h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $notification->data['message'] ?? '' }}
                                            </p>
                                            <div class="text-xs text-gray-500 mt-2">
                                                {{ $notification->created_at->diffForHumans() }}
                                                @if ($notification->read_at)
                                                    <span class="ml-2">{{ __('Read') }}: {{ $notification->read_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="flex space-x-2">
                                            @if (!$notification->read_at)
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <x-secondary-button type="submit" class="text-xs py-1 px-2">
                                                        {{ __('Mark as Read') }}
                                                    </x-secondary-button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this notification?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button type="submit" class="text-xs py-1 px-2">
                                                    {{ __('Delete') }}
                                                </x-danger-button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 