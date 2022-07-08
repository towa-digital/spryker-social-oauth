<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\Provider;

use Exception;
use Firebase\JWT\JWT;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\Exception\EncryptionConfigurationException;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\ResourceOwner\KeycloakResourceOwner;

class Keycloak extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * Keycloak URL, eg. http://localhost:8080/auth.
     *
     * @var string|null
     */
    public $authServerUrl;

    /**
     * Realm name, eg. demo.
     *
     * @var string|null
     */
    public $realm;

    /**
     * Encryption algorithm.
     *
     * You must specify supported algorithms for your application. See
     * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
     * for a list of spec-compliant algorithms.
     *
     * @var string|null
     */
    public $encryptionAlgorithm;

    /**
     * Encryption key.
     *
     * @var string|null
     */
    public $encryptionKey;

    /**
     * Constructs an OAuth 2.0 service provider.
     *
     * @param array $options An array of options to set on this provider.
     *     Options include `clientId`, `clientSecret`, `redirectUri`, and `state`.
     *     Individual providers may introduce more options, as needed.
     * @param array $collaborators An array of collaborators that may be used to
     *     override this provider's default behavior. Collaborators include
     *     `grantFactory`, `requestFactory`, `httpClient`, and `randomFactory`.
     *     Individual providers may introduce more collaborators, as needed.
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        if (isset($options['encryptionKeyPath'])) {
            $this->setEncryptionKeyPath($options['encryptionKeyPath']);
            unset($options['encryptionKeyPath']);
        }
        parent::__construct($options, $collaborators);
    }

    /**
     * Attempts to decrypt the given response.
     *
     * @param string|array|null $response
     *
     * @throws \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\Exception\EncryptionConfigurationException
     *
     * @return string|array|null
     */
    public function decryptResponse($response)
    {
        if (!is_string($response)) {
            return $response;
        }

        if (!$this->usesEncryption() || $this->encryptionKey === null) {
            throw EncryptionConfigurationException::undeterminedEncryption();
        }

        return json_decode(json_encode(JWT::decode(
            $response,
            $this->encryptionKey
        ), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/auth';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/userinfo';
    }

    /**
     * Builds the logout URL.
     *
     * @param array $options
     *
     * @return string Authorization URL
     */
    public function getLogoutUrl(array $options = [])
    {
        $base = $this->getBaseLogoutUrl();
        $params = $this->getAuthorizationParameters($options);
        $query = $this->getAuthorizationQuery($params);

        return $this->appendQuery($base, $query);
    }

    /**
     * Get logout url to logout of session token
     *
     * @return string
     */
    private function getBaseLogoutUrl()
    {
        return $this->getBaseUrlWithRealm() . '/protocol/openid-connect/logout';
    }

    /**
     * Creates base url from provider configuration.
     *
     * @return string
     */
    protected function getBaseUrlWithRealm()
    {
        return $this->authServerUrl . '/realms/' . $this->realm;
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return string[]
     */
    protected function getDefaultScopes()
    {
        return ['profile', 'email'];
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator, defaults to ','
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Check a provider response for errors.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param array|string $data Parsed response data
     *
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     *
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data['error'])) {
            $error = $data['error'] . ': ' . $data['error_description'];

            throw new IdentityProviderException($error, 0, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @return \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\ResourceOwner\KeycloakResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new KeycloakResourceOwner($response);
    }

    /**
     * Requests and returns the resource owner of given access token.
     *
     * @param \League\OAuth2\Client\Token\AccessToken $token
     *
     * @return \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\ResourceOwner\KeycloakResourceOwner
     */
    public function getResourceOwner(AccessToken $token)
    {
        $response = $this->fetchResourceOwnerDetails($token);

        // We are always getting an array. We have to check if it is
        // the array we created
        if (array_key_exists('jwt', $response)) {
            $response = $response['jwt'];
        }

        $response = $this->decryptResponse($response);

        return $this->createResourceOwner($response, $token);
    }

    /**
     * Updates expected encryption algorithm of Keycloak instance.
     *
     * @param string $encryptionAlgorithm
     *
     * @return $this
     */
    public function setEncryptionAlgorithm($encryptionAlgorithm)
    {
        $this->encryptionAlgorithm = $encryptionAlgorithm;

        return $this;
    }

    /**
     * Updates expected encryption key of Keycloak instance.
     *
     * @param string $encryptionKey
     *
     * @return $this
     */
    public function setEncryptionKey($encryptionKey)
    {
        $this->encryptionKey = $encryptionKey;

        return $this;
    }

    /**
     * Updates expected encryption key of Keycloak instance to content of given
     * file path.
     *
     * @param string $encryptionKeyPath
     *
     * @throws \Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\Exception\EncryptionConfigurationException
     *
     * @return $this
     */
    public function setEncryptionKeyPath($encryptionKeyPath)
    {
        try {
            $this->encryptionKey = file_get_contents($encryptionKeyPath);
        } catch (Exception $e) {
            throw new EncryptionConfigurationException();
        }

        return $this;
    }

    /**
     * Checks if provider is configured to use encryption.
     *
     * @return bool
     */
    public function usesEncryption()
    {
        return (bool)$this->encryptionAlgorithm && $this->encryptionKey;
    }

    /**
     * Parses the response according to its content-type header.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return array
     */
    protected function parseResponse(ResponseInterface $response)
    {
        // We have a problem with keycloak when the userinfo responses
        // with a jwt token
        // Because it just return a jwt as string with the header
        // application/jwt
        // This can't be parsed to a array
        // Dont know why this function only allow an array as return value...
        $content = (string)$response->getBody();
        $type = $this->getContentType($response);

        if (strpos($type, 'jwt') !== false) {
            // Here we make the temporary array
            return ['jwt' => $content];
        }

        return parent::parseResponse($response);
    }
}
