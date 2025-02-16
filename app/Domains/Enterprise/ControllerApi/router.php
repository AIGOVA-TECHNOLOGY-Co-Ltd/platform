<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::middleware(['user.auth.api', 'check.permission'])->group(function () {
    Route::get('/enterprise', GetEnterprise::class)->name('Enterprise.GetEnterprise');
    Route::post('/enterprise/create', CreateEnterprise::class)->name('Enterprise.CreateEnterprise');
    Route::patch('/enterprise/update', UpdateEnterprise::class)->name('Enterprise.UpdateEnterprise');
    Route::delete('/enterprise/delete', DeleteEnterprise::class)->name('Enterprise.DeleteEnterprise');
});
