<?php

use App\Enums\Status;
use App\Models\User;

it('fails when the user is not authenticated', function () {
    $response = $this->post(route('tasks.store'), [
        'title' => 'Test Task',
        'description' => 'Test task description',
        'dueDate' => '12/10/2024',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});



it('shows the task creation form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tasks.create'));

    $response->assertStatus(200);
    $response->assertViewIs('tasks.create');
    $response->assertSeeText('Add New Task');
    $response->assertSee('title');
    $response->assertSee('description');
    $response->assertSee('dueDate');
});


it('fails when title is missing', function (?string $title) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
        'title' => $title,
        'description' => 'Test description',
        'dueDate' => '12/30/2024',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.create'));
    $response->assertSessionHasErrors('title');
    $response->assertSessionHasInput('description', 'Test description');
    $response->assertSessionHasInput('dueDate', '12/30/2024');
})->with([
    null,
    '',
]);

it('fails when dueDate is missing', function (?string $dueDate) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
        'title' => 'Test Title',
        'description' => 'Test description',
        'dueDate' => $dueDate,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.create'));
    $response->assertSessionHasErrors('dueDate');
    $response->assertSessionHasInput('title', 'Test Title');
    $response->assertSessionHasInput('description', 'Test description');
})->with([
    null,
    '',
]);

it('fails when dueDate has an invalid format', function (string $dueDate) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
        'title' => 'Test Title',
        'description' => 'Test description',
        'dueDate' => $dueDate,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.create'));
    $response->assertSessionHasErrors('dueDate');
    $response->assertSessionHasInput('title', 'Test Title');
    $response->assertSessionHasInput('description', 'Test description');
})->with([
    '12-10-2024',
    '2024-12-10',
    '2024/10/12',
    '10/2024/12',
]);

it('fails when dueDate is before today', function (string $dueDate) {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
        'title' => 'Test Title',
        'description' => 'Test description',
        'dueDate' => $dueDate,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('tasks.create'));
    $response->assertSessionHasErrors('dueDate');
    $response->assertSessionHasInput('title', 'Test Title');
    $response->assertSessionHasInput('description', 'Test description');
})->with([
    '12/01/2023',
    '12/01/2024',
]);

it('can create a task', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('tasks.create'))->post(route('tasks.store'), [
        'title' => 'Test Task',
        'description' => 'Test task description',
        'dueDate' => '12/30/2024',
    ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'description' => 'Test task description',
        'due_date' => '2024-12-30',
        'user_id' => $user->id,
        'status' => Status::PENDING,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('tasks.create'));
});
