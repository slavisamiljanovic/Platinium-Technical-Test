<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\EventDto;
use App\Entity\Event;
use App\Utility\Context;

class EventMapper
{

    public function __construct(
        private OrganiserMapper $organiserMapper,
        private Context $context
    ) {}

    public function mapDtoToEntity(EventDto $dto, ?Event $entity = null): Event
    {
        $result = $entity ?: new Event();
        
        $result->setName($dto->name);
        $result->setIsActive($dto->isActive);

        if ($dto->description !== null) {
            $result->setDescription($dto->description !== '' ? $dto->description : null);
        }
        return $result;
    }

    public function mapEntityToDto(Event $entity): EventDto
    {
        $result = new EventDto();

        $result->id          = $entity->getId();
        $result->name        = $entity->getName();
        $result->description = $entity->getDescription();
        $result->isActive    = $entity->getIsActive();
        $result->createdAt   = $entity->getCreatedAt();
        $result->updatedAt   = $entity->getCreatedAt();

        if ($entity->getOrganiser() !== null) {
            $result->organiser = $this->organiserMapper->mapEntityToDto($entity->getOrganiser());
        }

        return $result;
    }

    public function mapEntityToInt(Event $event): int
    {
        return (int) $event->getId();
    }

    /**
     * @param  iterable<Event> $events
     * @return int[]
     */
    public function mapEntitiesToIntArray(iterable $events): array
    {
        $result = [];
        foreach ($events as $event) {
            $result[] = $this->mapEntityToInt($event);
        }
        sort($result);
        return $result;
    }

}
