<div>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Payment History') }}
                </h2>
                <x-primary-button wire:click="$dispatch('openModal', { component: 'payment.payment-filter' })">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    {{ __('Filter') }}
                </x-primary-button>
            </div>

            @if($payments->isEmpty())
                <div class="text-center py-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('No payments found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('You have not made any payments yet.') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            {{ __('Make a Payment') }}
                        </a>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Reference') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Type') }}
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $payment->reference_id ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->type->label() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        RM {{ number_format($payment->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status->color() }}">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('View') }}
                                            </a>
                                            
                                            @if($payment->status->isPending() && $payment->payment_method === 'bank_transfer')
                                                <button wire:click="uploadReceipt({{ $payment->id }})" class="text-green-600 hover:text-green-900">
                                                    {{ __('Upload Receipt') }}
                                                </button>
                                            @endif
                                            
                                            @if($payment->status->isCompleted())
                                                <a href="{{ route('payments.receipt', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                                    {{ __('Download Receipt') }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <!-- Upload Receipt Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Upload Payment Receipt') }}</h3>
                
                <form wire:submit="submitReceipt">
                    <div class="mb-4">
                        <x-input-label for="receipt" :value="__('Receipt Image')" />
                        <input type="file" id="receipt" wire:model="receiptFile" class="mt-1 block w-full" accept="image/*,.pdf" />
                        <x-input-error :messages="$errors->get('receiptFile')" class="mt-2" />
                        <p class="text-sm text-gray-500 mt-1">{{ __('Accepted formats: JPG, PNG, PDF. Max size: 2MB') }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <x-input-label for="notes" :value="__('Notes (Optional)')" />
                        <textarea id="notes" wire:model="receiptNotes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3"></textarea>
                        <x-input-error :messages="$errors->get('receiptNotes')" class="mt-2" />
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <x-secondary-button type="button" wire:click="closeUploadModal">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        
                        <x-primary-button type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitReceipt">{{ __('Upload') }}</span>
                            <span wire:loading wire:target="submitReceipt">{{ __('Uploading...') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div> 