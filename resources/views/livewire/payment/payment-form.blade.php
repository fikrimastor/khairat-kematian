<div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('khairat.payment_details') }}
                </h2>

                <!-- Payment Type -->
                <div class="mb-4">
                    <x-input-label for="payment_type" :value="__('khairat.payment_type')" />
                    <select id="payment_type" wire:model.live="paymentType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('khairat.select_payment_type') }}</option>
                        @foreach($paymentTypes as $type => $label)
                            <option value="{{ $type }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('paymentType')" class="mt-2" />
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                    <x-input-label for="payment_method" :value="__('khairat.payment_method')" />
                    <select id="payment_method" wire:model="paymentMethod" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('khairat.select_payment_method') }}</option>
                        @foreach($paymentMethods as $method => $label)
                            <option value="{{ $method }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('paymentMethod')" class="mt-2" />
                </div>

                <!-- Amount -->
                <div class="mb-4">
                    <x-input-label for="amount" :value="__('khairat.amount') . ' (RM)'" />
                    <x-text-input id="amount" type="number" step="0.01" class="mt-1 block w-full" wire:model="amount" readonly="{{ $isAmountReadOnly }}" />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    @if($calculatedAmount > 0)
                        <p class="text-sm text-gray-600 mt-1">{{ __('khairat.calculated_amount') }}: RM {{ number_format($calculatedAmount, 2) }}</p>
                    @endif
                </div>

                <!-- Reference -->
                <div class="mb-4">
                    <x-input-label for="reference" :value="__('khairat.reference')" />
                    <x-text-input id="reference" type="text" class="mt-1 block w-full" wire:model="reference" placeholder="{{ __('khairat.reference_optional') }}" />
                    <x-input-error :messages="$errors->get('reference')" class="mt-2" />
                </div>

                <!-- Payment Summary -->
                @if($showSummary)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900 mb-2">{{ __('khairat.payment_summary') }}</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>{{ __('khairat.payment_type') }}:</span>
                                <span class="font-medium">{{ $paymentTypes[$paymentType] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>{{ __('khairat.payment_method') }}:</span>
                                <span class="font-medium">{{ $paymentMethods[$paymentMethod] ?? '' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>{{ __('khairat.amount') }}:</span>
                                <span class="font-medium">RM {{ number_format($amount, 2) }}</span>
                            </div>
                            @if($reference)
                                <div class="flex justify-between">
                                    <span>{{ __('khairat.reference') }}:</span>
                                    <span class="font-medium">{{ $reference }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <x-secondary-button type="button" wire:click="cancel">
                {{ __('khairat.cancel') }}
            </x-secondary-button>
            
            <x-primary-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('khairat.proceed_to_payment') }}</span>
                <span wire:loading>{{ __('khairat.processing') }}</span>
            </x-primary-button>
        </div>
    </form>

    <!-- Payment Processing Modal -->
    @if($showProcessingModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('khairat.processing_payment') }}</h3>
                <div class="flex items-center justify-center mb-4">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-center text-gray-600">{{ __('khairat.please_wait_processing') }}</p>
            </div>
        </div>
    @endif
</div> 