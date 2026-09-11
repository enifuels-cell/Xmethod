<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\SupportCase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    private array $browserHeaders = [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/140.0 Safari/537.36',
    ];

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->withInvitation()->get('/');

        $response->assertStatus(200)
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet')
            ->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_contact_submission_stores_data_and_redirects_to_password_verification(): void
    {
        $response = $this->withInvitation()->post('/contact', [
            'email' => 'user@example.com',
            'username' => '@example_user',
            'message' => 'I need help with my account.',
        ]);

        $response->assertRedirect('/verify-password');
        $this->assertSame('user@example.com', session('contact.email'));
        $this->assertDatabaseHas('support_cases', [
            'email' => 'user@example.com',
            'username' => '@example_user',
            'status' => 'received',
        ]);
    }

    public function test_contact_submission_requires_valid_input(): void
    {
        $response = $this->withInvitation()->from('/contact')->post('/contact', [
            'email' => 'not-an-email',
            'username' => '',
            'message' => '',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors(['email', 'username', 'message']);
    }

    public function test_known_crawlers_are_rejected(): void
    {
        $response = $this->withInvitation()->withHeaders([
            'User-Agent' => 'Mozilla/5.0 compatible; ExampleBot/1.0',
        ])->get('/');

        $response->assertForbidden();
    }

    public function test_robots_file_disallows_crawling(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk()
            ->assertSeeText("User-agent: *\nDisallow: /");
    }

    public function test_direct_access_without_an_invitation_is_forbidden(): void
    {
        $response = $this->withHeaders($this->browserHeaders)->get('/');

        $response->assertForbidden();
    }

    public function test_signed_invitation_grants_access(): void
    {
        $url = URL::temporarySignedRoute(
            'invite.accept',
            now()->addHour(),
            ['token' => 'test-invitation']
        );

        $response = $this->withHeaders($this->browserHeaders)->get($url);

        $response->assertRedirect('/');
        $this->withHeaders($this->browserHeaders)->get('/')->assertOk();
    }

    public function test_expired_invitation_does_not_grant_access(): void
    {
        $url = URL::temporarySignedRoute(
            'invite.accept',
            now()->subMinute(),
            ['token' => 'expired-invitation']
        );

        $this->withHeaders($this->browserHeaders)->get($url)->assertForbidden();
    }

    public function test_admin_dashboard_requires_admin_authentication(): void
    {
        $this->withHeaders($this->browserHeaders)
            ->get('/admin')
            ->assertRedirect('/admin/login');
    }

    public function test_admin_can_generate_an_invitation_link(): void
    {
        $this->withHeaders($this->browserHeaders)
            ->post('/admin/login', ['access_key' => config('app.admin_access_key')])
            ->assertRedirect('/admin');

        $response = $this->withHeaders($this->browserHeaders)
            ->post('/admin/invites', ['hours' => 24]);

        $response->assertOk()
            ->assertSee('Invitation link generated')
            ->assertSee('/invite/');
    }

    public function test_admin_login_rejects_an_invalid_key(): void
    {
        $this->withHeaders($this->browserHeaders)
            ->from('/admin/login')
            ->post('/admin/login', ['access_key' => 'wrong-key'])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('access_key');
    }

    public function test_verified_user_and_admin_can_exchange_messages(): void
    {
        $supportCase = SupportCase::create([
            'email' => 'user@example.com',
            'username' => '@example_user',
            'message' => 'Please help.',
            'status' => 'verified',
        ]);

        $this->withInvitation()
            ->withSession([
                'support_case_id' => $supportCase->id,
                'verification_complete' => true,
            ])
            ->post('/messages/typing')
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->withInvitation()
            ->withSession([
                'support_case_id' => $supportCase->id,
                'verification_complete' => true,
            ])
            ->post('/messages', ['body' => 'Hello support'])
            ->assertRedirect('/messages');

        $this->assertDatabaseHas('support_messages', [
            'support_case_id' => $supportCase->id,
            'sender' => 'user',
            'body' => 'Hello support',
        ]);

        $this->withInvitation()
            ->withSession([
                'support_case_id' => $supportCase->id,
                'verification_complete' => true,
            ])
            ->get('/messages/poll')
            ->assertOk()
            ->assertJsonFragment(['body' => 'Hello support']);

        $this->withHeaders($this->browserHeaders)
            ->post('/admin/login', ['access_key' => config('app.admin_access_key')]);

        $this->withHeaders($this->browserHeaders)
            ->withSession(['admin_authenticated' => true])
            ->get('/admin/cases/'.$supportCase->id.'/typing')
            ->assertOk()
            ->assertJson(['typing' => true]);

        $this->withHeaders($this->browserHeaders)
            ->withSession(['admin_authenticated' => true])
            ->post('/admin/cases/'.$supportCase->id.'/typing')
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->withInvitation()
            ->withSession([
                'support_case_id' => $supportCase->id,
                'verification_complete' => true,
            ])
            ->get('/messages/typing')
            ->assertOk()
            ->assertJson(['typing' => true]);

        $this->withHeaders($this->browserHeaders)
            ->post('/admin/cases/'.$supportCase->id.'/messages', ['body' => 'How can we help?'])
            ->assertRedirect('/admin/cases/'.$supportCase->id);

        $this->assertDatabaseHas('support_messages', [
            'support_case_id' => $supportCase->id,
            'sender' => 'admin',
            'body' => 'How can we help?',
        ]);

        $this->withHeaders($this->browserHeaders)
            ->withSession(['admin_authenticated' => true])
            ->get('/admin/cases/'.$supportCase->id.'/messages')
            ->assertOk()
            ->assertJsonFragment(['body' => 'How can we help?']);
    }

    public function test_admin_can_open_a_clickable_user_record(): void
    {
        $supportCase = SupportCase::create([
            'email' => 'details@example.com',
            'username' => '@details_user',
            'message' => 'Full case details.',
        ]);

        $this->withHeaders($this->browserHeaders)
            ->withSession(['admin_authenticated' => true])
            ->get('/admin/cases/'.$supportCase->id)
            ->assertOk()
            ->assertSee('details@example.com')
            ->assertSee('Full case details.')
            ->assertSee('User details');
    }

    public function test_admin_can_end_a_user_session_and_revoke_access(): void
    {
        $supportCase = SupportCase::create([
            'email' => 'revoked@example.com',
            'username' => '@revoked_user',
            'message' => 'End this session.',
            'status' => 'verified',
        ]);
        $supportCase->messages()->create([
            'sender' => 'user',
            'body' => 'Remove this message too.',
        ]);

        $this->withHeaders($this->browserHeaders)
            ->withSession(['admin_authenticated' => true])
            ->post('/admin/cases/'.$supportCase->id.'/end-session')
            ->assertRedirect('/admin');

        $this->assertDatabaseMissing('support_cases', ['id' => $supportCase->id]);
        $this->assertDatabaseMissing('support_messages', [
            'support_case_id' => $supportCase->id,
        ]);

        $this->withInvitation()
            ->withSession(['support_case_id' => $supportCase->id])
            ->get('/messages/poll')
            ->assertForbidden();
    }

    private function withInvitation(): static
    {
        return $this->withHeaders($this->browserHeaders)
            ->withSession(['invitation_accepted' => true]);
    }
        }
