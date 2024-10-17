<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ProfileDto;
use App\Entity\Profile;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Mapper\ProfileMapper;
use App\Repository\ProfileRepository;
use App\Utility\Context;

class ProfileService
{

    public function __construct(
        private Context $context,
        private ProfileMapper $profileMapper,
        private ProfileRepository $profileRepository
    ) {}

    private function toDto(Profile $profile): ProfileDto
    {
        return $this->profileMapper->mapEntityToDto($profile);
    }

    private function findProfile(string $username): Profile
    {
        $profile = $this->findProfileSafe($username);
        if ($profile === null) {
            throw new NotFoundException('Profile "' . $username . '" does not exist');
        }
        return $profile;
    }

    private function findProfileSafe(string $username): ?Profile
    {
        return $this->profileRepository->findOneByUsername($username);
    }

    public function getProfile(string $username): ?ProfileDto
    {
        $result = null;
        $profile = $this->findProfileSafe($username);
        if ($profile !== null) {
            $result = $this->toDto($profile);
        }
        return $result;
    }

}
