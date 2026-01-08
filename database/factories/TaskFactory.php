<?php

namespace Database\Factories;

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $allStatuses = array_column(TaskStatus::cases(), 'value');

        return [
            'project_id' => Project::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->realText(200),
            'status' => $this->faker->randomElement($allStatuses),
        ];
    }
}
