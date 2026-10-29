<?php

namespace App\Providers;

use App\Enums\StatusIzin;
use App\Models\Izin;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        CarbonImmutable::setLocale(config('app.locale'));

        // Tangkap lazy loading (N+1) & atribut yang salah ketik saat development.
        Model::shouldBeStrict(! $this->app->isProduction());

        // Badge jumlah izin menunggu di navigasi admin.
        View::composer('components.layouts.admin', function ($view) {
            $view->with('izinPendingCount', Izin::status(StatusIzin::Pending)->count());
        });
    }
}
