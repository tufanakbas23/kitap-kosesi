<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Sepet servisi istek boyunca tek örnek
        $this->app->singleton(CartService::class);
    }

    public function boot(): void
    {
        App::setLocale('tr');
        Carbon::setLocale('tr');

        // Resource rotalarında Türkçe URL: /yeni, /duzenle
        Route::resourceVerbs(['create' => 'yeni', 'edit' => 'duzenle']);

        // Projeye özel sayfalama görünümü
        Paginator::defaultView('partials.pagination');

        // @money($tutar)  =>  1.234,50 TL
        Blade::directive('money', fn ($expr) => "<?php echo number_format((float) ($expr), 2, ',', '.') . ' TL'; ?>");

        // Mağaza başlığı/menüsü için ortak veriler
        View::composer(['layouts.app', 'partials.header', 'partials.navbar', 'partials.footer'], function ($view) {
            static $categories = null;
            $categories ??= Category::active()->get();

            $view->with('navCategories', $categories)
                 ->with('cartCount', app(CartService::class)->count());
        });
    }
}
