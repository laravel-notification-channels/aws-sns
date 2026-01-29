<?php

namespace NotificationChannels\AwsSns\Exceptions;

use Exception;

class InvalidReceiverException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public static function make()
    {
        return new static(
            'The notifiable did not have a receiving phone number. Add a routeNotificationForSns method or one of the conventional attributes to your notifiable.'
        );
    }
}
