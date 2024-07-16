<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\DropBoxController;
use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;
use Pion\Laravel\ChunkUploadExample\Http\Controllers\UploadController;
use App\Http\Controllers\Admin\LoginController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/store', [FileUploadController::class, 'store'])->name('store');
Route::get('/', [FileUploadController::class, 'index'])->name('files.index');
Route::get('/thank-you', [FileUploadController::class, 'thankyou'])->name('thankyou');
Route::get('/upload', [FileUploadController::class, 'upload'])->name('upload');
Route::get('/upload-submit', [FileUploadController::class, 'uploadSubmit'])->name('upload-submit');
Route::post('file-upload/upload-large-files', [FileUploadController::class, 'uploadLargeFiles'])->name('files.upload.large');

Route::get('admin/login',[LoginController::class,'login'])->name('admin.admin-login');
Route::post('/login-submit',[LoginController::class,'submit'])->name('admin.admin-login-submit');

Route::name('admin.')->middleware('admin.check')->prefix('admin')->group(function(){
    Route::get('/logout',[LoginController::class,'logout'])->name('admin-logout');
    Route::get('/view-videos',[DashboardController::class,'index'])->name('dashboard');
    Route::get('/delete-video/{id}',[DashboardController::class,'delete'])->name('delete-video');
});
