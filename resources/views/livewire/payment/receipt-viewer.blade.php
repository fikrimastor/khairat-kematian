<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Receipt') }} #{{ $receipt->receipt_number }}</h2>
            <div class="flex space-x-2">
                @if($receipt->receipt_path)
                    <a href="{{ $downloadUrl }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Download') }}
                    </a>
                @endif
                <button wire:click="toggleDetails" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring focus:ring-gray-200 disabled:opacity-25 transition">
                    {{ $showDetails ? __('Hide Details') : __('Show Details') }}
                </button>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Receipt Date') }}</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $receipt->formattedDate }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Payment Method') }}</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $payment->payment_method }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Amount') }}</p>
                    <p class="mt-1 text-sm text-gray-900">RM {{ number_format($payment->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">{{ __('Payment Year') }}</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $payment->year }}</p>
                </div>
            </div>
        </div>
        
        @if($showDetails)
            <div class="mt-6 border-t border-gray-200 pt-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Member Details') }}</h3>
                <div class="mt-2 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Name') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('ID Number') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->identification_number ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Email') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Phone') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $user->phone ?? '-' }}</p>
                    </div>
                </div>
                
                <h3 class="mt-6 text-lg font-medium text-gray-900">{{ __('Payment Details') }}</h3>
                <div class="mt-2 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Payment ID') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $payment->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Reference Number') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $payment->reference_no ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Household Count') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $payment->household_count }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Status') }}</p>
                        <p class="mt-1 text-sm text-gray-900">{{ $payment->status }}</p>
                    </div>
                    @if($payment->notes)
                        <div class="col-span-2">
                            <p class="text-sm font-medium text-gray-500">{{ __('Notes') }}</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $payment->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div> 