<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\FlashSaleController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HomeController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ResourceController;

/* |-------------------------------------------------------------------------- | Public Routes (No Auth) |-------------------------------------------------------------------------- */

// Auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class , 'register']);
    Route::post('/login', [AuthController::class , 'login']);
});

// Home
Route::get('/home', [HomeController::class , 'index']);

// Products
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class , 'index']);
    Route::get('/featured', [ProductController::class , 'featured']);
    Route::get('/{product}', [ProductController::class , 'show']);
    Route::get('/{product}/reviews', [ProductController::class , 'reviews']);
});

// Categories
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class , 'index']);
    Route::get('/{slug}/products', [CategoryController::class , 'products']);
});

// Brands
Route::get('/brands', [BrandController::class , 'index']);

// Flash Sales
Route::prefix('flash-sales')->group(function () {
    Route::get('/active', [FlashSaleController::class , 'active']);
    Route::get('/upcoming', [FlashSaleController::class , 'upcoming']);
});

// Promotions
Route::get('/promotions', [PromotionController::class , 'index']);

// Articles
Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class , 'index']);
    Route::get('/featured', [ArticleController::class , 'featured']);
    Route::get('/{slug}', [ArticleController::class , 'show']);
});

// Contact & Branches
Route::post('/contact', [ContactController::class , 'store']);
Route::get('/branches', [ContactController::class , 'branches']);

// Payment Callbacks (no auth, public webhooks)
Route::get('/payments/vnpay/callback', [PaymentController::class , 'vnpayCallback']);
Route::post('/payments/momo/callback', [PaymentController::class , 'momoCallback']);

/* |-------------------------------------------------------------------------- | Authenticated User Routes |-------------------------------------------------------------------------- */

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class , 'logout']);
            Route::get('/me', [AuthController::class , 'me']);
        }
        );

        // Profile
        Route::prefix('profile')->group(function () {
            Route::put('/', [ProfileController::class , 'update']);
            Route::post('/avatar', [ProfileController::class , 'uploadAvatar']);
            Route::put('/password', [ProfileController::class , 'changePassword']);
        }
        );

        // Addresses
        Route::prefix('addresses')->group(function () {
            Route::get('/', [AddressController::class , 'index']);
            Route::post('/', [AddressController::class , 'store']);
            Route::put('/{address}', [AddressController::class , 'update']);
            Route::delete('/{address}', [AddressController::class , 'destroy']);
            Route::put('/{address}/default', [AddressController::class , 'setDefault']);
        }
        );

        // Cart
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class , 'index']);
            Route::post('/items', [CartController::class , 'addItem']);
            Route::put('/items/{item}', [CartController::class , 'updateItem']);
            Route::delete('/items/{item}', [CartController::class , 'removeItem']);
            Route::delete('/clear', [CartController::class , 'clear']);
        }
        );

        // Orders
        Route::prefix('orders')->group(function () {
            Route::post('/', [OrderController::class , 'store']);
            Route::get('/', [OrderController::class , 'index']);
            Route::get('/{id}', [OrderController::class , 'show']);
            Route::put('/{id}/cancel', [OrderController::class , 'cancel']);
        }
        );

        // Payments
        Route::prefix('payments')->group(function () {
            Route::post('/vnpay/create', [PaymentController::class , 'createVnpay']);
            Route::post('/momo/create', [PaymentController::class , 'createMomo']);
        }
        );

        // Payment Methods
        Route::prefix('payment-methods')->group(function () {
            Route::get('/', [PaymentMethodController::class , 'index']);
            Route::post('/', [PaymentMethodController::class , 'store']);
            Route::delete('/{paymentMethod}', [PaymentMethodController::class , 'destroy']);
            Route::put('/{paymentMethod}/default', [PaymentMethodController::class , 'setDefault']);
        }
        );

        // Wishlist
        Route::prefix('wishlist')->group(function () {
            Route::get('/', [WishlistController::class , 'index']);
            Route::post('/', [WishlistController::class , 'store']);
            Route::delete('/{productId}', [WishlistController::class , 'destroy']);
        }
        );

        // Reviews
        Route::post('/reviews', [ReviewController::class , 'store']);

        // Coupons
        Route::prefix('coupons')->group(function () {
            Route::get('/my', [CouponController::class , 'myCoupons']);
            Route::post('/apply', [CouponController::class , 'apply']);
        }
        );
    });

