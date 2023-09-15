<?php

use Illuminate\Support\Facades\Route;

Route::get('/retrieveInstalledPackages', [\Larawatch\Http\Controllers\API\RetrieveInstalledPackagesController::class, 'retrieve'])->name('retrieveInstalledPackages');
