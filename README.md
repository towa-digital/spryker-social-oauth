# TowaSprykerOauth Module
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)

A Spryker wrapper for [`knpuniversity/oauth2-client-bundle`](https://github.com/knpuniversity/oauth2-client-bundle)

# Installation

```bash
docker/sdk cli composer require "towa/spryker-social-oauth" --no-update
docker/sdk cli composer update "towa/spryker-social-oauth"
```

# Usage

## Configuration

In your `config_default.php` add your provider configuration. E.g.

```
// config-default.php

use Towa\Service\TowaSprykerOauth\TowaSprykerOauthConstants;

$config[TowaSprykerOauthConstants::TOWA_SPRYKER_AUTH_CONFIG] = [
    'github' => [
        'clientId'          => '{github-client-id}',
        'clientSecret'      => '{github-client-secret}',
        'redirectUri'       => 'https://example.com/callback-url',
    ],
    'google' => [
        'clientId'     => '{google-client-id}',
        'clientSecret' => '{google-client-secret}',
        'redirectUri'  => 'https://example.com/callback-url',
        'hostedDomain' => 'example.com', // optional; used to restrict access to users on your G Suite/Google Apps for Business accounts
    ]
]
```

> Make sure you have the correct client installed for your provider. Please refer to https://github.com/knpuniversity/oauth2-client-bundle#configuring-a-client

Add the Towa Namespace to the Core Namespaces.

```
// config_default.php

$config[KernelConstants::CORE_NAMESPACES] = [
    ...
    'Towa'
];
```

## Agent Configuration

Extend the SecurityApplicationPlugin on Project level.

```php
// Pyz\Yves\Security\Plugin\Application\SecurityApplicationPlugin

class SecurityApplicationPlugin extends SprykerSecurityApplicationPlugin
{
    public const SERVICE_SECURITY_AUTHENTICATION_PROVIDER_TOWA = 'security.authentication_provider.agent.dao';

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    protected function addListenerPrototypes(ContainerInterface $container): ContainerInterface
    {
        $container = $this->addTowaAuthenticator($container);
        $container = parent::addListenerPrototypes($container);

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    protected function addAuthenticationProviderPrototypes(ContainerInterface $container): ContainerInterface
    {
        $container = parent::addAuthenticationProviderPrototypes($container);
        $container = $this->addTowaAuthenticationProvider($container);

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    private function addTowaAuthenticator(ContainerInterface $container): ContainerInterface
    {
        $container->set(
            AgentAuthenticator::AUTHENTICATOR_KEY,
            new AgentAuthenticator(
                $this->getFactory()->getProvider(),
                $this->getFactory()->getProviderClient(),
                $this->getFactory()->getUserClient(),
            )
        );

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    protected function addTowaAuthenticationProvider(ContainerInterface $container): ContainerInterface
    {
        $container->set(
            static::SERVICE_SECURITY_AUTHENTICATION_PROVIDER_KEYCLOAK,
            new AgentAuthenticationProvider(
                $container->get(AgentAuthenticator::AUTHENTICATOR_KEY),
                $this->getFactory()->createAgentUserProvider(),
                new UserChecker()
            )
        );

        return $container;
    }
}
```

In the SecurityFactory you now need to add functions for the proper Provider and Client.

```
// Pyz\Yves\Security\Plugin\Application\SecurityApplicationPlugin

class SecurityFactory extends SprykerSecurityFactory
{
    /**
     * @return \Pyz\Yves\Security\Dependency\Client\SecurityToCustomerClientInterface
     */
    public function getCustomerClient(): SecurityToCustomerClientInterface
    {
        return $this->getProvidedDependency(SecurityDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @return \Pyz\Client\User\UserClientInterface
     */
    public function getUserClient(): UserClientInterface
    {
        return $this->getProvidedDependency(SecurityDependencyProvider::CLIENT_USER);
    }

    /**
     * @return \Towa\Service\TowaSprykerOauth\TowaSprykerOauthServiceInterface
     */
    public function getTowaOauthService(): TowaOauthServiceInterface
    {
        return $this->getProvidedDependency(SecurityDependencyProvider::SERVICE_TOWAOAUTH);
    }

    /**
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function getProvider(): AbstractProvider
    {
        return $this->getTowaOauthService()->getSocialOauthProvider('github'); // replace github with whichever provider you are using
    }

    /**
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    public function getProviderClient(): OAuth2ClientInterface
    {
        return $this->getTowaOauthService()->getSocialOauthClient('github'); // replace github with whichever provider you are using
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    public function createAgentUserProvider(): UserProviderInterface
    {
        return new AgentUserProvider();
    }
}
```

Adjust the DependencyProvider

```
class SecurityDependencyProvider extends SprykerSecurityDependencyProvider
{
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';
    public const SERVICE_TOWAOAUTH = 'SERVICE_TOWAOAUTH';
    public const CLIENT_USER = 'CLIENT_USER';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCustomerClient($container);
        $container = $this->addTowaOauthService($container);
        $container = $this->addUserClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $container->set(static::CLIENT_CUSTOMER, function (Container $container): SecurityToCustomerClientInterface {
            return new SecurityToCustomerClientBridge(
                $container->getLocator()->customer()->client()
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    private function addTowaOauthService(Container $container): Container
    {
        $container->set(static::SERVICE_TOWAOAUTH, function (Container $container): TowaOauthServiceInterface {
            return $container->getLocator()->TowaSprykerOauth()->service();
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    private function addUserClient(Container $container): Container
    {
        $container->set(static::CLIENT_USER, function (Container $container): UserClientInterface {
            return $container->getLocator()->user()->client();
        });

        return $container;
    }
}

```

## Yves Configuration

TODO

## Zed Configuration

TODO
