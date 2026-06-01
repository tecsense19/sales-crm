<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\SmtpProviderController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TemplateController;

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
    Route::get('/kanban/board-data', [KanbanController::class, 'getBoardData'])->name('kanban.board-data');
    Route::get('/kanban/column-cards', [KanbanController::class, 'getColumnCards'])->name('kanban.column-cards');
    Route::post('/kanban/update-status', [KanbanController::class, 'updateStatus'])->name('kanban.update-status');
    Route::patch('/kanban/clients/{client}/quick-update', [KanbanController::class, 'quickUpdate'])->name('kanban.quick-update');

    // templates resource
    Route::post('/templates/bulk-destroy', [TemplateController::class, 'bulkDestroy'])->name('templates.bulk-destroy');
    Route::resource('templates', TemplateController::class);

    // billing resource
    Route::get('/billing/export', [BillingController::class, 'export'])->name('billing.export');
    Route::post('/billing/bulk-destroy', [BillingController::class, 'bulkDestroy'])->name('billing.bulk-destroy');
    Route::resource('billing', BillingController::class);


    // smtp providers (multi-provider rotation)
    Route::get('/smtp-providers', [SmtpProviderController::class, 'index'])->name('smtp-providers.index');
    Route::post('/smtp-providers', [SmtpProviderController::class, 'store'])->name('smtp-providers.store');
    Route::put('/smtp-providers/{smtpProvider}', [SmtpProviderController::class, 'update'])->name('smtp-providers.update');
    Route::delete('/smtp-providers/{smtpProvider}', [SmtpProviderController::class, 'destroy'])->name('smtp-providers.destroy');
    Route::post('/smtp-providers/{smtpProvider}/reset-counter', [SmtpProviderController::class, 'resetCounter'])->name('smtp-providers.reset-counter');
    Route::post('/smtp-providers/{smtpProvider}/toggle', [SmtpProviderController::class, 'toggleActive'])->name('smtp-providers.toggle');
    Route::get('/smtp-providers/pending-emails', [SmtpProviderController::class, 'pendingEmails'])->name('smtp-providers.pending');
    Route::post('/smtp-providers/pending-emails/bulk-destroy', [SmtpProviderController::class, 'bulkDestroyPending'])->name('smtp-providers.pending.bulk-destroy');


    // campaigns
    Route::get('/campaigns/export', [CampaignController::class, 'export'])->name('campaigns.export');
    Route::post('/campaigns/bulk-destroy', [CampaignController::class, 'bulkDestroy'])->name('campaigns.bulk-destroy');
    Route::post('/campaigns/{campaign}/send-now', [CampaignController::class, 'sendNow'])->name('campaigns.send-now');
    Route::resource('campaigns', CampaignController::class);

    // import
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');

    // profile
    Route::get('/profile', function () {
        return view('pages.profile', ['title' => 'Profile']);
    })->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/team-wise', [ReportController::class, 'teamWise'])->name('reports.team-wise');

    // dashboard pages
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
// Authentication pages
Route::get('/signin', function () {
    return view('pages.auth.signin', ['title' => 'Sign In']);
})->name('login');






















