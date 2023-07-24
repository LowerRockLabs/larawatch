<?php

use Illuminate\Support\Facades\Route;

Route::get('/retrieveInstalledPackages', [\Larawatch\Larawatch\Http\Controllers\Api\RetrieveInstalledPackagesController::class, 'retrieve'])->name('retrieveInstalledPackages');
