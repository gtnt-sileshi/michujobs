<?php

use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChapaController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\JobListingController;
use App\Http\Controllers\PostJobController;
use App\Http\Middleware\CheckAuth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Middleware\isPremiumUser;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [JobListingController::class, 'index'])->name('listing.index');
Route::get('/company/{id}', [JobListingController::class, 'company'])->name('company');
Route::get('/jobs/{listing:slug}', [JobListingController::class, 'show'])->name('job.show');

Route::post('/resume/upload', [FileUploadController::class, 'store'])->middleware('auth');

Route::get('/users', function () {
    return view('user.index');
});

Route::get('/register/seeker', [UserController::class, 'createSeeker'])->name('create.seeker')->middleware(CheckAuth::class);
Route::post('/register/seeker', [UserController::class, 'storeSeeker'])->name('store.seeker');
Route::get('/register/employer', [UserController::class, 'createEmployer'])->name('create.employer')->middleware(CheckAuth::class);
Route::post('/register/employer', [UserController::class, 'storeEmployer'])->name('store.employer');

Route::get('/login', [UserController::class, 'login'])->name('login')->middleware(CheckAuth::class);
Route::post('/login', [UserController::class, 'postLogin'])->name('login.post');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::get('user/profile', [UserController::class, 'profile'])->middleware('auth')->name('user.profile');
Route::post('user/profile', [UserController::class, 'update'])->middleware('auth')->name('user.update.profile');
Route::get('user/profile/seeker', [UserController::class, 'seekerProfile'])->middleware('auth')->name('seeker.profile');

Route::get('user/job/applied', [UserController::class, 'jobApplied'])->middleware(['auth', 'verified'])->name('job.applied');


Route::post('user/password', [UserController::class, 'changePassword'])->middleware('auth')->name('user.password');
Route::post('upload/resume', [UserController::class, 'uploadResume'])->middleware('auth')->name('upload.resume');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified', isPremiumUser::class])->name('dashboard');
Route::get('/verify', [DashboardController::class, 'verify'])->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/login');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/resend/email/verification/email', [DashboardController::class, 'resend'])->name('resend.email');

Route::get('/subscribe', [ChapaController::class, 'subscribe'])->name('subscribe');
Route::get('/pay/monthly', [ChapaController::class, 'initialize'])->name('pay.monthly');
Route::get('/pay/yearly', [ChapaController::class, 'initialize'])->name('pay.yearly');
Route::get('/payment/success', [ChapaController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/cancel', [ChapaController::class, 'cancel'])->name('payment.cancel');
Route::get('callback/{reference}', [ChapaController::class, 'callback'])->name('callback');

Route::get('/job/create', [PostJobController::class, 'create'])->name('job.create');
Route::post('/job/store', [PostJobController::class, 'store'])->name('job.store');
Route::get('/job/{listing}/edit', [PostJobController::class, 'edit'])->name('job.edit');
Route::put('/job/{id}/edit', [PostJobController::class, 'update'])->name('job.update');
Route::get('job', [PostJobController::class, 'index'])->name('job.index');
Route::delete('/job/{id}/delete', [PostJobController::class, 'destroy'])->name('job.delete');


Route::get('applicants', [ApplicantController::class, 'index'])->name('applicants.index');
Route::get('applicants/{listing:slug}', [ApplicantController::class, 'show'])->name('applicants.show');
Route::post('shortlist/{listingId}/{userId}', [ApplicantController::class, 'shortlist'])->name('applicant.shortlist');
Route::post('application/{listingId}/submit', [ApplicantController::class, 'apply'])->name('applicantion.submit');