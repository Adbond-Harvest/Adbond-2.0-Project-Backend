<?php

use Doctrine\DBAL\Driver\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Http\Middleware\ClientAuth;
use app\Http\Middleware\UserAuth;

//User Controllers
use app\Http\Controllers\User\ProjectTypeController as UserProjectTypeController;
use app\Http\Controllers\User\ProjectController as UserProjectController;
use app\Http\Controllers\User\PackageController as UserPackageController;
use app\Http\Controllers\User\IndexController as UserIndexController;
use app\Http\Controllers\User\ClientController as UserClientController;
use app\Http\Controllers\User\FileController as UserFileCOntroller;
use app\Http\Controllers\User\Client\WalletController as UserClientWalletController;
use app\Http\Controllers\User\Client\TransactionController as UserTransactionController;
use app\Http\Controllers\User\PostController as UserPostController;
use app\Http\Controllers\User\CommentController as UserCommentController;
use app\Http\Controllers\User\PaymentController as UserPaymentController;
use app\Http\Controllers\User\SiteTourController as UserSiteTourController;

// Client Controllers
use app\Http\Controllers\Client\PromoController;
use app\Http\Controllers\Client\OrderController;
use app\Http\Controllers\Client\PaymentController;
use app\Http\Controllers\Client\DashboardController;
use app\Http\Controllers\Client\WalletController;
use app\Http\Controllers\Client\TransactionController;
use app\Http\Controllers\Client\ProjectController as ClientProjectController;
use app\Http\Controllers\Client\PackageController as ClientPackageController;
use app\Http\Controllers\Client\AssetController;

//Public Controllers
use app\Http\Controllers\ProjectController;
use app\Http\Controllers\PackageController;
use app\Http\Controllers\UtilityController;

