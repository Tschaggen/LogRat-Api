<?php

namespace App\Controller;

use LogRat\Core\Event\RegisterEndpointEvent;
use App\Service\ModuleRegistry;
use LogRat\Core\Event\RegisterModuleEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EndpointController extends AbstractController {

    #[Route('/{module}/{endpoint}')]
    function endpoint(ModuleRegistry $moduleRegistry, string $module, string $endpoint) : JsonResponse {

        $dispatcher = new EventDispatcher();

        $registerModuleEvent = new RegisterEndpointEvent($moduleRegistry);
        $dispatcher->dispatch($registerModuleEvent, RegisterModuleEvent::NAME);

        $registerEndpointEvent = new RegisterEndpointEvent($moduleRegistry);
        $dispatcher->dispatch($registerEndpointEvent, RegisterEndpointEvent::NAME);

        $resonse = null;

        $moduleRegistry->addModule('test');
        $moduleRegistry->addEndpoint('test',[
            'endpoint' => 'sex',
            'callback' => ahh
        ]);

        var_dump($moduleRegistry->getEndpoints());

        if(!in_array($module,$moduleRegistry->getModules())) {
            $resonse = [
                'error_code' => '404',
                'error_msg' => 'module not found'
            ];
            return new JsonResponse($resonse);
        }

        if(!array_key_exists($module,$moduleRegistry->getEndpoints())) {
            $resonse = [
                'error_code' => '404',
                'error_msg' => 'module has no endpoints'
            ];
            return new JsonResponse($resonse);
        }

        if(!in_array($endpoint,$moduleRegistry->getEndpoints()[$module])) {
            $resonse = [
                'error_code' => '404',
                'error_msg' => 'endpoint not found'
            ];
            return new JsonResponse($resonse);
        }

        return new JsonResponse($resonse);
    }
}

function ahh() {

}