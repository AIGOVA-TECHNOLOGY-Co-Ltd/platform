<?php declare(strict_types=1);

namespace App\Domains\Enterprise\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::get('/enterprise/get-enterprise', GetEnterprise::class)->name('Enterprise.GetEnterprise');