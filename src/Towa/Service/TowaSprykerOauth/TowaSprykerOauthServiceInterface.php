<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;

/**
 * @method \Towa\Service\TowaSprykerOauth\TowaSprykerOauthServiceFactory getFactory()
 */
interface TowaSprykerOauthServiceInterface
{
    /**
     * Specifications:
     * - Retrieves a social client by key
     *
     * @api
     *
     * @param string $key
     *
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    public function getSocialOAuthClient(string $key): OAuth2ClientInterface;

    /**
     * Specifications:
     * - Retrievs a social provider by key
     *
     * @api
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getSocialOauthProvider(string $key);
}
