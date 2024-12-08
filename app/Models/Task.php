<?php

namespace App\Models;

use App\Enums\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['formatted_due_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function formattedDueDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->due_date
                ? Carbon::createFromFormat('Y-m-d', $this->due_date)->format('m/d/Y')
                : null,
        );
    }

    public function isCompleted(): bool
    {
        return $this->status === Status::COMPLETED->value;
    }

    public function owner(): bool
    {
        return $this->user_id == auth()->id();
    }

    public function getDueDate(): string
    {
        return Carbon::createFromFormat('Y-m-d', $this->due_date)->format('d-m-Y');
    }

    public function getCompletedDate(): string
    {
        if (!$this->completed_at) {
            return 'Unavailable';
        }

        return Carbon::createFromFormat('Y-m-d H:i:s', $this->completed_at)->format('d-m-Y H:i:s');
    }

    public function assignedTo(): string
    {
        if ($this->user->id == auth()->id()) {
            return 'Me (' . $this->user->name . ')';
        }

        return $this->user->name;
    }
}
