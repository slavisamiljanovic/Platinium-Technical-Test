<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

use App\ApiResource\Event;
use App\Service\EventService;

/**
 * @implements ProcessorInterface<Event, Event>
 */
class EventUpdateProcessor implements ProcessorInterface
{
    public function __construct(private EventService $service)
    {}

    /**
     * @param  Event  $data
     * @param  Operation  $operation
     * @param  int[]      $uriVariables
     * @param  string[][] $context
     * @return Event
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = new Event();
        if ($data->event !== null) {
            $result->event = $this->service->updateEvent($uriVariables['id'], $data->event);
        }
        return $result;
    }

}
