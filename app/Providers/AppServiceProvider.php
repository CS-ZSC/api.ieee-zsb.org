<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Chapter;
use App\Models\Committee;
use App\Models\Track;
use App\Models\User;
use App\Policies\ChapterPolicy;
use App\Policies\CommitteePolicy;
use App\Policies\TrackPolicy;
use App\Policies\UserPolicy;


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
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000')), '/');
            return $frontend . '/reset-password?token=' . $token . '&email=' . urlencode($notifiable->getEmailForPasswordReset());
        });

        Relation::enforceMorphMap([
            'user'      => User::class,
            'chapter'   => Chapter::class,
            'committee' => Committee::class,
        ]);
    }

    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Chapter::class   => ChapterPolicy::class,
        Committee::class => CommitteePolicy::class,
        Track::class     => TrackPolicy::class,
        User::class      => UserPolicy::class,
    ];
}
