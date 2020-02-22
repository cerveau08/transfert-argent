<?php

namespace App\Security\Voter;

use App\Entity\Affectation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AffectationVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['POST_EDIT', 'POST_VIEW'])
            && $subject instanceof Affectation;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'POST':
                if (($user->getRoles()[0] === 'ROLE_PARTENAIRE' || $user->getRoles()[0] === 'ROLE_ADMIN_PARTENAIRE') &&
                ($subject->getUserComptePartenaire()->getProfil()->getLibelle() === 'ROLE_CAISSIER_PARTENAIRE'))
                {
                    return true;
                }
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
