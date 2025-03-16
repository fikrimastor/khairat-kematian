<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ __('Welcome back, :name!', ['name' => Auth::user()->name]) }}</h2>
                    <p class="text-gray-600">{{ __('You are logged in to the Khairat Kematian Management System.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Profile Summary Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('My Profile') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">{{ __('Manage your personal information and contact details.') }}</p>
                        <a href="{{ route('member.show') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            {{ __('View Profile') }} →
                        </a>
                    </div>
                </div>

                <!-- Family Members Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Family Members') }}</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-2">{{ __('You have :count family members registered.', ['count' => Auth::user()->dependents->count()]) }}</p>
                        
                        <a href="{{ route('dependent.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            {{ __('Manage Family Members') }} →
                        </a>
                    </div>
                </div>

                <!-- Payment Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 p-3 rounded-full mr-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Payment Status') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">{{ __('View your payment history and make new payments.') }}</p>
                        <a href="{{ route('payments.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            {{ __('View Payments') }} →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Recent Activity') }}</h3>
                    
                    <div class="border rounded-md overflow-hidden">
                        <div class="px-4 py-5 sm:p-6">
                            <!-- If no recent activity -->
                            <p class="text-gray-500 text-sm">{{ __('No recent activity to show.') }}</p>
                            
                            <!-- For future implementation: Recent activity like payment history, profile updates, dependent changes, etc. -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
