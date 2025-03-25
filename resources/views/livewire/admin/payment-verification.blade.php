<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900">{{ __('Payment Verification') }}</h1>
            <p class="mt-2 text-sm text-gray-700">{{ __('Verify or reject pending payments.') }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/3">
                    <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="{{ __('Search by name, email, ID...') }}">
                    </div>
                </div>
                
                <div class="w-full md:w-1/3">
                    <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                    <select wire:model.live="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="all">{{ __('All Statuses') }}</option>
                        @foreach($statuses as $statusOption)
                            <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Member') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Month/Year') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Method') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $payment->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    RM {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->month }} {{ $payment->year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($payment->payment_method instanceof \App\Enums\PaymentMethod)
                                        {{ $payment->payment_method->label() }}
                                    @else
                                        {{ \App\Enums\PaymentMethod::from($payment->payment_method)->label() }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if ($payment->status instanceof \App\Enums\PaymentStatus)
                                            bg-{{ $payment->status->color() }}-100 text-{{ $payment->status->color() }}-800
                                        @else
                                            bg-{{ \App\Enums\PaymentStatus::from($payment->status)->color() }}-100 text-{{ \App\Enums\PaymentStatus::from($payment->status)->color() }}-800
                                        @endif
                                    ">
                                        @if ($payment->status instanceof \App\Enums\PaymentStatus)
                                            {{ $payment->status->label() }}
                                        @else
                                            {{ \App\Enums\PaymentStatus::from($payment->status)->label() }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $payment->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="selectPayment({{ $payment->id }})" class="text-indigo-600 hover:text-indigo-900">
                                        {{ __('Verify') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ __('No payments found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    @if($showModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ __('Verify Payment') }} #{{ $selectedPayment->id }}
                                </h3>
                                
                                <div class="mt-4 bg-gray-50 p-4 rounded-md">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Member') }}</p>
                                            <p class="text-sm font-medium">{{ $selectedPayment->user->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Amount') }}</p>
                                            <p class="text-sm font-medium">RM {{ number_format($selectedPayment->amount, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Month/Year') }}</p>
                                            <p class="text-sm font-medium">{{ $selectedPayment->month }} {{ $selectedPayment->year }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Payment Method') }}</p>
                                            <p class="text-sm font-medium">
                                                @if ($selectedPayment->payment_method instanceof \App\Enums\PaymentMethod)
                                                    {{ $selectedPayment->payment_method->label() }}
                                                @else
                                                    {{ \App\Enums\PaymentMethod::from($selectedPayment->payment_method)->label() }}
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Reference No') }}</p>
                                            <p class="text-sm font-medium">{{ $selectedPayment->reference_no ?? '-' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">{{ __('Date') }}</p>
                                            <p class="text-sm font-medium">{{ $selectedPayment->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($selectedPayment->proofs->count() > 0)
                                    <div class="mt-4">
                                        <h4 class="text-md font-medium text-gray-900">{{ __('Payment Proof') }}</h4>
                                        <div class="mt-2 grid grid-cols-2 gap-4">
                                            @foreach($selectedPayment->proofs as $proof)
                                                <div class="border border-gray-200 rounded-md p-2">
                                                    <a href="{{ Storage::url($proof->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                        @if(in_array($proof->file_type, ['image/jpeg', 'image/png', 'image/gif']))
                                                            <img src="{{ Storage::url($proof->file_path) }}" alt="Payment Proof" class="w-full h-32 object-cover rounded-md">
                                                        @else
                                                            <div class="w-full h-32 flex items-center justify-center bg-gray-100 rounded-md">
                                                                <svg class="h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        <p class="mt-1 text-xs truncate">{{ $proof->file_name }}</p>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mt-4">
                                    <label for="verificationNotes" class="block text-sm font-medium text-gray-700">{{ __('Notes') }}</label>
                                    <textarea wire:model="verificationNotes" id="verificationNotes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="{{ __('Add verification notes or rejection reason...') }}"></textarea>
                                    @error('verificationNotes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="approve" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Approve') }}
                        </button>
                        <button wire:click="reject" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Reject') }}
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div> 