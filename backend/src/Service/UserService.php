<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\User;
use App\Exception\UnauthorizedException;
use App\Mapper\UserMapper;
use App\Repository\UserRepository;
use App\Utility\Context;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private Context $context,
        private UserMapper $userMapper,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private JWTTokenManagerInterface $tokenManager,
    ) {
    }

    private function save(UserDto $data, ?User $user = null): User
    {
        $result = $this->userMapper->mapDtoToEntity($data, $user);
        if ($data->password !== null) {
            $result->setPassword($this->passwordHasher->hashPassword($result, $data->password));
        }
        $this->userRepository->save($result);
        return $result;
    }

    private function toDto(User $user): UserDto
    {
        $result = $this->userMapper->mapEntityToDto($user);
        $result->token = $this->tokenManager->create($user);
        return $result;
    }

    private function getContextUser(): User
    {
        return $this->context->getUser();
    }

    public function createUser(UserDto $data): UserDto
    {
        $user = $this->save($data);
        return $this->toDto($user);
    }

    public function loginUser(UserDto $data): UserDto
    {
        $user = $this->userRepository->findOneByEmail((string) $data->email);
        if ($user === null || !$this->passwordHasher->isPasswordValid($user, (string) $data->password)) {
            throw new UnauthorizedException('Email or password is not valid');
        }
        return $this->toDto($user);
    }

    public function getCurrentUser(): UserDto
    {
        $user = $this->getContextUser();
        return $this->toDto($user);
    }

    public function updateCurrentUser(UserDto $data): UserDto
    {
        $user = $this->getContextUser();
        $this->save($data, $user);
        return $this->toDto($user);
    }
}
