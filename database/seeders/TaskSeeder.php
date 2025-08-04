<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::create([
            'title' => 'Project Proposal Draft',
            'description' => 'Create initial project proposal draft',
            'status' => 'pending',
            'due_date' => now()->addDays(0),
            'assigned_to' => 2, // John Doe
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'Client Meeting',
            'description' => 'Prepare for client meeting presentation',
            'status' => 'pending',
            'due_date' => now()->addDays(1),
            'assigned_to' => 3, // Sarah Smith
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'Feature Implementation',
            'description' => 'Implement new dashboard features',
            'status' => 'in_progress',
            'due_date' => now()->addDays(3),
            'assigned_to' => 4, // Mike Johnson
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'Bug Fixes',
            'description' => 'Fix reported bugs in the system',
            'status' => 'in_progress',
            'due_date' => now()->addDays(5),
            'assigned_to' => 5, // Anna Lee
            'created_by' => 1,
        ]);

        Task::create([
            'title' => 'Project Documentation',
            'description' => 'Complete project documentation',
            'status' => 'completed',
            'due_date' => now()->subDays(1),
            'assigned_to' => 2, // John Doe
            'created_by' => 1,
        ]);
    }
}