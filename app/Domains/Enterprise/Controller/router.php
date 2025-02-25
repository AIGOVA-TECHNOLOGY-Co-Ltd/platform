<?php declare(strict_types=1);
namespace App\Domains\Enterprise\Controller;

use Illuminate\Support\Facades\Route;
use App\Domains\Enterprise\Controller\EnterpriseController;

Route::group(['middleware' => ['user-auth']], static function () {
    Route::get('/enterprise', [EnterpriseController::class, 'index'])->name('enterprise.index');
    Route::get('/enterprise/create', [EnterpriseController::class, 'create'])->name('enterprise.create');
    // create route handle save enterprise
    Route::post('/enterprise', [EnterpriseController::class, 'store'])->name('enterprise.store');
});
