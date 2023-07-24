<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('larawatch_scheduled_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('type')->nullable();
            $table->string('cron_expression');
            $table->string('timezone')->nullable();
            $table->string('ping_url')->nullable();

            $table->dateTime('last_started_at')->nullable();
            $table->dateTime('last_finished_at')->nullable();
            $table->dateTime('last_failed_at')->nullable();
            $table->dateTime('last_skipped_at')->nullable();

            $table->dateTime('registered_on_larawatch_at')->nullable();
            $table->dateTime('last_pinged_at')->nullable();
            $table->integer('grace_time_in_minutes');

            $table->timestamps();
        });


        Schema::create('larawatch_scheduled_task_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('larawatch_scheduled_task_id');
            $table
                ->foreign('larawatch_scheduled_task_id', 'fk_scheduled_task_id')
                ->references('id')
                ->on('larawatch_scheduled_tasks')
                ->cascadeOnDelete();

            $table->string('type');

            $table->json('meta')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('larawatch_scheduled_tasks');
        Schema::dropIfExists('larawatch_scheduled_task_items');

    }
};
