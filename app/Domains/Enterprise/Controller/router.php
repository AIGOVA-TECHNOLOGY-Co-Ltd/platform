<?php declare(strict_types=1);

namespace App\Domains\Enterprise\Controller;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/enterprise', [EnterpriseController::class, 'index'])->name('enterprise.index');
    Route::get('/enterprise/create', [EnterpriseController::class, 'create'])->name('enterprise.create');
    // create route handle save enterprise
    Route::post('/enterprise', [EnterpriseController::class, 'store'])->name('enterprise.store');
    // create route show page update
    Route::get('/enterprise/{id}', [EnterpriseController::class, 'show'])->name('enterprise.show');
    // create route handle update
    Route::put('/enterprise/update/{id}', [EnterpriseController::class, 'update'])->name('enterprise.update');
    // create route handle delete
    Route::delete('/enterprise/{id}', [EnterpriseController::class, 'destroy'])->name('enterprise.destroy');
});
