<div>
	{{-- Success is as dangerous as failure. --}}
	<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
		<h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('Make Payment') }}</h2>
		
		<form wire:submit.prevent="submitPayment" class="space-y-6">
			<!-- Period Selection -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<label for="month" class="block text-sm font-medium text-gray-700">{{ __('Month') }}</label>
					<select id="month"
							wire:model="month"
							class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
						<option value="">{{ __('Select Month') }}</option>
						@foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $monthOption)
							<option value="{{ $monthOption }}">{{ __($monthOption) }}</option>
						@endforeach
					</select>
					@error('month') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
				</div>
				
				<div>
					<label for="year" class="block text-sm font-medium text-gray-700">{{ __('Year') }}</label>
					<select id="year"
							wire:model="year"
							class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
						<option value="">{{ __('Select Year') }}</option>
						@foreach (range(now()->year - 1, now()->year + 1) as $yearOption)
							<option value="{{ $yearOption }}">{{ $yearOption }}</option>
						@endforeach
					</select>
					@error('year') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
				</div>
			</div>
			
			<!-- Household Count -->
			<div>
				<label for="householdCount"
						class="block text-sm font-medium text-gray-700">{{ __('Number of Household Members') }}</label>
				<input type="number"
						id="householdCount"
						wire:model="householdCount"
						min="1"
						max="20"
						class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
				@error('householdCount') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
			</div>
			
			<!-- Amount -->
			<div>
				<label for="amount"
						class="block text-sm font-medium text-gray-700">{{ __('Payment Amount (RM)') }}</label>
				<div class="mt-1 relative rounded-md shadow-sm">
					<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
						<span class="text-gray-500 sm:text-sm">RM</span>
					</div>
					<input type="number"
							id="amount"
							wire:model="amount"
							step="0.01"
							min="0.01"
							readonly
							class="pl-12 mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
				</div>
				<p class="text-sm text-gray-500 mt-1">{{ __('Amount is calculated at RM20 per person') }}</p>
				@error('amount') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
			</div>
			
			<!-- Payment Method -->
			<div>
				<label class="block text-sm font-medium text-gray-700">{{ __('Payment Method') }}</label>
				<div class="mt-2 space-y-2">
					@foreach ($this->paymentMethods as $value => $label)
						<div class="flex items-center">
							<input id="method-{{ $value }}"
									type="radio"
									wire:model="paymentMethod"
									value="{{ $value }}"
									class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
							<label for="method-{{ $value }}"
									class="ml-3 block text-sm font-medium text-gray-700">{{ $label }}</label>
						</div>
					@endforeach
				</div>
				@error('paymentMethod') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
			</div>
			
			<!-- Receipt Upload (for Bank Transfer) -->
			@if ($paymentMethod === 'bank_transfer')
				<div>
					<label for="receiptImage"
							class="block text-sm font-medium text-gray-700">{{ __('Upload Receipt') }}</label>
					<div class="mt-1 flex items-center">
						<input id="receiptImage"
								type="file"
								wire:model="receiptImage"
								accept="image/*"
								class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
					</div>
					<p class="text-sm text-gray-500 mt-1">{{ __('Please upload a clear image of your bank transfer receipt') }}</p>
					@error('receiptImage') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
					
					<!-- Receipt Preview -->
					@if ($receiptImage)
						<div class="mt-3">
							<p class="text-sm font-medium text-gray-700">{{ __('Receipt Preview:') }}</p>
							<img src="{{ $receiptImage->temporaryUrl() }}"
									alt="Receipt Preview"
									class="mt-2 h-48 object-contain border border-gray-200 rounded">
						</div>
					@endif
				</div>
			@endif
			
			<!-- Notes -->
			<div>
				<label for="notes" class="block text-sm font-medium text-gray-700">{{ __('Additional Notes') }}</label>
				<textarea id="notes"
						wire:model="notes"
						rows="3"
						class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
				@error('notes') <span class="text-red-600 text-sm mt-1">{{ $message }}</span> @enderror
			</div>
			
			<!-- Submit Button -->
			<div class="flex justify-end">
				<button type="submit"
						class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
						wire:loading.attr="disabled">
					<svg wire:loading
							wire:target="submitPayment"
							class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
							xmlns="http://www.w3.org/2000/svg"
							fill="none"
							viewBox="0 0 24 24">
						<circle class="opacity-25"
								cx="12"
								cy="12"
								r="10"
								stroke="currentColor"
								stroke-width="4"></circle>
						<path class="opacity-75"
								fill="currentColor"
								d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
					{{ __('Submit Payment') }}
				</button>
			</div>
		</form>
	</div>
	
	<!-- Payment Redirect Modal -->
	@if ($showPaymentRedirect)
		<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-10">
			<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
				<div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
					<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
						<div class="sm:flex sm:items-start">
							<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
								<h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Proceed to Payment') }}</h3>
								<div class="mt-2">
									<p class="text-sm text-gray-500">{{ __('You will be redirected to the payment gateway to complete your payment.') }}</p>
								</div>
							</div>
						</div>
					</div>
					<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
						<a href="{{ $paymentUrl }}"
								target="_blank"
								class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
							{{ __('Proceed to Payment') }}
						</a>
						<button type="button"
								wire:click="closeModals"
								class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
							{{ __('Cancel') }}
						</button>
					</div>
				</div>
			</div>
		</div>
	@endif
	
	<!-- Bank Details Modal -->
	@if ($showBankDetails)
		<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-10">
			<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
				<div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
					<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
						<div class="sm:flex sm:items-start">
							<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
								<h3 class="text-lg leading-6 font-medium text-gray-900">{{ __('Bank Transfer Details') }}</h3>
								<div class="mt-4">
									<div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
										<p class="text-sm font-medium text-gray-700">{{ __('Bank Name') }}:</p>
										<p class="text-sm text-gray-900 mb-2">{{ $bankDetails['bank_name'] ?? 'Bank Islam' }}</p>
										
										<p class="text-sm font-medium text-gray-700">{{ __('Account Number') }}:</p>
										<p class="text-sm text-gray-900 mb-2">{{ $bankDetails['account_number'] ?? '12345678901234' }}</p>
										
										<p class="text-sm font-medium text-gray-700">{{ __('Account Holder') }}:</p>
										<p class="text-sm text-gray-900 mb-2">{{ $bankDetails['account_holder'] ?? 'Masjid Al-Makmur' }}</p>
										
										<p class="text-sm font-medium text-gray-700">{{ __('Reference') }}:</p>
										<p class="text-sm text-gray-900">{{ __('Khairat Kematian') }} - {{ Auth::user()->name }}</p>
									</div>
									<p class="mt-2 text-sm text-gray-500">
										{{ __('Please make your payment and upload the receipt.') }}
									</p>
								</div>
							</div>
						</div>
					</div>
					<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
						<button type="button"
								wire:click="closeModals"
								class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm">
							{{ __('Close') }}
						</button>
					</div>
				</div>
			</div>
		</div>
	@endif
</div>
