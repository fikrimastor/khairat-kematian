<div>
    @if(!$payment->paymentProof)
        <button wire:click="$toggle('showUploadModal')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
            </svg>
            {{ __('Upload Receipt') }}
        </button>
    @else
        <div class="text-sm text-gray-600">
            {{ __('Receipt uploaded on') }} {{ $payment->paymentProof->created_at->format('d M Y, h:i A') }}
        </div>
    @endif
    
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
                        <x-secondary-button type="button" wire:click="$toggle('showUploadModal')">
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