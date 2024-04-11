<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $user->setFirstName($this->getRandomFirstName());
            $user->setLastName($this->getRandomLastName());
            $user->setEmail($user->getFirstName() . '.' . $user->getLastName() . '@test.com');
            $user->setUsername($user->getFirstName() . $user->getLastName() . strval($i));
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                "password"
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $this->addReference($user->getUsername(), $user);
            array_push($users, $user->getUsername());
        }

        for ($i=0; $i < 4; $i++) { 
            $category = new Category();
            $category->setName('Category' . strval($i));

            $manager->persist($category);
            $this->addReference($category->getName(), $category);
        }

        for ($i=0; $i < 4; $i++) { 
            $article = new Article();
            $article->setTitle('title' . strval($i));
            $article->setContent('article content' . strval($i));
            $article->setPublishedAt(new DateTime());
            $article->setSlug('/article/slug' . strval($i));
            $article->setStatus('draft');
            $article->setAuthorId($this->getReference($users[array_rand($users)]));
            $article->setCategoryId($this->getReference('Category' . strval($i)));

            $manager->persist($article);
            $this->addReference('article' . strval($i), $article);
        }

        for ($i=0; $i < 4; $i++) { 
            $comment = new Comment();
            $comment->setContent('comment content' . strval($i));
            $comment->setCreatedAt(new DateTime());
            $comment->setAuthorId($this->getReference($users[array_rand($users)]));
            $comment->setArticleId($this->getReference('article' . strval($i)));

            $manager->persist($comment);
        }

        $manager->flush();
    }

    private function getRandomFirstName(): string
    {
        $names = [
            'Michael',
            'Jim',
            'Dwight',
            'Pam',
            'Andy',
            'Ryan'
        ];

        return $names[array_rand($names)];
    }

    private function getRandomLastName(): string
    {
        $names = [
            'Scott',
            'Halpert',
            'Schrute',
            'Beesly',
            'Bernard',
            'Howard'
        ];

        return $names[array_rand($names)];
    }
}
