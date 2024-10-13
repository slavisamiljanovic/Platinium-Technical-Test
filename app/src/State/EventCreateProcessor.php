<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

use App\ApiResource\Event;
use App\Service\EventService;
use App\Exception\InvalidInputException;

/**
 * @implements ProcessorInterface<Event, Event>
 */
class EventCreateProcessor implements ProcessorInterface
{

    public function __construct(private EventService $service)
    {}

    /**
     * @param  Event  $data
     * @param  Operation  $operation
     * @param  string[]   $uriVariables
     * @param  string[][] $context
     * @return Event
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = new Event();

        if ($data->event !== null) {
            $result->event = $this->service->createEvent($data->event);

        } else {
            throw new InvalidInputException('The event cannot be null.');
        }

        return $result;
    }

}
