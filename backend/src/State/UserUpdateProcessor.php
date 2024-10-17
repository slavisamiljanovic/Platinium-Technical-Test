<?php

declare(strict_types=1);

namespace App\State;

use App\ApiResource\User;
use App\Service\UserService;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

/**
 * @implements ProcessorInterface<User, User>
 */
class UserUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserService $service,
    ) {
    }

    /**
     * @param User $data
     * @param Operation $operation
     * @param string[] $uriVariables
     * @param string[][] $context
     * @return User
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = new User();
        if ($data->user !== null) {
            $result->user = $this->service->updateCurrentUser($data->user);
        }
        return $result;
    }
}
