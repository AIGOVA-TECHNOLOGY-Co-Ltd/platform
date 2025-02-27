<?php declare(strict_types=1);

use App\Domains\User\Role\Feature\Controller\Index as FeatureIndex;
use App\Domains\User\Role\Feature\Controller\Create as FeatureCreate;
use App\Domains\User\Role\Feature\Controller\Update as FeatureUpdate;
use App\Domains\User\Role\Feature\Controller\Delete as FeatureDelete;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], function () {
    Route::get('/user/role/feature', FeatureIndex::class)
        ->name('user.role.feature.index');
    // ->middleware('user.role.feature.access:role-feature');

    Route::match(['get', 'post'], '/user/role/feature/create', FeatureCreate::class)
        ->name('user.role.feature.create');

    Route::match(['get', 'patch'], '/user/role/feature/{id}', FeatureUpdate::class)
        ->name('user.role.feature.update');

    Route::delete('/user/role/feature/{id}', FeatureDelete::class)
        ->name('user.role.feature.delete');
});