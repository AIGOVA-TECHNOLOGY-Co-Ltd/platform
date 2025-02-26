<?php declare(strict_types=1);

use App\Domains\Permissions\Controller\Index as PermissionIndex;
use App\Domains\Permissions\Controller\Create as PermissionCreate;
use App\Domains\Permissions\Controller\Update as PermissionUpdate;

use Illuminate\Support\Facades\Route;


Route::middleware(['user-auth'])->group(function () {
    Route::get('/permissions', PermissionIndex::class)->name('permissions.index');
    Route::any('/permissions/create', PermissionCreate::class)->name('permissions.create');

    Route::get('/permissions/role/{role_id}/edit', [PermissionUpdate::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/role/{role_id}/update', [PermissionUpdate::class, 'update'])->name('permissions.update');
});