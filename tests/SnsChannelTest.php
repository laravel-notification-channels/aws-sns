<?php

namespace NotificationChannels\AwsSns\Test;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Mockery;
use NotificationChannels\AwsSns\Sns;
use NotificationChannels\AwsSns\SnsChannel;
use NotificationChannels\AwsSns\SnsMessage;

class SnsChannelTest extends TestCase
{
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|Sns
     */
    protected $sns;

    /**
     * @var Dispatcher|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $dispatcher;

    /**
     * @var SnsChannel
     */
    protected $channel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sns = Mockery::mock(Sns::class);
        $this->dispatcher = Mockery::mock(Dispatcher::class);
        $this->channel = new SnsChannel($this->sns, $this->dispatcher);
    }

    public function test_it_will_not_send_a_message_without_known_receiver()
    {
        $notifiable = new Notifiable;
        $notification = Mockery::mock(Notification::class);

        $this->dispatcher->shouldReceive('dispatch')
            ->atLeast()
            ->once()
            ->with(Mockery::type(NotificationFailed::class));

        $result = $this->channel->send($notifiable, $notification);

        $this->assertNull($result);
    }

    public function test_it_will_send_a_sms_message_to_the_result_of_the_route_method_of_the_notifiable()
    {
        $notifiable = new NotifiableWithMethod;
        $message = new SnsMessage('Message text');

        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toSns')->andReturn($message);

        $this->sns->shouldReceive('send')
            ->atLeast()->once()
            ->with($message, '+1111111111');

        $this->channel->send($notifiable, $notification);
    }

    public function test_it_will_make_a_call_to_the_phone_number_attribute_of_the_notifiable()
    {
        $notifiable = new NotifiableWithAttribute;
        $message = new SnsMessage('Some content to send');

        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toSns')->andReturn($message);

        $this->sns->shouldReceive('send')
            ->atLeast()->once()
            ->with($message, '+22222222222');

        $this->channel->send($notifiable, $notification);
    }

    public function test_it_will_convert_a_string_to_a_sms_message()
    {
        $notifiable = new NotifiableWithAttribute;

        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toSns')->andReturn('Message text');

        $this->sns->shouldReceive('send')
            ->atLeast()->once()
            ->with(Mockery::type(SnsMessage::class), Mockery::any());

        $this->channel->send($notifiable, $notification);
    }

    public function test_it_will_dispatch_an_event_in_case_of_an_invalid_message()
    {
        $notifiable = new NotifiableWithAttribute;

        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toSns')->andReturn(-1);

        $this->dispatcher->shouldReceive('dispatch')
            ->atLeast()->once()
            ->with(Mockery::type(NotificationFailed::class));

        $this->channel->send($notifiable, $notification);
    }

    public function test_it_will_send_a_sms_to_an_anonymous_notifiable()
    {
        $notification = Mockery::mock(Notification::class);
        $notification->shouldReceive('toSns')->andReturn('Message text');

        $phoneNumber = '+22222222222';
        $anonymousNotifiable = NotificationFacade::route('sns', $phoneNumber);

        $this->sns->shouldReceive('send')
            ->atLeast()->once()
            ->with(Mockery::type(SnsMessage::class), $phoneNumber);

        $this->channel->send($anonymousNotifiable, $notification);
    }
}

class Notifiable
{
    public $phone_number;

    public function routeNotificationFor()
    {
        //
    }
}

class NotifiableWithMethod
{
    public function routeNotificationFor()
    {
        return '+1111111111';
    }
}

class NotifiableWithAttribute
{
    public $phone_number = '+22222222222';

    public function routeNotificationFor()
    {
        //
    }
}
