<?php

use App\Enums\Status;
use App\Models\Task;
use App\Models\User;

it('prevents unauthenticated users from changing a task status', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING->value
    ]);

    $newStatus = Status::COMPLETED->value;

    $response = $this->from(route('tasks.index'))->patch(route('tasks.status', $task), [
        'status' => $newStatus,
    ]);

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => Status::PENDING->value,
    ]);
});

it('validates the status input when changing task status', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING->value,
    ]);

    $invalidStatus = 'invalid_status';

    $response = $this->actingAs($user)->from(route('tasks.index'))->patch(route('tasks.status', $task), [
        'status' => $invalidStatus,
    ]);

    $response->assertStatus(302)
        ->assertSessionHasErrors('status');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => Status::PENDING->value,
    ]);
});



it('prevents a user from changing the status of another user\'s task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create([
        'user_id' => $otherUser->id,
        'status' => Status::PENDING->value
    ]);

    $newStatus = Status::COMPLETED->value;

    $response = $this->actingAs($user)->from(route('tasks.index'))->patch(route('tasks.status', $task), [
        'status' => $newStatus,
    ]);

    $response->assertStatus(403);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => Status::PENDING->value,
    ]);
});

it('allows a user to change the status of their task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $newStatus = Status::COMPLETED->value;

    $response = $this->actingAs($user)->from(route('tasks.index'))->patch(route('tasks.status', $task), [
        'status' => $newStatus,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.index'));
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'status' => $newStatus,
    ]);
});

it('returns 404 when trying to change the status of a non-existent task', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('tasks.status', 9999), [
        'status' => Status::COMPLETED->value,
    ]);

    $response->assertStatus(404);
});
