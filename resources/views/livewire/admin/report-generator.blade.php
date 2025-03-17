<div>
    <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Generate Reports') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- Report Type Selection -->
            <div>
                <x-input-label for="reportType" :value="__('Report Type')" />
                <select id="reportType" wire:model.live="reportType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="payment-summary">{{ __('Payment Summary') }}</option>
                    <option value="member-statistics">{{ __('Member Statistics') }}</option>
                    <option value="payment-history">{{ __('Payment History') }}</option>
                </select>
            </div>
            
            <!-- Date Range Selection -->
            <div>
                <x-input-label for="dateRange" :value="__('Date Range')" />
                <select id="dateRange" wire:model.live="dateRange" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="today">{{ __('Today') }}</option>
                    <option value="this-week">{{ __('This Week') }}</option>
                    <option value="this-month">{{ __('This Month') }}</option>
                    <option value="this-year">{{ __('This Year') }}</option>
                    <option value="last-month">{{ __('Last Month') }}</option>
                    <option value="custom">{{ __('Custom Range') }}</option>
                </select>
            </div>
            
            <!-- Export Format -->
            <div>
                <x-input-label for="exportFormat" :value="__('Export Format')" />
                <select id="exportFormat" wire:model="exportFormat" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
        </div>
        
        <!-- Custom Date Range -->
        @if ($dateRange === 'custom')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <x-input-label for="startDate" :value="__('Start Date')" />
                <x-text-input id="startDate" type="date" wire:model.live="startDate" class="mt-1 block w-full" />
            </div>
            <div>
                <x-input-label for="endDate" :value="__('End Date')" />
                <x-text-input id="endDate" type="date" wire:model.live="endDate" class="mt-1 block w-full" />
            </div>
        </div>
        @endif
        
        <!-- Payment History Filters -->
        @if ($reportType === 'payment-history')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <x-input-label for="status" :value="__('Payment Status')" />
                <select id="status" wire:model.live="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach ($paymentStatuses as $paymentStatus)
                        <option value="{{ $paymentStatus->value }}">{{ __($paymentStatus->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="paymentType" :value="__('Payment Type')" />
                <select id="paymentType" wire:model.live="paymentType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">{{ __('All Types') }}</option>
                    @foreach ($paymentTypes as $type)
                        <option value="{{ $type->value }}">{{ __($type->name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        
        <div class="flex items-center gap-4">
            <x-primary-button wire:click="generateReport" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="generateReport">{{ __('Generate Report') }}</span>
                <span wire:loading wire:target="generateReport">{{ __('Generating...') }}</span>
            </x-primary-button>
            
            <x-secondary-button wire:click="exportReport" wire:loading.attr="disabled" class="ml-3">
                <span wire:loading.remove wire:target="exportReport">{{ __('Export') }}</span>
                <span wire:loading wire:target="exportReport">{{ __('Exporting...') }}</span>
            </x-secondary-button>
        </div>
        
        @if ($generatedFilePath)
        <div class="mt-4 p-4 bg-green-50 text-green-700 rounded-md">
            {{ __('Report generated successfully!') }} 
            <a href="{{ str_replace(storage_path('app/public'), '/storage', $generatedFilePath) }}" 
               download="{{ $generatedFileName }}" 
               class="underline font-medium">
                {{ __('Download') }} {{ $generatedFileName }}
            </a>
        </div>
        @endif
    </div>
    
    <!-- Report Preview -->
    @if (!$reportData->isEmpty())
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('Report Preview') }}</h2>
        
        @if ($reportType === 'payment-summary')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payment Type') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Payments') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($reportData as $row)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ __($row->payment_type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ __($row->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->total_payments }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">RM {{ number_format($row->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif ($reportType === 'member-statistics')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($reportData as $key => $value)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 uppercase">{{ __(str_replace('_', ' ', $key)) }}</h3>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>
                </div>
                @endforeach
            </div>
        @elseif ($reportType === 'payment-history')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Member') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payment Method') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Payment Type') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($reportData as $payment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">RM {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ __($payment->payment_method) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ __($payment->payment_type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $payment->status === 'verified' ? 'bg-green-100 text-green-800' : 
                                       ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ __($payment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('reportGenerated', (data) => {
                // You can add additional client-side functionality here if needed
                console.log('Report generated:', data);
            });
        });
    </script>
</div> 