# Amazon Simple Notification Service (AWS SNS) notification channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/aws-sns.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/aws-sns)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/aws-sns/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/aws-sns)
[![StyleCI](https://styleci.io/repos/65772445/shield)](https://styleci.io/repos/65772445)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/aws-sns.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/aws-sns)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/aws-sns/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/aws-sns/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/aws-sns.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/aws-sns)

This package makes it easy to send notifications using [AWS SNS](https://aws.amazon.com/pt/sns/) with Laravel framework.
Since Laravel already ships with SES email support, this package focuses on sending only SMS notifications for now.
More advanced features like support for topics could be added in the future.


## Contents

- [Installation](#installation)
	- [Setting up the AwsSns service](#setting-up-the-aws-sns-service)
- [Usage](#usage)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

``` bash
composer require laravel-notification-channels/aws-sns --update-with-dependencies
```

### Setting up the AWS SNS service

Add your AWS key ID, secret and default region to your `config/services.php`:

```php
<?php

return [

    // ...

    'sns' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
```

## Usage

Now you can use the channel in your `via()` method inside the notification:

```php
<?php

use NotificationChannels\AwsSns\SnsChannel;
use NotificationChannels\AwsSns\SnsMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [SnsChannel::class];
    }

    public function toSns($notifiable)
    {
        // You can just return a plain string:
        return "Your {$notifiable->service} account was approved!";
        
        // OR explicitly return a SnsMessage object passing the message body:
        return new SnsMessage("Your {$notifiable->service} account was approved!");
        
        // OR return a SnsMessage passing the arguments via `create()` or `__construct()`:
        return SnsMessage::create([
            'body' => "Your {$notifiable->service} account was approved!",
            'transactional' => true,
            'sender' => 'MyBusiness',
        ]);

        // OR create the object with or without arguments and then use the fluent API:
        return SnsMessage::create()
            ->body("Your {$notifiable->service} account was approved!")
            ->promotional()
            ->sender('MyBusiness');
    }
}
```

In order to let your Notification know which phone are you sending to, the channel 
will look for the `phone`, `phone_number` or `full_phone` attribute of the 
Notifiable model. If you want to override this behaviour, add the 
`routeNotificationForSns` method to your Notifiable model.

```php
<?php

use Illuminate\Notifications\Notifiable;

class SomeModel {
    use Notifiable;

    public function routeNotificationForSns($notification)
    {
        return '+1234567890';
    }
}
```

### Available SnsMessage methods

- `create([])`: Accepts an array of key-values where the keys corresponds to the methods below and the values are passed as parameters;
- `body('')`: Accepts a string value for the notification body. Messages with more than 140 characters will be split into multiple messages by SNS without breaking any words;
- `promotional(bool)`: Sets the delivery type as promotional (default). Optimizes the delivery for lower costs;
- `transactional(bool)`: Sets the delivery type as transactional. Optimizes the delivery to achieve the highest reliability (it also costs more); 
- `sender(string)`: Up to 11 characters with no spaces, that is displayed as the sender on the receiving device. [Support varies by country](https://docs.aws.amazon.com/sns/latest/dg/sns-supported-regions-countries.html);
- `originationNumber(string)`: A numeric string that identifies an SMS message sender's phone number. Support may not be available in your country, see the [AWS SNS Origination number docs](https://docs.aws.amazon.com/sns/latest/dg/channels-sms-originating-identities-origination-numbers.html).

More information about the SMS Attributes can be found on the [AWS SNS Docs](https://docs.aws.amazon.com/pt_br/sdk-for-php/v3/developer-guide/sns-examples-sending-sms.html#get-sms-attributes).
It's important to know that the attributes set on the message will override the
default ones configured in your AWS account. 

## Exception handling
Exceptions are not thrown by the package in order to give other channels a chance to work properly. Instead, a `Illuminate\Notifications\Events\NotificationFailed` event is dispatched. For debugging purposes you may listen to this event in the `boot` method of `EventServiceProvider.php`.

```php
Event::listen(function (\Illuminate\Notifications\Events\NotificationFailed $event) {
    //Dump and die
    dd($event);
    
    //or log the event
    Log::error('SNS error', $event->data)
});
```

## Laravel Vapor
By default [Laravel Vapor](https://vapor.laravel.com/) creates a role `laravel-vapor-role` in AWS which does not have permission to send SMS via SNS. This results in SMS being sent successfully in local but will not be sent on a Vapor environment. Note that no exception will be thrown as described above.

In the AWS console, navigate to Identity and Access Management (IAM) and click on roles. Select `laravel-vapor-role` then add the `AmazonSNSFullAccess` policy to enable sending in Vapor.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email claudson@outlook.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Claudson Martins](https://github.com/claudsonm)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
