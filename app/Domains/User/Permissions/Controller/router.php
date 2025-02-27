<?php declare(strict_types=1);

use App\Domains\User\Permissions\Controller\Index as PermissionIndex;
use App\Domains\User\Permissions\Controller\Create as PermissionCreate;
use App\Domains\User\Permissions\Controller\Update as PermissionUpdate;
use App\Domains\User\Permissions\Action\Delete as PermissionDelete;


use Illuminate\Support\Facades\Route;


Route::middleware(['user-auth'])->group(function () {
    Route::get('/user/permissions', PermissionIndex::class)->name('user.permissions.index');
    Route::any('/user/permissions/create', PermissionCreate::class)->name('user.permissions.create');

    Route::get('/user/permissions/role/{role_id}/edit', [PermissionUpdate::class, 'edit'])->name('user.permissions.edit');
    Route::put('/user/permissions/role/{role_id}/update', [PermissionUpdate::class, 'update'])->name('user.permissions.update');
    Route::delete('/user/permissions/role/{role_id}', PermissionDelete::class)->name('user.permissions.delete');
});