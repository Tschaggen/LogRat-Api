<?php

namespace App\Controller;

use LogRat\Core\Event\RegisterEndpointEvent;
use LogRat\Core\Event\RegisterModuleEvent;
use LogRat\Core\Exception\EndpointRegisterException;
use LogRat\Core\Exception\UserException;
use LogRat\Core\Service\EndpointRegistry;
use LogRat\Core\Service\ModuleRegistry;
use LogRat\Core\Service\UserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class EndpointController extends AbstractController {

    #[Route('/{module}/{endpoint}')]
    function endpoint(UserHandler $userHandler, EventDispatcherInterface $eventDispatcher,ModuleRegistry $moduleRegistry,EndpointRegistry $endpointRegistry, string $module, string $endpoint) : JsonResponse {

        $registerModuleEvent = new RegisterModuleEvent($moduleRegistry);
        $eventDispatcher->dispatch($registerModuleEvent, RegisterModuleEvent::NAME);

        $registerEndpointEvent = new RegisterEndpointEvent($endpointRegistry);
        $eventDispatcher->dispatch($registerEndpointEvent, RegisterEndpointEvent::NAME);

        $response = null;

        if (!in_array($module, $moduleRegistry->getModules())) {
            $response = [
                'error_code' => '404',
                'error_msg' => 'module not found'
            ];
            return new JsonResponse($response);
        }

        try {
            if (count($endpointRegistry->getEndpoints($module)) <= 0) {
                $response = [
                    'error_code' => '404',
                    'error_msg' => 'module has no endpoints'
                ];
                return new JsonResponse($response);
            }

            if (!in_array($endpoint, $endpointRegistry->getEndpoints($module))) {
                $response = [
                    'error_code' => '404',
                    'error_msg' => 'endpoint not found'
                ];
                return new JsonResponse($response);
            }
        }
        catch (EndpointRegisterException $exception) {
            $response = [
                'exception_type' => EndpointRegisterException::class,
                'error_code' => $exception->getCode(),
                'error_msg' => $exception->getMessage()
            ];
            return new JsonResponse($response);
        }

        $options = $endpointRegistry->getEndpointData($module,$endpoint);

        try {
            $userHandler->getUser();
        }
        catch (UserException $exception) {
            $response = [
                'exception_type' => EndpointRegisterException::class,
                'error_code' => $exception->getCode(),
                'error_msg' => $exception->getMessage()
            ];
            return new JsonResponse($response);
        }

        if(!($userHandler->checkApitoken() || $userHandler->checkPassword())) {
            $response = [
                'error_code' => 401,
                'error_msg' => 'Incorrect password or apitoken.'
            ];
            return new JsonResponse($response);
        }

        if($options['security_level'] >= $userHandler->getSecurityLevel() ) {
            $response = [
                'error_code' => 401,
                'error_msg' => 'User security level is to low'
            ];
            return new JsonResponse($response);
        }


        if(array_key_exists('callback',$options)) {
            $response = $this->forward($endpointRegistry->getEndpointData($module, $endpoint)['callback'], []);
            return new JsonResponse($response->getContent(),200,[],true);
        }
        else {
            $response = [
                'error_code' => '404',
                'error_msg' => 'endpoint has no callback'
            ];
        }
        return new JsonResponse($response);
    }
}