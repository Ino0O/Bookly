<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function search(
        string $search,
        string $status,
        string $genre,
        User $user
    ): array
    {
        $query = $this->createQueryBuilder('b')
            ->andWhere('b.owner = :user')
            ->setParameter('user', $user);

        if ($search !== '') {
            $query
                ->andWhere('b.title LIKE :search OR b.author LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($status !== '') {
            $query
                ->andWhere('b.status = :status')
                ->setParameter('status', $status);
        }

        if ($genre !== '') {
            $query
                ->andWhere('b.genre = :genre')
                ->setParameter('genre', (int) $genre);
        }

        return $query
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
