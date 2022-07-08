<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\ProviderConfiguration;

use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\KeycloakProviderConfigurator;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\Provider\Keycloak;

class KeycloakProviderConfiguration extends KeycloakProviderConfigurator
{
    /**
     * @var array
     */
    private array $config;

    /**
     * possible parameters for $config:
     * - clientId
     * - clientSecret
     * - authServerUrl
     * - realm
     * - encryptionAlgorithm
     * - encryptionKeyPath
     * - encryptionKey
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public function getProviderClass(array $config): string
    {
        return Keycloak::class;
    }

    /**
     * $config is unused
     *
     * @param array $config
     *
     * @return array
     */
    public function getProviderOptions(array $config)
    {
        return $this->config;
    }
}
