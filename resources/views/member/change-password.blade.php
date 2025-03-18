<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('khairat.change_password') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-5 flex justify-between items-center">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('khairat.security_settings') }}</h3>
                        <a href="{{ route('member.show') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            {{ __('khairat.back') }} →
                        </a>
                    </div>
                    
                    <form method="POST" action="{{ route('member.update-password') }}">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-6 max-w-md">
                            <!-- Current Password -->
                            <div>
                                <x-input-label for="current_password" :value="__('khairat.current_password')" />
                                <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                            </div>

                            <!-- New Password -->
                            <div>
                                <x-input-label for="password" :value="__('khairat.new_password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">{{ __('khairat.password_requirement') }}</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <x-input-label for="password_confirmation" :value="__('khairat.confirm_password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('khairat.change_password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 