<div>
	{{-- Stop trying to control. --}}
	<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
		<!-- Filters -->
		<div class="p-6 border-b border-gray-200">
			<div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4">
				<!-- Search -->
				<div class="w-full md:w-1/3">
					<label for="search" class="sr-only">{{ __('Search') }}</label>
					<div class="relative">
						<div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
							<svg class="w-5 h-5 text-gray-500"
									fill="currentColor"
									viewBox="0 0 20 20"
									xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd"
										d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
										clip-rule="evenodd"></path>
							</svg>
						</div>
						<input type="text"
								wire:model.debounce.300ms="search"
								id="search"
								class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5"
								placeholder="{{ __('Search') }}">
					</div>
				</div>
				
				<!-- Filters -->
				<div class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
					<select wire:model="statusFilter"
							class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
						<option value="">{{ __('All Statuses') }}</option>
						@foreach($this->paymentStatuses as $status)
							<option value="{{ $status['value'] }}">{{ $status['label'] }}</option>
						@endforeach
					</select>
					
					<select wire:model="methodFilter"
							class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
						<option value="">{{ __('All Methods') }}</option>
						@foreach($this->paymentMethods as $value => $label)
							<option value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
					
					<select wire:model="monthFilter"
							class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
						<option value="">{{ __('All Months') }}</option>
						@foreach($this->months as $month)
							<option value="{{ $month }}">{{ __($month) }}</option>
						@endforeach
					</select>
					
					<select wire:model="yearFilter"
							class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
						<option value="">{{ __('All Years') }}</option>
						@foreach($this->years as $year)
							<option value="{{ $year }}">{{ $year }}</option>
						@endforeach
					</select>
					
					<button wire:click="resetFilters"
							class="flex items-center justify-center text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-4 py-2.5">
						<svg class="w-4 h-4 mr-2"
								fill="none"
								stroke="currentColor"
								viewBox="0 0 24 24"
								xmlns="http://www.w3.org/2000/svg">
							<path stroke-linecap="round"
									stroke-linejoin="round"
									stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
						</svg>
						{{ __('Reset') }}
					</button>
				</div>
			</div>
		</div>
		
		<!-- Table -->
		<div class="overflow-x-auto">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
				<tr>
					<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
						<div class="flex items-center cursor-pointer" wire:click="sortBy('created_at')">
							{{ __('Date') }}
							@if ($sortField === 'created_at')
								<svg class="w-4 h-4 ml-1"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
										xmlns="http://www.w3.org/2000/svg">
									@if ($sortDirection === 'asc')
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M5 15l7-7 7 7"></path>
									@else
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M19 9l-7 7-7-7"></path>
									@endif
								</svg>
							@endif
						</div>
					</th>
					@if ($isAdmin)
						<th scope="col"
								class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
							<div class="flex items-center cursor-pointer" wire:click="sortBy('user_id')">
								{{ __('Member') }}
								@if ($sortField === 'user_id')
									<svg class="w-4 h-4 ml-1"
											fill="none"
											stroke="currentColor"
											viewBox="0 0 24 24"
											xmlns="http://www.w3.org/2000/svg">
										@if ($sortDirection === 'asc')
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M5 15l7-7 7 7"></path>
										@else
											<path stroke-linecap="round"
													stroke-linejoin="round"
													stroke-width="2"
													d="M19 9l-7 7-7-7"></path>
										@endif
									</svg>
								@endif
							</div>
						</th>
					@endif
					<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
						<div class="flex items-center cursor-pointer" wire:click="sortBy('month')">
							{{ __('Period') }}
							@if ($sortField === 'month')
								<svg class="w-4 h-4 ml-1"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
										xmlns="http://www.w3.org/2000/svg">
									@if ($sortDirection === 'asc')
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M5 15l7-7 7 7"></path>
									@else
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M19 9l-7 7-7-7"></path>
									@endif
								</svg>
							@endif
						</div>
					</th>
					<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
						<div class="flex items-center cursor-pointer" wire:click="sortBy('amount')">
							{{ __('Amount') }}
							@if ($sortField === 'amount')
								<svg class="w-4 h-4 ml-1"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
										xmlns="http://www.w3.org/2000/svg">
									@if ($sortDirection === 'asc')
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M5 15l7-7 7 7"></path>
									@else
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M19 9l-7 7-7-7"></path>
									@endif
								</svg>
							@endif
						</div>
					</th>
					<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
						<div class="flex items-center cursor-pointer" wire:click="sortBy('payment_method')">
							{{ __('Method') }}
							@if ($sortField === 'payment_method')
								<svg class="w-4 h-4 ml-1"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
										xmlns="http://www.w3.org/2000/svg">
									@if ($sortDirection === 'asc')
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M5 15l7-7 7 7"></path>
									@else
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M19 9l-7 7-7-7"></path>
									@endif
								</svg>
							@endif
						</div>
					</th>
					<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
						<div class="flex items-center cursor-pointer" wire:click="sortBy('status')">
							{{ __('Status') }}
							@if ($sortField === 'status')
								<svg class="w-4 h-4 ml-1"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
										xmlns="http://www.w3.org/2000/svg">
									@if ($sortDirection === 'asc')
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M5 15l7-7 7 7"></path>
									@else
										<path stroke-linecap="round"
												stroke-linejoin="round"
												stroke-width="2"
												d="M19 9l-7 7-7-7"></path>
									@endif
								</svg>
							@endif
						</div>
					</th>
					<th scope="col"
							class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
						{{ __('Receipt') }}
					</th>
					<th scope="col"
							class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
						{{ __('Actions') }}
					</th>
				</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
				@forelse ($payments as $payment)
					<tr>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ $payment->created_at->format('d/m/Y H:i') }}
						</td>
						@if ($isAdmin)
							<td class="px-6 py-4 whitespace-nowrap">
								<div class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</div>
								<div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
							</td>
						@endif
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ $payment->month }} {{ $payment->year }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
							RM {{ number_format($payment->amount, 2) }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							{{ $payment->payment_method instanceof \UnitEnum ? $payment->payment_method->label() : $payment->payment_method }}
						</td>
						<td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($payment->status->value === 'verified')
                                        bg-green-100 text-green-800
                                    @elseif ($payment->status->value === 'rejected')
                                        bg-red-100 text-red-800
                                    @elseif ($payment->status->value === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif ($payment->status->value === 'processing')
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ $payment->status instanceof \UnitEnum ? $payment->status->label() : $payment->status }}
                                </span>
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
							@if ($payment->receipt)
								<a href="{{ route('payments.download-receipt', $payment) }}"
										class="text-indigo-600 hover:text-indigo-900"
										target="_blank">
									{{ $payment->receipt->receipt_number }}
								</a>
							@else
								-
							@endif
						</td>
						<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
							<a href="{{ route('payments.show', $payment) }}"
									class="text-indigo-600 hover:text-indigo-900">
								{{ __('View') }}
							</a>
							@if ($isAdmin && in_array($payment->status->value, ['pending', 'processing']))
								<span class="text-gray-300 mx-1">|</span>
								<button wire:click="$emitTo('payment.status', 'showVerification', {{ $payment->id }})"
										class="text-indigo-600 hover:text-indigo-900">
									{{ __('Verify') }}
								</button>
							@endif
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="{{ $isAdmin ? 8 : 7 }}"
								class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
							{{ __('No payments found.') }}
						</td>
					</tr>
				@endforelse
				</tbody>
			</table>
		</div>
		
		<!-- Pagination -->
		<div class="px-6 py-3 border-t border-gray-200">
			{{ $payments->links() }}
		</div>
	</div>
</div>
