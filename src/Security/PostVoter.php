<?php
namespace App\Security;

use App\Entity\Trick;
use App\Entity\User;
use App\Security\Exception\AccountException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PostVoter extends Voter
{
    // these strings are just invented: you can use anything
    const DELETE = 'delete';
    const EDIT = 'edit';
    const CREATE = "create";

    protected function supports(string $attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE, self::EDIT, self::CREATE])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Trick) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Trick $trick */
        $trick = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($trick, $user);
            case self::EDIT:
                return $this->canEdit($trick, $user);
        }

    }

    private function canEdit(Trick $trick, User $user): bool
    {
        // if they can edit, they can view
        if ($user){
            return true;
        }
        throw new \LogicException('This code should not be reached!');

    }

    private function canDelete(Trick $trick, User $user): bool
    {
       return $user === $trick->getUser();
    }
}