<?php

namespace AppBundle\Form\Type\Config;

use Kalamu\CmsAdminBundle\Form\Type\WysiwygDashboardType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class TemplateConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('analytics', TextType::class, [
                'label' => "Google Analytics code",
                'required' => false,
                'sonata_help' => "Traking code of Google Analytics to get advanced visitor statistics"
            ])
            ->add('footer_template', WysiwygDashboardType::class, [
                'label' => "Footer of the pages",
                'required' => false
            ])
        ;
    }

    public function getParent() {
        return FormType::class;
    }

}
