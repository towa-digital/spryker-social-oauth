# TowaSprykerOAuth Module
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

A Spryker wrapper for [`knpuniversity/oauth2-client-bundle`](https://github.com/knpuniversity/oauth2-client-bundle)

# Installation

```bash
docker/sdk cli composer require "towa/spryker-social-oauth" --no-update
docker/sdk cli composer update "towa/spryker-social-oauth"
```

# Usage

## Configuration

In your `config_default.php` add your provider configuration. E.g.

```
// config-default.php

use Towa\Service\TowaSprykerOAuth\TowaSprykerOAuthConstants;

$config[TowaSprykerOAuthConstants::TOWA_SPRYKER_AUTH_CONFIG] = [
    'github' => [
        'clientId'          => '{github-client-id}',
        'clientSecret'      => '{github-client-secret}',
        'redirectUri'       => 'https://example.com/callback-url',
    ],
    'google' => [
        'clientId'     => '{google-client-id}',
        'clientSecret' => '{google-client-secret}',
        'redirectUri'  => 'https://example.com/callback-url',
        'hostedDomain' => 'example.com', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
    ]
]
```

> Make sure you have the correct client installed for your provider. Please refer to https://github.com/knpuniversity/oauth2-client-bundle#configuring-a-client

## Agent Configuration

TODO
