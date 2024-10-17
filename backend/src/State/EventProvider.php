<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\ApiResource\Event;
use App\Service\EventService;

/**
 * @implements ProviderInterface<Event>
 */
class EventProvider implements ProviderInterface
{

    public function __construct(private EventService $service)
    {}

    /**
     * @param  Operation      $operation
     * @param  int[]          $uriVariables
     * @param  string[][]     $context
     * @return Event|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Event
    {
        $result    = null;
        $event = $this->service->getEvent($uriVariables['id']);
        if ($event !== null) {
            $result = new Event();
            $result->event = $event;
        }
        return $result;
    }

}
