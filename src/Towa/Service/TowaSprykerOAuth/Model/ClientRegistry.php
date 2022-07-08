<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Model;

use InvalidArgumentException;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use Symfony\Component\HttpFoundation\RequestStack;

class ClientRegistry
{
    /**
     * @var \KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface[]
     */
    private array $serviceMap;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private RequestStack $requestStack;

    /**
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface[] $serviceMap
     */
    public function __construct(
        RequestStack $requestStack,
        array $serviceMap = []
    ) {
        $this->serviceMap = $serviceMap;
        $this->requestStack = $requestStack;
    }

    /**
     * Easy accessor for client objects.
     *
     * @param string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    public function getClient(string $key): OAuth2ClientInterface
    {
        if (isset($this->serviceMap[$key])) {
            $client = $this->createClient($this->serviceMap[$key]);
            if (!$client instanceof OAuth2ClientInterface) {
                throw new InvalidArgumentException(sprintf('Somehow the "%s" client is not an instance of OAuth2ClientInterface.', $key));
            }

            return $client;
        }

        throw new InvalidArgumentException(sprintf('There is no OAuth2 client called "%s". Available are: %s', $key, implode(', ', array_keys($this->serviceMap))));
    }

    /**
     * @param string $key
     *
     * @throws \InvalidArgumentException
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    public function getProvider(string $key): AbstractProvider
    {
        if (isset($this->serviceMap[$key])) {
            return $this->createProvider($this->serviceMap[$key]);
        }

        throw new InvalidArgumentException(sprintf('There is no OAuth2 client called "%s". Available are: %s', $key, implode(', ', array_keys($this->serviceMap))));
    }

    /**
     * @param \KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface $providerConfig
     *
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface
     */
    private function createClient(ProviderConfiguratorInterface $providerConfig): OAuth2ClientInterface
    {
        $clientClass = $providerConfig->getClientClass([]);
        $providerOptions = $providerConfig->getProviderOptions([]);
        $providerClass = $providerConfig->getProviderClass([]);
        $provider = new $providerClass($providerOptions);

        return new $clientClass($provider, $this->requestStack);
    }

    /**
     * @param \KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ProviderConfiguratorInterface $providerConfigurator
     *
     * @return \League\OAuth2\Client\Provider\AbstractProvider
     */
    private function createProvider(ProviderConfiguratorInterface $providerConfigurator): AbstractProvider
    {
        $providerClass = $providerConfigurator->getProviderClass([]);
        $providerOptions = $providerConfigurator->getProviderOptions([]);

        return new $providerClass($providerOptions);
    }
}
