<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use Towa\Shared\TowaSprykerOauth\TowaSprykerOauthConstants;
use Spryker\Service\Kernel\AbstractBundleConfig;

/**
 * @method \Towa\Service\TowaSprykerOauth\TowaSprykerOauthServiceFactory getFactory()
 */
class TowaSprykerOauthConfig extends AbstractBundleConfig
{
    /**
     * @return array
     */
    public function getKeyCloakConfig(): array
    {
        return $this->get(TowaSprykerOauthConstants::KEYCLOAK_CONFIG, []);
    }
}
