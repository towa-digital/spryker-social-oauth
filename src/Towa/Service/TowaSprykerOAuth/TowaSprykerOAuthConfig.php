<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth;

use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\AmazonProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\AppIdProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\AppleProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\Auth0ProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\AzureProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\BitbucketProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\BoxProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\BuddyProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\BufferProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\CanvasLMSProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\CleverProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\DevianArtProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\DigitalOceanProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\DiscordProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\DribbbleProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\DropboxProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\DrupalProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ElanceProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\EventbriteProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\EveOnlineProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\FacebookProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\FitbitProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\FoursquareProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\FusionAuthProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\GenericProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\GeocachingProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\GithubProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\GitlabProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\GoogleProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\HeadHunterProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\HerokuProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\InstagramProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\JiraProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\KeycloakProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\LinkedInProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\MailRuProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\MicrosoftProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\MollieProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\OdnoklassnikiProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\OktaProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\PaypalProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\PsnProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\SalesforceProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\SlackProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\SpotifyProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\StravaProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\StripeProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\SymfonyConnectProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\TwitchHelixProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\TwitchProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\UberProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\UnsplashProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\VimeoProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\VKontakteProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\WaveProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\YahooProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\YandexProviderConfigurator;
use KnpU\OAuth2ClientBundle\DependencyInjection\Providers\ZendeskProviderConfigurator;
use Spryker\Service\Kernel\AbstractBundleConfig;

/**
 * @method \Towa\Service\TowaSprykerOAuth\TowaSprykerOAuthServiceFactory getFactory()
 */
class TowaSprykerOAuthConfig extends AbstractBundleConfig
{

    /** @var array */
    private static $supportedProviderTypes = [
        'amazon' => AmazonProviderConfigurator::class,
        'appid' => AppIdProviderConfigurator::class,
        'apple' => AppleProviderConfigurator::class,
        'auth0' => Auth0ProviderConfigurator::class,
        'azure' => AzureProviderConfigurator::class,
        'bitbucket' => BitbucketProviderConfigurator::class,
        'box' => BoxProviderConfigurator::class,
        'buddy' => BuddyProviderConfigurator::class,
        'buffer' => BufferProviderConfigurator::class,
        'canvas_lms' => CanvasLMSProviderConfigurator::class,
        'clever' => CleverProviderConfigurator::class,
        'devian_art' => DevianArtProviderConfigurator::class,
        'digital_ocean' => DigitalOceanProviderConfigurator::class,
        'discord' => DiscordProviderConfigurator::class,
        'dribbble' => DribbbleProviderConfigurator::class,
        'dropbox' => DropboxProviderConfigurator::class,
        'drupal' => DrupalProviderConfigurator::class,
        'elance' => ElanceProviderConfigurator::class,
        'eve_online' => EveOnlineProviderConfigurator::class,
        'eventbrite' => EventbriteProviderConfigurator::class,
        'facebook' => FacebookProviderConfigurator::class,
        'fitbit' => FitbitProviderConfigurator::class,
        'four_square' => FoursquareProviderConfigurator::class,
        'fusion_auth' => FusionAuthProviderConfigurator::class,
        'geocaching' => GeocachingProviderConfigurator::class,
        'github' => GithubProviderConfigurator::class,
        'gitlab' => GitlabProviderConfigurator::class,
        'google' => GoogleProviderConfigurator::class,
        'headhunter' => HeadHunterProviderConfigurator::class,
        'heroku' => HerokuProviderConfigurator::class,
        'instagram' => InstagramProviderConfigurator::class,
        'jira' => JiraProviderConfigurator::class,
        'keycloak' => KeycloakProviderConfigurator::class,
        'linkedin' => LinkedInProviderConfigurator::class,
        'mail_ru' => MailRuProviderConfigurator::class,
        'microsoft' => MicrosoftProviderConfigurator::class,
        'mollie' => MollieProviderConfigurator::class,
        'odnoklassniki' => OdnoklassnikiProviderConfigurator::class,
        'okta' => OktaProviderConfigurator::class,
        'paypal' => PaypalProviderConfigurator::class,
        'psn' => PsnProviderConfigurator::class,
        'salesforce' => SalesforceProviderConfigurator::class,
        'slack' => SlackProviderConfigurator::class,
        'spotify' => SpotifyProviderConfigurator::class,
        'symfony_connect' => SymfonyConnectProviderConfigurator::class,
        'strava' => StravaProviderConfigurator::class,
        'stripe' => StripeProviderConfigurator::class,
        'twitch' => TwitchProviderConfigurator::class,
        'twitch_helix' => TwitchHelixProviderConfigurator::class,
        'uber' => UberProviderConfigurator::class,
        'unsplash' => UnsplashProviderConfigurator::class,
        'vimeo' => VimeoProviderConfigurator::class,
        'vkontakte' => VKontakteProviderConfigurator::class,
        'wave' => WaveProviderConfigurator::class,
        'yahoo' => YahooProviderConfigurator::class,
        'yandex' => YandexProviderConfigurator::class,
        'zendesk' => ZendeskProviderConfigurator::class,
        'generic' => GenericProviderConfigurator::class,
    ];

    /**
     * @return array
     */
    public function getAllSupportedTypes(): array
    {
        return self::$supportedProviderTypes;
    }

    /**
     * @return array
     */
    public function getAuthConfig(): array
    {
        return $this->get(TowaSprykerOAuthConstants::TOWA_SPRYKER_AUTH_CONFIG, []);
    }
}
