<?php

namespace Larawatch\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Larawatch\Support\Concerns\UsesScheduleMonitoringModels;

/**
 * Larawatch\Models\MonitoredScheduledTaskLogItem
 *
 * @property int $id
 * @property int|null $monitored_scheduled_task_id
 * @property string|null $type
 * @property array|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $lrl_id
 * @property-read \Larawatch\Models\MonitoredScheduledTask|null $monitoredScheduledTask
 * @method static Builder|MonitoredScheduledTaskLogItem newModelQuery()
 * @method static Builder|MonitoredScheduledTaskLogItem newQuery()
 * @method static Builder|MonitoredScheduledTaskLogItem query()
 * @method static Builder|MonitoredScheduledTaskLogItem whereCreatedAt($value)
 * @method static Builder|MonitoredScheduledTaskLogItem whereId($value)
 * @method static Builder|MonitoredScheduledTaskLogItem whereLrlId($value)
 * @method static Builder|MonitoredScheduledTaskLogItem whereMeta($value)
 * @method static Builder|MonitoredScheduledTaskLogItem whereMonitoredScheduledTaskId($value)
 * @method static Builder|MonitoredScheduledTaskLogItem whereType($value)
 * @method static Builder|MonitoredScheduledTaskLogItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MonitoredScheduledTaskLogItem extends Model
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
