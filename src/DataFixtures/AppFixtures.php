<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# On va hacher les mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
# on va récupérer notre entité User
use App\Entity\User;

class AppFixtures extends Fixture
{
    # attribut contenant le hacher de mot de passe
    private UserPasswordHasherInterface $passwordHasher;

    # constructeur qui remplit les attributs
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        # hache le mot de passe
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        ###
        # Instanciation d'un User Admin
        #
        $user = new User();
        $user->setUsername('admin');
        $user->setUserMail('admin@gmail.com');
        $user->setRoles(['ROLE_ADMIN','ROLE_REDAC','ROLE_MODERATOR']);
        # hachage du mot de passe
        $pwdHash = $this->passwordHasher->hashPassword($user, 'admin');
        # insertion du mot de passe haché
        $user->setPassword($pwdHash);
        $user->setUserActive(true);
        $user->setUserRealName('The Admin !');

        # Utilisation du $manager pour mettre le
        # User en mémoire
        $manager->persist($user);

        ###
        # Instanciation de 5 Rédacteurs
        #
        for($i = 1; $i <= 5; $i++){
            $user = new User();
            $user->setUsername('redac'.$i);
            $user->setUserMail('redac'.$i.'@gmail.com');
            $user->setRoles(['ROLE_REDAC']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'redac'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Redac '.$i.' !');
            # Utilisation du $manager pour mettre le
            # User en mémoire
            $manager->persist($user);
        }


        for($i = 1; $i <= 3; $i++){
            $user = new User();
            $user->setUsername('moderator'.$i);
            $user->setUserMail('moderator'.$i.'@gmail.com');
            $user->setRoles(['ROLE_MODERATOR']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'moderator'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Moderator '.$i.' !');
            # Utilisation du $manager pour mettre le
            # User en mémoire
            $manager->persist($user);
        }

        # envoie à la base de donnée (commit)
        $manager->flush();
    }
}