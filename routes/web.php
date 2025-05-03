<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\ListComplaineeController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\EmailController;
use App\Http\Middleware\PreventBackHistory;
use App\Http\Controllers\ContentController;

Route::get('/', [AdminAuthController::class, 'showLoginForm'])->name('login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

Route::middleware([PreventBackHistory::class, 'discipline'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDisciplineDashboard'])->name('admin.dashboard');
    Route::get('/admin/generate-report', [DashboardController::class, 'generateReport'])->name('admin.generate.report');

    Route::get('/profile', [ProfileController::class, 'showDisciplineProfile'])->name('admin.profile');
    Route::post('update-profile', [ProfileController::class, 'updateProfile'])->name('admin.updateProfile');

    Route::get('/users', [UserController::class, 'showUsers'])->name('admin.users.users');
    Route::post('/admin/users/change-status/{id}', [UserController::class, 'changeStatus'])->name('changeStatus');
    Route::get('/create-account', [UserController::class, 'showCreateAccountPage'])->name('admin.users.create-account');
    Route::post('/users/store', [UserController::class, 'storeAccount'])->name('admin.users.store');

    Route::get('/incident-reports', [ReportsController::class, 'showReportsDiscipline'])->name('admin.reports.incident-reports');
    Route::get('/reports/view/{id}', [ReportsController::class, 'viewReportDiscipline'])->name('admin.reports.view');
    Route::put('incident-reports/{id}/change-status', [ReportsController::class, 'changeStatus'])->name('admin.reports.changeStatus');
    Route::get('/export/csv', [ReportExportController::class, 'exportCSV'])->name('reports.export.csv');
    Route::get('/export/xlsx', [ReportExportController::class, 'exportXLSX'])->name('reports.export.xlsx');
    
    Route::post('admin/reports/update', [ReportsController::class, 'updateReport'])->name('admin.updateReport');
    Route::get('/search-id-number', [ReportsController::class, 'searchIdNumber'])->name('search.idNumber');

    //appointment
    Route::get('/appointment', [AppointmentController::class, 'showAppointmentPage'])->name('admin.appointment.appointment');
    Route::get('/appointment/summary', [AppointmentController::class, 'showAppointmentSummaryPage'])->name('admin.appointment.summary');
    Route::post('/appointments', [AppointmentController::class, 'storeAppointment'])->name('appointments.store');
    
    Route::post('/appointments/change-status', [AppointmentController::class, 'changeStatus']);
    Route::post('/appointments/filter', [AppointmentController::class, 'filterAppointments']);

    Route::get('/complainees', [ListController::class, 'showListOfPerpetrators'])->name('admin.list.list-perpetrators');
    Route::get('/complainees/{identifier}', [ListController::class, 'viewPerpetratorDiscipline'])
    ->name('admin.perpetrator.discipline');

    Route::get('/reports/{reportId}/appointment', [AppointmentController::class, 'getAppointmentForReport']);


    Route::get('/complainees/reports/{idNumber}', [ListController::class, 'viewReportsByIdNumber'])->name('admin.reports.byIdNumber');
    
    Route::get('/complainees/reports/view/{id}', [ListController::class, 'viewReportForComplainee'])->name('admin.list.view-report');

    Route::get('/complainee/add', [ListController::class, 'showAddComplainee'])->name('admin.list.add-complainee');
    Route::get('/export-complainees-csv', [ListComplaineeController::class, 'exportComplaineesCSV'])->name('export.csv');
    Route::get('/export-complainees-xlsx', [ListComplaineeController::class, 'exportComplaineesXLSX'])->name('export.xlsx');

    Route::get('/email-management', [EmailController::class, 'showEmailManagementPage'])->name('admin.email.email-management');
    Route::post('/store-email-content', [EmailController::class, 'storeEmailContent'])->name('storeEmailContent');

    Route::post('/admin-logout', [AdminAuthController::class, 'logoutAdmin'])->name('admin.logout');

    Route::get('/audit-trails', [UserController::class, 'showAuditLog'])->name('admin.users.audit-log');

    Route::post('/admin/reports/get-print-content', [ReportsController::class, 'getPrintContent'])->name('admin.reports.get-print-content');

 
    // Main content management page
    Route::get('/content-management', [ContentController::class, 'showContentPage'])->name('admin.content.content-management');
    
    // Form Builder API Routes
    Route::post('/form-builders', [ContentController::class, 'createFormBuilder'])->name('form-builders.create');
    Route::get('/form-builders/{id}', [ContentController::class, 'getFormBuilder'])->name('form-builders.get');
    
    // Steps
    Route::post('/form-builders/{formId}/steps', [ContentController::class, 'addStep'])->name('form-builders.steps.add');
    // Elements
    Route::post('/form-elements', [ContentController::class, 'addElement'])->name('form-elements.add');
    Route::put('/form-elements/{id}', [ContentController::class, 'updateElement'])->name('form-elements.update');
    Route::delete('/form-elements/{id}', [ContentController::class, 'deleteElement'])->name('form-elements.delete');
    Route::post('/form-elements/{id}/duplicate', [ContentController::class, 'duplicateElement'])->name('form-elements.duplicate');
    
    // Element Options
    Route::put('/form-elements/{id}/options', [ContentController::class, 'updateElementOptions'])->name('form-elements.options.update');
    
    // File Upload Settings
    Route::put('/form-elements/{id}/file-settings', [ContentController::class, 'updateFileUploadSettings'])->name('form-elements.file-settings.update');
    Route::get('/form-elements/{formId}/{stepId}', [ContentController::class, 'getElementsByStep'])
    ->name('form-elements.get-by-step');

    Route::put('/form-builders/{id}', [ContentController::class, 'updateFormBuilder'])->name('form-builders.update');

    Route::delete('form-builders/{formId}/steps/{stepId}', [ContentController::class, 'deleteStep'])->name('form-builders.steps.delete');

});