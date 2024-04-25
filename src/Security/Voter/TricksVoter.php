<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TricksVoter extends Voter
{
    public const ADD = 'TRICK_ADD';
    public const EDIT = 'TRICK_EDIT';
    public const DELETE = 'TRICK_DELETE';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $trick): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $trick instanceof Trick;
    }

    protected function voteOnAttribute($attribute, $trick, TokenInterface $token): bool
    {
        // on récupère l'utilisateur à partir du token
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // on vérifie si l'utilisateur est admin

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // on vérifie les permissions
        switch ($attribute) {
            case self::ADD:
                // on vérifie si l'utilisteur peut ajouter
                return $this->canAdd();
                break;
            case self::EDIT:
                // on vérifie si l'utilisteur peut éditer
                return $this->canEdit();
                break;
            case self::DELETE:
                // on vérifie si l'utilisateur peut supprimer
                return $this->canDelete();
                break;
        }
    }

    private function canAdd()
    {
        return $this->security->isGranted('ROLE_USER');
    }

    private function canEdit()
    {
        return $this->security->isGranted('ROLE_USER');
    }

    private function canDelete()
    {
        return $this->security->isGranted('ROLE_USER');
    }
}
