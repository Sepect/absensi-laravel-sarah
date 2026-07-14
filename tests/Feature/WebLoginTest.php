<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_on_web(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_intern_cannot_login_on_web(): void
    {
        $intern = User::factory()->intern()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $intern->email,
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $admin = User::factory()->admin()->create([
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }
}
