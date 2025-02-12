<?php declare(strict_types=1);

namespace App\Domains\Report\ControllerApi;

use Illuminate\Support\Facades\Route;

Route::get('/report/get-device-video-report', GetDeviceVideoReport::class)->name('Report.GetDeviceVideoReport');