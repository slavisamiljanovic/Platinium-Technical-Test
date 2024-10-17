<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\ApiResource\Organiser;
use App\Service\OrganiserService;

/**
 * @implements ProviderInterface<Organiser>
 */
class OrganiserProvider implements ProviderInterface
{

    public function __construct(private OrganiserService $service)
    {}

    /**
     * @param  Operation      $operation
     * @param  int[]          $uriVariables
     * @param  string[][]     $context
     * @return Organiser|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Organiser
    {
        $result    = null;
        $organiser = $this->service->getOrganiser($uriVariables['id']);
        if ($organiser !== null) {
            $result = new Organiser();
            $result->organiser = $organiser;
        }
        return $result;
    }

}
