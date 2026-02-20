<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Chapter;
use App\Models\Committee;
use App\Models\Track;
use App\Models\User;
use App\Models\Goal;
use App\Models\Activity;
use App\Models\News;
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
        //
        Relation::enforceMorphMap([
            'user'      => User::class,
            'chapter'   => Chapter::class,
            'committee' => Committee::class,
            'track'     => Track::class,
            'goal'      => Goal::class,
            'activity'  => Activity::class,
            'news'      => News::class,
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
