<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receipts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(count($receipts) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="py-3 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Receipt Number') }}
                                        </th>
                                        <th class="py-3 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Member') }}
                                        </th>
                                        <th class="py-3 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Amount') }}
                                        </th>
                                        <th class="py-3 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Date') }}
                                        </th>
                                        <th class="py-3 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipts as $receipt)
                                        <tr>
                                            <td class="py-4 px-4 border-b border-gray-200 text-sm">
                                                {{ $receipt->receipt_number }}
                                            </td>
                                            <td class="py-4 px-4 border-b border-gray-200 text-sm">
                                                {{ $receipt->payment->user->name }}
                                            </td>
                                            <td class="py-4 px-4 border-b border-gray-200 text-sm">
                                                RM {{ number_format($receipt->payment->amount, 2) }}
                                            </td>
                                            <td class="py-4 px-4 border-b border-gray-200 text-sm">
                                                {{ $receipt->formattedDate }}
                                            </td>
                                            <td class="py-4 px-4 border-b border-gray-200 text-sm">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-900">
                                                        {{ __('View') }}
                                                    </a>
                                                    @if($receipt->receipt_path)
                                                        <a href="{{ route('receipts.download', $receipt) }}" class="text-green-600 hover:text-green-900">
                                                            {{ __('Download') }}
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
                            {{ $receipts->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">{{ __('No receipts found.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 