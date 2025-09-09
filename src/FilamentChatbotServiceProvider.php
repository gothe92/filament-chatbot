<?php

namespace FilamentChatbot;

use FilamentChatbot\Livewire\ChatbotWidget;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class FilamentChatbotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/filament-chatbot.php',
            'filament-chatbot'
        );
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filament-chatbot');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Register Livewire component
        Livewire::component('filament-chatbot::chatbot-widget', ChatbotWidget::class);

        // Publish config
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/filament-chatbot.php' => config_path('filament-chatbot.php'),
            ], 'filament-chatbot-config');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/filament-chatbot'),
            ], 'filament-chatbot-views');

            // Publish assets
            $this->publishes([
                __DIR__.'/../resources/js' => public_path('vendor/filament-chatbot/js'),
                __DIR__.'/../resources/css' => public_path('vendor/filament-chatbot/css'),
            ], 'filament-chatbot-assets');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'filament-chatbot-migrations');
        }
    }
}
