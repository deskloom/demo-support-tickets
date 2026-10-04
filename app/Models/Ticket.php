<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    public const STATUSES = ['open', 'in_progress', 'resolved'];

    protected $fillable = [
        'category_id', 'title', 'description', 'status', 'priority', 'assignee_name',
    ];

    // Mirrors the migration's column defaults so a freshly created in-memory model already
    // reflects them. Eloquent does not refresh attributes left unset after an INSERT, so
    // relying on the DB default alone left `create($request->validated())` returning a model
    // with status/priority = null even though the row itself had the correct default
    // (found while verifying the API response with the smoke test, not by checking only the DB row).
    protected $attributes = [
        'status' => 'open',
        'priority' => 'normal',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeStatus(Builder $query, mixed $status): Builder
    {
        return in_array($status, self::STATUSES, true) ? $query->where('status', $status) : $query;
    }

    public function isOpen(): bool
    {
        return $this->status !== 'resolved';
    }
}
