<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

use App\ApiResource\Organiser;
use App\Service\OrganiserService;

/**
 * @implements ProcessorInterface<Organiser, Organiser>
 */
class OrganiserUpdateProcessor implements ProcessorInterface
{
    public function __construct(private OrganiserService $service)
    {}

    /**
     * @param  Organiser  $data
     * @param  Operation  $operation
     * @param  int[]      $uriVariables
     * @param  string[][] $context
     * @return Organiser
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = new Organiser();
        if ($data->organiser !== null) {
            $result->organiser = $this->service->updateOrganiser($uriVariables['id'], $data->organiser);
        }
        return $result;
    }

}
