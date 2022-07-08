<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\Exception;

use Exception;
use RuntimeException;

class EncryptionConfigurationException extends Exception
{
    /**
     * Returns properly formatted exception when response decryption fails.
     *
     * @return \RuntimeException
     */
    public static function undeterminedEncryption()
    {
        return new RuntimeException(
            'The given response may be encrypted and sufficient ' .
            'encryption configuration has not been provided.',
            400
        );
    }
}
