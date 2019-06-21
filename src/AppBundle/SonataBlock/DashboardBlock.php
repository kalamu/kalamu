<?php

namespace AppBundle\SonataBlock;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;


class DashboardBlock extends AbstractBlockService
{

    protected $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'title'    => 'Dashboard',
            'template' => '@App/Block/dashboard.html.twig',
        ));
    }


    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = $blockContext->getSettings();

        return new Response($this->twig->render('@App/Block/dashboard.html.twig', array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        )));
    }

}