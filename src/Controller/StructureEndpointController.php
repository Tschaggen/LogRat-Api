<?php

namespace App\Controller;

use LogRat\Core\Service\EndpointRegistry;
use LogRat\Core\Service\ModuleRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

class StructureEndpointController
{

    public function endpoint(EndpointRegistry $endpointRegistry,ModuleRegistry $moduleRegistry) {

        $response = [];

        foreach ($moduleRegistry->getModules() as $module) {
            $response[$module] = $endpointRegistry->getEndpoints($module);
        }


        return new JsonResponse($response);
    }
}