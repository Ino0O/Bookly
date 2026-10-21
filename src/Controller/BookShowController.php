<?php

namespace App\Controller;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class BookShowController extends AbstractController
{
   //méthode pour modifier
    #[Route('/books/{id}/edit', name: 'app_book_edit')]
    public function edit(
        int $id,
        BookRepository $bookRepository,
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
    ): Response
    {
        $book = $bookRepository->find($id);

        //erreur
        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        //vérifie l'appartenance
        if ($book->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce livre.');
        }

        //formulaire de modif
        $form = $this->createForm(BookType::class, $book, [
        'submit_label' => 'Modifier',
        ]);

        $form->handleRequest($request);

        //Vérification et redirection
        if ($form->isSubmitted() && $form->isValid()) {

        $imageFile = $form->get('image')->getData();

        if ($imageFile instanceof UploadedFile) {
            $originalFilename = pathinfo(
                $imageFile->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            $safeFilename = $slugger->slug($originalFilename);

            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/books',
                $newFilename
            );

            $book->setImageFilename($newFilename);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Le livre a bien été modifié !');

        return $this->redirectToRoute('app_books');
    }

        //Afficher le form
        return $this->render('book/edit.html.twig', [
        'form' => $form,
    ]);

    }

//Méthode pour ajouter
#[Route('/books/new', name: 'app_book_new')]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    SluggerInterface $slugger
): Response
{
    $book = new Book();

    // formulaire pour le livre
    $form = $this->createForm(BookType::class, $book);

    $form->handleRequest($request);

    // Validation du formulaire
    if ($form->isSubmitted() && $form->isValid()) {

        $imageFile = $form->get('image')->getData();

        if ($imageFile instanceof UploadedFile) {

            $originalFilename = pathinfo(
                $imageFile->getClientOriginalName(),
                PATHINFO_FILENAME
            );

            $safeFilename = $slugger->slug($originalFilename);

            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/books',
                $newFilename
            );

            $book->setImageFilename($newFilename);
        }

        $book->setCreatedAt(new \DateTimeImmutable());

        // récupère l'utilisateur
        $book->setOwner($this->getUser());

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

        //vérifie l'appartenance du livre
        if ($book->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce livre.');
        }
        return $this->render('book_show/index.html.twig', [
            'book' => $book,
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

        //erreur
        if (!$book) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        //vérifie l'appartenance
        if ($book->getOwner() !== $this->getUser()) {
        throw $this->createAccessDeniedException('Vous n\'avez pas accès à ce livre.');
        }

        //Le supprime et redirige
        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a bien été supprimé !');

        return $this->redirectToRoute('app_books');
    }
}
