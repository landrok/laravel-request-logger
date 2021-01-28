Laravel Request Logger
======================

[![Build Status](https://travis-ci.org/landrok/language-detector.svg?branch=master)](https://travis-ci.org/landrok/language-detector)
[![Test Coverage](https://codeclimate.com/github/landrok/laravel-request-logger/badges/coverage.svg)](https://codeclimate.com/github/landrok/laravel-request-logger/coverage)
[![Code Climate](https://codeclimate.com/github/landrok/laravel-request-loggerbadges/gpa.svg)](https://codeclimate.com/github/landrok/language-detector)

Laravel Request Logger provides a middleware that logs HTTP requests
into a table.

It can be reconfigured to target specific requests or to log only
specified informations.

## What is logged ?

For each request, the following informations are stored.

__User__

- session_id
- user_id
- ip
- route
- route_params: optional

__Performances__

- duration
- mem_alloc

__HTTP stuff__

- method
- status_code
- url
- referer
- referer_host
- request_headers: optional
- response_headers: optional

__Device__

The following values are provided by the `jenssegers/agent` package.

- device
- os
- os_version
- browser
- browser_version
- is_desktop
- is_mobile
- is_tablet
- is_phone
- is_robot
- robot_name
- user_agent

__Miscellaneous__

- meta : this field is for custom logging. See RequestLogger::meta($value) 
- created_at


________________________________________________________________________

## Table of contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Contributing](#contributing)
- [License](#license)


________________________________________________________________________

## Requirements

Laravel Request Logger supports Laravel 6, 7, 8.

________________________________________________________________________

## Installation

```
composer require landrok/laravel-request-logger
```
________________________________________________________________________

## Configuration

You may log every calls (default), only routes that match some patterns
and only specified criterias.

You have to publish configuration file before.

`php artisan vendor:publish --provider="Landrok\Laravel\RequestLogger\RequestLoggerServiceProvider"`

The config file can be found at `config/requestlogger.php`.

________________________________________________________________________

## Meta::set(string $key, $value) 

This tool is made to log anything from anywhere in your code
(Controller, View, Service, etc...) into the `meta` column.

```php
use Landrok\Laravel\RequestLogger\Meta;

Meta::set($key, $value);

```

`$value` can be a string or an array or a serializable. It will be
stored as a JSON string.

Before using this method, you have to authorize this field in the config
file.
________________________________________________________________________

## Contributing

Feel free to open issues and make PR. Contributions are welcome.

________________________________________________________________________

## License

Laravel Request License is licensed under [The MIT License
(MIT)](LICENSE).
