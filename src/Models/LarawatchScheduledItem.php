<?php

namespace Larawatch\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class LarawatchScheduledItem extends Model
{
    use HasFactory;
    use MassPrunable;

    public $guarded = [];

    public const TYPE_STARTING = 'starting';

    public const TYPE_FINISHED = 'finished';

    public const TYPE_FAILED = 'failed';

    public const TYPE_SKIPPED = 'skipped';

    public $casts = [
        'metadata' => 'json',
    ];

    public function updateMetadata(array $values): self
    {
        $this->update(['metadata' => $values]);

        return $this;
    }

    public function prunable(): Builder
    {
        return static::where('larawatch_dispatch_status', true)->where('created_at', '<=', now()->subDays(config('larawatch.delete_log_items_older_than_days')));
    }
}
