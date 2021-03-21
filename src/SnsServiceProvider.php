<?php

namespace NotificationChannels\AwsSns;

use Aws\Sns\SnsClient as SnsService;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class SnsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app->when(SnsChannel::class)
            ->needs(Sns::class)
            ->give(function () {
                return new Sns($this->app->make(SnsService::class));
            });

        $this->app->bind(SnsService::class, function () {
            $config = array_merge(['version' => 'latest'], $this->app['config']['services.sns']);

            return new SnsService($this->addSnsCredentials($config));
        });
    }

    protected function addSnsCredentials($config): array
    {
        if (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }
}
