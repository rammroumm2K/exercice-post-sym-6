# exerciceG1Sym6

- Créez un fork de ce projet
- Suivez les `README.md` de https://github.com/WebDevCF2m2023/EntitiesG1

À partir des `entités` et du `.env` de ce `repository`,

Créez la base de donnée, trouvez un template front et/ou un autre template back.

Vous devez pouvoir vous connecter avec un `User` (avec mot de passe crypté) au rôle `ROLE_ADMIN`

Créez une administration en back-end,

Mais surtout un site (+-) fonctionnel en front-end

## Les fixtures

Ce sont des données générées pour remplir nos bases de données en `dev`

Voir la documentation : 

https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html

### Installation

     composer require --dev orm-fixtures

Cette commande nous crée un fichier par défaut : `src/DataFixtures/AppFixtures.php`

On va commencer à insérer un `User` :

```php
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# on va récupérer notre entité User
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        # Instanciation d'un User
        $user = new User();
        $user->setUsername('admin');
        $user->setUserMail('admin@gmail.com');
        $user->setRoles(['ROLE_ADMIN','ROLE_REDAC','ROLE_MODERATOR']);
        $user->setPassword('admin');
        $user->setUserActive(true);
        $user->setUserRealName('The Admin !');

        # Utilisation du $manager pour mettre le
        # User en mémoire
        $manager->persist($user);

        # envoie à la base de donnée (commit)
        $manager->flush();
    }
}

```

Pour l'insérer dans la DB, on peut utiliser

    php bin/console doctrine:fixtures:load

ou

    php bin/console d:f:l

**Ceci écrase la DB !**, Pour éviter, vous pouvez ajouter :

    php bin/console d:f:l --append

#### Hachages des mots de passe

Ici notre mot de passe n'est pas crypté, et seul notre Admin est disponible

On va importer le hacher de mot de passe

```php
### ...
# On va hacher les mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
### ...
# attribut contenant le hacher de mot de passe
    private UserPasswordHasherInterface $passwordHasher;

    # constructeur qui remplit les attributs
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        # hache le mot de passe
        $this->passwordHasher = $passwordHasher;
    }
### ...
        # hachage du mot de passe
        $pwdHash = $this->passwordHasher->hashPassword($user, 'admin');
        # insertion du mot de passe haché
        $user->setPassword($pwdHash);
        $user->setUserActive(true);
### ...

```


### On souhaite avoir ces utilisateurs

- admin -> admin -> [ROLE_ADMIN, ROLE_REDAC, ROLE_MODERATOR]
- redac1 -> redac1 -> [ROLE_REDAC]
- redac2 -> redac2 -> [ROLE_REDAC]
- redac3 -> redac3 -> [ROLE_REDAC]
- redac4 -> redac4 -> [ROLE_REDAC]
- redac5 -> redac5 -> [ROLE_REDAC]
- moderator1 -> moderator1 -> [ROLE_MODERATOR]
- moderator2 -> moderator2 -> [ROLE_MODERATOR]
- moderator3 -> moderator3 -> [ROLE_MODERATOR]
- une trentaine d'utilisateur sans rôle le login == mot de passe

On va devoir ajouter un module permettant de créer du faux contenu.

### Installation de Faker

        composer require fakerphp/faker

La documentation :

https://fakerphp.org/

#### Fixtures ok pour les `User`

```php
<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
# On va hacher les mots de passe
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
# Chargement de Faker et création d'un alias nommé Faker
use Faker\Factory as Faker;
# on va récupérer notre entité User
use App\Entity\User;

class AppFixtures extends Fixture
{
    # attribut contenant le hacher de mot de passe
    private UserPasswordHasherInterface $passwordHasher;


    # constructeur qui remplit les attributs
    public function __construct(
        UserPasswordHasherInterface $passwordHasher,

    )
    {
        # hache le mot de passe
        $this->passwordHasher = $passwordHasher;
    }

    # constructeur qui remplit les attributs
    public function load(ObjectManager $manager): void
    {

    ###
    # GESTION de USER
    ###
        // Création de Faker
        $faker = Faker::create();

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

        ###
        # Instanciation de 3 modérateurs
        #
        for($i = 1; $i <= 3; $i++){
            $user = new User();
            $user->setUsername('modo'.$i);
            $user->setUserMail('modo'.$i.'@gmail.com');
            $user->setRoles(['ROLE_MODERATOR']);
            $pwdHash = $this->passwordHasher->hashPassword($user, 'modo'.$i);
            $user->setPassword($pwdHash);
            $user->setUserActive(true);
            $user->setUserRealName('The Moderator '.$i.' !');
            # Utilisation du $manager pour mettre le
            # User en mémoire
            $manager->persist($user);
        }


        ###
        # Instanciation entre 20 et 40 User sans rôles
        # en utilisant Faker
        #
        $hasard = mt_rand(20,40);
        for($i = 1; $i <= $hasard; $i++){
            $user = new User();
            # nom d'utilisateur au hasard commençant par user-1234
            $username = $faker->numerify('user-####');
            $user->setUsername($username);
            # création d'un mail au hasard
            $mail = $faker->email();
            $user->setUserMail($mail);
            $user->setRoles(['ROLE_USER']);
            # transformation du nom en mot de passe
            # (pour tester)
            $pwdHash = $this->passwordHasher->hashPassword($user, $username);
            $user->setPassword($pwdHash);
            # on va activer 1 user sur 3
            $randActive = mt_rand(0,2);
            $user->setUserActive($randActive);
            # Création d'un 'vrai' nom en français
            $realName = $faker->name();
            $user->setUserRealName($realName);

            $manager->persist($user);

        }

    ###
    # GESTION de POST
    ###

        
        # envoie à la base de donnée (commit)
        $manager->flush();
    }
}


```

On peut se connecter avec les différents utilisateurs

### Création des `Post` fixtures

La principale difficulté, c'est qu'un post doit être écrit par un utilisateur valide !

On va donc créer des tableaux des `User` qui peuvent poster des articles !