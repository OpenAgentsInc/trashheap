<?php

use App\Models\Agent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

// user can send a message in a conversation
test('user can send a message', function() {

  // create a user
  $user = User::factory()->create();
  $this->actingAs($user);

  // create an agent
  $user->agents()->create();

  // create a conversation
  $this->postJson(route('conversations.store'), [
    'agent_id' => Agent::first()->id,
  ]);

  // create a message
  $this->postJson(route('messages.store'), [
    'body' => 'Hello, world!',
    'conversation_id' => Conversation::first()->id,
  ]);

  // assert that the message exists
  $this->assertCount(1, Message::all());
});

test('body cannot be null', function() {
  // expect an exception
  $this->withoutExceptionHandling();
  $this->expectException('Illuminate\Validation\ValidationException');

  // create a user
  $user = User::factory()->create();
  $this->actingAs($user);

  // create an agent
  $user->agents()->create();

  // create a conversation
  $this->postJson(route('conversations.store'), [
    'agent_id' => Agent::first()->id,
  ]);

  // create a message
  $this->postJson(route('messages.store'), [
    // 'body' => 'Hello, world!',
    'conversation_id' => Conversation::first()->id,
  ]);

  // assert that the message exists
  $this->assertCount(0, Message::all());
});

test('conversation_id cannot be null', function() {
  // expect an exception
  $this->withoutExceptionHandling();
  $this->expectException('Illuminate\Validation\ValidationException');

  // create a user
  $user = User::factory()->create();
  $this->actingAs($user);

  // create an agent
  $user->agents()->create();

  // create a conversation
  $this->postJson(route('conversations.store'), [
    'agent_id' => Agent::first()->id,
  ]);

  // create a message
  $this->postJson(route('messages.store'), [
    'body' => 'Hello, world!',
    // 'conversation_id' => Conversation::first()->id,
  ]);

  // assert that the message exists
  $this->assertCount(0, Message::all());
});

// agent can send a message -- similar to user sends message, except no API call needed - we call a method on the model
test('agent can send a message', function() {

  // create a user
  $user = User::factory()->create();
  $this->actingAs($user);

  // create an agent
  $user->agents()->create();

  $agent = Agent::first();
  $agent->sendMessage(
    Conversation::factory()->create(['agent_id' => $agent->id])->id,
    'Hello, world!'
  );

  // assert that the message exists
  $this->assertCount(1, Message::all());
});
