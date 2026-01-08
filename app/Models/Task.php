<?php

namespace App\Models;

use App\Enum\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    protected $fillable = [
        'title',
        'project_id',
        'description',
        'status',
        'due_date'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
