<?php

namespace App\Livewire;

use App\Enums\NotificationType;
use App\Models\NotificationPreference;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationPreferences extends Component
{
    public $preferences = [];

    public $isSaving = false;

    public $saveSuccess = false;

    public function mount()
    {
        $this->loadPreferences();
    }

    public function loadPreferences()
    {
        $user = Auth::user();
        $this->preferences = [];

        foreach (NotificationType::cases() as $type) {
            $preference = NotificationPreference::getPreference($user->id, $type->value);

            $this->preferences[$type->value] = [
                'type' => $type->value,
                'display_name' => $type->translatedName(),
                'description' => $type->translatedDescription(),
                'email' => $preference->email,
                'in_app' => $preference->in_app,
                'sms' => $preference->sms,
            ];
        }
    }

    public function savePreferences()
    {
        $this->isSaving = true;
        $user = Auth::user();

        foreach ($this->preferences as $type => $settings) {
            NotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'notification_type' => $type,
                ],
                [
                    'email' => $settings['email'],
                    'in_app' => $settings['in_app'],
                    'sms' => $settings['sms'],
                ]
            );
        }

        $this->saveSuccess = true;
        $this->isSaving = false;

        $this->dispatch('preferences-saved');
    }

    public function render()
    {
        return view('livewire.notification-preferences');
    }
}
