<?php

declare(strict_types=1);

namespace App\Utility;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use App\Entity\User;
use App\Entity\Profile;
use App\Exception\UnauthorizedException;

class Context
{

    public function __construct(
        private TokenStorageInterface $tokenStorage
    ) {}

    public function getUser(): User
    {
        $result = $this->getUserSafe();
        if ($result === null) {
            throw new UnauthorizedException();
        }
        return $result;
    }

    public function getUserSafe(): ?User
    {
        $result = null;
        $token = $this->tokenStorage->getToken();
        if ($token) {
            /** @var User */
            $result = $token->getUser();
        }
        return $result;
    }

    public function getProfile(): Profile
    {
        $result = $this->getProfileSafe();
        if ($result === null) {
            throw new UnauthorizedException();
        }
        return $result;
    }

    public function getProfileSafe(): ?Profile
    {
        return $this->getUserSafe()?->getProfile();
    }

}
