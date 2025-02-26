<?php declare(strict_types=1);

use App\Domains\Role\Feature\Controller\Index as FeatureIndex;
use App\Domains\Role\Feature\Controller\Create as FeatureCreate;
use App\Domains\Role\Feature\Controller\Update as FeatureUpdate;
use App\Domains\Role\Feature\Controller\Delete as FeatureDelete;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], function () {
    Route::get('/role/feature', FeatureIndex::class)
        ->name('role.feature.index');
    // ->middleware('user.role.feature.access:role-feature');

    Route::match(['get', 'post'], '/role/feature/create', FeatureCreate::class)
        ->name('role.feature.create');

    Route::match(['get', 'patch'], '/role/feature/{id}', FeatureUpdate::class)
        ->name('role.feature.update');

    Route::delete('/role/feature/{id}', FeatureDelete::class)
        ->name('role.feature.delete');
});