<?php

use App\Http\Controllers\IssueController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Removed dashboard route; users are redirected to projects index after login

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::get('projects/{project}/issues', [ProjectController::class, 'issues'])->name('projects.issues');
    Route::resource('tags', TagController::class)->only(['index', 'store',]);

    Route::post('tags/validate', [TagController::class, 'validateAjax'])->name('tags.validate');
    Route::get('tags/table', [TagController::class, 'table'])->name('tags.table');

    Route::resource('issues', IssueController::class);
    Route::get('issues/{issue}/members', [IssueController::class, 'members'])->name('issues.members.index');
    Route::post('issues/{issue}/members', [IssueController::class, 'addMember'])->name('issues.members.store');
    Route::delete('issues/{issue}/members/{user}', [IssueController::class, 'removeMember'])->name('issues.members.destroy');
    Route::get('issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
    Route::post('issues/{issue}/comments', [CommentController::class, 'store'])->name('issues.comments.store');
});


require __DIR__.'/auth.php';

// Fallback: redirect any unknown/invalid route to projects index
Route::fallback(function () {
    return redirect()->route('projects.index');
});
