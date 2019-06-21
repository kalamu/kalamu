<?php

namespace AppBundle\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Kalamu\CmsAdminBundle\Entity\User;

/**
 * Security voter to give full access to the super admin even when role hierarchy
 * is not fully defined
 */
class SuperAdminVoter extends RoleVoter
{

    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {

        if(!$token->getUser() instanceof User){
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        return in_array(User::ROLE_SUPER_ADMIN, $user->getRoles()) ? VoterInterface::ACCESS_GRANTED : VoterInterface::ACCESS_ABSTAIN;
    }

}