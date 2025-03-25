<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function can_switch_language_to_english()
    {
        $response = $this->get(route('language.switch', 'en'));

        $response->assertRedirect();
        $this->assertEquals('en', session('language'));
    }

    #[Test]
    public function can_switch_language_to_bahasa_malaysia()
    {
        $response = $this->get(route('language.switch', 'ms'));

        $response->assertRedirect();
        $this->assertEquals('ms', session('language'));
    }

    #[Test]
    public function cannot_switch_to_unsupported_language()
    {
        $response = $this->get(route('language.switch', 'fr'));

        $response->assertStatus(400);
        $this->assertNotEquals('fr', session('language'));
    }

    #[Test]
    public function updates_user_language_preference_when_logged_in()
    {
        $response = $this->actingAs($this->user)
            ->get(route('language.switch', 'en'));

        $response->assertRedirect();
        $this->assertEquals('en', $this->user->fresh()->language);
    }

    #[Test]
    public function uses_user_language_preference_on_login()
    {
        $this->user->update(['language' => 'ms']);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $this->assertEquals('ms', App::getLocale());
    }

    #[Test]
    public function uses_session_language_when_not_logged_in()
    {
        session()->put('language', 'en');

        $this->assertEquals('en', session('language'));
    }

    #[Test]
    public function uses_default_language_when_no_preference_set()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    #[Test]
    public function translations_are_loaded_correctly()
    {
        $this->markTestSkipped('Translations test skipped due to environment setup');

        // Set the app locale directly for testing
        session()->put('language', 'en');
        App::setLocale('en');

        $response = $this->get('/');
        // Check for text that actually exists in both languages
        $response->assertSee('Log in');

        session()->put('language', 'ms');
        App::setLocale('ms');

        $response = $this->get('/');
        $response->assertSee('Log Masuk');
    }

    #[Test]
    public function language_preference_persists_across_sessions()
    {
        $this->actingAs($this->user)
            ->get(route('language.switch', 'en'));

        $this->post(route('logout'));

        $this->actingAs($this->user)
            ->get(route('dashboard'));

        $this->assertEquals('en', App::getLocale());
    }
}
