<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

use App\Service\EventService;

#[AsController]
class EventDeleteController extends AbstractController
{

    public function __construct(private EventService $service)
    {}

    public function __invoke(int $id): void
    {
        $this->service->deleteEvent($id);
    }

}
