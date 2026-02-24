<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Auth\AuthRepository;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\Club\ClubRepository;
use App\Repositories\Club\ClubRepositoryInterface;
use App\Repositories\Friendship\FriendshipRepository;
use App\Repositories\Friendship\FriendshipRepositoryInterface;
use App\Repositories\MatchScheduleRequest\MatchScheduleRequestRepository;
use App\Repositories\MatchScheduleRequest\MatchScheduleRequestRepositoryInterface;
use App\Repositories\Match\MatchRepository;
use App\Repositories\Match\MatchRepositoryInterface;
use App\Repositories\Stadium\StadiumRepository;
use App\Repositories\Stadium\StadiumRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(StadiumRepositoryInterface::class, StadiumRepository::class);
        $this->app->bind(FriendshipRepositoryInterface::class, FriendshipRepository::class);
        $this->app->bind(ClubRepositoryInterface::class, ClubRepository::class);
        $this->app->bind(MatchScheduleRequestRepositoryInterface::class, MatchScheduleRequestRepository::class);
        $this->app->bind(MatchRepositoryInterface::class, MatchRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
