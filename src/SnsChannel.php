<?php

namespace NotificationChannels\AwsSns;

use Aws\Result;
use Illuminate\Notifications\Notification;
use NotificationChannels\AwsSns\Exceptions\InvalidMessageException;
use NotificationChannels\AwsSns\Exceptions\InvalidReceiverException;

class SnsChannel
{
    /**
     * Create a new instance of the class.
     */
    public function __construct(protected Sns $sns)
    {
        //
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): Result
    {
        $destination = $this->getDestination($notifiable, $notification);
        $message = $this->getMessage($notifiable, $notification);

        return $this->sns->send($message, $destination);
    }

    /**
     * Get the phone number to send a notification to.
     *
     * @throws \NotificationChannels\AwsSns\Exceptions\InvalidReceiverException
     */
    protected function getDestination($notifiable, Notification $notification)
    {
        if ($to = $notifiable->routeNotificationFor('sns', $notification)) {
            return $to;
        }

        return $this->guessDestination($notifiable);
    }

    /**
     * Try to get the phone number from some commonly used attributes for that.
     *
     * @throws \NotificationChannels\AwsSns\Exceptions\InvalidReceiverException
     */
    protected function guessDestination($notifiable)
    {
        $commonAttributes = ['phone', 'phone_number', 'full_phone'];
        foreach ($commonAttributes as $attribute) {
            if (isset($notifiable->{$attribute})) {
                return $notifiable->{$attribute};
            }
        }

        throw InvalidReceiverException::make();
    }

    /**
     * Get the SNS Message object.
     *
     * @throws \NotificationChannels\AwsSns\Exceptions\InvalidMessageException
     */
    protected function getMessage($notifiable, Notification $notification): SnsMessage
    {
        $message = $notification->toSns($notifiable);
        if (is_string($message)) {
            return new SnsMessage($message);
        }

        if ($message instanceof SnsMessage) {
            return $message;
        }

        throw InvalidMessageException::make($message);
    }
}
