<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('khairat.reports_generation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">{{ __('khairat.reports_generation_message') }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <!-- Payment Summary Report Card -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-md overflow-hidden">
                            <div class="p-5 bg-blue-50">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('khairat.payment_summary') }}</h3>
                                <p class="text-gray-600 mb-4">{{ __('khairat.payment_summary_desc') }}</p>
                                <a href="{{ route('admin.reports.payment-summary') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('khairat.view_report') }}
                                </a>
                            </div>
                        </div>
                        
                        <!-- Member Statistics Report Card -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-md overflow-hidden">
                            <div class="p-5 bg-purple-50">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('khairat.member_statistics') }}</h3>
                                <p class="text-gray-600 mb-4">{{ __('khairat.member_statistics_desc') }}</p>
                                <a href="{{ route('admin.reports.member-statistics') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('khairat.view_report') }}
                                </a>
                            </div>
                        </div>
                        
                        <!-- Payment History Report Card -->
                        <div class="bg-white rounded-lg border border-gray-200 shadow-md overflow-hidden">
                            <div class="p-5 bg-green-50">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('khairat.payment_history') }}</h3>
                                <p class="text-gray-600 mb-4">{{ __('khairat.payment_history_desc') }}</p>
                                <a href="{{ route('admin.reports.payment-history') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('khairat.view_report') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-right">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('khairat.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout> 