<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

use App\ApiResource\Ticket;
use App\Service\TicketService;

/**
 * @implements ProcessorInterface<Ticket, Ticket>
 */
class TicketUpdateProcessor implements ProcessorInterface
{
    public function __construct(private TicketService $service)
    {}

    /**
     * @param  Ticket  $data
     * @param  Operation  $operation
     * @param  int[]      $uriVariables
     * @param  string[][] $context
     * @return Ticket
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = new Ticket();
        if ($data->ticket !== null) {
            $result->ticket = $this->service->updateTicket($uriVariables['id'], $data->ticket);
        }
        return $result;
    }

}
