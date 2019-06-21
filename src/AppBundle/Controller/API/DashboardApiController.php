<?php

namespace AppBundle\Controller\API;

use AppBundle\Manager\DashboardRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * API for user deshboard
 */
class DashboardApiController extends Controller
{

    /**
     * Get the list of dashboard for the user
     */
    public function getAction($manager, $identifier){
        $datas = $this->getRegistry($manager)->get($identifier);
        return $this->createJsonResponse($datas);
    }

    /**
     * Set a new dashboard (create or update)
     */
    public function setAction($manager, $identifier){
        $datas = $this->getRequest()->request->all();
        $this->getRegistry($manager)->set($identifier, $datas);
        return $this->createJsonResponse(['status' => 'OK']);
    }

    /**
     * Remove a dashboard
     * @param string $name
     */
    public function removeAction($manager, $identifier){
        $this->getRegistry($manager)->remove($identifier);
        return $this->createJsonResponse(['status' => 'OK']);
    }

    /**
     * @return DashboardRegistry
     */
    protected function getRegistry($manager){
        return $this->container->get('kalamu.dashboard_registry')->getStorage($manager);
    }

    protected function createJsonResponse($datas){
        $response = new Response(json_encode($datas, JSON_PRETTY_PRINT));
        $response->headers->set('Content-type', 'application/json');
        return $response;
    }

}
