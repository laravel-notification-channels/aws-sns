<?php

namespace NotificationChannels\AwsSns\Test;

use Aws\Result;
use Aws\Sns\SnsClient as SnsService;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use NotificationChannels\AwsSns\Sns;
use NotificationChannels\AwsSns\SnsMessage;

class SnsTest extends TestCase
{
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|SnsService
     */
    protected $snsService;

    /**
     * @var Dispatcher|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $dispatcher;

    /**
     * @var Sns
     */
    protected $sns;

    protected function setUp(): void
    {
        parent::setUp();

        $this->snsService = Mockery::mock(SnsService::class);
        $this->dispatcher = Mockery::mock(Dispatcher::class);

        $this->sns = new Sns($this->snsService);
    }

    public function test_it_can_send_a_promotional_sms_message_to_sns()
    {
        $message = new SnsMessage('Message text');

        $this->snsService->shouldReceive('publish')
            ->atLeast()
            ->once()
            ->with([
                'Message' => 'Message text',
                'PhoneNumber' => '+1111111111',
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Promotional',
                    ],
                ],
            ])
            ->andReturn(new Result);

        $this->sns->send($message, '+1111111111');
    }

    public function test_it_can_send_a_transactional_sms_message_to_sns()
    {
        $message = new SnsMessage(['body' => 'Message text', 'transactional' => true]);

        $this->snsService->shouldReceive('publish')
            ->atLeast()
            ->once()
            ->with([
                'Message' => 'Message text',
                'PhoneNumber' => '+22222222222',
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional',
                    ],
                ],
            ])
            ->andReturn(new Result);

        $this->sns->send($message, '+22222222222');
    }

    public function test_it_can_send_a_sms_message_with_sender_id()
    {
        $message = new SnsMessage(['body' => 'Message text', 'sender' => 'CompanyInc']);

        $this->snsService->shouldReceive('publish')
            ->atLeast()
            ->once()
            ->with([
                'Message' => 'Message text',
                'PhoneNumber' => '+33333333333',
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Promotional',
                    ],
                    'AWS.SNS.SMS.SenderID' => [
                        'DataType' => 'String',
                        'StringValue' => 'CompanyInc',
                    ],
                ],
            ])
            ->andReturn(new Result);

        $this->sns->send($message, '+33333333333');
    }
}
