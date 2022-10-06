<?php

namespace App\Singleton;

class ModuleSingleton
{
    private static $instance = null;
    private string $moduleKey = '';

    private function __construct()
    {

    }

    public static function getInstance() : ModuleSingleton
    {
        if (self::$instance == null)
        {
            self::$instance = new ModuleSingleton();
        }

        return self::$instance;
    }

    /**
     * @param string $moduleKey
     */
    public function setModuleKey(string $moduleKey): void
    {
        $this->moduleKey = $moduleKey;
    }

    /**
     * @return string
     */
    public function getModuleKey(): string
    {
        return $this->moduleKey;
    }

}