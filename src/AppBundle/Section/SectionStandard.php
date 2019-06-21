<?php

namespace AppBundle\Section;

use Kalamu\DashboardBundle\Model\AbstractConfigurableElement;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;

/**
 * Simple section for background color
 */
class SectionStandard extends AbstractConfigurableElement
{

    public function getTitle() {
        return 'cms.section.section_standard.title';
    }

    public function getDescription() {
        return 'cms.section.section_standard.description';
    }

    public function getForm(Form $form){
        $form->add("class", ChoiceType::class, array(
            'label' => 'Background color',
            'choices'   =>
                [
                    'None' => '',
                    'Blue' => 'bg-primary',
                    'Green' => 'bg-success',
                    'Light blue' => 'bg-info',
                    'Yellow' => 'bg-warning',
                    'Red' => 'bg-danger',
                ],
            'choices_as_values' => true,
            'label_attr' => array('class' => 'center-block text-left')))
        ->add('id_css', TextType::class, ['required' => false]);

        return $form;
    }

    /**
     * @return string
     */
    public function render(TwigEngine $templating){
        return $templating->render('AppBundle:Section:section_standard.html.twig', $this->parameters);
    }

}
