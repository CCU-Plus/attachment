<?php

namespace CCUPlus\Attachment;

use Illuminate\Support\ServiceProvider;

class AttachmentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/attachments.php' => config_path('attachments.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/attachments.php', 'attachments'
        );
    }
}
