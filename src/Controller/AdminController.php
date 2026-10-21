<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BookRepository;
use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use App\Repository\ContactMessageRepository;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(
    UserRepository $userRepository,
    ContactMessageRepository $contactMessageRepository
    ): Response
    {
        $users = $userRepository->findAll();
        $messages = $contactMessageRepository->findBy(
            [],
            ['createdAt' => 'DESC']
        );

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'messages' => $messages,
        ]);
    }

    //afficher et supprimer un user
    #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(
        User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository
    ): Response
    {
        if ($user === $this->getUser()) {
            throw $this->createAccessDeniedException(
                'Vous ne pouvez pas supprimer votre propre compte.'
            );
        }

        if (!$this->isCsrfTokenValid(
            'delete_user_' . $user->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        $books = $bookRepository->findBy([
            'owner' => $user,
        ]);

        foreach ($books as $book) {
            $entityManager->remove($book);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/genres', name: 'app_admin_genres')]
    public function genres(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findAll();

        return $this->render('admin/genres.html.twig', [
            'genres' => $genres,
        ]);
    }

    //ajouter un genre
    #[Route('/admin/genres/new', name: 'app_admin_genre_new')]
    public function newGenre(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $genre = new Genre();

        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();

            $this->addFlash('success', 'Genre ajouté.');

            return $this->redirectToRoute('app_admin_genres');
        }

        return $this->render('admin/genre_new.html.twig', [
            'form' => $form,
        ]);
    }

    //supprimer un genre
    #[Route('/admin/genres/{id}/delete', name: 'app_admin_genre_delete', methods: ['POST'])]
    public function deleteGenre(
        Genre $genre,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        if (!$this->isCsrfTokenValid(
            'delete_genre_' . $genre->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        if ($genre->getBooks()->count() > 0) {
            $this->addFlash(
                'error',
                'Impossible de supprimer ce genre car il est utilisé par des livres.'
            );

            return $this->redirectToRoute('app_admin_genres');
        }

        $entityManager->remove($genre);
        $entityManager->flush();

        $this->addFlash('success', 'Genre supprimé.');

        return $this->redirectToRoute('app_admin_genres');
    }
}