<?php

// 2. app/Notifications/SubmissionReceived.php
// Pastikan file ini ada dan isinya seperti ini.

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReceived extends Notification
{
    use Queueable;

    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Hanya simpan ke database
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Data ini HARUS konsisten
            'task_id' => $this->task->id,
            'sender_name' => $this->task->assignedTo->name, // Nama mahasiswa yang mengumpulkan
            'message' => 'telah mengumpulkan tugas: ' . $this->task->title,
        ];
    }
}