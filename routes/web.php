<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Mağaza (herkese açık)
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::view('/yardim', 'shop.help')->name('help');

Route::controller(CatalogController::class)->group(function () {
    Route::get('/kitaplar', 'index')->name('books.index');
    Route::get('/kitap/{book:slug}', 'show')->name('books.show');
    Route::get('/kategori/{category:slug}', 'category')->name('categories.show');
    Route::get('/yazar/{author:slug}', 'author')->name('authors.show');
    Route::get('/yayinevi/{publisher:slug}', 'publisher')->name('publishers.show');
    Route::get('/cok-satanlar', 'bestsellers')->name('books.bestsellers');
    Route::get('/editorun-sectikleri', 'featured')->name('books.featured');
    Route::get('/indirimli-kitaplar', 'discounted')->name('books.discounted');
});

Route::prefix('sepet')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/ekle/{book}', 'add')->name('add');
    Route::patch('/{book}', 'update')->name('update');
    Route::delete('/{book}', 'remove')->name('remove');
    Route::post('/kupon', 'applyCoupon')->name('coupon.apply');
    Route::delete('/kupon/kaldir', 'removeCoupon')->name('coupon.remove');
});

/*
|--------------------------------------------------------------------------
| Giriş / Kayıt
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/giris', 'showLogin')->name('login');
    Route::post('/giris', 'login')->middleware('throttle:10,1');
    Route::get('/uye-ol', 'showRegister')->name('register');
    Route::post('/uye-ol', 'register');
});
Route::post('/cikis', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Giriş gerektiren alanlar
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Ödeme
    Route::get('/odeme', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/odeme', [CheckoutController::class, 'store'])->name('checkout.store')->block();
    Route::get('/siparis-tamamlandi/{order:order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Favori ve yorum
    Route::post('/favori/{book}', [Account\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/kitap/{book}/yorum', [Account\ReviewController::class, 'store'])->name('reviews.store');

    // Hesabım
    Route::prefix('hesabim')->name('account.')->group(function () {
        Route::get('/', [Account\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/uyelik-bilgilerim', [Account\ProfileController::class, 'edit'])->name('profile');
        Route::put('/uyelik-bilgilerim', [Account\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/sifre', [Account\ProfileController::class, 'updatePassword'])->name('password.update');

        Route::resource('adreslerim', Account\AddressController::class)
            ->except('show')->parameters(['adreslerim' => 'address'])->names('addresses');

        Route::get('/siparislerim', [Account\OrderController::class, 'index'])->name('orders.index');
        Route::get('/siparislerim/{order:order_number}', [Account\OrderController::class, 'show'])->name('orders.show');
        Route::post('/siparislerim/{order:order_number}/iptal', [Account\OrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('/iade-degisim', [Account\ReturnController::class, 'index'])->name('returns.index');
        Route::get('/iade-degisim/yeni/{item}', [Account\ReturnController::class, 'create'])->name('returns.create');
        Route::post('/iade-degisim/yeni/{item}', [Account\ReturnController::class, 'store'])->name('returns.store');

        Route::get('/favorilerim', [Account\FavoriteController::class, 'index'])->name('favorites');
        Route::get('/yorumlarim', [Account\ReviewController::class, 'index'])->name('reviews');
    });
});

/*
|--------------------------------------------------------------------------
| Yönetim Paneli
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('books', Admin\BookController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('authors', Admin\AuthorController::class)->except('show');
    Route::resource('publishers', Admin\PublisherController::class)->except('show');
    Route::resource('coupons', Admin\CouponController::class)->except('show');

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

    Route::get('returns', [Admin\ReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/{returnRequest}', [Admin\ReturnController::class, 'show'])->name('returns.show');
    Route::put('returns/{returnRequest}', [Admin\ReturnController::class, 'update'])->name('returns.update');

    Route::get('users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/toggle', [Admin\UserController::class, 'toggle'])->name('users.toggle');

    Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}', [Admin\ReviewController::class, 'toggle'])->name('reviews.toggle');
    Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
});
