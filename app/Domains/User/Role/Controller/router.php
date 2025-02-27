<?php declare(strict_types=1);

use App\Domains\User\Role\Controller\Index as RoleIndex;
use App\Domains\User\Role\Controller\Create;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], function () {
    Route::get('/user/role', RoleIndex::class)->name('user.role.index');
    Route::get('/user/role/create', Create::class)->name('user.role.create');
    Route::post('/user/role/store', [Create::class, 'store'])->name('user.role.store');
    Route::get('/user/role/{id}/edit', [Create::class, 'edit'])->name('user.role.edit');
    Route::put('/user/role/{id}/update', [Create::class, 'update'])->name('user.role.update');
    Route::delete('/user/role/{id}', [Create::class, 'destroy'])->name('user.role.destroy');
});