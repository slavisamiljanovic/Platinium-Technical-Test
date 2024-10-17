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
class EventsFeedProvider implements ProviderInterface
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
        /*
        $eventsList = [];
        if (!empty($context['filters']['eventsList'])) {
            // Split the string by commas and convert to integers.
            $eventsList = array_map('intval', explode(',', $context['filters']['eventsList']));
        }
        */
        $result = new Event();
        $result->eventsCount = $this->service->countEventsFeed();
        if ($result->eventsCount) {
            $result->events = $this->service->getEventsFeed();
        }
        return $result;
    }

}
