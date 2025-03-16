<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Maklumat Keahlian') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Status keahlian anda dalam sistem Khairat Kematian.") }}
        </p>
    </header>

    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
        <div class="max-w-xl">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Status Keahlian</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($user->isActive())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Tidak Aktif
                            </span>
                        @endif
                    </dd>
                </div>

                @if($user->membership_expires_at)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tarikh Tamat</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->membership_expires_at->format('d M Y') }}
                        @if($user->membership_expires_at->isPast())
                            <span class="text-xs text-red-600">(Tamat)</span>
                        @else
                            <span class="text-xs text-gray-500">({{ $user->membership_expires_at->diffForHumans() }})</span>
                        @endif
                    </dd>
                </div>
                @endif

                @if($user->last_payment_date)
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Bayaran Terakhir</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $user->last_payment_date->format('d M Y') }}
                    </dd>
                </div>
                @endif

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Tindakan</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if(!$user->isActive() || $user->membership_expires_at?->diffInMonths(now()) < 3)
                        <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            @if(!$user->isActive() || $user->isMembershipExpired())
                                Daftar Keahlian
                            @else
                                Pembaharuan Keahlian
                            @endif
                        </a>
                        @endif
                        <a href="{{ route('payments.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                            Lihat Sejarah Pembayaran
                        </a>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</section> 