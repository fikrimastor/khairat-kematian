<x-app-layout>
	<x-slot name="header">
		<div class="flex justify-between items-center">
			<h2 class="font-semibold text-xl text-gray-800 leading-tight">
				{{ __('Payment Details') }}
			</h2>
			<a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
				{{ __('Back to Payments') }}
			</a>
		</div>
	</x-slot>
	
	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if (session('success'))
				<div class="mb-4 rounded-md bg-green-50 p-4">
					<div class="flex">
						<div class="flex-shrink-0">
							<svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
							</svg>
						</div>
						<div class="ml-3">
							<p class="text-sm font-medium text-green-800">
								{{ session('success') }}
							</p>
						</div>
					</div>
				</div>
			@endif
			
			@if (session('error'))
				<div class="mb-4 rounded-md bg-red-50 p-4">
					<div class="flex">
						<div class="flex-shrink-0">
							<svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
								<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
							</svg>
						</div>
						<div class="ml-3">
							<p class="text-sm font-medium text-red-800">
								{{ session('error') }}
							</p>
						</div>
					</div>
				</div>
			@endif
			
			<livewire:payment.payment-status :payment="$payment" />
			
			@if(auth()->user()->hasPermissionTo('payment.verify') && in_array($payment->status->value, ['pending', 'processing']))
				<div class="mt-6">
					<livewire:payment.payment-verification :paymentId="$payment->id" />
				</div>
			@endif
		</div>
	</div>
	
	@push('scripts')
		<script>
            window.addEventListener('show-success', event => {
                alert(event.detail.message);
            })

            window.addEventListener('show-error', event => {
                alert(event.detail.message);
            })
		</script>
	@endpush
</x-app-layout>