<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentService;
use App\Domain\Campaign\Campaign;
use App\Services\Payments\MockPaymentService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentService::class, MockPaymentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('spa', function ($view) {
            $pageSeo = [
                'title' => null,
                'description' => null,
                'og_type' => null,
                'og_image' => null,
                'robots' => null,
                'canonical' => url()->current(),
            ];

            if (request()->is(
                'login',
                'register',
                'dashboard',
                'dashboard/*',
                'me/*',
                'profile',
                'confirm-password',
                'verify-email',
                'forgot-password',
                'reset-password/*'
            )) {
                $pageSeo['robots'] = 'noindex, nofollow';
            }

            if (request()->is('campaigns/*')) {
                $slug = (string) request()->segment(2);

                $campaign = Campaign::query()
                    ->where('slug', $slug)
                    ->with('user')
                    ->first();

                if ($campaign) {
                    $pageSeo['title'] = $campaign->title;
                    $pageSeo['description'] = Str::limit(
                        trim((string) $campaign->description),
                        160,
                        '…'
                    );
                    $pageSeo['og_type'] = 'article';
                    $pageSeo['og_image'] = $campaign->cover_image_path;
                }
            }

            $view->with('pageSeo', $pageSeo);
        });
    }
}
