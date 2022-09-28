<?php

namespace App\Controller;

use LogRat\Core\Event\RegisterEndpointEvent;
use LogRat\Core\Event\RegisterModuleEvent;
use LogRat\Core\Service\EndpointRegistry;
use LogRat\Core\Service\ModuleRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EndpointController extends AbstractController {

    #[Route('/{module}/{endpoint}')]
    function endpoint(EventDispatcher $eventDispatcher,ModuleRegistry $moduleRegistry,EndpointRegistry $endpointRegistry, string $module, string $endpoint) : JsonResponse {

        $registerModuleEvent = new RegisterEndpointEvent($moduleRegistry);
        $eventDispatcher->dispatch($registerModuleEvent, RegisterModuleEvent::NAME);

        $registerEndpointEvent = new RegisterEndpointEvent($endpointRegistry);
        $eventDispatcher->dispatch($registerEndpointEvent, RegisterEndpointEvent::NAME);

        $response = null;

        if(!in_array($module,$moduleRegistry->getModules())) {
            $response = [
                'error_code' => '404',
                'error_msg' => 'module not found'
            ];
            return new JsonResponse($response);
        }

        if(count($endpointRegistry->getEndpoints($module)) <= 0) {
            $response = [
                'error_code' => '404',
                'error_msg' => 'module has no endpoints'
            ];
            return new JsonResponse($response);
        }

        if(!in_array($endpoint,$endpointRegistry->getEndpoints($module))) {
            $response = [
                'error_code' => '404',
                'error_msg' => 'endpoint not found'
            ];
            return new JsonResponse($response);
        }

        return new JsonResponse($response);
    }
}