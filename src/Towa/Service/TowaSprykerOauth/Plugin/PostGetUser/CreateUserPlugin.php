<?php

namespace Pyz\Yves\Security\Plugin\Authentication;

use Generated\Shared\Transfer\UserTransfer;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Pyz\Client\User\UserClientInterface;
use Ramsey\Uuid\Uuid;
use SprykerShop\Yves\AgentPage\Plugin\Security\AgentPageSecurityPlugin;
use SprykerShop\Yves\AgentPage\Security\Agent;
use Symfony\Component\Security\Core\User\UserInterface;
use Towa\Service\TowaSprykerOauth\Plugin\PostGetUser\PostGetUserInterface;

class CreateUserPlugin implements PostGetUserInterface
{
    /**
     * @var \Pyz\Client\User\UserClientInterface
     */
    private UserClientInterface $userClient;

    public function __construct(UserClientInterface $userClient)
    {
        $this->userClient = $userClient;
    }

    /**
     * @param UserInterface $user
     * @param ResourceOwnerInterface $resourceOwner
     *
     * @return mixed|void
     */
    public function execute(UserInterface $user, ResourceOwnerInterface $resourceOwner)
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
}
