<?php

namespace Larawatch\Larawatch\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Larawatch\Larawatch\Support\Concerns\UsesScheduleMonitoringModels;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem as SpatieMonitoredScheduledTaskLogItem;

class MonitoredScheduledTaskLogItem extends SpatieMonitoredScheduledTaskLogItem
{
    use UsesScheduleMonitoringModels;
    use HasFactory;
    use MassPrunable;

    public $guarded = [];

    public const TYPE_STARTING = 'starting';

    public const TYPE_FINISHED = 'finished';

    public const TYPE_FAILED = 'failed';

    public const TYPE_SKIPPED = 'skipped';

    public $casts = [
        'meta' => 'array',
    ];

    public function monitoredScheduledTask(): BelongsTo
    {
        return $this->belongsTo($this->getMonitoredScheduleTaskModel(), 'monitored_scheduled_task_id');
    }

    public function updateMeta(array $values): self
    {
        $this->update(['meta' => $values]);

        return $this;
    }

    public function prunable(): Builder
    {
        $days = config('larawatch.delete_log_items_older_than_days');

        return static::where('created_at', '<=', now()->subDays($days));
    }
}
