<?php

use Illuminate\Support\Facades\Route;
use Larawatch\Http\Controllers\API\{LarawatchRunChecks,RetrieveInstalledPackagesController};

Route::prefix(config('larawatch.routes.route_prefix', 'larawatch'))->name(config('larawatch.routes.route_name_prefix', 'larawatch.'))->group(function () {
    Route::get('/retrieveInstalledPackages', [RetrieveInstalledPackagesController::class, 'retrieve'])->name('retrieveInstalledPackages');
    Route::get('/runchecks', [LarawatchRunChecks::class, 'verify'])->name('runchecks');
});

