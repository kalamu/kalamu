<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class SiteController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * Get the last published contents
     */
    public function getLastContent($Request, $contentType, $firstResult, $maxResults, $terms){

        $queryBuilder = $this->get('kalamu_cms_core.content_type.manager')->getType($contentType)
            ->getBasePublicQuery($Request)
            ->resetDQLPart('orderBy')
            ->orderBy('c.published_at', 'desc');

        if($terms != null){
            $id_terms = [];
            $queryBuilder->leftJoin('c.terms', 'term')
                ->andWhere("term.id IN (:id_terms)");
            foreach ($terms as $key => $term) {
                $id_terms[] = $term->getId();
            }
            $queryBuilder->setParameter('id_terms', $id_terms);
        }

        $queryBuilder->setFirstResult( $firstResult )
            ->setMaxResults( $maxResults );
        return $queryBuilder->getQuery()->getResult();
    }

    protected function getConfigLink($parameter){
        $typeManager = $this->get('kalamu_cms_core.content_type.manager');

        $link_contact = $this->get('kalamu_dynamique_config')->get($parameter, null);
        if($link_contact && $link_contact['type']){
            if($link_contact['identifier']){
                $content = $typeManager->getType($link_contact['type'])->getPublicContent($link_contact['identifier'], $link_contact['context']);
                if(!$content){
                    return null;
                }
                return $typeManager->getType($link_contact['type'])->getPublicReadLink($content, array('_context' => $link_contact['context']));
            }else{
                return $typeManager->getType($link_contact['type'])->getPublicIndexLink(array('_context' => $link_contact['context']));
            }
        }
    }

    public function getDynamiqueConfigAction($parameter){

        $value = $this->get('kalamu_dynamique_config')->get('default_template['.$parameter.']', null);

        return new JsonResponse(array('value' => $value));
    }


}
