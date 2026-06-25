<?php

namespace App\Providers;

use App\Repositories\EloquentMemberRepository;
use App\Repositories\EloquentLoanRepository;
use App\Repositories\EloquentSavingsRepository;
use App\Repositories\MemberRepositoryInterface;
use App\Repositories\LoanRepositoryInterface;
use App\Repositories\SavingsRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MemberRepositoryInterface::class, EloquentMemberRepository::class);
        $this->app->bind(SavingsRepositoryInterface::class, EloquentSavingsRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, EloquentLoanRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
