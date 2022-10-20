<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use Spryker\Service\Kernel\AbstractService;

/**
 * @method \Towa\Service\TowaSprykerOauth\TowaSprykerOauthServiceFactory getFactory()
 */
class TowaSprykerOauthService extends AbstractService implements TowaSprykerOauthServiceInterface
{
    /**
     * @param string $key
     *
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    public function getSocialOauthClient(string $key): OAuth2ClientInterface
    {
        return $this->getFactory()
            ->createClientRegistry()
            ->getClient($key);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getSocialOauthProvider(string $key)
    {
        return $this->getFactory()
            ->createClientRegistry()
            ->getProvider($key);
    }
}
