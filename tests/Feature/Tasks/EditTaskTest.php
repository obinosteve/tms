<?php

use App\Enums\Status;
use App\Models\Task;
use App\Models\User;

it('fails when the user is not authenticated', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->get(route('tasks.edit', $task));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

it('shows the task edit form', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('tasks.edit', $task));

    $response->assertStatus(200);
    $response->assertViewIs('tasks.edit');
    $response->assertSeeText('Edit Task');
    $response->assertSee('title');
    $response->assertSee('description');
    $response->assertSee('dueDate');
});

it('fails when dueDate has an invalid format', function (string $dueDate) {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->from(route('tasks.edit', $task))->put(route('tasks.update', $task), [
        'title' => 'Test Title',
        'description' => 'Test description',
        'dueDate' => $dueDate,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.edit', $task));
    $response->assertSessionHasErrors('dueDate');
    $response->assertSessionHasInput('title', 'Test Title');
    $response->assertSessionHasInput('description', 'Test description');
})->with([
    '12-10-2024',
    '2024-12-10',
    '2024/10/12',
    '10/2024/12',
]);

it('prevents a user from editing another user\'s task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->put(route('tasks.update', $task), [
        'title' => 'Unauthorized Update',
        'description' => 'This should not be allowed',
        'dueDate' => '12/15/2024',
    ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
    ]);

    $response->assertStatus(403);
});

it('prevents unauthorized users from editing a task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->put(route('tasks.update', $task), [
        'title' => 'Unauthorized Update',
        'description' => 'This should not be allowed',
        'dueDate' => '12/30/2024',
    ]);

    $response->assertRedirect(route('login'));
});


it('can update a task successfully', function (string $title, string $description) {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->from(route('tasks.edit', $task))->put(route('tasks.update', $task), [
        'title' => $title,
        'description' => $description,
        'dueDate' => '12/30/2024',
    ]);

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => $title,
        'description' => $description,
        'due_date' => '2024-12-30',
        'status' => Status::PENDING->value
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('tasks.index'));
})->with([
    ['Updated Task Title', 'Updated Task Description']
]);

it('fails to update a task when title is missing', function (?string $title) {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->from(route('tasks.edit', $task))->put(route('tasks.update', $task), [
        'title' => $title,
        'description' => 'Updated Task Description',
        'dueDate' => '12/15/2024',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.edit', $task));
    $response->assertSessionHasErrors('title');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
    ]);
})->with([
    null,
    '',
]);
