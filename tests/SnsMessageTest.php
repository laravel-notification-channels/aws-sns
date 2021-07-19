<?php

namespace NotificationChannels\AwsSns\Test;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use NotificationChannels\AwsSns\SnsMessage;
use PHPUnit\Framework\TestCase;

class SnsMessageTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_can_accept_a_plain_string_when_constructing_a_message()
    {
        $message = new SnsMessage('Do not touch my booty');
        $this->assertEquals('Do not touch my booty', $message->getBody());
    }

    /** @test */
    public function it_can_accept_some_initial_content_when_constructing_a_message()
    {
        $message = new SnsMessage(['body' => 'My message body']);
        $this->assertEquals('My message body', $message->getBody());
    }

    /** @test */
    public function it_provides_a_create_method()
    {
        $message = SnsMessage::create(['body' => 'My body from create']);
        $this->assertEquals('My body from create', $message->getBody());
    }

    /** @test */
    public function the_body_content_can_be_set_using_a_proper_method()
    {
        $message = SnsMessage::create();
        $this->assertEmpty($message->getBody());
        $message->body('The brand new body');
        $this->assertEquals('The brand new body', $message->getBody());
    }

    /** @test */
    public function the_default_sms_delivery_type_is_promotional()
    {
        $message = SnsMessage::create();
        $this->assertEquals('Promotional', $message->getDeliveryType());
    }

    /** @test */
    public function the_sms_delivery_type_can_be_changed_using_a_proper_method()
    {
        $message = SnsMessage::create()->transactional();
        $this->assertEquals('Transactional', $message->getDeliveryType());
    }

    /** @test */
    public function the_sms_delivery_type_can_be_explicitly_as_promotional()
    {
        $message = SnsMessage::create()->promotional();
        $this->assertEquals('Promotional', $message->getDeliveryType());
    }

    /** @test */
    public function the_default_sms_sender_id_is_empty()
    {
        $message = SnsMessage::create();
        $this->assertEmpty($message->getSender());
    }

    /** @test */
    public function the_sms_sender_id_can_be_changed_using_a_proper_method()
    {
        $message = SnsMessage::create()->sender('Test');
        $this->assertEquals('Test', $message->getSender());
    }

    /** @test */
    public function it_can_accept_all_the_contents_when_constructing_a_message()
    {
        $message = SnsMessage::create([
            'body' => 'My mass body',
            'transactional' => true,
            'sender' => 'Test',
        ]);
        $this->assertEquals('My mass body', $message->getBody());
        $this->assertEquals('Transactional', $message->getDeliveryType());
        $this->assertEquals('Test', $message->getSender());
    }

    /** @test */
    public function it_can_send_sms_message_with_origination_number()
    {
        $originationNumber = '+13347814073';
        $message = SnsMessage::create([
            'body' => 'Message text',
            'sender' => 'Test',
            'originationNumber' => $originationNumber,
        ]);

        $this->assertEquals('Message text', $message->getBody());
        $this->assertEquals('Test', $message->getSender());
        $this->assertEquals($originationNumber, $message->getOriginationNumber());
    }
}
