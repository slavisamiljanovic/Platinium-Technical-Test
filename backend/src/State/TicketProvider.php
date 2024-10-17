<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\ApiResource\Ticket;
use App\Service\TicketService;

/**
 * @implements ProviderInterface<Ticket>
 */
class TicketProvider implements ProviderInterface
{

    public function __construct(private TicketService $service)
    {}

    /**
     * @param  Operation      $operation
     * @param  int[]          $uriVariables
     * @param  string[][]     $context
     * @return Ticket|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Ticket
    {
        $result    = null;
        $ticket = $this->service->getTicket($uriVariables['id']);
        if ($ticket !== null) {
            $result = new Ticket();
            $result->ticket = $ticket;
        }
        return $result;
    }

}
