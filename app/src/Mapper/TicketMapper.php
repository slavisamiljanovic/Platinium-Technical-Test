<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\TicketDto;
use App\Entity\Ticket;
use App\Entity\Event;
use App\Repository\EventRepository;

class TicketMapper
{

    public function __construct(
        private EventRepository $eventRepository,
        private EventMapper $eventMapper
    ) {}

    /**
     * @param  Ticket $ticket
     * @param  int[]  $events
     * @return void
     */
    private function setEvents(Ticket $ticket, array $events): void
    {
        $oldEvents = $ticket->getEvents()->map(fn (Event $event) => $event->getId())->getValues();
        $newEvents = array_map(
            fn(Event $event) => $event->getId(),
            $this->eventMapper->mapDtoArrayToEntityArray($events)
        );
        $delEvents = array_diff($oldEvents, $newEvents);

        if (!empty($delEvents)) {
            foreach ($ticket->getEvents()->filter(fn (Event $event) => in_array($event->getId(), $delEvents)) as $event) {
                $ticket->removeEvent($event);
            }
        }

        $addEvents = array_diff($newEvents, $oldEvents);
        if (!empty($addEvents)) {
            $events = $this->eventRepository->findEventsBy($addEvents);
            foreach ($events as $event) {
                $ticket->addEvent($event);
            }
        }
    }

    public function mapDtoToEntity(TicketDto $dto, ?Ticket $entity = null): Ticket
    {
        $result = $entity ?: new Ticket();

        $result->setName($dto->name);

        if ($dto->description !== null) {
            $result->setDescription($dto->description !== '' ? $dto->description : null);
        }

        if ($dto->events !== null) {
            $this->setEvents($result, $dto->events);
        }

        return $result;
    }

    public function mapEntityToDto(Ticket $entity): TicketDto
    {

        $result = new TicketDto();
        $result->id          = $entity->getId();
        $result->name        = $entity->getName();
        $result->description = $entity->getDescription();
        $result->events      = $this->eventMapper->mapEntityArrayToDtoArray($entity->getEvents());
        $result->createdAt   = $entity->getCreatedAt();
        $result->updatedAt   = $entity->getUpdatedAt();

        return $result;
    }

}
