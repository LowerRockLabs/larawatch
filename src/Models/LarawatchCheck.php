<?php

namespace Larawatch\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class LarawatchCheck extends Model
{
    use HasFactory;
    use MassPrunable;

    public $guarded = [];

    public $casts = [
        'result_data' => 'json',
        'check_data' => 'json',
    ];


    public function prunable(): Builder
    {
        return static::where('larawatch_dispatch_status', true)->where('created_at', '<=', now()->subDays(config('larawatch.delete_log_items_older_than_days')));
    }
}
