<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'description', 'status', 'priority', 'assignee_name',
    ];

    // Mirrors the migration's column defaults so a freshly created in-memory model already
    // reflects them. Eloquent does not refresh attributes left unset after an INSERT, so
    // relying on the DB default alone left `create($request->validated())` returning a model
    // with status/priority = null even though the row itself had the correct default
    // (caught by tests/Feature/TicketApiTest.php asserting the API response, not just the DB row).
    protected $attributes = [
        'status' => 'open',
        'priority' => 'normal',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function isOpen(): bool
    {
        return $this->status !== 'resolved';
    }
}
