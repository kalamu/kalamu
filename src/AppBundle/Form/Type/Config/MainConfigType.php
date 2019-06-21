<?php

namespace AppBundle\Form\Type\Config;

use Kalamu\CmsAdminBundle\Form\Type\CmsLinkSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class MainConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('required' => true, 'sonata_help' => 'Title shown in browser bar'))
            ->add('description', TextareaType::class, array('required' => false, 'sonata_help' => 'Short description of the website (mainly for search engines)'))
            ->add('homepage_content', CmsLinkSelectorType::class, array('label' => "Page d'accueil", 'sonata_help' => "Content used as homepage"))
            ->add('search_engine_allow', ChoiceType::class, array(
                'choices' => array('Yes' => true, 'No' => false),
                'choices_as_values' => true,
                'label' => "Allow search engines indexation",
                'sonata_help' => "Allow search engines to reference the content of the website.",
                'expanded' => true,
            ))
        ;
    }

    public function getParent() {
        return FormType::class;
    }

}
