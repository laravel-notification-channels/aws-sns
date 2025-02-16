<?php

namespace NotificationChannels\AwsSns;

use Aws\Result;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\AwsSns\Exceptions\CouldNotSendNotification;

class SnsChannel
{
    public function __construct(protected Sns $sns, protected Dispatcher $events)
    {
        //
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): ?Result
    {
        try {
            $destination = $this->getDestination($notifiable, $notification);
            $message = $this->getMessage($notifiable, $notification);

            return $this->sns->send($message, $destination);
        } catch (Exception $e) {
            $this->events->dispatch(new NotificationFailed(
                $notifiable,
                $notification,
                'sns',
                ['message' => $e->getMessage(), 'exception' => $e]
            ));

            return null;
        }
    }

    /**
     * Get the phone number to send a notification to.
     *
     * @throws CouldNotSendNotification
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
     * @throws CouldNotSendNotification
     */
    protected function guessDestination($notifiable)
    {
        $commonAttributes = ['phone', 'phone_number', 'full_phone'];
        foreach ($commonAttributes as $attribute) {
            if (isset($notifiable->{$attribute})) {
                return $notifiable->{$attribute};
            }
        }

        throw CouldNotSendNotification::invalidReceiver();
    }

    /**
     * Get the SNS Message object.
     *
     * @throws CouldNotSendNotification
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

        throw CouldNotSendNotification::invalidMessageObject($message);
    }
}
