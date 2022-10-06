<?php

namespace App\EventSubscriber;

use App\Singleton\ModuleSingleton;
use LogRat\Core\Event\RegisterModuleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RegisterCoreModuleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [];
    }

    public function onLogRatCoreModuleRegister(RegisterModuleEvent $event)
    {
        $key = $event->getModuleRegistry()->addModule('core');
        ModuleSingleton::getInstance()->setModuleKey($key);
    }
}
