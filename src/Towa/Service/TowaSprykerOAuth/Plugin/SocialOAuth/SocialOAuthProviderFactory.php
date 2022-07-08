<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth;

use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\ProviderConfiguration\KeycloakProviderConfiguration;
use Spryker\Service\Kernel\AbstractPlugin;

/**
 * @method \Towa\Service\TowaSprykerOauth\TowaSprykerOAuthConfig getConfig()
 */
class SocialOAuthProviderFactory extends AbstractPlugin
{
    /**
     * @return \KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface
     */
    public function createKeyCloakConfig(): ProviderConfiguratorInterface
    {
        return new KeycloakProviderConfiguration(
            $this->getConfig()->getKeyCloakConfig()
        );
    }
}
