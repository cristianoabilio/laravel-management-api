<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp(); // Always call the parent setUp()

        // Create and set the user as the acting user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_a_task_can_be_created()
    {
        $project = Project::factory()->create();

        $data = [
            'project_id' => $project->id,
            'title' => 'Test task',
            'description' => 'A description',
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201) // Assert a 201 Created status
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('tasks', $data); // Verify the record is in the database
    }

    public function test_task_creation_requires_valid_data()
    {
        $response = $this->postJson('/api/tasks', ['title' => null]); // Invalid data

        $response->assertStatus(422) // Assert a 422 Unprocessable Entity status
                 ->assertJsonValidationErrors(['title']); // Check for specific validation errors
    }

    public function test_a_task_can_be_retrieved()
    {
        $task = Task::factory()->create(); // Create a task using a factory

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $task->id,
                'title' => $task->title,
            ]);
    }

    public function test_a_list_of_tasks_can_be_retrieved()
    {
        $project = Project::factory()->create();

        Task::factory()->count(3)->create([
            'project_id' => $project->id
        ]); // Create multiple tasks

        $response = $this->getJson('/api/tasks');

        $this->assertCount(3, $project->tasks);
        $this->assertDatabaseCount('tasks', 3);
        $this->assertDatabaseCount('projects', 1);

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data'); // Assuming API Resources wrap data in a 'data' key
    }

    public function test_a_task_can_be_updated()
    {
        $project = Project::factory()->create();

        $task = Task::factory()->create([
            'project_id' => $project->id
        ]); // Create multiple tasks

        $newData = [
            'project_id' => $project->id,
            'title' => 'Updated title',
            'status' => $task->status
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $newData);

        $response->assertStatus(201)
                 ->assertJsonFragment($newData);

        $this->assertDatabaseHas('tasks', $newData);
    }

    public function test_a_task_can_be_deleted()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(204); // Assert a 204 No Content status for successful deletion

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]); // Verify the record is gone
    }
}
