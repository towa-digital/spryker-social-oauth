<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Yves\TowaSprykerOAuth\Authentication;

use Exception;
use Generated\Shared\Transfer\UserTransfer;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use PostGetUserInterface;
use Pyz\Client\User\UserClientInterface;
use Pyz\Service\TowaOauth\Plugin\SocialOAuth\Provider\Keycloak;
use Pyz\Yves\AgentPage\Plugin\Authentication\AgentPageSecurityPlugin;
use Pyz\Yves\AgentPage\Plugin\Handler\AgentAuthenticationSuccessHandler;
use Pyz\Yves\AgentPage\Plugin\Router\AgentPageRouteProviderPlugin;
use Ramsey\Uuid\Uuid;
use SprykerShop\Yves\AgentPage\Plugin\Handler\AgentAuthenticationFailureHandler;
use SprykerShop\Yves\AgentPage\Security\Agent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Towa\Service\TowaSprykerOauth\Plugin\PostGetUser\PostGetUserInterface;

class AgentKeycloakAuthenticator extends SocialAuthenticator
{
    public const AUTHENTICATOR_KEY = 'AGENT_KEYCLOAK_AUTHENTICATOR';

    private Keycloak $keycloakProvider;

    private OAuth2ClientInterface $keycloakClient;

    private UserClientInterface $userClient;

    /**
     * @var PostGetUserInterface[] $parameterFilters
     */
    private array $postGetUserPlugins;

    /**
     * @param \Pyz\Service\TowaOauth\Plugin\SocialOAuth\Provider\Keycloak $keycloakProvider
     * @param \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface $keycloakClient
     * @param \Pyz\Client\User\UserClientInterface $userClient
     */
    public function __construct(
        Keycloak $keycloakProvider,
        OAuth2ClientInterface $keycloakClient,
        UserClientInterface $userClient,
        array $postGetUserPlugins = []

    ) {
        $this->keycloakClient = $keycloakClient;
        $this->keycloakProvider = $keycloakProvider;
        $this->userClient = $userClient;
        $this->postGetUserPlugins = $postGetUserPlugins;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, ?AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            AgentPageRouteProviderPlugin::ROUTE_NAME_LOGIN,
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return $request->query->get('code') &&
            $request->query->get('state') &&
            $request->query->get('session_state') &&
            str_contains($request->getPathInfo(), AgentPageRouteProviderPlugin::ROUTE_NAME_AGENT_LOGIN_CHECK);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        $code = $request->query->get('code');

        return $this->keycloakProvider->getAccessToken(
            'authorization_code',
            ['code' => $code]
        );
    }

    /**
     * @inheritDoc
     *
     * @throws \Exception
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var ResourceOwnerInterface $resourceOwner */
        $resourceOwner = $this->keycloakClient->fetchUserFromToken($credentials);

        if (!$resourceOwner->getEmail()) {
            throw new Exception('No email given for resourceOwner');
        }

        $user = $userProvider->loadUserByUsername($resourceOwner->getEmail());

        foreach($this->postGetUserPlugins as $postGetUserPlugin) {
            $user = $postGetUserPlugin->execute($user, $resourceOwner);
        }

        return $user;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function createSecurityUser(UserTransfer $userTransfer): UserInterface
    {
        return new Agent(
            $userTransfer,
            [AgentPageSecurityPlugin::ROLE_AGENT, AgentPageSecurityPlugin::ROLE_ALLOWED_TO_SWITCH]
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return (new AgentAuthenticationFailureHandler())->onAuthenticationFailure($request, $exception);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return (new AgentAuthenticationSuccessHandler())->onAuthenticationSuccess($request, $token);
    }
}
