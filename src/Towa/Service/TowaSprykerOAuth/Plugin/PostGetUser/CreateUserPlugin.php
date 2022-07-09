<?php

namespace Towa\Service\TowaSprykerOauth\Plugin\PostGetUser;

use Generated\Shared\Transfer\UserTransfer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Towa\Service\TowaSprykerOauth\Plugin\SocialOAuth\ResourceOwner\KeycloakResourceOwner;

class CreateUserPlugin implements PostGetUserInterface
{
    public function __construct($user)
    {

    }

    /**
     * @param UserInterface $user
     * @param KeycloakResourceOwner $resourceOwner
     *
     * @return mixed|void
     */
    public function execute(UserInterface $user, KeycloakResourceOwner $resourceOwner)
    {
        if (!$user->getUsername()) {
            $resourceOwnerData = $resourceOwner->toArray();
            $userTransfer = (new UserTransfer())
                ->setUsername($resourceOwner->getEmail())
                ->setFirstName($resourceOwnerData['given_name'])
                ->setLastName($resourceOwnerData['family_name'])
                ->setPassword(Uuid::uuid5(Uuid::NAMESPACE_OID, $resourceOwner->getEmail()))
                ->setStatus('active')
                ->setIsAgent(true);
            // FYI: locale is not yet returned and will be set to EN by default.

            $userTransfer = $this->userClient->createUser($userTransfer);
            $user = $this->createSecurityUser($userTransfer);
        }
    }
}
