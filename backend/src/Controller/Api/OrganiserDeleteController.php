<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

use App\Service\OrganiserService;

#[AsController]
class OrganiserDeleteController extends AbstractController
{

    public function __construct(private OrganiserService $service)
    {}

    public function __invoke(int $id): void
    {
        $this->service->deleteOrganiser($id);
    }

}
