<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
        'created_by',
        'submission_file_path', // 1. Tambahkan ini
        'submitted_at',         // 2. Tambahkan ini
    ];

    /**
     * Mendefinisikan relasi "belongsTo" dengan User (yang ditugaskan).
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Mendefinisikan relasi "belongsTo" dengan User (yang membuat tugas).
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}