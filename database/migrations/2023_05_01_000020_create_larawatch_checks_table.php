<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('larawatch_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('check_name');
            $table->json('result_data')->nullable();
            $table->text('result_message')->nullable();
            $table->string('result_status')->nullable();
            $table->json('check_data')->nullable();
            $table->dateTime('started_at')->default(now())->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->boolean('larawatch_dispatch_status')->default(false);
            $table->text('larawatch_dispatch_message')->nullable();
            $table->dateTime('larawatch_dispatch_first_sent')->nullable();
            $table->dateTime('larawatch_dispatch_last_sent')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('larawatch_checks');
    }
};
