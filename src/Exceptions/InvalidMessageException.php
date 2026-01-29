<?php

namespace NotificationChannels\AwsSns\Exceptions;

use Exception;
use NotificationChannels\AwsSns\SnsMessage;

class InvalidMessageException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public static function make($message)
    {
        $type = is_object($message) ? get_class($message) : gettype($message);

        return new static(
            'Notification was not sent. The message should be an instance of `'.SnsMessage::class."` and a `{$type}` was given."
        );
    }
}
