<?php

namespace AppBundle\Controller\Admin;

use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller to manage access to the administration interface
 */
class SecurityController extends BaseSecurityController
{


    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        return $this->container->get('templating')->renderResponse('AppBundle:Security:login.html.twig', $data);
    }

    public function profileEditAction(Request $request){
        $user= $this->get('security.token_storage')->getToken()->getUser();

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $formFactory = $this->get('fos_user.profile.form.factory');
        $form = $formFactory->createForm()->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'required' => false,
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ));
        $form->remove('current_password');
        $form->remove('username');
        $form->setData($user);

        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);
            $userManager->updateUser($user);
            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_security_profile');
                $response = new RedirectResponse($url);
            }
            $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            return $response;
        }

        return $this->render('AppBundle:Security:profile_edit.html.twig', array('form' => $form->createView(), 'user'=>$user, 'environment' => 'frontend'));
    }

    public function profileAction(){
        $user= $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('AppBundle:Security:profile.html.twig', array('user'=>$user, 'environment' => 'frontend'));
    }

}
