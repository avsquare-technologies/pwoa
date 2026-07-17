<?php

use App\Http\Controllers\FrontBusinessController;
use App\Http\Controllers\FrontCertificateController;
use App\Http\Controllers\FrontCourseController;
use App\Http\Controllers\FrontEventController;
use App\Http\Controllers\FrontQuizController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TokenPurchaseController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\ContractorController;
use App\Http\Controllers\Frontend\EducationController;
use App\Http\Controllers\Frontend\VendorController;
use App\Http\Controllers\Frontend\EventController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\MembershipController as FrontMembershipController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/tokenomics', [PageController::class, 'tokenomics'])->name('tokenomics');
Route::get('/compliance', [PageController::class, 'compliance'])->name('compliance.index');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [PageController::class, 'termsAndConditions'])->name('terms-and-conditions');

/*
|--------------------------------------------------------------------------
| Discovery & Directory Routes (Public/Protected mixed)
|--------------------------------------------------------------------------
*/
Route::prefix('user')->group(function () {
    // Public portions
    Route::get('/directory', [FrontBusinessController::class, 'directory'])->name('directory');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 'membership.active'
    // Dashboard & Core
    Route::middleware(['verified', 'active', 'membership.active'])->prefix('user')->group(function () {
        // Route::middleware(['verified', 'active'])->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        // Protected Directory Details
        Route::get('/business/manage', [FrontBusinessController::class, 'manage'])
            ->name('business.manage')
            ->middleware('has_no_business');
        Route::get('/business/{slug}', [FrontBusinessController::class, 'profile'])->name('business.profile');

        // Token-Gated Learning Center & Community Events (Gating done via UI Blade component)
        Route::middleware('auth')->group(function () {
            // Listings
            Route::get('/events', [FrontEventController::class, 'index'])->name('events');
            Route::get('/courses', [FrontCourseController::class, 'index'])->name('courses');

            // Details & Content
            Route::get('/events/{event}', [FrontEventController::class, 'detail'])->name('events.detail');
            Route::get('/courses/{slug}', [FrontCourseController::class, 'player'])->name('courses.player');
            Route::get('/quiz/{quiz}', [FrontQuizController::class, 'engine'])->name('quiz.engine');
        });
    });

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    // Membership Management
    Route::get('/membership/status', [MembershipController::class, 'status'])->name('membership.status');
    Route::get('/subscribe', [MembershipController::class, 'subscribe'])->name('membership.subscribe_form');
    Route::get('/membership/upgrade', \App\Livewire\MembershipUpgrade::class)->name('membership.upgrade');
    Route::get('/billing/portal', \App\Http\Controllers\BillingPortalController::class)->name('billing.portal');

    // Payment History
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');

    // Certificates
    Route::get('/my-certificates', [FrontCertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificate/{number}', [FrontCertificateController::class, 'show'])->name('certificates.show');
    Route::post('/certificate/{id}/mint', [FrontCertificateController::class, 'mintNft'])->name('certificates.mint');

    // Upgrade route for missing WASH tokens
    Route::get('/wash/upgrade', function (\App\Services\WashBalanceService $balanceService) {
        $currentBalance = $balanceService->getBalance(auth()->user());
        return view('frontend.wallet.upgrade', compact('currentBalance'));
    })->name('wash.upgrade');


    // Wallet & Token Purchases
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::post('/xaman/login', [WalletController::class, 'createXamanLogin'])->name('xaman.login');
        Route::get('/xaman/status/{uuid}', [WalletController::class, 'checkXamanLogin'])->name('xaman.status');
        Route::post('/xaman/check-access', [WalletController::class, 'checkXamanAccess'])->name('xaman.check-access');
        Route::post('/transfer', [WalletController::class, 'transfer'])->name('transfer');
    });

    Route::get('/token/purchase', function () {
        return view('frontend.wallet.purchase');
    })->name('token.purchase');

    Route::prefix('token')->name('token.')->group(function () {
        Route::match(['get', 'post'], '/checkout', [TokenPurchaseController::class, 'checkout'])->name('checkout');
        Route::get('/success', [TokenPurchaseController::class, 'success'])->name('success');
        Route::get('/cancel', [TokenPurchaseController::class, 'cancel'])->name('cancel');
    });
});
// });

