<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\EventDto;
use App\Entity\Organiser;
use App\Entity\Event;
use App\Exception\NotFoundException;
use App\Mapper\EventMapper;
use App\Mapper\OrganiserMapper;
use App\Repository\EventRepository;
use App\Repository\OrganiserRepository;
use App\Utility\Context;

class EventService
{
    public function __construct(
        private EventMapper $eventMapper,
        private EventRepository $eventRepository,
        private OrganiserRepository $organiserRepository,
        private OrganiserMapper $organiserMapper,
        private Context $context
    ) {}

    private function toDto(Event $event): EventDto
    {
        return $this->eventMapper->mapEntityToDto($event);
    }

    private function findOrganiser(int $id): Organiser
    {
        $organiser = $this->organiserRepository->find($id);
        if ($organiser === null) {
            throw new NotFoundException('Organiser ID "' . $id . '" does not exist');
        }
        return $organiser;
    }

    private function save(Organiser $organiser, EventDto $data, ?Event $event = null): Event
    {
        // @todo-Slavisa: Move setOrganiser() to Mapper.
        $result = $this->eventMapper->mapDtoToEntity($data, $event);
        $result->setOrganiser($organiser);
        $this->eventRepository->save($result);
        return $result;
    }

    private function findEvent(int $id): Event
    {
        $event = $this->eventRepository->find($id);
        if ($event === null) {
            throw new NotFoundException('Event ID "' . $id . '" does not exist');
        }
        return $event;
    }

    public function getEvent(int $id): ?EventDto
    {
        $result    = null;
        $event = $this->findEvent($id);
        if ($event !== null) {
            $result = $this->toDto($event);
        }
        return $result;
    }

    public function createEvent(EventDto $data): EventDto
    {
        $organiser = $this->findOrganiser($data->organiser->id);
        $event     = $this->save($organiser, $data);
        return $this->toDto($event);
    }

    public function updateEvent(int $id, EventDto $data): EventDto
    {
        $organiser = $this->findOrganiser($data->organiser->id);
        $event     = $this->findEvent($id);
        $this->save($organiser, $data, $event);
        return $this->toDto($event);
    }

    public function deleteEvent(int $id): void
    {
        $event = $this->findEvent($id);
        $this->eventRepository->remove($event);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return EventDto[]
     */
    /*
    public function getEventsEvent(int $limit, int $offset): array
    {
        $events = $this->eventRepository->findEventsEvent($profile, $limit, $offset);
        return array_map(fn (Event $event) => $this->toDto($event), $events);
    }
    */

    public function countEvents(): int
    {
        return $this->eventRepository->countEvents();
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return EventDto[]
     */
    public function getEvents(int $limit, int $offset,): array
    {
        $events = $this->eventRepository->findEvents($limit, $offset);
        return array_map(fn (Event $event) => $this->toDto($event), $events);
    }

}
