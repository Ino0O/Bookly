<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;
use App\Entity\Genre;
use App\Entity\User;
use App\Enum\BookStatus;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('ino@example.com');
        $user1->setPassword(
            $this->passwordHasher->hashPassword($user1, 'password')
        );

        $user2 = new User();
        $user2->setEmail('lina@example.com');
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'password')
        );

        $fantasy = new Genre();
        $fantasy->setName('Fantasy');

        $scienceFiction = new Genre();
        $scienceFiction->setName('Science-fiction');

        $policier = new Genre();
        $policier->setName('Policier');

        $romance = new Genre();
        $romance->setName('Romance');

        $book1 = new Book();
        $book1->setTitle('Harry Potter');
        $book1->setAuthor('J.K. Rowling');
        $book1->setStatus(BookStatus::A_LIRE);
        $book1->setCreatedAt(new \DateTimeImmutable());
        $book1->setOwner($user1);
        $book1->setGenre($fantasy);

        $book2 = new Book();
        $book2->setTitle('Le Hobbit');
        $book2->setAuthor('J.R.R. Tolkien');
        $book2->setStatus(BookStatus::EN_COURS);
        $book2->setCreatedAt(new \DateTimeImmutable());
        $book2->setOwner($user1);
        $book2->setGenre($fantasy);

        $book3 = new Book();
        $book3->setTitle('Dune');
        $book3->setAuthor('Frank Herbert');
        $book3->setStatus(BookStatus::A_LIRE);
        $book3->setCreatedAt(new \DateTimeImmutable());
        $book3->setOwner($user2);
        $book3->setGenre($scienceFiction);

        $book4 = new Book();
        $book4->setTitle('Sherlock Holmes');
        $book4->setAuthor('Arthur Conan Doyle');
        $book4->setStatus(BookStatus::TERMINE);
        $book4->setCreatedAt(new \DateTimeImmutable());
        $book4->setOwner($user2);
        $book4->setGenre($policier);


        $manager->persist($user1);
        $manager->persist($user2);
        $manager->persist($fantasy);
        $manager->persist($scienceFiction);
        $manager->persist($policier);
        $manager->persist($romance);
        $manager->persist($book1);
        $manager->persist($book2);
        $manager->persist($book3);
        $manager->persist($book4);

        $manager->flush();

    }
}
