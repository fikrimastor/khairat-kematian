<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Member Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Personal Information') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('Your personal details are displayed below.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                                            <div class="mt-1 text-sm text-gray-900">{{ $user->name }}</div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Email address') }}</label>
                                            <div class="mt-1 text-sm text-gray-900">{{ $user->email }}</div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Phone Number') }}</label>
                                            <div class="mt-1 text-sm text-gray-900">{{ $user->phone ?? __('Not provided') }}</div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Identification Number') }}</label>
                                            <div class="mt-1 text-sm text-gray-900">{{ $user->identification_number ?? __('Not provided') }}</div>
                                        </div>

                                        <div class="col-span-6">
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Address') }}</label>
                                            <div class="mt-1 text-sm text-gray-900">{{ $user->address ?? __('Not provided') }}</div>
                                        </div>

                                        <div class="col-span-6 sm:col-span-3">
                                            <label class="block text-sm font-medium text-gray-700">{{ __('Language Preference') }}</label>
                                            <div class="mt-1 text-sm text-gray-900">
                                                {{ $user->language == 'ms' ? __('Bahasa Malaysia') : __('English') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                    <a href="{{ route('member.edit') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Edit Profile') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:block" aria-hidden="true">
                        <div class="py-5">
                            <div class="border-t border-gray-200"></div>
                        </div>
                    </div>

                    <div class="md:grid md:grid-cols-3 md:gap-6 mt-10">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Dependents') }}</h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('Family members registered under your account.') }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <div class="shadow overflow-hidden sm:rounded-md">
                                <div class="px-4 py-5 bg-white sm:p-6">
                                    @if($dependents->isEmpty())
                                        <p class="text-sm text-gray-500">{{ __('No dependents registered yet.') }}</p>
                                    @else
                                        <ul class="divide-y divide-gray-200">
                                            @foreach($dependents as $dependent)
                                                <li class="py-4">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $dependent->name }}
                                                            </p>
                                                            <p class="text-sm text-gray-500 truncate">
                                                                {{ __('Relationship') }}: {{ $dependent->relationship ?? __('Not specified') }}
                                                            </p>
                                                            @if($dependent->identification_number)
                                                                <p class="text-sm text-gray-500 truncate">
                                                                    {{ __('ID') }}: {{ $dependent->identification_number }}
                                                                </p>
                                                            @endif
                                                            @if($dependent->birth_date)
                                                                <p class="text-sm text-gray-500 truncate">
                                                                    {{ __('Birth Date') }}: {{ $dependent->birth_date->format('d/m/Y') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                    <a href="{{ route('dependent.create') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Add Dependent') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 