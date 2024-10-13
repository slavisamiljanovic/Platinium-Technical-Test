<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\OrganiserDto;
use App\Entity\Organiser;
use App\Utility\Context;

class OrganiserMapper
{

    public function __construct(private Context $context)
    {}

    public function mapDtoToEntity(OrganiserDto $dto, ?Organiser $entity = null): Organiser
    {
        $result = $entity ?: new Organiser();
        if ($dto->name !== null) {
            $result->setName($dto->name);
        }
        if ($dto->city !== null) {
            $result->setCity($dto->city);
        }
        if ($dto->phone !== null) {
            $result->setPhone($dto->phone !== '' ? $dto->phone : null);
        }
        if ($dto->description !== null) {
            $result->setDescription($dto->description !== '' ? $dto->description : null);
        }
        return $result;
    }

    public function mapEntityToDto(Organiser $entity): OrganiserDto
    {
        $result = new OrganiserDto();

        $result->id          = $entity->getId();
        $result->name        = $entity->getName();
        $result->city        = $entity->getCity();
        $result->phone       = $entity->getPhone();
        $result->description = $entity->getDescription();
        $result->createdAt   = $entity->getCreatedAt();
        $result->updatedAt   = $entity->getCreatedAt();

        return $result;
    }

}
