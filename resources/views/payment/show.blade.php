<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('payments.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to Payments') }}
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('Payment #:id', ['id' => $payment->id]) }}
                        </h3>
                        
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $payment->status->color() }}">
                            {{ $payment->status->label() }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">{{ __('Payment Information') }}</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="grid grid-cols-1 gap-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Reference ID') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->reference_id ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Type') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->type->label() }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Amount') }}</dt>
                                        <dd class="text-sm text-gray-900">RM {{ number_format($payment->amount, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Payment Method') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->payment_method }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Date') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->created_at->format('d M Y, h:i A') }}</dd>
                                    </div>
                                    @if($payment->reference)
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">{{ __('Reference') }}</dt>
                                            <dd class="text-sm text-gray-900">{{ $payment->reference }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-md font-medium text-gray-700 mb-3">{{ __('Payment Status') }}</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <dl class="grid grid-cols-1 gap-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                        <dd class="text-sm text-gray-900">{{ $payment->status->label() }}</dd>
                                    </div>
                                    @if($payment->verified_at)
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">{{ __('Verified At') }}</dt>
                                            <dd class="text-sm text-gray-900">{{ $payment->verified_at->format('d M Y, h:i A') }}</dd>
                                        </div>
                                    @endif
                                    @if($payment->verified_by)
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500">{{ __('Verified By') }}</dt>
                                            <dd class="text-sm text-gray-900">{{ $payment->verifier->name ?? 'N/A' }}</dd>
                                        </div>
                                    @endif
                                </dl>
                                
                                @if($payment->status->isPending() && $payment->payment_method === 'bank_transfer')
                                    <div class="mt-4 border-t border-gray-200 pt-4">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">{{ __('Bank Transfer Instructions') }}</h5>
                                        <p class="text-sm text-gray-600 mb-2">{{ __('Please transfer the exact amount to:') }}</p>
                                        <div class="bg-white p-3 rounded border border-gray-200 text-sm">
                                            <p><strong>{{ __('Bank') }}:</strong> {{ config('services.bank_transfer.bank_name', 'Bank Islam') }}</p>
                                            <p><strong>{{ __('Account Number') }}:</strong> {{ config('services.bank_transfer.account_number', '12345678901') }}</p>
                                            <p><strong>{{ __('Account Holder') }}:</strong> {{ config('services.bank_transfer.account_holder', 'Masjid Al-Makmur') }}</p>
                                            <p><strong>{{ __('Reference') }}:</strong> {{ $payment->reference_id ?? 'KK-' . auth()->id() }}</p>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <livewire:payment.upload-receipt :payment="$payment" />
                                        </div>
                                    </div>
                                @endif
                                
                                @if($payment->status->isCompleted())
                                    <div class="mt-4 border-t border-gray-200 pt-4">
                                        <a href="{{ route('payments.receipt', $payment) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                            </svg>
                                            {{ __('Download Receipt') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @if($payment->paymentProof)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-700 mb-3">{{ __('Payment Proof') }}</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-sm text-gray-600">{{ __('Uploaded at') }}: {{ $payment->paymentProof->created_at->format('d M Y, h:i A') }}</p>
                                        @if($payment->paymentProof->notes)
                                            <p class="text-sm text-gray-600 mt-1">{{ __('Notes') }}: {{ $payment->paymentProof->notes }}</p>
                                        @endif
                                    </div>
                                    <a href="{{ Storage::url($payment->paymentProof->file_path) }}" target="_blank" class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ __('View Receipt') }}
                                    </a>
                                </div>
                                
                                <div class="border border-gray-200 rounded overflow-hidden">
                                    @php
                                        $fileExtension = pathinfo(Storage::url($payment->paymentProof->file_path), PATHINFO_EXTENSION);
                                    @endphp
                                    
                                    @if(in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ Storage::url($payment->paymentProof->file_path) }}" alt="Payment Receipt" class="w-full h-auto max-h-64 object-contain">
                                    @else
                                        <div class="flex items-center justify-center p-6 bg-gray-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span class="ml-2 text-gray-600">{{ strtoupper($fileExtension) }} {{ __('File') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 