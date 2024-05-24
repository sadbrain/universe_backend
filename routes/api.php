<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CompanyController;
/*

|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware("api")->prefix("v1")->group(function(){

    Route::prefix("auth")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::get('/user-profile', [AuthController::class, 'userProfile']);
        });
        Route::post('/login', [AuthController::class, 'login'])->name("login");
        Route::post('/register', [AuthController::class, 'register'])->name("register"); 
    });

    Route::prefix("users")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::post('/register', [UserController::class, 'register'])->name("createUser"); 
            Route::get('', [UserController::class, 'getAll']); 
            Route::get('/get-roles', [UserController::class, 'getRoles']); 
            Route::get('/{id}', [UserController::class, 'get']); 
            Route::put('/perrmission/{id}', [UserController::class, 'perrmission']); 
            Route::get('/lock-unclock/{id}', [UserController::class, 'lockUnlock']); 
        });
    });

    Route::prefix("categories")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::get('/{id}', [CategoryController::class, 'get'])->whereNumber("id");
            Route::delete('/{id}', [CategoryController::class, 'delete'])->whereNumber("id");
            Route::put('/{id}', [CategoryController::class, 'update'])->whereNumber("id");
            Route::post('', [CategoryController::class, 'create']);
        });
        Route::get('', [CategoryController::class, 'getAll']);

    }); 

    Route::prefix("companies")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::get('/{id}', [CompanyController::class, 'get'])->whereNumber("id");
            Route::delete('/{id?}', [CompanyController::class, 'delete'])->whereNumber("id");
            Route::put('/{id}', [CompanyController::class, 'update'])->whereNumber("id");
            Route::post('', [CompanyController::class, 'create']);
            Route::get('', [CompanyController::class, 'getAll']);
        });

    });

    Route::prefix("carts")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::post('/summary', [CartController::class, 'summary']);
            Route::get('/order-confirmation/{id}', [CartController::class, 'orderConfirmation'])->whereNumber("id");
            Route::post('/add-to-cart', [CartController::class, 'addToCart']);
            Route::get('/plus/{id}', [CartController::class, 'plus']);
            Route::get('/minus/{id}', [CartController::class, 'minus']);
            Route::get('/delete/{id}', [CartController::class, 'delete']);
            Route::get('/show-cart', [CartController::class, 'showCart']);
            Route::post('/show-cart-for-sumary', [CartController::class, 'showCartForSumary']);
        });
    });

    Route::prefix("orders")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::get('/get-daily-orders', [OrderController::class, 'getDailyOrders']);
            Route::get('/get-total-revenue-order', [OrderController::class, 'getTotalRevenueOrder']);
            Route::get('/get-current-year-total-revenue-order', [OrderController::class, 'getCurrentYearTotalRevenueOrder']);
            Route::get('/get-monthly-orders', [OrderController::class, 'getMonthlyOrders']);
            Route::get('/get-yearly-orders', [OrderController::class, 'getYearlyOrders']);
            Route::get('/customer/{status}', [OrderController::class, 'getAllOfCustomers'])->whereAlpha("status");
            Route::get('/admin/{status}', [OrderController::class, 'getAllOfAdmin'])->whereAlpha("status");
            Route::get('/detail/{id}', [OrderController::class, 'detail'])->whereNumber("id");
            Route::put('/detail/{id}', [OrderController::class, 'detailPost'])->whereNumber("id");
            Route::put('/paynow', [OrderController::class, 'paynow']);
            Route::put('/start-processing', [OrderController::class, 'startProcessing']);
            Route::put('/ship-order', [OrderController::class, 'shipOrder']);
            Route::put('/cancel-order', [OrderController::class, 'cancelOrder']);
        });
        
    });
    Route::prefix("products")->group(function(){
        Route::middleware('jwt.auth')->group(function() {
            Route::get('/', [ProductController::class, 'getAll']);
            Route::delete('/{id?}', [ProductController::class, 'delete'])->whereNumber("id");
            Route::put('/{id}', [ProductController::class, 'update'])->whereNumber("id");
            Route::post('', [ProductController::class, 'create']);
            Route::post('create-size-more/{id}', [ProductController::class, 'createSizeMore']);
            Route::post('create-color-more/{id}', [ProductController::class, 'createColorMore']);
            Route::delete('delete-size-more/{id}', [ProductController::class, 'deleteSizeMore']);
            Route::delete('delete-color-more/{id}', [ProductController::class, 'deleteColorMore']);
            
        });
        Route::get('/{id?}', [ProductController::class, 'getProduct'])->whereNumber(['id']);
        Route::get('/get-products-by-category/{id?}/{page?}', [ProductController::class, 'getProductsByCategory'])->whereNumber(['id','page']);
        Route::get('/get-products-by-price/{price?}/{page?}', [ProductController::class, 'getProductsByPrice'])->whereNumber(['price','page']);
        Route::get('/get-related-products/{cate_id}', [ProductController::class, 'getRelatedProducts'])->whereNumber(['id']);
        Route::get('/get-best-seller-products', [ProductController::class, 'getBestSellProducts']);
        Route::get('/get-best-rating-products', [ProductController::class, 'getBestRatingProducts']);

    });
});

