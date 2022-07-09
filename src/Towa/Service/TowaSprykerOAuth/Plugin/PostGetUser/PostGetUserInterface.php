<?php

namespace Towa\Service\TowaSprykerOauth\Plugin\PostGetUser;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface PostGetUserInterface
{
    /**
     * @param UserInterface $user
     * @param ResourceOwnerInterface $resourceOwner
     *
     * @return mixed
     */
    public function execute(UserInterface $user, ResourceOwnerInterface $resourceOwner);
}
