<?php

namespace NotificationChannels\AwsSns;

class SnsMessage
{
    const PROMOTIONAL_SMS_TYPE = 'Promotional';

    const TRANSACTIONAL_SMS_TYPE = 'Transactional';

    /**
     * The body of the message.
     *
     * @var string
     */
    protected $body = '';

    /**
     * The delivery type of the message.
     *
     * @var bool
     */
    protected $promotional = true;

    /**
     * The sender identification of the message.
     *
     * @var string
     */
    protected $sender = '';

    public function __construct($content)
    {
        if (is_string($content)) {
            $this->body($content);
        }

        if (is_array($content)) {
            foreach ($content as $property => $value) {
                if (method_exists($this, $property)) {
                    $this->{$property}($value);
                }
            }
        }
    }

    /**
     * Creates a new instance of the message.
     *
     * @return SnsMessage
     */
    public static function create(array $data = [])
    {
        return new self($data);
    }

    /**
     * Sets the message body.
     *
     * @return $this
     */
    public function body(string $content)
    {
        $this->body = trim($content);

        return $this;
    }

    /**
     * Get the message body.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the message delivery type as promotional.
     *
     * @return $this
     */
    public function promotional(bool $active = true)
    {
        $this->promotional = $active;

        return $this;
    }

    /**
     * Sets the message delivery type as transactional.
     *
     * @return $this
     */
    public function transactional(bool $active = true)
    {
        $this->promotional = ! $active;

        return $this;
    }

    /**
     * Get the message delivery type.
     *
     * @return string
     */
    public function getDeliveryType()
    {
        return $this->promotional ? self::PROMOTIONAL_SMS_TYPE : self::TRANSACTIONAL_SMS_TYPE;
    }

    /**
     * Sets the message sender identification.
     *
     * @return $this
     */
    public function sender(string $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get the message sender identification.
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }
}
