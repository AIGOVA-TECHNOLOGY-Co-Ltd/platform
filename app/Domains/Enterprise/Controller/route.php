<?php

namespace App\Domains\Enterprise\Controller;


use App\Providers\Route;

Route::group(['middleware' => ['user-auth-admin-mode']], static function () {
    Route::get('/enterprise', Index::class)->name('enterprise.index');
});
