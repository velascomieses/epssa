<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
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
        Password::defaults(function () {
            return Password::min(8)
                ->letters()->mixedCase()
                ->numbers()->symbols()
                ->uncompromised();
        });

        Filament::serving(function () {
            Filament::registerNavigationGroups([
                NavigationGroup::make()
                    ->label('Plataforma'),
                NavigationGroup::make()
                    ->label('Pagos'),
                NavigationGroup::make()
                    ->label('Inventario'),
                NavigationGroup::make()
                    ->label('Configuraciones'),
            ]);
        });
    }
}