use app\Http\Controllers\TestController;
use App\Utilities;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::group(['prefix' => '/v2',], function () {

    //Auth URLS
    Route::group(['prefix' => '/auth', 'namespace' => 'Auth',], function () {
        Route::group(['prefix' => '/client'], function () {
            Route::post('/send_verification_mail', 'ClientAuthController@sendVerificationMail');
            Route::post('/verify_email_token', 'ClientAuthController@verifyEmailToken');
            Route::post('/register', 'ClientAuthController@register');
            Route::post('/login', 'ClientAuthController@login');
            Route::get('/google/get_url', 'GoogleController@getAuthUrl');
            Route::post('/google/login', 'GoogleController@postLogin');
        });
        Route::group(['prefix' => '/user'], function () {
            Route::post('/login', 'UserAuthController@login');
            Route::post('/send_password_reset_code', 'UserAuthController@sendPasswordResetCode');
            Route::post('/verify_password_reset_code', 'UserAuthController@verifyPasswordResetToken');
            Route::post('/reset_password', 'UserAuthController@resetPassword');
        });
    });

    //User/Admin/Staff Routes
    Route::group(['middleware' => UserAuth::class, 'prefix' => '/user', 'namespace' => 'User',], function () {
        Route::get('/dashboard', [UserIndexController::class, "dashboard"]);

        Route::group(['prefix' => '/profile'], function () {
            Route::post('/set_password', 'ProfileController@setPassword');
            Route::post('/update', 'ProfileController@update');
        });
        Route::post('/upload_photo', 'FileController@savePhoto');

        //Project Types
        Route::group(['prefix' => '/project_types'], function () {
            Route::post('/update', [UserProjectTypeController::class, "update"]);
            Route::get('', [UserProjectTypeController::class, "projectTypes"]);
            Route::get('/{id}', [UserProjectTypeController::class, "projectType"]);
        });
        // Project
        Route::group(['prefix' => '/projects'], function () {
            Route::post('', [UserProjectController::class, "save"]);
            Route::patch('', [UserProjectController::class, "update"]);
            Route::post('/activate', [UserProjectController::class, "activate"]);
            Route::post('/deactivate', [UserProjectController::class, "deactivate"]);
            Route::post('/delete', [UserProjectController::class, "delete"]);
            // Route::post('/filter/{projectTypeId}', [UserProjectController::class, "filter"]);
            Route::get('/types', [UserProjectController::class, "types"]);
            // Route::get('/summary/{projectTypeId}', [UserProjectController::class, "summary"]);
            Route::get('/all/{projectTypeId}', [UserProjectController::class, "projects"]);
            // Route::get('/search/{projectTypeId}', [UserProjectController::class, "search"]);
            Route::get('/export/{projectTypeId}', [UserProjectController::class, "export"]);
            Route::get('/{id}', [UserProjectController::class, "project"]);
        });
        // Package
        Route::group(['prefix' => '/packages'], function () {
            Route::post('', [UserPackageController::class, "save"]);
            Route::post('/save_media', [UserPackageController::class, "saveMedia"]);
            Route::post('/save_multiple_media', [UserPackageController::class, "saveMultipleMedia"]);
            Route::patch('/{id}', [UserPackageController::class, "update"]);
            Route::patch('/mark_as_sold_out', [UserPackageController::class, "markAsSoldOut"]);
            Route::patch('/mark_as_in_stock', [UserPackageController::class, "markAsInStock"]);
            Route::post('/activate', [UserPackageController::class, "activate"]);
            Route::post('/deactivate', [UserPackageController::class, "deactivate"]);
            Route::post('/delete', [UserPackageController::class, "delete"]);
            Route::post('/filter/{projectId}', [UserPackageController::class, "filter"]);
            Route::get('/all/{projectId}', [UserPackageController::class, "packages"]);
            Route::get('/search/{projectId}', [UserPackageController::class, "search"]);
            Route::get('/export/{projectId}', [UserPackageController::class, "export"]);
            Route::get('/{id}', [UserPackageController::class, "package"]);
        });

        Route::group(['prefix' => '/payments'], function () {
            Route::post('/confirm', [UserPaymentController::class, 'confirm']);
            Route::post('/reject', [UserPaymentController::class, 'reject']);
            Route::post('/flag', [UserPaymentController::class, 'flag']);
        });

        Route::group(['prefix' => '/site_tour'], function () {
            Route::group(['prefix' => '/schedules'], function () {
                Route::post('', [UserSiteTourController::class, 'createSchedule']);
                Route::patch('/{id}', [UserSiteTourController::class, 'updateSchedule']);
                Route::delete('/{id', [UserSiteTourController::class, 'deleteSchedule']);
                Route::get('', [UserSiteTourController::class, 'schedules']);
                Route::get('/{id}', [UserSiteTourController::class, 'schedule']);
            });
        });

        Route::group(['prefix' => '/posts'], function () {
            Route::post('', [UserPostController::class, "save"]);
            Route::post('/{postId}', [UserPostController::class, "update"]);
            Route::post('/toggle_activate', [UserPostController::class, "toggleActivate"]);
            Route::get('', [UserPostController::class, "posts"]);
            Route::get('/{postId}', [UserPostController::class, "post"]);
        });

        Route::group(['prefix' => '/comments'], function () {
            Route::post('', [UserCommentController::class, "save"]);
        });

        // Client
        Route::group(['prefix' => '/clients'], function () {
            Route::get('', [UserClientController::class, "index"]);
            Route::get('/{clientId}', [UserClientController::class, "show"]);
            Route::post('/{clientId}', [UserClientController::class, "update"]);
            Route::post('/re_upload_document/{assetId}', [UserFileController::class, "saveClientDocument"]);

            // Wallet Routes
            Route::group(['prefix' => '/wallet', 'namespace' => 'Client'], function () {
                Route::post('/link_bank_account', [UserClientWalletController::class, "linkBankAccount"]);
                Route::get('/{clientId}', [UserClientWalletController::class, "index"]);
                Route::get('/transactions/{clientId}', [UserClientWalletController::class, "transactions"]);
                Route::get('/withdrawal_requests/{clientId}', [UserClientWalletController::class, "withdrawalRequests"]);
                Route::get('/withdrawal_request/{requestId}', [UserClientWalletController::class, "withdrawalRequest"]);
                Route::post('/withdrawal_requests/approve', [UserClientWalletController::class, "approveRequest"]);
                Route::post('/withdrawal_requests/reject', [UserClientWalletController::class, "rejectRequest"]);
            });
            
            Route::group(['prefix' => '/transactions', 'namespace' => 'Client'], function () {
                Route::get('/{clientId}', [UserTransactionController::class, "transactions"]);
                Route::get('/show/{transactionId}', [UserTransactionController::class, "transaction"]);
            });
        });

        Route::get('/bank_accounts', [UtilityController::class, "bankAccounts"]);
        Route::get('/resell_orders', [UtilityController::class, "resellOrders"]);
    });

    /*
        Public Routes Begins here
    */
    Route::group(['prefix' => '/projects'], function () {
        // Project Routes
        Route::get('', [ProjectController::class, 'getProjects']);
        Route::get('/{projectTypeId}', [ProjectController::class, 'getProjects']);
        Route::get('/types', [ProjectController::class, 'getTypes']);
        Route::get('/view/{projectId}', [ProjectController::class, 'getProject']);
    });

    Route::group(['prefix' => '/packages'], function () {
        // Package Routes
        Route::get('{projectId}', [PackageController::class, 'getPackages']);
        // Route::get('/types', [ProjectController::class, 'getTypes']);
        Route::get('/view/{packageId}', [PackageController::class, 'getPackage']);
    });

    //Utitlity Routes
    Route::group(['prefix' => '/'], function () {    
        Route::get('benefits', [UtilityController::class, 'benefits']);
        Route::get('banks', [UtilityController::class, 'banks']);
    });


    /*
        Client Routes Begins Here
    */

    // Client Routes
    Route::group(['middleware' => ClientAuth::class, 'prefix' => '/client', 'namespace' => 'Client',], function () {
        Route::group(['prefix' => '/dashboard',], function () {
            Route::get('', [DashboardController::class, 'index']);
        });
        // Client Profile
        Route::group(['prefix' => '/profile',], function () {
            Route::post('/update', 'ClientController@update');
            Route::post('/save_next_of_kin', 'ClientController@addNextOfKin');
        });
        Route::group(['prefix' => '/file',], function () {
            Route::post('/upload_profile_photo', 'FileController@saveProfilePhoto');
            Route::post('/upload_payment_evidence', 'FileController@savePaymentEvidence');
        });

        // Project Routes
        Route::group(['prefix' => '/projects',], function () {
            Route::get('/summary', [ClientProjectController::class, 'summary']);
            Route::get('', [ClientProjectController::class, 'projects']);
            Route::get('/{projectTypeId}', [ClientProjectController::class, 'projects']);
            Route::get('/get_project/{projectId}', [ClientProjectController::class, 'project']);
            Route::get('/get_project/all/{projectTypeId}', [ClientProjectController::class, 'projectType']);
        });

         // Package Routes
         Route::group(['prefix' => '/packages',], function () {
            Route::get('/{packageId}', [ClientPackageController::class, 'package']);
        });

        //Order Routes
        Route::group(['prefix' => '/order',], function () {
            Route::post('/validate_promo_code', [PromoController::class, 'validate']);
            Route::post('/prepare', [OrderController::class, 'prepareOrder']);
        });

        // Payment Routes
        Route::group(['prefix' => '/payment',], function () {
            Route::post('/initialize_card_payment', [PaymentController::class, 'initializeCardPayment']);
            Route::post('/prepare_additional_payment', [PaymentController::class, 'prepareAdditionalPayment']);
            Route::post('/save', [PaymentController::class, 'save']);
            Route::post('/save_additional_payment', [PaymentController::class, 'saveAdditionalPayment']);
        });

        //Assets Routes
        Route::group(['prefix' => '/assets'], function () {
            Route::get('/summary', [AssetController::class, 'summary']);
            Route::get('', [AssetController::class, 'assets']);
            Route::get('/{assetId}', [AssetController::class, 'asset']);
        });

        //Wallet Routes
        Route::group(['prefix' => '/wallet',], function () {
            Route::get('', [WalletController::class, 'index']);
            Route::get('/transactions', [WalletController::class, 'transactions']);
            Route::post('/link_bank_account', [WalletController::class, 'LinkBankAccount']);
            Route::post('/set_transaction_pin', [WalletController::class, 'setTransactionPin']);
            Route::post('/validate_withdrawal', [WalletController::class, 'validateWithdrawal']);
            Route::post('/withdraw', [WalletController::class, 'withdraw']);
        });

        //Transaction Routes
        Route::group(['prefix' => '/transactions',], function () {
            Route::get('', [TransactionController::class, 'index']);
            Route::get('/{transactionId}', [TransactionController::class, 'transaction']);
            Route::get('/export/{transactionId}', [TransactionController::class, 'export']);
        });


        Route::get('/bank_accounts', [UtilityController::class, "activeBankAccounts"]);
        Route::get('/resell_orders', [UtilityController::class, "resellOrders"]);
    });

    // Route::get('test/benefit', [TestController::class, 'benefit']);

    // Route::get('/orders-mail', function() {
    //     return view('emails/new_order');
    // });
});