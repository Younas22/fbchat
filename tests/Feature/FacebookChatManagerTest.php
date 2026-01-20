<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FacebookPage;
use App\Models\Conversation;
use App\Models\SavedChat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FacebookChatManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test User Registration
     */
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'user', 'token']);
    }

    /**
     * Test User Login
     */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'user', 'token']);
    }

    /**
     * Test Get Current User
     */
    public function test_user_can_get_profile()
    {
        $response = $this->actingAs($this->user)
                        ->getJson('/api/me');

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    /**
     * Test Get Connected Pages
     */
    public function test_user_can_get_pages()
    {
        // Create test pages
        FacebookPage::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->getJson('/api/pages');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(3, 'data');
    }

    /**
     * Test Get Page Details
     */
    public function test_user_can_get_page_details()
    {
        $page = FacebookPage::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->getJson("/api/pages/{$page->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'page_name' => $page->page_name,
                     'page_id' => $page->page_id
                 ]);
    }

    /**
     * Test Disconnect Page
     */
    public function test_user_can_disconnect_page()
    {
        $page = FacebookPage::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->deleteJson("/api/pages/{$page->id}");

        $response->assertStatus(200);
        $this->assertFalse($page->fresh()->is_active);
    }

    /**
     * Test Get Conversations
     */
    public function test_user_can_get_conversations()
    {
        $page = FacebookPage::factory()->create(['user_id' => $this->user->id]);
        Conversation::factory(5)->create([
            'user_id' => $this->user->id,
            'page_id' => $page->id
        ]);

        $response = $this->actingAs($this->user)
                        ->getJson("/api/conversations/{$page->id}");

        $response->assertStatus(200);
    }

    /**
     * Test Archive Conversation
     */
    public function test_user_can_archive_conversation()
    {
        $conversation = Conversation::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->patchJson("/api/conversations/{$conversation->id}/archive");

        $response->assertStatus(200);
        $this->assertTrue($conversation->fresh()->is_archived);
    }

    /**
     * Test Save Chat
     */
    public function test_user_can_save_chat()
    {
        $conversation = Conversation::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->postJson("/api/saved-chats/{$conversation->id}", [
                            'notes' => 'This is an important chat'
                        ]);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('saved_chats', [
            'conversation_id' => $conversation->id,
            'notes' => 'This is an important chat'
        ]);
    }

    /**
     * Test Get Saved Chats
     */
    public function test_user_can_get_saved_chats()
    {
        SavedChat::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->getJson('/api/saved-chats');

        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test Update Saved Chat
     */
    public function test_user_can_update_saved_chat()
    {
        $savedChat = SavedChat::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->patchJson("/api/saved-chats/{$savedChat->id}", [
                            'notes' => 'Updated notes'
                        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated notes', $savedChat->fresh()->notes);
    }

    /**
     * Test Delete Saved Chat
     */
    public function test_user_can_delete_saved_chat()
    {
        $savedChat = SavedChat::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
                        ->deleteJson("/api/saved-chats/{$savedChat->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('saved_chats', ['id' => $savedChat->id]);
    }

    /**
     * Test Unauthorized Access
     */
    public function test_unauthenticated_user_cannot_access_api()
    {
        $response = $this->getJson('/api/pages');

        $response->assertStatus(401);
    }

    /**
     * Test User Cannot Access Other User's Data
     */
    public function test_user_cannot_access_other_user_pages()
    {
        $otherUser = User::factory()->create();
        $page = FacebookPage::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
                        ->deleteJson("/api/pages/{$page->id}");

        $response->assertStatus(404);
    }
}