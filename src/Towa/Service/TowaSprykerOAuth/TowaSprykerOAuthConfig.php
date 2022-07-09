<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use Towa\Service\TowaSprykerOAuth\TowaSprykerOAuthConstants;
use Spryker\Service\Kernel\AbstractBundleConfig;

/**
 * @method \Towa\Service\TowaSprykerOAuth\TowaSprykerOAuthServiceFactory getFactory()
 */
class TowaSprykerOAuthConfig extends AbstractBundleConfig
{
    /**
     * @return array
     */
    public function getKeyCloakConfig(): array
    {
        return $this->get(TowaSprykerOAuthConstants::KEYCLOAK_CONFIG, []);
    }
}
