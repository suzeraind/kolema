<?php

use App\Events\MessageSentEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;

test('index page loads and displays messages', function () {
    $user = User::factory()->create();
    $messages = Message::factory()->count(3)->create();

    actingAs($user)
        ->get(route('websocket.demo'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('WebSocketDemo')
            ->has('initialMessages', 3)
        );
});

test('sendMessage creates message and broadcasts event', function () {
    Event::fake();

    $user = User::factory()->create();

    actingAs($user)
        ->post(route('api.websocket.send'), [
            'message' => 'Test message',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'message' => 'Test message',
        'user_id' => $user->id,
    ]);

    Event::assertDispatched(MessageSentEvent::class);
});

test('sendMessage validates required message', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('api.websocket.send'), [
            'message' => '',
        ])
        ->assertSessionHasErrors('message');
});

test('sendMessage validates message max length', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('api.websocket.send'), [
            'message' => str_repeat('a', 501),
        ])
        ->assertSessionHasErrors('message');
});

test('user can delete their own message', function () {
    Event::fake();

    $user = User::factory()->create();
    $message = Message::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('api.websocket.delete', $message))
        ->assertRedirect();

    $this->assertDatabaseMissing('messages', [
        'id' => $message->id,
    ]);

    Event::assertDispatched(\App\Events\MessageDeletedEvent::class);
});

test('user cannot delete another users message', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $message = Message::factory()->create(['user_id' => $otherUser->id]);

    actingAs($user)
        ->delete(route('api.websocket.delete', $message))
        ->assertForbidden();

    $this->assertDatabaseHas('messages', [
        'id' => $message->id,
    ]);
});

test('index page includes currentUser data', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('websocket.demo'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('WebSocketDemo')
            ->has('currentUser')
            ->where('currentUser.id', $user->id)
            ->where('currentUser.name', $user->name)
        );
});
