<?php

declare(strict_types=1);

namespace App\Tests\Service;

// use App\Exception\ForbiddenException;
// use App\Exception\NotFoundException;
// use App\Exception\UnauthorizedException;
use App\Mapper\ProfileMapper;
use App\Repository\ProfileRepository;
use App\Service\ProfileService;
use PHPUnit\Framework\MockObject\MockObject;
// use Throwable;

class ProfileServiceTest extends ServiceTestCase
{

    /**
     * @var MockObject&ProfileRepository
     */
    private $profileRepository;

    private function createService(?int $contextUserId = null): ProfileService
    {
        $this->profileRepository = $this->buildProxy(ProfileRepository::class);

        return new ProfileService(
            $this->createContext($contextUserId),
            $this->getService(ProfileMapper::class),
            $this->profileRepository,
        );
    }

}
