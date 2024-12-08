<?php

use App\Models\Task;
use App\Models\User;

it('prevents unauthenticated users from viewing a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->get(route('tasks.show', $task));

    $response->assertRedirect(route('login'));
});

it('prevents a user from viewing another user\'s task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->get(route('tasks.show', $task));

    $response->assertStatus(403);
});

it('returns 404 for a non-existent task', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tasks.show', 9999));

    $response->assertStatus(404);
});

it('can display a task successfully', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('tasks.show', $task->id));

    $response->assertStatus(200)
        ->assertViewIs('tasks.show')
        ->assertViewHas('task', function ($viewTask) use ($task) {
            return $viewTask->id === $task->id;
        });

    $response->assertSeeText(ucwords($task->title))
        ->assertSeeText($task->description)
        ->assertSeeText($task->getDueDate())
        ->assertSeeText(ucfirst($task->status));
});
