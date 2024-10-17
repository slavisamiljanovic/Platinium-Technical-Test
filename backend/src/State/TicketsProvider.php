<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Config\TicketConfig;
use App\ApiResource\Ticket;
use App\Service\TicketService;

/**
 * @implements ProviderInterface<Ticket>
 */
class TicketsProvider implements ProviderInterface
{

    public function __construct(private TicketService $service)
    {}

    /**
     * @param  Operation  $operation
     * @param  string[]   $uriVariables
     * @param  string[][] $context
     * @return Ticket
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Ticket
    {
        $result = new Ticket();
        $result->ticketsCount = $this->service->countTickets();
        if ($result->ticketsCount) {
            $limit  = intval($context['filters']['limit'] ?? TicketConfig::OUTPUT_LIST_LIMIT);
            $offset = intval($context['filters']['offset'] ?? TicketConfig::OUTPUT_LIST_OFFSET);

            $result->tickets = $this->service->getTickets($limit, $offset);
        }
        return $result;
    }

}
