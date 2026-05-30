<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\SmtpController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KanbanController;

// Auth Routes
Route::post('/signin', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // clients resource
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
    Route::post('/clients/bulk-destroy', [ClientController::class, 'bulkDestroy'])->name('clients.bulk-destroy');
    Route::resource('clients', ClientController::class);

    // Client board
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');
    Route::post('/kanban/update-status', [KanbanController::class, 'updateStatus'])->name('kanban.update-status');
    Route::patch('/kanban/clients/{client}/quick-update', [KanbanController::class, 'quickUpdate'])->name('kanban.quick-update');

    // templates resource
    Route::resource('templates', App\Http\Controllers\TemplateController::class);

    // billing resource
    Route::get('/billing/export', [BillingController::class, 'export'])->name('billing.export');
    Route::post('/billing/bulk-destroy', [BillingController::class, 'bulkDestroy'])->name('billing.bulk-destroy');
    Route::resource('billing', BillingController::class);

    // smtp settings
    Route::get('/smtp-settings', [SmtpController::class, 'index'])->name('smtp.index');
    Route::post('/smtp-settings', [SmtpController::class, 'update'])->name('smtp.update');

    // campaigns
    Route::get('/campaigns/export', [CampaignController::class, 'export'])->name('campaigns.export');
    Route::post('/campaigns/bulk-destroy', [CampaignController::class, 'bulkDestroy'])->name('campaigns.bulk-destroy');
    Route::post('/campaigns/{campaign}/send-now', [CampaignController::class, 'sendNow'])->name('campaigns.send-now');
    Route::resource('campaigns', CampaignController::class);

    // import
    Route::get('/import', [App\Http\Controllers\ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [App\Http\Controllers\ImportController::class, 'store'])->name('import.store');

    // profile update
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // reports
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/team-wise', [App\Http\Controllers\ReportController::class, 'teamWise'])->name('reports.team-wise');

    // dashboard pages
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

// calender pages
Route::get('/calendar', function () {
    return view('pages.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.profile', ['title' => 'Profile']);
})->name('profile');

// form pages
Route::get('/form-elements', function () {
    return view('pages.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('login');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');






















