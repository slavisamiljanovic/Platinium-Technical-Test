<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

use App\ApiResource\Ticket;
use App\Service\TicketService;
use App\Exception\InvalidInputException;

/**
 * @implements ProcessorInterface<Ticket, Ticket>
 */
class TicketCreateProcessor implements ProcessorInterface
{

    public function __construct(private TicketService $service)
    {}

    /**
     * @param  Ticket  $data
     * @param  Operation  $operation
     * @param  string[]   $uriVariables
     * @param  string[][] $context
     * @return Ticket
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = new Ticket();

        if ($data->ticket !== null) {
            $result->ticket = $this->service->createTicket($data->ticket);

        } else {
            throw new InvalidInputException('The ticket cannot be null.');
        }

        return $result;
    }

}
