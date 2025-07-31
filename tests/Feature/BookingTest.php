<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase {
    use RefreshDatabase;

    public function test_user_can_login() {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect('/booking');
    }
}
