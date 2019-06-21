<?php

namespace AppBundle\Manager;

use AppBundle\Manager\DashboardPersistenceInterface;
use Kalamu\CmsAdminBundle\Entity\User;
use Symfony\Bridge\Doctrine\ManagerRegistry;

/**
 * Manager to persist user dashboards
 */
class DashboardUserManager implements DashboardPersistenceInterface
{
    /**
     *
     * @var ManagerRegistry
     */
    protected $em;

    protected $security_context;

    public function __construct(ManagerRegistry $ManagerRegistry, $security_context){
        $this->em = $ManagerRegistry->getManager();
        $this->security_context = $security_context;
    }

    /**
     * Get the list of dashbord of the user
     *
     * @param string $group
     * @return array
     */
    public function getAll(){
        throw new \Exception("Not yet available");
    }

    /**
     * Get the configuration of a dashboard
     *
     * @param string $group
     * @param string $name
     * @return array
     */
    public function get($identifier){
        if($this->getUser()->hasAttr('dashboard_storage.'.$identifier)){
            return json_decode($this->getUser()->getAttr('dashboard_storage.'.$identifier), true);
        }
    }

    /**
     * Set a dashboard configuration
     *
     * @param string $group
     * @param string $name
     * @param array $config
     */
    public function set($identifier, $parameters){
        $this->getUser()->addAttr('dashboard_storage.'.$identifier, json_encode($parameters));
        $this->em->persist($this->getUser());
        $this->em->flush();
    }

    /**
     * Remove a dashboard
     *
     * @param string $group
     * @param type $name
     */
    public function remove($identifier){
        if($this->getUser()->hasAttr('dashboard_storage.'.$identifier)){
            $this->getUser()->removeAttr('dashboard_storage.'.$identifier);
            $this->em->persist($this->getUser());
            $this->em->flush();
        }
    }

    /**
     * @return User
     */
    protected function getUser(){
        return $this->security_context->getToken()->getUser();
    }
}
