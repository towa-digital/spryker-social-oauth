<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use Towa\Service\TowaSprykerOauth\Model\ClientRegistry;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOAuthConfigurationMap;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOAuthProviderFactory;
use Spryker\Service\Kernel\AbstractServiceFactory;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method \Towa\Service\TowaSprykerOauth\TowaSprykerOAuthConfig getConfig()
 */
class TowaSprykerOAuthServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOAuthProviderFactory
     */
    public function createSocialOAuthProviderFactory(): SocialOAuthProviderFactory
    {
        return new SocialOAuthProviderFactory();
    }

    /**
     * @return \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOAuthConfigurationMap
     */
    public function createSocialOauthConfigurationMap(): SocialOAuthConfigurationMap
    {
        return new SocialOAuthConfigurationMap($this->createSocialOAuthProviderFactory());
    }

    /**
     * @return \Towa\Service\TowaSprykerOauth\Model\ClientRegistry
     */
    public function createClientRegistry(): ClientRegistry
    {
        return new ClientRegistry(
            $this->getRequestStack(),
            $this->createSocialOauthConfigurationMap()->getOauthProviderServices()
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequestStack(): RequestStack
    {
        return $this->getProvidedDependency(TowaSprykerOAuthDependencyProvider::SERVICE_REQUEST_STACK);
    }
}
