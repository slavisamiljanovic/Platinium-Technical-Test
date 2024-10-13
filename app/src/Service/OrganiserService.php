<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\OrganiserDto;
use App\Entity\Organiser;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Mapper\OrganiserMapper;
use App\Repository\OrganiserRepository;
use App\Utility\Context;

class OrganiserService
{
    public function __construct(
        private OrganiserMapper $organiserMapper,
        private OrganiserRepository $organiserRepository,
        private Context $context
    ) {}

    private function toDto(Organiser $organiser): OrganiserDto
    {
        return $this->organiserMapper->mapEntityToDto($organiser);
    }

    private function save(OrganiserDto $data, ?Organiser $organiser = null): Organiser
    {
        $result = $this->organiserMapper->mapDtoToEntity($data, $organiser);
        $this->organiserRepository->save($result);
        return $result;
    }

    private function findOrganiser(int $id): Organiser
    {
        $organiser = $this->organiserRepository->find($id);
        if ($organiser === null) {
            throw new NotFoundException('Organiser ID "' . $id . '" does not exist');
        }
        return $organiser;
    }

    public function getOrganiser(int $id): ?OrganiserDto
    {
        $result    = null;
        $organiser = $this->findOrganiser($id);
        if ($organiser !== null) {
            $result = $this->toDto($organiser);
        }
        return $result;
    }

    public function createOrganiser(OrganiserDto $data): OrganiserDto
    {
        $organiser = $this->save($data);
        return $this->toDto($organiser);
    }

    public function updateOrganiser(int $id, OrganiserDto $data): OrganiserDto
    {
        $organiser = $this->findOrganiser($id);
        $this->save($data, $organiser);
        return $this->toDto($organiser);
    }

    public function deleteOrganiser(int $id): void
    {
        $organiser = $this->findOrganiser($id);
        $this->organiserRepository->remove($organiser);
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return OrganiserDto[]
     */
    /*
    public function getOrganisersEvent(int $limit, int $offset): array
    {
        $organisers = $this->organiserRepository->findOrganisersEvent($profile, $limit, $offset);
        return array_map(fn (Organiser $organiser) => $this->toDto($organiser), $organisers);
    }
    */

    public function countOrganisers(): int
    {
        return $this->organiserRepository->countOrganisers();
    }

    /**
     * @param integer $limit
     * @param integer $offset
     * @return OrganiserDto[]
     */
    public function getOrganisers(int $limit, int $offset,): array
    {
        $organisers = $this->organiserRepository->findOrganisers($limit, $offset);
        return array_map(fn (Organiser $organiser) => $this->toDto($organiser), $organisers);
    }

}
