<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use Towa\Service\TowaSprykerOauth\Model\ClientRegistry;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOauthConfigurationMap;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOauthProviderFactory;
use Spryker\Service\Kernel\AbstractServiceFactory;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method \Towa\Service\TowaSprykerOauth\TowaSprykerOauthConfig getConfig()
 */
class TowaSprykerOauthServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOauthProviderFactory
     */
    public function createSocialOAuthProviderFactory(): SocialOauthProviderFactory
    {
        return new SocialOauthProviderFactory();
    }

    /**
     * @return \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOauthConfigurationMap
     */
    public function createSocialOauthConfigurationMap(): SocialOauthConfigurationMap
    {
        return new SocialOauthConfigurationMap($this->createSocialOAuthProviderFactory());
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
        return $this->getProvidedDependency(TowaSprykerOauthDependencyProvider::SERVICE_REQUEST_STACK);
    }
}
