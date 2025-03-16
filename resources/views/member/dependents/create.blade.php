<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Family Member') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('dependent.store') }}">
                        @csrf

                        <div class="grid grid-cols-6 gap-6">
                            <!-- Name -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('name') }}" required>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Relationship -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="relationship" class="block text-sm font-medium text-gray-700">{{ __('Relationship') }}</label>
                                <select name="relationship" id="relationship" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                                    <option value="">{{ __('Select Relationship') }}</option>
                                    <option value="spouse" {{ old('relationship') == 'spouse' ? 'selected' : '' }}>{{ __('Spouse') }}</option>
                                    <option value="child" {{ old('relationship') == 'child' ? 'selected' : '' }}>{{ __('Child') }}</option>
                                    <option value="parent" {{ old('relationship') == 'parent' ? 'selected' : '' }}>{{ __('Parent') }}</option>
                                    <option value="sibling" {{ old('relationship') == 'sibling' ? 'selected' : '' }}>{{ __('Sibling') }}</option>
                                    <option value="other" {{ old('relationship') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                </select>
                                @error('relationship')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Identification Number -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="identification_number" class="block text-sm font-medium text-gray-700">{{ __('Identification Number') }}</label>
                                <input type="text" name="identification_number" id="identification_number" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('identification_number') }}">
                                @error('identification_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Birth Date -->
                            <div class="col-span-6 sm:col-span-3">
                                <label for="birth_date" class="block text-sm font-medium text-gray-700">{{ __('Birth Date') }}</label>
                                <input type="date" name="birth_date" id="birth_date" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ old('birth_date') }}">
                                @error('birth_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('dependent.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 