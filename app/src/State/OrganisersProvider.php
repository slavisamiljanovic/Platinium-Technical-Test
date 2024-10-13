<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Config\OrganiserConfig;
use App\ApiResource\Organiser;
use App\Service\OrganiserService;

/**
 * @implements ProviderInterface<Organiser>
 */
class OrganisersProvider implements ProviderInterface
{

    public function __construct(private OrganiserService $service)
    {}

    /**
     * @param  Operation  $operation
     * @param  string[]   $uriVariables
     * @param  string[][] $context
     * @return Organiser
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Organiser
    {
        $result = new Organiser();
        $result->organisersCount = $this->service->countOrganisers();
        if ($result->organisersCount) {
            $limit  = intval($context['filters']['limit'] ?? OrganiserConfig::OUTPUT_LIST_LIMIT);
            $offset = intval($context['filters']['offset'] ?? OrganiserConfig::OUTPUT_LIST_OFFSET);

            $result->organisers = $this->service->getOrganisers($limit, $offset);
        }
        return $result;
    }

}
