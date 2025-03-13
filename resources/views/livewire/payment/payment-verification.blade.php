<div>
	{{-- The best athlete wants his opponent at his best. --}}
	<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
		<h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('Payment Verification') }}</h2>
		
		<!-- Payment Details Summary -->
		<div class="bg-gray-50 rounded-lg p-4 mb-6">
			<h3 class="text-md font-medium text-gray-900 mb-2">{{ __('Payment Information') }}</h3>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Member') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->user->name }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Payment ID') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->id }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Amount') }}:</p>
					<p class="text-sm text-gray-900">RM {{ number_format($payment->amount, 2) }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Payment Method') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->payment_method instanceof \UnitEnum ? $payment->payment_method->label() : $payment->payment_method }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Period') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->month }} {{ $payment->year }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Payment Date') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
				</div>
			</div>
		</div>
		
		<!-- Receipt View (if available) -->
		@if ($payment->receipt && $payment->receipt->receipt_path)
			<div class="mb-6">
				<h3 class="text-md font-medium text-gray-900 mb-2">{{ __('Receipt') }}</h3>
				<div class="border border-gray-200 rounded-lg overflow-hidden">
					<img src="{{ Storage::url($payment->receipt->receipt_path) }}"
							alt="{{ __('Receipt') }}"
							class="w-full h-auto object-contain">
				</div>
				<div class="mt-2 flex justify-end">
					<a href="{{ route('payments.download-receipt', $payment) }}"
							target="_blank"
							class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
						<svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
						</svg>
						{{ __('Download Receipt') }}
					</a>
				</div>
			</div>
		@else
			<div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4">
				<div class="flex">
					<div class="flex-shrink-0">
						<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
									d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
									clip-rule="evenodd"></path>
						</svg>
					</div>
					<div class="ml-3">
						<p class="text-sm text-yellow-700">
							{{ __('No receipt has been uploaded for this payment.') }}
						</p>
					</div>
				</div>
			</div>
		@endif
		
		<!-- Verification Form -->
		@if ($canBeVerified)
			<form wire:submit.prevent="verifyPayment" class="mt-6">
				<div class="shadow overflow-hidden sm:rounded-md">
					<div class="px-4 py-5 bg-white space-y-6 sm:p-6">
						<fieldset>
							<legend class="text-base font-medium text-gray-900">{{ __('Verification Decision') }}</legend>
							<div class="mt-4 space-y-4">
								<div class="flex items-center">
									<input id="verified"
											wire:model="status"
											value="verified"
											type="radio"
											class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
									<label for="verified" class="ml-3 block text-sm font-medium text-gray-700">
										{{ __('Verify Payment') }}
									</label>
								</div>
								<div class="flex items-center">
									<input id="rejected"
											wire:model="status"
											value="rejected"
											type="radio"
											class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
									<label for="rejected" class="ml-3 block text-sm font-medium text-gray-700">
										{{ __('Reject Payment') }}
									</label>
								</div>
							</div>
						</fieldset>
						
						<div>
							<label for="notes"
									class="block text-sm font-medium text-gray-700">{{ __('Verification Notes') }}</label>
							<div class="mt-1">
								<textarea id="notes"
										wire:model="notes"
										rows="3"
										class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
										placeholder="{{ __('Add any notes or reason for verification/rejection') }}"></textarea>
							</div>
							<p class="mt-2 text-sm text-gray-500">
								{{ __('These notes will be visible to the member.') }}
							</p>
						</div>
					</div>
					
					<!-- Verification Result (if any) -->
					@if ($verificationResult)
						<div class="px-4 py-3
                            @if ($verificationResult['success'])
                                bg-green-50 text-green-800
                            @else
                                bg-red-50 text-red-800
                            @endif
                        ">
							{{ $verificationResult['message'] }}
						</div>
					@endif
					
					<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
						<button type="submit"
								class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
							{{ __('Submit Verification') }}
						</button>
					</div>
				</div>
			</form>
		@else
			<div class="rounded-md bg-gray-50 p-4 mt-6">
				<div class="flex">
					<div class="flex-shrink-0">
						<svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
									d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
									clip-rule="evenodd"></path>
						</svg>
					</div>
					<div class="ml-3">
						<h3 class="text-sm font-medium text-gray-800">
							{{ __('Payment verification unavailable') }}
						</h3>
						<div class="mt-2 text-sm text-gray-700">
							<p>
								{{ __('This payment cannot be verified because its current status is') }}
								<strong>{{ $payment->status instanceof \UnitEnum ? $payment->status->label() : $payment->status }}</strong>.
							</p>
						</div>
					</div>
				</div>
			</div>
		@endif
	</div>
</div>
