<?php

namespace App\Service;

class ModuleRegistry
{

    private array $endpoints = [];
    private array $modules = [];

    public function addEndpoint(string $module,array $endpoint) : bool {

        $endpointArray = [];

        if (!array_key_exists('endpoint',$endpoint)) {
            return false;
        }

        if (!array_key_exists('callback',$endpoint)) {
            return false;
        }

        $endpointArray['callback'] = $endpoint['callback'];

        if(array_key_exists('sec_lvl',$endpoint)) {
            $endpointArray['sec_lvl'] = $endpoint['sec_lvl'];
        }
        else {
            $endpointArray['sec_lvl'] = UserHandler::SECURITY_LEVEL_NONE;
        }

        if(!in_array($module,$this->modules)) {
            return false;
        }

        var_dump($endpoint);

        $this->endpoints[$module][] = $endpointArray;

        return true;
    }

    public function addModule(string $module) : bool {

        if(array_search($module,$this->modules)) {
            return false;
        }

        $this->modules[] = $module;
        return true;
    }

    public function getModules() {
        return $this->modules;
    }

    public function getEndpoints() {
        return $this->endpoints;
    }
}