/* |-------------------------------------------------------------------------- | Admin Routes (Auth + Role: Admin) |-------------------------------------------------------------------------- */

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {

    // Dashboard
    Route::prefix('dashboard')->group(function () {
            Route::get('/stats', [DashboardController::class , 'stats']);
            Route::get('/revenue-chart', [DashboardController::class , 'revenueChart']);
            Route::get('/order-status', [DashboardController::class , 'orderStatus']);
            Route::get('/recent-orders', [DashboardController::class , 'recentOrders']);
        }
        );

        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class , 'index']);
            Route::post('/', [AdminProductController::class , 'store']);
            Route::put('/{product}', [AdminProductController::class , 'update']);
            Route::delete('/{product}', [AdminProductController::class , 'destroy']);
            Route::post('/{product}/images', [AdminProductController::class , 'uploadImages']);
        }
        );

        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [AdminCategoryController::class , 'index']);
            Route::post('/', [AdminCategoryController::class , 'store']);
            Route::put('/{category}', [AdminCategoryController::class , 'update']);
            Route::delete('/{category}', [AdminCategoryController::class , 'destroy']);
        }
        );

        // Brands
        Route::prefix('brands')->group(function () {
            Route::get('/', [ResourceController::class , 'brandIndex']);
            Route::post('/', [ResourceController::class , 'brandStore']);
            Route::put('/{brand}', [ResourceController::class , 'brandUpdate']);
            Route::delete('/{brand}', [ResourceController::class , 'brandDestroy']);
        }
        );

        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [ResourceController::class , 'orderIndex']);
            Route::get('/{order}', [ResourceController::class , 'orderShow']);
            Route::put('/{order}/status', [ResourceController::class , 'orderUpdateStatus']);
        }
        );

        // Users
        Route::prefix('users')->group(function () {
            Route::get('/', [ResourceController::class , 'userIndex']);
            Route::put('/{user}', [ResourceController::class , 'userUpdate']);
        }
        );

        // Coupons
        Route::prefix('coupons')->group(function () {
            Route::get('/', [ResourceController::class , 'couponIndex']);
            Route::post('/', [ResourceController::class , 'couponStore']);
            Route::put('/{coupon}', [ResourceController::class , 'couponUpdate']);
            Route::delete('/{coupon}', [ResourceController::class , 'couponDestroy']);
        }
        );

        // Flash Sales
        Route::prefix('flash-sales')->group(function () {
            Route::get('/', [ResourceController::class , 'flashSaleIndex']);
            Route::post('/', [ResourceController::class , 'flashSaleStore']);
            Route::put('/{flashSale}', [ResourceController::class , 'flashSaleUpdate']);
            Route::delete('/{flashSale}', [ResourceController::class , 'flashSaleDestroy']);
        }
        );

        // Articles
        Route::prefix('articles')->group(function () {
            Route::get('/', [ResourceController::class , 'articleIndex']);
            Route::post('/', [ResourceController::class , 'articleStore']);
            Route::put('/{article}', [ResourceController::class , 'articleUpdate']);
            Route::delete('/{article}', [ResourceController::class , 'articleDestroy']);
        }
        );

        // Banners
        Route::prefix('banners')->group(function () {
            Route::get('/', [ResourceController::class , 'bannerIndex']);
            Route::post('/', [ResourceController::class , 'bannerStore']);
            Route::put('/{banner}', [ResourceController::class , 'bannerUpdate']);
            Route::delete('/{banner}', [ResourceController::class , 'bannerDestroy']);
        }
        );

        // Promotions
        Route::prefix('promotions')->group(function () {
            Route::get('/', [ResourceController::class , 'promotionIndex']);
            Route::post('/', [ResourceController::class , 'promotionStore']);
            Route::put('/{promotion}', [ResourceController::class , 'promotionUpdate']);
            Route::delete('/{promotion}', [ResourceController::class , 'promotionDestroy']);
        }
        );

        // Contacts
        Route::prefix('contacts')->group(function () {
            Route::get('/', [ResourceController::class , 'contactIndex']);
            Route::put('/{contact}/read', [ResourceController::class , 'contactMarkRead']);
        }
        );

        // Branches
        Route::prefix('branches')->group(function () {
            Route::get('/', [ResourceController::class , 'branchIndex']);
            Route::post('/', [ResourceController::class , 'branchStore']);
            Route::put('/{branch}', [ResourceController::class , 'branchUpdate']);
            Route::delete('/{branch}', [ResourceController::class , 'branchDestroy']);
        }
        );
    });
