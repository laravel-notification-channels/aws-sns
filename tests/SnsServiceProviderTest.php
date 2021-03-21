<?php

namespace NotificationChannels\AwsSns\Test;

use Aws\Sns\SnsClient;
use Aws\Sns\SnsClient as SnsService;
use Illuminate\Contracts\Foundation\Application;
use Mockery;
use NotificationChannels\AwsSns\Sns;
use NotificationChannels\AwsSns\SnsChannel;
use NotificationChannels\AwsSns\SnsServiceProvider;
use PHPUnit\Framework\TestCase;

class SnsServiceProviderTest extends TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var App|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $app;

    /**
     * @var SnsServiceProvider
     */
    protected $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = Mockery::mock(App::class);
        $this->provider = new SnsServiceProvider($this->app);
    }

    /** @test */
    public function it_gives_an_instantiated_sns_object_when_the_channel_asks_for_it()
    {
        $configArray = [
            'key' => 'aws-key-123',
            'secret' => 'aws-secret-ashd1i26312873asw',
            'region' => 'us-east-1',
            'version' => 'latest',
        ];

        $this->app->shouldReceive('offsetGet')
            ->with('config')
            ->andReturn([
                'services.sns' => $configArray,
            ]);

        $this->app->shouldReceive('make')
            ->with(SnsService::class)
            ->andReturn(Mockery::mock(SnsService::class));

        $this->app->shouldReceive('when')
            ->with(SnsChannel::class)
            ->once()
            ->andReturn($this->app);

        $this->app->shouldReceive('needs')
            ->with(Sns::class)
            ->once()
            ->andReturn($this->app);

        $this->app->shouldReceive('give')
            ->with(Mockery::on(function ($sns) {
                return $sns() instanceof Sns;
            }))
            ->once();

        $this->app->shouldReceive('bind')
            ->with(SnsService::class, Mockery::on(function ($sns) {
                return $sns() instanceof SnsService;
            }))
            ->once()
            ->andReturn($this->app);

        $this->provider->boot();
    }

    /** @test */
    public function it_creates_the_aws_credentials_from_the_key_and_secret_options()
    {
        $this->app->shouldReceive('when')
            ->with(SnsChannel::class)
            ->once()
            ->andReturn($this->app);

        $this->app->shouldReceive('needs')
            ->with(Sns::class)
            ->once()
            ->andReturn($this->app);

        $this->app->shouldReceive('give')
            ->with(Mockery::on(function ($sns) {
                return $sns() instanceof Sns;
            }))
            ->once();

        $this->app->shouldReceive('make')
            ->with(SnsService::class)
            ->andReturn(Mockery::mock(SnsService::class));

        $this->app->shouldReceive('bind')
            ->with(SnsService::class, Mockery::on(function ($sns) {
                /** @var SnsClient $snsClient */
                $snsClient = $sns();
                $credentials = $snsClient->getCredentials()->wait();
                $this->assertSame([
                    'key' => 'aws-key-123',
                    'secret' => 'aws-secret-ashd1i26312873asw',
                    'token' => null,
                    'expires' => null,
                ], $credentials->toArray());

                return true;
            }))
            ->once()
            ->andReturn($this->app);

        $configArray = [
            'key' => 'aws-key-123',
            'secret' => 'aws-secret-ashd1i26312873asw',
            'region' => 'us-east-1',
        ];

        $this->app->shouldReceive('offsetGet')
            ->with('config')
            ->andReturn([
                'services.sns' => $configArray,
            ]);

        $this->provider->boot();
    }
}

interface App extends Application, \ArrayAccess
{
}
