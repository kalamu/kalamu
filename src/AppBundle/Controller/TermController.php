<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TermController extends Controller
{

    public function readAction(Request $Request, $identifier){
        $master_manager = $this->get('kalamu_cms_core.content_type.manager');
        $manager = $master_manager->getType('term');
        $term = $manager->getPublicContent($identifier);
        $page = $Request->query->getInt('page', 1);
        if(!$term){
            throw $this->createNotFoundException();
        }

        $content_type = $term->getTaxonomy()->getApplyOn();
        if(count($content_type)>1){
            throw $this->createNotFoundException("Unable to identify the content type");
        }

        $manager_content = $master_manager->getManagerForClass(current($content_type));
        $queryBuilder = $manager_content->getQueryBuilderForIndex($Request);
        $queryBuilder->leftJoin('c.terms', 'term')
                ->andWhere('term.id = :id_term')
                ->setParameter('id_term', $term->getId());
        $paginator = $this->get('knp_paginator')->paginate($queryBuilder, $page, $manager_content->maxPerPage());
        if($page>1 && $page>$paginator->getPageCount()){
            throw $this->createNotFoundException("There is not that much page.");
        }

        return $this->render($manager->getTemplateFor($term), array(
            'term' => $term,
            'paginator' => $paginator,
            'master_manager' => $master_manager,
            'manager_term' => $manager,
            'manager_content' => $manager_content));
    }

}
