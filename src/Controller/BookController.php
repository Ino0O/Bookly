<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GenreRepository;

//Affiche les livres et filtre
final class BookController extends AbstractController
{
    #[Route('/books', name: 'app_books')]

public function index(
    BookRepository $bookRepository,
    GenreRepository $genreRepository,
    Request $request
    ): Response
    {
        $search = $request->query->get('search', '');
        $status = $request->query->get('status', '');
        $genre = $request->query->get('genre', '');

        $books = $bookRepository->search(
        $search,
        $status,
        $genre,
        $this->getUser()
        );

        $genres = $genreRepository->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
            'genres' => $genres,
        ]);
    }
}
