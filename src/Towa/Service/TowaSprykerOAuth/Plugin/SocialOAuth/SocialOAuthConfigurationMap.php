<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth;

class SocialOAuthConfigurationMap
{
    /**
     * @var \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\SocialOAuthProviderFactory
     */
    private SocialOAuthProviderFactory $socialOauthProviderFactory;

    /**
     * @param SocialOAuthProviderFactory $socialOauthProviderFactory
     */
    public function __construct(SocialOAuthProviderFactory $socialOauthProviderFactory)
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
