<?php

namespace AppBundle\Manager;

use AppBundle\Manager\DashboardPersistenceInterface;


class DashboardRegistry
{

    protected $storages = [];

    public function registerStorage($alias, DashboardPersistenceInterface $service){
        $this->storages[$alias] = $service;
    }

    public function getStorage($alias){
        return $this->storages[$alias];
    }

}
