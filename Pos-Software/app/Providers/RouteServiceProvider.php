<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Models\PosSetting;
use Illuminate\Support\Facades\Auth;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $settings = PosSetting::first();
                $siteTitle = $settings ? $settings->company : 'EIL Electro || Eclipse Intellitech Limited POS Software';
                $logo = $settings->logo;
                $facebook = $settings->facebook;
                $address = $settings->address;
                $header = $settings->header_text;
                $phone = $settings->phone;
                $email = $settings->email;
                $invoice_logo_type = $settings->invoice_logo_type;
                $invoice_type = $settings->invoice_type;
                $barcode = $settings->barcode;
                $discount = $settings->discount;
                $tax = $settings->tax;
                $selling_price_edit = $settings->selling_price_edit;
                $via_sale = $settings->via_sale;
                $view->with([
                    'siteTitle' => $siteTitle,
                    'logo' => $logo,
                    'header' => $header,
                    'address' => $address,
                    'facebook' => $facebook,
                    'phone' => $phone,
                    'email' => $email,
                    'invoice_logo_type' => $invoice_logo_type,
                    'invoice_type' => $invoice_type,
                    'barcode' => $barcode,
                    'discount' => $discount,
                    'tax' => $tax,
                    'selling_price_edit' => $selling_price_edit,
                    'via_sale' => $via_sale
                ]);
            }
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
