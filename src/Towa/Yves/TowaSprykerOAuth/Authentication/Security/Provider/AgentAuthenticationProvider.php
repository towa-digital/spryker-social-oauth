<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Yves\TowaSprykerOAuth\Security\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PreAuthenticationGuardToken;
use Towa\Yves\TowaSprykerOAuth\Authentication\AgentAuthenticator;
use UnexpectedValueException;

class AgentAuthenticationProvider implements AuthenticationProviderInterface
{
    private AgentAuthenticator $agentAuthenticator;

    private UserProviderInterface $agentUserProvider;

    private UserChecker $userChecker;

    private string $providerKey;

    /**
     * @param \Towa\Yves\TowaSprykerOAuth\Authentication\AgentAuthenticator $agentAuthenticator
     * @param \Symfony\Component\Security\Core\User\UserProviderInterface $agentUserProvider
     * @param \Symfony\Component\Security\Core\User\UserChecker $userChecker
     */
    public function __construct(
        AgentAuthenticator $agentAuthenticator,
        UserProviderInterface $agentUserProvider,
        UserChecker $userChecker
    ) {
        $this->agentAuthenticator = $agentAuthenticator;
        $this->agentUserProvider = $agentUserProvider;
        $this->userChecker = $userChecker;
        $this->providerKey = 'agent';
    }

    /**
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @return bool
     */
    public function supports(TokenInterface $token): bool
    {
        return $token instanceof PreAuthenticationGuardToken;
    }

    /**
     * Attempts to authenticate a TokenInterface object.
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
     *
     * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     * @throws \UnexpectedValueException
     * @throws \Symfony\Component\Security\Core\Exception\BadCredentialsException
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\TokenInterface An authenticated TokenInterface instance, never null
     */
    public function authenticate(TokenInterface $token): TokenInterface
    {
        $user = $this->agentAuthenticator->getUser($token->getCredentials(), $this->agentUserProvider);

        if ($user === null) {
            $e = new UsernameNotFoundException(sprintf('Null returned from "%s::getUser()".', get_class($this->agentAuthenticator)));
            $e->setUsername($token->getUsername());

            throw $e;
        }

        if (!$user instanceof UserInterface) {
            throw new UnexpectedValueException(sprintf('The "%s::getUser()" method must return a UserInterface. You returned "%s".', get_class($this->agentAuthenticator), is_object($user) ? get_class($user) : gettype($user)));
        }

        $this->userChecker->checkPreAuth($user);

        if (!$this->agentAuthenticator->checkCredentials($token->getCredentials(), $user)) {
            throw new BadCredentialsException(sprintf('Authentication failed because "%s::checkCredentials()" did not return true.', get_class($this->agentAuthenticator)));
        }
        $this->userChecker->checkPostAuth($user);

        // turn the UserInterface into a TokenInterface
        $authenticatedToken = $this->agentAuthenticator->createAuthenticatedToken($user, $this->providerKey);
        if (!$authenticatedToken instanceof TokenInterface) {
            throw new UnexpectedValueException(sprintf('The "%s::createAuthenticatedToken()" method must return a TokenInterface. You returned "%s".', get_class($this->agentAuthenticator), is_object($authenticatedToken) ? get_class($authenticatedToken) : gettype($authenticatedToken)));
        }

        return $authenticatedToken;
    }
}
