<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\ApiResource\Organiser;
use App\Service\OrganiserService;

/**
 * @implements ProviderInterface<Event>
 */
class OrganisersFeedProvider implements ProviderInterface
{

    public function __construct(private OrganiserService $service)
    {}

    /**
     * @param  Operation  $operation
     * @param  string[]   $uriVariables
     * @param  string[][] $context
     * @return Event
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Organiser
    {
        $result = new Organiser();
        $result->organisersCount = $this->service->countOrganisersFeed();
        if ($result->organisersCount) {
            $result->organisers = $this->service->getOrganisersFeed();
        }
        return $result;
    }

}
