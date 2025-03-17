<div>
    <!-- Summary Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Members Card -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Members') }}</p>
                    <p class="text-2xl font-bold">{{ $totalMembers }}</p>
                </div>
            </div>
        </div>

        <!-- Active Members Card -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Active Members') }}</p>
                    <p class="text-2xl font-bold">{{ $activeMembers }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Payments Card -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Pending Payments') }}</p>
                    <p class="text-2xl font-bold">{{ $pendingPayments }}</p>
                    @if($pendingPayments > 0)
                        <a href="{{ route('admin.payment-verification') }}" class="text-sm text-yellow-600 hover:text-yellow-800 mt-1 inline-block">
                            {{ __('Verify Payments') }} →
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">{{ __('Total Revenue') }}</p>
                    <p class="text-2xl font-bold">RM {{ number_format($totalRevenue, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Section -->
    <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Revenue Overview') }}</h3>
            <div class="mt-4 flex space-x-4">
                <button wire:click="setTimeframe('month')" class="px-4 py-2 rounded-md {{ $timeframe === 'month' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                    {{ __('This Month') }}
                </button>
                <button wire:click="setTimeframe('year')" class="px-4 py-2 rounded-md {{ $timeframe === 'year' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                    {{ __('This Year') }}
                </button>
                <button wire:click="setTimeframe('all')" class="px-4 py-2 rounded-md {{ $timeframe === 'all' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' }}">
                    {{ __('All Time') }}
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="text-center">
                <p class="text-gray-500 text-sm">{{ __('Revenue') }} ({{ $timeframe === 'month' ? __('This Month') : ($timeframe === 'year' ? __('This Year') : __('All Time')) }})</p>
                <p class="text-3xl font-bold mt-2">RM {{ number_format($periodRevenue, 2) }}</p>
            </div>
            
            <!-- Monthly Trends Chart (Placeholder) -->
            <div class="mt-6 h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                <p class="text-gray-500">{{ __('Monthly revenue chart will be displayed here') }}</p>
                <!-- In a real implementation, you would use a chart library like Chart.js or ApexCharts -->
            </div>
        </div>
    </div>

    <!-- Recent Payments and System Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Payments -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Recent Payments') }}</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Member') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Amount') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($recentPayments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-gray-500 font-medium">{{ $payment->user->initials() }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $payment->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $payment->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">RM {{ number_format($payment->amount, 2) }}</div>
                                        <div class="text-sm text-gray-500">{{ $payment->payment_type }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $payment->status === \App\Enums\PaymentStatus::VERIFIED ? 'bg-green-100 text-green-800' : 
                                               ($payment->status === \App\Enums\PaymentStatus::PENDING ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ $payment->status->value }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->created_at->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('No recent payments found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="{{ route('payments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        {{ __('View all payments') }} →
                    </a>
                </div>
            </div>
        </div>

        <!-- System Settings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('System Settings') }}</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">{{ __('Registration Fee') }}</h4>
                        <p class="text-lg font-semibold">RM {{ number_format($registrationFee, 2) }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">{{ __('Renewal Fee') }}</h4>
                        <p class="text-lg font-semibold">RM {{ number_format($renewalFee, 2) }}</p>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.settings') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('Manage Settings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Quick Actions') }}</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.payment-verification') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium">{{ __('Verify Payments') }}</p>
                    <p class="text-sm text-gray-500">{{ __('Review and verify pending payments') }}</p>
                </div>
            </a>
            <a href="{{ route('admin.members') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium">{{ __('Manage Members') }}</p>
                    <p class="text-sm text-gray-500">{{ __('View and manage member accounts') }}</p>
                </div>
            </a>
            <a href="{{ route('admin.reports') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <p class="font-medium">{{ __('Generate Reports') }}</p>
                    <p class="text-sm text-gray-500">{{ __('Create and download reports') }}</p>
                </div>
            </a>
        </div>
    </div>
</div> 