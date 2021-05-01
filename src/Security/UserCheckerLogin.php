<?php


namespace App\Security;
use App\Entity\User;
use App\Security\Exception\AccountException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserCheckerLogin implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return; // @codeCoverageIgnore
        }

        if (!$user->getStatus()) {
            throw new AccountException($user, "Votre compte est pas activé, merci de vérifier vos emails ou contacter un administrateur.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}