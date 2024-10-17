<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Config\EventConfig;
use App\ApiResource\Event;
use App\Service\EventService;

/**
 * @implements ProviderInterface<Event>
 */
class EventsProvider implements ProviderInterface
{

    public function __construct(private EventService $service)
    {}

    /**
     * @param  Operation  $operation
     * @param  string[]   $uriVariables
     * @param  string[][] $context
     * @return Event
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Event
    {
        $result = new Event();
        $result->eventsCount = $this->service->countEvents();
        if ($result->eventsCount) {
            $limit  = intval($context['filters']['limit'] ?? EventConfig::OUTPUT_LIST_LIMIT);
            $offset = intval($context['filters']['offset'] ?? EventConfig::OUTPUT_LIST_OFFSET);

            $result->events = $this->service->getEvents($limit, $offset);
        }
        return $result;
    }

}
