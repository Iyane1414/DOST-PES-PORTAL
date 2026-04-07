<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortalController::class, 'index'])->name('portal.home');
Route::get('/gates-projects/{collectionSlug}', [PortalController::class, 'gatesCollection'])->name('portal.gates.show');
Route::get('/resources/{collectionSlug}', [PortalController::class, 'materialCollection'])->name('portal.resources.show');
Route::get('/dost-dx/{domainSlug}/{subProgramSlug}', [PortalController::class, 'dxProgramShow'])->name('portal.dx.program.show');
Route::post('/contact', [PortalController::class, 'contact'])->name('portal.contact');
Route::post('/subscribe', [PortalController::class, 'subscribe'])->name('portal.subscribe');
Route::post('/assistant', [PortalController::class, 'assistant'])->name('portal.assistant');

Route::prefix('admin')->group(function (): void {
    Route::middleware('admin.guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'create'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'store'])->name('admin.login.store');
    });

    Route::middleware('admin.auth')->group(function (): void {
        Route::get('/', [DashboardController::class, 'overview'])->name('admin.dashboard');
        Route::get('/workspace/{tab?}', [DashboardController::class, 'workspace'])->name('admin.workspace');
        Route::get('/messages/{contactMessage}', [DashboardController::class, 'showMessage'])->name('admin.messages.show');
        Route::post('/logout', [AuthController::class, 'destroy'])->name('admin.logout');

        Route::post('/issuances', [ResourceController::class, 'storeIssuance'])->name('admin.issuances.store');
        Route::put('/issuances/{issuance}', [ResourceController::class, 'updateIssuance'])->name('admin.issuances.update');
        Route::delete('/issuances/{issuance}', [ResourceController::class, 'destroyIssuance'])->name('admin.issuances.destroy');

        Route::post('/materials', [ResourceController::class, 'storeMaterial'])->name('admin.materials.store');
        Route::put('/materials/{material}', [ResourceController::class, 'updateMaterial'])->name('admin.materials.update');
        Route::delete('/materials/{material}', [ResourceController::class, 'destroyMaterial'])->name('admin.materials.destroy');

        Route::post('/gates-projects', [ResourceController::class, 'storeGatesProject'])->name('admin.gates.store');
        Route::put('/gates-projects/{gatesProject}', [ResourceController::class, 'updateGatesProject'])->name('admin.gates.update');
        Route::delete('/gates-projects/{gatesProject}', [ResourceController::class, 'destroyGatesProject'])->name('admin.gates.destroy');

        Route::post('/news', [ResourceController::class, 'storeNews'])->name('admin.news.store');
        Route::put('/news/{news}', [ResourceController::class, 'updateNews'])->name('admin.news.update');
        Route::delete('/news/{news}', [ResourceController::class, 'destroyNews'])->name('admin.news.destroy');

        Route::post('/divisions', [ResourceController::class, 'storeDivision'])->name('admin.divisions.store');
        Route::delete('/divisions/{division}', [ResourceController::class, 'destroyDivision'])->name('admin.divisions.destroy');

        Route::post('/dx-items', [ResourceController::class, 'storeDxItem'])->name('admin.dx-items.store');
        Route::put('/dx-items/{dxItem}', [ResourceController::class, 'updateDxItem'])->name('admin.dx-items.update');
        Route::delete('/dx-items/{dxItem}', [ResourceController::class, 'destroyDxItem'])->name('admin.dx-items.destroy');

        Route::post('/categories', [ResourceController::class, 'storeCategory'])->name('admin.categories.store');
        Route::delete('/categories/{issuanceCategory}', [ResourceController::class, 'destroyCategory'])->name('admin.categories.destroy');

        Route::post('/ai-settings', [ResourceController::class, 'storeAiSettings'])->name('admin.ai-settings.store');
        Route::post('/messages/{contactMessage}/reply', [ResourceController::class, 'replyToMessage'])->name('admin.messages.reply');
    });
});
