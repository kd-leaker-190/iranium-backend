<?php

namespace App\Providers;

use App\Observers\ActionObserver;
use App\Models\Action;
use App\Models\User;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected array $policies = [
        // TODO List Other Policies
        \Modules\Task\Models\TaskTeam::class => \App\Policies\TaskTeamPolicy::class,
        \App\Models\Team::class => \App\Policies\TeamPolicy::class,
        \App\Models\ActionTeam::class => \App\Policies\ActionTeamPolicy::class,
    ];

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
        Schema::defaultStringLength(191);

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Gate::define('viewLogViewer', static function (?User $user) {
            return app()->isLocal() || (bool) $user;
        });

        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        Action::observe(ActionObserver::class);
    }
}
