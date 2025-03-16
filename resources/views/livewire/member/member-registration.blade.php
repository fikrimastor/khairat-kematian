<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-2xl font-extrabold text-gray-900">
            {{ __('Register for Khairat Kematian') }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            {{ __('Please fill in your details to register') }}
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form wire:submit.prevent="register" class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        {{ __('Name') }}
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="name" id="name" type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required autofocus />
                    </div>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        {{ __('Email address') }}
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="email" id="email" type="email" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required />
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        {{ __('Password') }}
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="password" id="password" type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required />
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        {{ __('Confirm Password') }}
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="password_confirmation" id="password_confirmation" type="password" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required />
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">
                        {{ __('Address') }}
                    </label>
                    <div class="mt-1">
                        <textarea wire:model.defer="address" id="address" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                    </div>
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        {{ __('Phone Number') }}
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="phone" id="phone" type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ID Number -->
                <div>
                    <label for="identification_number" class="block text-sm font-medium text-gray-700">
                        {{ __('Identification Number') }}
                    </label>
                    <div class="mt-1">
                        <input wire:model.defer="identification_number" id="identification_number" type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    </div>
                    @error('identification_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Language Preference -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ __('Language Preference') }}
                    </label>
                    <div class="mt-1 flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.defer="language" name="language" value="ms" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" checked />
                            <span class="ml-2">{{ __('Bahasa Malaysia') }}</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" wire:model.defer="language" name="language" value="en" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" />
                            <span class="ml-2">{{ __('English') }}</span>
                        </label>
                    </div>
                    @error('language')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Register') }}
                    </button>
                </div>

                <div class="text-sm text-center">
                    <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        {{ __('Already have an account? Log in') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div> 