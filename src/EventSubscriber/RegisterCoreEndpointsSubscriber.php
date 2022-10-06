<?php

namespace App\EventSubscriber;

use App\Singleton\ModuleSingleton;
use LogRat\Core\Enums\DisplayTypes;
use LogRat\Core\Enums\SecurityLevel;
use LogRat\Core\Event\RegisterEndpointEvent;
use LogRat\Core\Event\RegisterModuleEvent;

class RegisterCoreEndpointsSubscriber
{
    public static function getSubscribedEvents()
    {
        return [];
    }

    public function onLogRatCoreEndpointRegister(RegisterEndpointEvent $event)
    {
        $registry = $event->getEndpointRegistry();

        $coreKey = ModuleSingleton::getInstance()->getModuleKey();
        $registry->addEndpoint('structure','core',$coreKey,[
            'security_level' => SecurityLevel::SECURITY_LEVEL_NONE,
            'display_type' => DisplayTypes::TREE,
            'callback' => 'App\Controller\StructureEndpointController::endpoint'
        ]);
    }
}