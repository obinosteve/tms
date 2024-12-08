<?php

use App\Enums\Status;
use App\Models\Task;
use App\Models\User;

it('cannot render task list screen for unauthenticated user', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertStatus(302)
        ->assertRedirect(route('login'));
});

it('can render task list screen for authenticated user', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('tasks.index'));

    $response->assertStatus(200)
        ->assertViewIs('tasks.index')
        ->assertSeeText('Task List');
});

it('can display 25 tasks on the first page', function () {
    $user = User::factory()->create();
    Task::factory()->count(40)->create();

    $response = $this->actingAs($user)->get(route('tasks.index', ['page' => 1]));

    // Assert that the response is successful and the correct view is rendered
    $response->assertStatus(200)
        ->assertViewIs('tasks.index')
        ->assertViewHas('tasks')
        ->assertSee('Next');

    // Assert that exactly 25 tasks are shown on the first page
    $tasks = $response->viewData('tasks');
    $this->assertCount(25, $tasks);

    // Assert that the correct tasks appear (first task in the list)
    $firstTask = Task::orderBy('created_at', 'asc')->first();
    $response->assertSee(e($firstTask->name));
});

it('can display the second page with the next set of tasks', function () {
    $user = User::factory()->create();

    Task::factory()->count(40)->create();

    $response = $this->actingAs($user)->get(route('tasks.index', ['page' => 2]));

    $response->assertStatus(200)
        ->assertViewIs('tasks.index')
        ->assertViewHas('tasks')
        ->assertSee('Previous');

    $tasks = $response->viewData('tasks');
    $this->assertCount(15, $tasks);

    $taskOnSecondPage = Task::orderBy('created_at', 'asc')->skip(25)->first();
    $response->assertSee(e($taskOnSecondPage->name));
});

it('can only see their own tasks', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    Task::factory()->count(5)->create(['user_id' => $user1->id]);
    Task::factory()->count(3)->create(['user_id' => $user2->id]);

    $response = $this->actingAs($user1)->get(route('tasks.index'));

    $response->assertStatus(200)
        ->assertViewIs('tasks.index')
        ->assertViewHas('tasks');

    // Assert that only tasks for user1 are visible
    $tasks = $response->viewData('tasks');
    foreach ($tasks as $task) {
        expect($task->user_id)->toBe($user1->id);
    }

    $user2Tasks = Task::where('user_id', $user2->id)->get();
    foreach ($user2Tasks as $task) {
        $response->assertDontSee(e($task->title));
    }
});

it('can filter tasks by status and dueDate', function () {
    $user = User::factory()->create();

    $pendingTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING,
        'due_date' => '2024-12-10',
    ]);

    $completedTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::COMPLETED,
        'due_date' => '2024-12-15',
    ]);

    // Filtering by status 'pending' and dueDate '12/10/2024'
    $response = $this->actingAs($user)->get(route('tasks.index', [
        'status' => Status::PENDING,
        'dueDate' => '12/10/2024',
    ]));

    $response->assertStatus(200)
        ->assertSeeText($pendingTask->title)
        ->assertDontSeeText($completedTask->title);
});

it('can filter tasks by status only', function () {
    $user = User::factory()->create();

    $pendingTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING,
        'due_date' => '2024-12-10',
    ]);

    $completedTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::COMPLETED,
        'due_date' => '2024-12-15',
    ]);

    // Filtering by status 'pending' only
    $response = $this->actingAs($user)->get(route('tasks.index', [
        'status' => Status::PENDING,
    ]));

    $response->assertStatus(200)
        ->assertSeeText($pendingTask->title)
        ->assertDontSeeText($completedTask->title);
});


it('can filter tasks by dueDate only', function () {
    $user = User::factory()->create();

    $pendingTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING,
        'due_date' => '2024-12-10',
    ]);

    $completedTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::COMPLETED,
        'due_date' => '2024-12-15',
    ]);

    // Filtering by dueDate '12/15/2024' only
    $response = $this->actingAs($user)->get(route('tasks.index', [
        'dueDate' => '12/15/2024',
    ]));

    $response->assertStatus(200)
        ->assertSeeText($completedTask->title)
        ->assertDontSeeText($pendingTask->title);
});

it('can filter tasks by status and dueDate again', function () {
    $user = User::factory()->create();

    $pendingTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::PENDING,
        'due_date' => '2024-12-10',
    ]);

    $completedTask = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Status::COMPLETED,
        'due_date' => '2024-12-15',
    ]);

    // Filtering by status 'completed' and dueDate '12/15/2024'
    $response = $this->actingAs($user)->get(route('tasks.index', [
        'status' => Status::COMPLETED,
        'dueDate' => '12/15/2024',
    ]));

    $response->assertStatus(200)
        ->assertSeeText($completedTask->title)
        ->assertDontSeeText($pendingTask->title);
});
