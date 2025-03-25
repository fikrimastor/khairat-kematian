<div class="relative inline-block text-left">
    <div>
        <button type="button" class="inline-flex justify-center items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="language-menu-button" aria-expanded="true" aria-haspopup="true" x-data="{}" x-on:click="$refs.dropdown.classList.toggle('hidden')">
            {{ $currentLocale === 'ms' ? __('khairat.malay_language') : __('khairat.english_language') }}
            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div x-ref="dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1">
        <div class="py-1" role="none">
            <button wire:click="switchLanguage('ms')" class="w-full text-left px-4 py-2 text-sm {{ $currentLocale === 'ms' ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" role="menuitem" tabindex="-1">
                {{ __('khairat.malay_language') }}
            </button>
            <button wire:click="switchLanguage('en')" class="w-full text-left px-4 py-2 text-sm {{ $currentLocale === 'en' ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}" role="menuitem" tabindex="-1">
                {{ __('khairat.english_language') }}
            </button>
        </div>
    </div>
</div> 