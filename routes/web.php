<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ModelController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\TrackClickController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModelController::class, 'index']);
Route::get('/category/{category}', [ModelController::class, 'index'])->name('category.show')
    ->where('category', 'text|image|video|audio|embedding|open-source');

Route::get('/api-cost/{slug}', [ModelController::class, 'show'])->name('model.show');

Route::get('/compare/{model_one}-vs-{model_two}', [CompareController::class, 'index'])
    ->name('compare.show')
    ->where(['model_one' => '[a-z0-9-]+', 'model_two' => '[a-z0-9-]+']);

Route::get('/out/{provider}', TrackClickController::class)->name('track.out');

Route::post('/api/leads', [LeadController::class, 'store'])->name('leads.store');
Route::post('/api/subscribe', [LeadController::class, 'subscribe'])->name('subscribe');

Route::get('/admin/login', [DashboardController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [DashboardController::class, 'authenticate'])->name('admin.login.post');
Route::get('/admin/logout', [DashboardController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/leads/{id}', [DashboardController::class, 'markLead'])->name('admin.leads.update');
});

Route::get('/robots.txt', function () {
    $content = view('robots-txt')->with('app_url', config('app.url'))->render();
    return response($content, 200, ['Content-Type' => 'text/plain']);
});

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/providers', [ProviderController::class, 'index'])->name('provider.index');
Route::get('/provider/{slug}', [ProviderController::class, 'show'])->name('provider.show');

Route::get('/advertise', fn () => view('advertise'))->name('advertise');
