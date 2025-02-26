<?php declare(strict_types=1);

use App\Domains\Role\Feature\Controller\Index as FeatureIndex;
use App\Domains\Role\Feature\Controller\Create as FeatureCreate;
use App\Domains\Role\Feature\Controller\Update as FeatureUpdate;

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['user-auth']], function () {
    Route::get('/role/feature', FeatureIndex::class)
        ->name('role.feature.index');
    // ->middleware('user.role.feature.access:role-feature'); // Nếu cần middleware riêng

    Route::match(['get', 'post'], '/role/feature/create', FeatureCreate::class)
        ->name('role.feature.create');

    Route::match(['get', 'patch'], '/role/feature/{id}', FeatureUpdate::class)
        ->name('role.feature.update');
});