/*
|--------------------------------------------------------------------------
| Membership (Frontend & Stripe)
|--------------------------------------------------------------------------
*/
Route::prefix('membership')->name('membership.')->group(function () {
    Route::get('/', fn() => redirect()->route('membership.subscribe_form'))->name('index');
    Route::get('/gold', fn() => redirect()->route('membership.subscribe_form'))->name('gold');
    Route::get('/subscribe', fn() => redirect()->route('membership.subscribe_form'))->name('subscribe');
    Route::get('/success', [MembershipController::class, 'success'])->name('success')->middleware('auth');
    Route::get('/cancel', fn() => redirect()->route('membership.subscribe_form'))->name('cancel');

    // Stripe Webhook (CSRF Excluded)
    Route::post('/webhook/stripe', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
        ->name('webhook.stripe')
        ->withoutMiddleware(['web']);
});

/*
|--------------------------------------------------------------------------
| Contractor & Vendor Directories
|--------------------------------------------------------------------------
*/
Route::prefix('contractors')->name('contractors.')->group(function () {
    Route::get('/', [ContractorController::class, 'index'])->name('index');
    Route::get('/{slug}', [ContractorController::class, 'show'])->name('show');

    Route::middleware('auth')->group(function () {
        Route::get('/my-listing/edit', [ContractorController::class, 'edit'])->name('edit');
        Route::put('/my-listing', [ContractorController::class, 'update'])->name('update');
    });
});

Route::prefix('vendors')->name('vendors.')->group(function () {
    Route::get('/', [VendorController::class, 'index'])->name('index');
    Route::get('/{slug}', [VendorController::class, 'show'])->name('show');

    Route::middleware('auth')->group(function () {
        Route::get('/my-listing/edit', [VendorController::class, 'edit'])->name('edit');
        Route::put('/my-listing', [VendorController::class, 'update'])->name('update');
    });
});

/*
|--------------------------------------------------------------------------
| Event Management (Frontend)
|--------------------------------------------------------------------------
*/
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    Route::get('/{slug}/purchase', [EventController::class, 'purchase'])->name('purchase.get')->middleware(['auth', 'wash.balance']);
    Route::post('/{slug}/purchase', [EventController::class, 'purchase'])->name('purchase')->middleware(['auth', 'wash.balance']);
    Route::get('/{slug}/order/{orderId}', [EventController::class, 'order'])->name('order')->middleware('auth');
    Route::get('/{slug}/ticket/{ticketId}', [EventController::class, 'ticket'])->name('ticket')->middleware('auth');
    Route::get('/{slug}/ticket/{ticketId}/pdf', [EventController::class, 'ticketPdf'])->name('ticket.pdf')->middleware('auth');
    Route::get('/verify-ticket', [EventController::class, 'verifyTicket'])->name('verify');

    // QR Scanner Routes
    Route::get('/admin/scan-ticket', [App\Http\Controllers\TicketScannerController::class, 'index'])->name('admin.scan-ticket')->middleware('auth');
    Route::post('/api/validate-ticket', [App\Http\Controllers\TicketScannerController::class, 'validateTicket'])->name('api.validate-ticket')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| Education & Certification
|--------------------------------------------------------------------------
*/
Route::prefix('education')->name('education.')->group(function () {
    Route::get('/', [EducationController::class, 'index'])->name('index');
    Route::get('/certification/{slug}', [EducationController::class, 'track'])->name('track');
    Route::get('/course/{slug}', [EducationController::class, 'course'])->name('course');
    Route::get('/course/{slug}/lesson/{lesson}', [EducationController::class, 'lesson'])->name('lesson')->middleware('auth');
    Route::post('/course/{slug}/enroll', [EducationController::class, 'enroll'])->name('enroll')->middleware(['auth', 'wash.balance']);
    Route::post('/course/{slug}/lesson/{lesson}/complete', [EducationController::class, 'completeLesson'])->name('lesson.complete')->middleware(['auth', 'wash.balance']);

    // Exams
    Route::get('/exam/{slug}', [EducationController::class, 'exam'])->name('exam')->middleware('auth');
    Route::post('/exam/{slug}/submit', [EducationController::class, 'submitExam'])->name('exam.submit')->middleware(['auth', 'wash.balance']);
    Route::get('/certificate/{id}', [EducationController::class, 'certificate'])->name('certificate')->middleware('auth');
});

/*
|--------------------------------------------------------------------------
| Contact & Support
|--------------------------------------------------------------------------
*/
Route::prefix('contact')->name('contact')->group(function () {
    Route::get('/', [ContactController::class, 'index']);
    Route::post('/send', [ContactController::class, 'send'])->name('.send');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';


Route::middleware(['auth'])->group(function () {
    Route::get('/complaints', [App\Http\Controllers\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [App\Http\Controllers\ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [App\Http\Controllers\ComplaintController::class, 'store'])->name('complaints.store')->middleware('wash.balance');
    Route::get('/complaints/{complaint}', [App\Http\Controllers\ComplaintController::class, 'show'])->name('complaints.show');
    Route::post('/complaints/{complaint}/reply', [App\Http\Controllers\ComplaintController::class, 'reply'])->name('complaints.reply')->middleware('wash.balance');
});

// Public Tracking
Route::get('/track-complaint', [App\Http\Controllers\TrackingController::class, 'index'])->name('complaints.track');
Route::post('/track-complaint', [App\Http\Controllers\TrackingController::class, 'track'])->name('complaints.track.submit');
