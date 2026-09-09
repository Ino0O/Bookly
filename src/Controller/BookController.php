<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

//Récupere les livres et les affiches
final class BookController extends AbstractController
{
    #[Route('/books', name: 'app_books')]

    public function index(BookRepository $bookRepository): Response
{
    $books = $bookRepository->findAll();

    return $this->render('book/index.html.twig', [
        'books' => $books,
    ]);
}
}
