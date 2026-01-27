<?php

namespace App\Providers\Filament;

use App\Filament\Water\Widgets\CustomWaterInfo;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class WaterPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('water')
            ->path('water')
            ->login()
            ->profile()
            ->passwordReset()
            ->favicon(asset('images/favicon.png'))
            ->brandName('পানি বিল ম্যানেজমেন্ট সিস্টেম')
            ->brandLogo(asset('images/logo.png'))
            ->spa(hasPrefetching: true)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Water/Resources'), for: 'App\Filament\Water\Resources')
            ->discoverPages(in: app_path('Filament/Water/Pages'), for: 'App\Filament\Water\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Water/Widgets'), for: 'App\Filament\Water\Widgets')
            ->widgets([
                AccountWidget::class,
                CustomWaterInfo::class,
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->userMenuItems(self::userMenuItems());
    }

    public static function userMenuItems(): array
    {
        // Return a closure so it's evaluated at runtime
        return [
            Action::make('admin')
                ->label('এডমিন প্যানেল')
                ->url(fn() => auth()->user()?->hasRole('super_admin') ? '/admin' : null)
                ->icon('heroicon-o-cog')
                ->visible(fn() => auth()->user()?->hasRole('super_admin')),
            Action::make('electricity')
                ->label('বিদ্যুৎ বিল ম্যানেজমেন্ট সিস্টেম')
                ->url(fn() => auth()->user()?->hasRole('super_admin') ? '/electricity' : null)
                ->icon('heroicon-o-cog')
                ->visible(fn() => auth()->user()?->hasRole('super_admin')),
        ];
    }
}
