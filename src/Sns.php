<?php

namespace NotificationChannels\AwsSns;

use Aws\Result;
use Aws\Sns\SnsClient;

class Sns
{
    /**
     * Create a new instance of the class.
     */
    public function __construct(protected SnsClient $sns)
    {
        //
    }

    /**
     * Send the message to the given E.164 destination phone number.
     *
     * @throws Aws\Exception\AwsException
     */
    public function send(SnsMessage $message, string $destination): Result
    {
        $attributes = [
            'AWS.SNS.SMS.SMSType' => [
                'DataType' => 'String',
                'StringValue' => $message->getDeliveryType(),
            ],
        ];

        if (! empty($message->getSender())) {
            $attributes += [
                'AWS.SNS.SMS.SenderID' => [
                    'DataType' => 'String',
                    'StringValue' => $message->getSender(),
                ],
            ];
        }

        if (! empty($message->getOriginationNumber())) {
            $attributes += [
                'AWS.MM.SMS.OriginationNumber' => [
                    'DataType' => 'String',
                    'StringValue' => $message->getOriginationNumber(),
                ],
            ];
        }

        $parameters = [
            'Message' => $message->getBody(),
            'PhoneNumber' => $destination,
            'MessageAttributes' => $attributes,
        ];

        return $this->sns->publish($parameters);
    }
}
