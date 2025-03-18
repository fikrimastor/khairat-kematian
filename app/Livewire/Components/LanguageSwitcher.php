<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $currentLocale;

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale)
    {
        if (!in_array($locale, ['en', 'ms'])) {
            return;
        }

        // Set session language
        Session::put('language', $locale);

        // Update user preference if logged in
        if (Auth::check()) {
            Auth::user()->update(['language' => $locale]);
        }

        // Update locale for the current request
        App::setLocale($locale);

        // Update current locale property
        $this->currentLocale = $locale;

        // Refresh the page to apply language changes
        $this->redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.components.language-switcher');
    }
}
