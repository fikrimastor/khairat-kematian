<!-- Membership Information -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Maklumat Keahlian') }}</h3>
            <div class="flex space-x-2">
                @if(!$user->isActive() || $user->membership_expires_at?->diffInMonths(now()) < 3)
                <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    @if(!$user->isActive() || $user->isMembershipExpired())
                        {{ __('Daftar Keahlian') }}
                    @else
                        {{ __('Pembaharuan Keahlian') }}
                    @endif
                </a>
                @endif
                <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Lihat Sejarah Pembayaran') }}
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dl>
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 rounded-t-md">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Status Keahlian') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            @if($user->isActive())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ __('Aktif') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ __('Tidak Aktif') }}
                                </span>
                            @endif
                        </dd>
                    </div>
                    
                    @if($user->membership_expires_at)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Tarikh Tamat') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user->membership_expires_at->format('d M Y') }}
                            @if($user->membership_expires_at->isPast())
                                <span class="text-xs text-red-600">({{ __('Tamat') }})</span>
                            @else
                                <span class="text-xs text-gray-500">({{ $user->membership_expires_at->diffForHumans() }})</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div>
                <dl>
                    @if($user->last_payment_date)
                    <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 rounded-t-md">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Bayaran Terakhir') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user->last_payment_date->format('d M Y') }}
                        </dd>
                    </div>
                    @endif
                    
                    @if($user->membership_type)
                    <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                        <dt class="text-sm font-medium text-gray-500">{{ __('Jenis Keahlian') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ ucfirst($user->membership_type) }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div> 