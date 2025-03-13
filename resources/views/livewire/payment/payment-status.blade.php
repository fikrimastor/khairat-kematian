<div>
	{{-- Care about people's approval and you will be their prisoner. --}}
	<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
		<h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('Payment Status') }}</h2>
		
		<div class="mb-6">
			<div class="flex items-center">
				<div class="flex-shrink-0">
					@if ($payment->status->value === 'verified')
						<svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
					@elseif ($payment->status->value === 'rejected')
						<svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
					@elseif ($payment->status->value === 'pending')
						<svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
					@elseif ($payment->status->value === 'processing')
						<svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
						</svg>
					@else
						<svg class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
					@endif
				</div>
				<div class="ml-3">
					<h3 class="text-lg leading-6 font-medium text-gray-900">
						{{ __('Status') }}:
						<span class="
                            @if ($payment->status->value === 'verified')
                                text-green-600
                            @elseif ($payment->status->value === 'rejected')
                                text-red-600
                            @elseif ($payment->status->value === 'pending')
                                text-yellow-600
                            @elseif ($payment->status->value === 'processing')
                                text-blue-600
                            @else
                                text-gray-600
                            @endif
                        ">
                            {{ $payment->status instanceof \UnitEnum ? $payment->status->label() : $payment->status }}
                        </span>
					</h3>
					<p class="mt-1 text-sm text-gray-500">
						@if ($payment->status->value === 'verified')
							{{ __('This payment has been verified and is complete.') }}
						@elseif ($payment->status->value === 'rejected')
							{{ __('This payment has been rejected. Please see notes for details.') }}
						@elseif ($payment->status->value === 'pending')
							{{ __('This payment is pending verification by an administrator.') }}
						@elseif ($payment->status->value === 'processing')
							{{ __('This payment is currently being processed.') }}
						@else
							{{ __('Payment status is being updated.') }}
						@endif
					</p>
				</div>
			</div>
		</div>
		
		<!-- Payment details -->
		<div class="bg-gray-50 rounded-lg p-4 mb-6">
			<h3 class="text-md font-medium text-gray-900 mb-2">{{ __('Payment Details') }}</h3>
			<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
					<p class="text-sm font-medium text-gray-500">{{ __('Payment Date') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Period') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->month }} {{ $payment->year }}</p>
				</div>
				<div>
					<p class="text-sm font-medium text-gray-500">{{ __('Household Count') }}:</p>
					<p class="text-sm text-gray-900">{{ $payment->household_count }} {{ __('persons') }}</p>
				</div>
				@if ($payment->payment_date)
					<div>
						<p class="text-sm font-medium text-gray-500">{{ __('Payment Completed Date') }}:</p>
						<p class="text-sm text-gray-900">{{ $payment->payment_date->format('d/m/Y H:i') }}</p>
					</div>
				@endif
				@if ($payment->verified_at)
					<div>
						<p class="text-sm font-medium text-gray-500">{{ __('Verification Date') }}:</p>
						<p class="text-sm text-gray-900">{{ $payment->verified_at->format('d/m/Y H:i') }}</p>
					</div>
				@endif
			</div>
			
			@if ($payment->notes)
				<div class="mt-3">
					<p class="text-sm font-medium text-gray-500">{{ __('Notes') }}:</p>
					<p class="text-sm text-gray-900 mt-1">{{ $payment->notes }}</p>
				</div>
			@endif
		</div>
		
		<!-- Receipt information -->
		@if ($hasReceipt)
			<div class="mb-6">
				<h3 class="text-md font-medium text-gray-900 mb-2">{{ __('Receipt Information') }}</h3>
				<div class="bg-gray-50 rounded-lg p-4">
					<div class="flex justify-between">
						<div>
							<p class="text-sm font-medium text-gray-500">{{ __('Receipt Number') }}:</p>
							<p class="text-sm text-gray-900">{{ $payment->receipt->receipt_number }}</p>
						</div>
						<div>
							<a href="{{ route('payments.download-receipt', $payment) }}"
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
				</div>
			</div>
		@endif
		
		<!-- Verification actions (admin only) -->
		@if ($canBeVerified && auth()->user()->hasPermissionTo('payment.verify'))
			<div class="mt-6 flex justify-end">
				<button wire:click="showVerification"
						class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
					{{ __('Verify This Payment') }}
				</button>
			</div>
		@endif
	</div>
	
	<!-- Verification Modal -->
	@if ($showVerificationModal)
		<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-10">
			<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
				<div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
					<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
						<div class="sm:flex sm:items-start">
							<div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
								<svg class="h-6 w-6 text-indigo-600"
										fill="none"
										viewBox="0 0 24 24"
										stroke="currentColor">
									<path stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
								</svg>
							</div>
							<div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
								<h3 class="text-lg leading-6 font-medium text-gray-900">
									{{ __('Verify Payment') }}
								</h3>
								<div class="mt-4 w-full">
									<form wire:submit.prevent="verifyPayment">
										<div>
											<label for="status"
													class="block text-sm font-medium text-gray-700">{{ __('Verification Decision') }}</label>
											<select id="status"
													wire:model="status"
													class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
												<option value="verified">{{ __('Verify Payment') }}</option>
												<option value="rejected">{{ __('Reject Payment') }}</option>
											</select>
										</div>
										<div class="mt-3">
											<label for="notes"
													class="block text-sm font-medium text-gray-700">{{ __('Notes (Optional)') }}</label>
											<textarea id="notes"
													wire:model="notes"
													rows="3"
													class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
										</div>
										
										<!-- Verification Result (if any) -->
										@if ($verificationResult)
											<div class="mt-3 rounded-md p-3
                                            @if ($verificationResult['success'])
                                                bg-green-50 text-green-800
                                            @else
                                                bg-red-50 text-red-800
                                            @endif
                                        ">
												{{ $verificationResult['message'] }}
											</div>
										@endif
										
										<div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
											<button type="submit"
													class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
												{{ __('Submit Verification') }}
											</button>
											<button type="button"
													wire:click="cancelVerification"
													class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
												{{ __('Cancel') }}
											</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	@endif
</div>
