<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-4">
                {{ __('khairat.payment_history') }}
            </h2>

            <!-- Filters -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <h3 class="text-md font-medium text-gray-700 mb-3">{{ __('khairat.filter_payments') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <x-input-label for="search" :value="__('khairat.search')" />
                        <x-text-input id="search" wire:model.live.debounce.300ms="search" type="text" class="mt-1 block w-full" placeholder="{{ __('khairat.reference_or_notes') }}" />
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <x-input-label for="status" :value="__('khairat.status')" />
                        <select id="status" wire:model.live="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('khairat.all_statuses') }}</option>
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Method Filter -->
                    <div>
                        <x-input-label for="paymentMethod" :value="__('khairat.payment_method')" />
                        <select id="paymentMethod" wire:model.live="paymentMethod" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('khairat.all_methods') }}</option>
                            @foreach($paymentMethods as $methodOption)
                                <option value="{{ $methodOption->value }}">{{ $methodOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Type Filter -->
                    <div>
                        <x-input-label for="paymentType" :value="__('khairat.payment_type')" />
                        <select id="paymentType" wire:model.live="paymentType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('khairat.all_types') }}</option>
                            @foreach($paymentTypes as $typeOption)
                                <option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div>
                        <x-input-label for="year" :value="__('khairat.year')" />
                        <select id="year" wire:model.live="year" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('khairat.all_years') }}</option>
                            @foreach($years as $yearOption)
                                <option value="{{ $yearOption }}">{{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Filter -->
                    <div>
                        <x-input-label for="month" :value="__('khairat.month')" />
                        <select id="month" wire:model.live="month" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">{{ __('khairat.all_months') }}</option>
                            @foreach($months as $key => $monthName)
                                <option value="{{ $key }}">{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Reset Filters Button -->
                <div class="mt-4">
                    <x-secondary-button wire:click="resetFilters" class="mt-2">
                        {{ __('khairat.reset_filters') }}
                    </x-secondary-button>
                </div>
            </div>

            <!-- Payments Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                                {{ __('khairat.date') }}
                                @if($sortField === 'created_at')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        @endif
                                    </span>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('reference_no')">
                                {{ __('khairat.reference') }}
                                @if($sortField === 'reference_no')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        @endif
                                    </span>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('amount')">
                                {{ __('khairat.amount') }}
                                @if($sortField === 'amount')
                                    <span class="ml-1">
                                        @if($sortDirection === 'asc')
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                        @else
                                            <svg class="w-3 h-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        @endif
                                    </span>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('khairat.type') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('khairat.method') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('khairat.period') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('khairat.status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('khairat.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->reference_no ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    RM {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($payment->payment_type))
                                        {{ \App\Enums\PaymentType::from($payment->payment_type)->label() }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="inline-flex items-center">
                                        <i class="{{ $payment->payment_method->icon() }} mr-1"></i>
                                        {{ $payment->payment_method->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($payment->month)
                                        {{ $months[$payment->month] ?? $payment->month }}
                                    @endif
                                    {{ $payment->year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $payment->status->color() }}-100 text-{{ $payment->status->color() }}-800">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('khairat.view') }}
                                        </a>
                                        
                                        @if($payment->receipt)
                                            <a href="{{ route('receipts.show', $payment->receipt) }}" class="text-green-600 hover:text-green-900">
                                                {{ __('khairat.receipt') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ __('khairat.no_payments_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div> 