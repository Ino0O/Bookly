<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookShowController extends AbstractController
{
   //méthode pour modifier
    #[Route('/books/{id}/edit', name: 'app_book_edit')]
    public function edit(
        int $id,
        BookRepository $bookRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        //formulaire de modif
        $form = $this->createForm(BookType::class, $book, [
        'submit_label' => 'Modifier',
        ]);

        $form->handleRequest($request);

        //Vérification et redirection
        if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a bien été modifié !');

        return $this->redirectToRoute('app_books');
    }

        //Afficher la
        return $this->render('book/edit.html.twig', [
        'form' => $form,
    ]);
    
    }

//Méthode pour ajouter
    #[Route('/books/new', name: 'app_book_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ): Response
    {
        $book = new Book();

        //formulaire pour le livre
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        //Validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $book->setCreatedAt(new \DateTimeImmutable());

            $user = $userRepository->findOneBy([        
            'email' => 'ino@example.com'
            ]);

            $book->setOwner($user);

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a bien été ajouté !');

            return $this->redirectToRoute('app_books');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form,
        ]);
    }

   //Méthode pour afficher un livre
    #[Route('/books/{id<\d+>}', name: 'app_books_show')]
    public function index(
        int $id,
        BookRepository $bookRepository
    ): Response
    {
        $book = $bookRepository->find($id);

        //en cas d'erreur
        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        return $this->render('book_show/index.html.twig', [
            'book' => $book,
            'controller_name' => 'BookShowController',
        ]);
    }

//Méthodes pour supprimer les livres
    #[Route('/books/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    public function delete(
        int $id,
        BookRepository $bookRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        //Cherche le livre en question
        $book = $bookRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        //Le supprime et redirige
        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a bien été supprimé !');

        return $this->redirectToRoute('app_books');
    }
}
