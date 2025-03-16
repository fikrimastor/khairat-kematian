<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('khairat.payment_history') }}
                </h2>
                <div class="flex space-x-3">
                    <!-- View Mode Toggle -->
                    <x-secondary-button wire:click="toggleViewMode">
                        @if($viewMode === 'list')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            {{ __('khairat.advanced_view') }}
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h7" />
                            </svg>
                            {{ __('khairat.simple_view') }}
                        @endif
                    </x-secondary-button>
                    
                    @if($viewMode === 'list')
                        <x-primary-button wire:click="$dispatch('openModal', { component: 'payment.payment-filter' })">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            {{ __('khairat.filter') }}
                        </x-primary-button>
                    @endif
                </div>
            </div>

            <!-- History View - Advanced Filters -->
            @if($viewMode === 'history')
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
                                @foreach($this->statuses as $statusOption)
                                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Method Filter -->
                        <div>
                            <x-input-label for="paymentMethod" :value="__('khairat.payment_method')" />
                            <select id="paymentMethod" wire:model.live="paymentMethod" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('khairat.all_methods') }}</option>
                                @foreach($this->paymentMethods as $methodOption)
                                    <option value="{{ $methodOption->value }}">{{ $methodOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Payment Type Filter -->
                        <div>
                            <x-input-label for="paymentType" :value="__('khairat.payment_type')" />
                            <select id="paymentType" wire:model.live="paymentType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('khairat.all_types') }}</option>
                                @foreach($this->paymentTypes as $typeOption)
                                    <option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Year Filter -->
                        <div>
                            <x-input-label for="year" :value="__('khairat.year')" />
                            <select id="year" wire:model.live="year" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('khairat.all_years') }}</option>
                                @foreach($this->years as $yearOption => $yearValue)
                                    <option value="{{ $yearValue }}">{{ $yearValue }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Month Filter -->
                        <div>
                            <x-input-label for="month" :value="__('khairat.month')" />
                            <select id="month" wire:model.live="month" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">{{ __('khairat.all_months') }}</option>
                                @foreach($this->months as $key => $monthName)
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
            @endif

            @if($this->payments->isEmpty())
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('khairat.no_payments_found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('khairat.you_have_not_made_any_payments') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('payments.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('khairat.make_a_payment') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Date Column - Sortable -->
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
                                
                                <!-- Reference Column - Sortable -->
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
                                
                                <!-- Amount Column - Sortable -->
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
                                
                                @if($viewMode === 'history')
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('khairat.period') }}
                                </th>
                                @endif
                                
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('khairat.status') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('khairat.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($this->payments as $payment)
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
                                    
                                    @if($viewMode === 'history')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($payment->month)
                                            {{ $this->months[$payment->month] ?? $payment->month }}
                                        @endif
                                        {{ $payment->year }}
                                    </td>
                                    @endif
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $payment->status->color() }}-100 text-{{ $payment->status->color() }}-800">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('payments.show', $payment) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('khairat.view') }}
                                            </a>
                                            
                                            @if($payment->status->isPending() && $payment->payment_method === 'bank_transfer')
                                                <button wire:click="uploadReceipt({{ $payment->id }})" class="text-green-600 hover:text-green-900">
                                                    {{ __('khairat.upload_receipt') }}
                                                </button>
                                            @endif
                                            
                                            @if($payment->receipt)
                                                <a href="{{ route('receipts.show', $payment->receipt) }}" wire:navigate class="text-blue-600 hover:text-blue-900">
                                                    {{ __('khairat.receipt') }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $viewMode === 'history' ? '8' : '7' }}" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ __('khairat.no_payments_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $this->payments->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Upload Receipt Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('khairat.upload_payment_receipt') }}</h3>
                
                <form wire:submit="submitReceipt">
                    <div class="mb-4">
                        <x-input-label for="receipt" :value="__('khairat.receipt_image')" />
                        <input type="file" id="receipt" wire:model="receiptFile" class="mt-1 block w-full" accept="image/*,.pdf" />
                        <x-input-error :messages="$errors->get('receiptFile')" class="mt-2" />
                        <p class="text-sm text-gray-500 mt-1">{{ __('khairat.accepted_formats') }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <x-input-label for="notes" :value="__('khairat.notes_optional')" />
                        <textarea id="notes" wire:model="receiptNotes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                        <x-input-error :messages="$errors->get('receiptNotes')" class="mt-2" />
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <x-secondary-button type="button" wire:click="closeUploadModal">
                            {{ __('khairat.cancel') }}
                        </x-secondary-button>
                        
                        <x-primary-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitReceipt">{{ __('khairat.upload') }}</span>
                            <span wire:loading wire:target="submitReceipt">{{ __('khairat.uploading') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div> 