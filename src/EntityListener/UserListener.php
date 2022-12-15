<?php

namespace App\EntityListener;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        
    }

    public function prePersist(User $user)
    {
        $this->HashPassword($user);
    }

    public function preUpdate(User $user)
    {
        $this->HashPassword($user);
    }

    /**
     * Summary of HashPassword
     * @param User $user
     * @return void
     */
    public function HashPassword(User $user)
    {
        if ($user->getPlainPassword() === null) {
            return;
        }

        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            )
        );

        $user->setPlainPassword(null);

        

    }

}
