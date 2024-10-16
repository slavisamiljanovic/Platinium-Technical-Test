<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\TicketDto;
use App\Entity\Ticket;
use App\Entity\Event;
use App\Exception\NotFoundException;
use App\Mapper\TicketMapper;
use App\Repository\TicketRepository;
use App\Repository\EventRepository;
use App\Utility\Context;

class TicketService
{
    public function __construct(
        private TicketMapper $ticketMapper,
        private TicketRepository $ticketRepository,
        private EventRepository $eventRepository,
        private Context $context
    ) {}

    private function toDto(Ticket $ticket): TicketDto
    {
        return $this->ticketMapper->mapEntityToDto($ticket);
    }

    /**
     * @param  TicketDto $data
     * @param  Ticket    $ticket
     * @return Ticket
     */
    private function save(TicketDto $data, ?Ticket $ticket = null): Ticket
    {
        $result = $this->ticketMapper->mapDtoToEntity($data, $ticket);
        $this->ticketRepository->save($result);
        return $result;
    }

    private function findTicket(int $id): Ticket
    {
        $ticket = $this->ticketRepository->find($id);
        if ($ticket === null) {
            throw new NotFoundException('Ticket ID "' . $id . '" does not exist');
        }
        return $ticket;
    }

    public function getTicket(int $id): ?TicketDto
    {
        $result    = null;
        $ticket = $this->findTicket($id);
        if ($ticket !== null) {
            $result = $this->toDto($ticket);
        }
        return $result;
    }

    public function createTicket(TicketDto $data): TicketDto
    {
        $ticket = $this->save($data);
        return $this->toDto($ticket);
    }

    public function updateTicket(int $id, TicketDto $data): TicketDto
    {
        $ticket = $this->findTicket($id);
        $this->save($data, $ticket);
        return $this->toDto($ticket);
    }

    public function deleteTicket(int $id): void
    {
        $ticket = $this->findTicket($id);
        $this->ticketRepository->remove($ticket);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return TicketDto[]
     */
    /*
    public function getTicketsEvent(int $limit, int $offset): array
    {
        $tickets = $this->ticketRepository->findTicketsEvent($profile, $limit, $offset);
        return array_map(fn (Ticket $ticket) => $this->toDto($ticket), $tickets);
    }
    */

    public function countTickets(): int
    {
        return $this->ticketRepository->countTickets();
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return TicketDto[]
     */
    public function getTickets(int $limit, int $offset): array
    {
        $tickets = $this->ticketRepository->findTickets($limit, $offset);
        return array_map(fn (Ticket $ticket) => $this->toDto($ticket), $tickets);
    }

}
