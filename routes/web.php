<?php

use Illuminate\Support\Facades\Route;
use Larawatch\Http\Controllers\API\{LarawatchRunChecks,LarawatchCheckResultsController, LarawatchCheckResultsController2, RetrieveInstalledPackagesController};

Route::prefix(config('larawatch.routes.route_prefix', 'larawatch'))->name(config('larawatch.routes.route_name_prefix', 'larawatch.'))->group(function () {
    Route::get('/retrieveInstalledPackages', [RetrieveInstalledPackagesController::class, 'retrieve'])->name('retrieveInstalledPackages');
    Route::get('/runchecks', [LarawatchRunChecks::class, 'verify'])->name('runchecks');

    Route::get('/getlocalresults', [LarawatchCheckResultsController::class, 'report'])->name('listruns');
    Route::get('/getunsubmittedruns', [LarawatchCheckResultsController::class, 'listruns'])->name('listruns');
    Route::get('/getrunbyid/{runID}', [LarawatchCheckResultsController::class, 'getRunByID'])->name('getRunByID');
    Route::get('/testEncrypt', [LarawatchCheckResultsController::class, 'testEncrypt'])->name('testEncrypt');
    Route::get('/testDecrypt', [LarawatchCheckResultsController::class, 'testDecrypt'])->name('testDecrypt');
    Route::get('/testHandshake', [LarawatchCheckResultsController2::class, 'testHandshake'])->name('testHandshake');

    
    
});

