<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (! \Illuminate\Support\Collection::hasMacro('paginate')) {
            \Illuminate\Support\Collection::macro('paginate', function ($perPage = 15, $page = null, $options = []) {
                    $page = $page ?: (\Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() ?: 1);
                    return (new \Illuminate\Pagination\LengthAwarePaginator (
                        $this->forPage($page, $perPage)->values()->all(), $this->count(), $perPage, $page, $options))
                        ->withPath(\Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath());
            });
        }
    }
}
