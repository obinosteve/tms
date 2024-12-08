<?php

use App\Models\Task;
use App\Models\User;

it('prevents a user from deleting another user\'s task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->from(route('tasks.index'))->delete(route('tasks.destroy', $task));

    $response->assertStatus(403);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});

it('prevents unauthenticated users from deleting a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create();

    $response = $this->from(route('tasks.index'))->delete(route('tasks.destroy', $task->id));

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
    ]);
});


it('allows a user to delete their task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->from(route('tasks.index'))->delete(route('tasks.destroy', $task));

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.index'));

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
    ]);
});

it('returns 404 when trying to delete a non-existent task', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('tasks.destroy', 9999));

    $response->assertStatus(404);
});
