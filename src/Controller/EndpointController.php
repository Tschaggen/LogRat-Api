<?php

namespace LogRat\Api\Controller;

use LogRat\Core\Events\RegisterEndpointEvent;
use LogRat\Core\Services\ModuleRegistry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LuckyController
{
    #[Route('/{module}/{endpoint}')]
    public function number(string $module,string $endpoint, ModuleRegistry $moduleRegistry): JsonResponse
    {
        $dispatcher = new EventDispatcher();

        $event = new RegisterEndpointEvent($moduleRegistry);
        $dispatcher->dispatch($event, RegisterEndpointEvent::NAME);

        return new JsonResponse([

        ]);
    }
}