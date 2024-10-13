<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\ProfileDto;
use App\Entity\Profile;
use App\Utility\Context;

class ProfileMapper
{

    public function __construct(private Context $context)
    {}

    public function mapEntityToDto(Profile $entity): ProfileDto
    {
        $result = new ProfileDto();

        $result->username  = $entity->getUsername();
        $result->bio       = $entity->getBio();
        $result->image     = $entity->getImage();
        $result->createdAt = $entity->getCreatedAt();
        $result->updatedAt = $entity->getCreatedAt();

        return $result;
    }

}
