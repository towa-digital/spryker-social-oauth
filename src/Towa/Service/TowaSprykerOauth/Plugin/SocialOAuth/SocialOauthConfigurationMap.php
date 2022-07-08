<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth;

class SocialOauthConfigurationMap
{
    /**
     * @var \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOauthProviderFactory
     */
    private SocialOauthProviderFactory $socialOauthProviderFactory;

    /**
     * @param \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOauthProviderFactory $socialOauthProviderFactory
     */
    public function __construct(SocialOauthProviderFactory $socialOauthProviderFactory)
    {
        $this->socialOauthProviderFactory = $socialOauthProviderFactory;
    }

    /**
     * @return array
     */
    public function getOauthProviderServices(): array
    {
        return [
            'keycloak' => $this->socialOauthProviderFactory->createKeyCloakConfig(),
        ];
    }
}
