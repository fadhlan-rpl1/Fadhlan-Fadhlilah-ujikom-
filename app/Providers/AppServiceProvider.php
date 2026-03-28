<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Pagination\Paginator; // <-- 1. TAMBAHKAN INI DI SINI

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // <-- 2. TAMBAHKAN INI AGAR TAMPILAN HALAMAN RAPI
        Paginator::useBootstrap(); 

        // Definisi Gate untuk Role
        // Pastikan menggunakan simbol $ pada variabel user dan roles
        // Pastikan seperti ini di AppServiceProvider
        Gate::define('role', function (User $user, ...$roles) {
            return in_array($user->role, $roles);
        });
    }
}