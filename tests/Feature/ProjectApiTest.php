<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Database\Factories\ProjectFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectApiTest extends TestCase
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

    /**
     * A project can be created.
     */
    public function test_a_project_can_be_created()
    {
        $data = [
            'name' => 'Test Project',
            'description' => 'A description',
        ];

        $response = $this->postJson('/api/projects', $data);

        $response->assertStatus(201) // Assert a 201 Created status
                 ->assertJsonFragment($data);

        $this->assertDatabaseHas('projects', $data); // Verify the record is in the database
    }
    /**
     * Project creation requires valid data
     */
    public function test_project_creation_requires_valid_data()
    {
        $response = $this->postJson('/api/projects', ['name' => '']); // Invalid data

        $response->assertStatus(422) // Assert a 422 Unprocessable Entity status
                 ->assertJsonValidationErrors(['name']); // Check for specific validation errors
    }

    public function test_a_project_can_be_retrieved()
    {
        $project = Project::factory()->create(); // Create a project using a factory

        $response = $this->getJson('/api/projects/' . $project->id);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $project->id,
                'name' => $project->name,
            ]);
    }

    public function test_a_list_of_projects_can_be_retrieved()
    {
        Project::factory()->count(3)->create(); // Create multiple projects

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data'); // Assuming API Resources wrap data in a 'data' key
    }

    public function test_a_project_can_be_updated()
    {
        $project = Project::factory()->create();
        $newData = [
            'name' => 'Updated Name',
        ];

        $response = $this->putJson('/api/projects/' . $project->id, $newData);

        $response->assertStatus(201)
                 ->assertJsonFragment($newData);

        $this->assertDatabaseHas('projects', $newData);
    }

    public function test_a_project_can_be_deleted()
    {
        $project = Project::factory()->create();

        $response = $this->deleteJson('/api/projects/' . $project->id);

        $response->assertStatus(204); // Assert a 204 No Content status for successful deletion

        $this->assertDatabaseMissing('projects', ['id' => $project->id]); // Verify the record is gone
    }
}
