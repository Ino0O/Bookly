<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookNewController extends AbstractController
{
    #[Route('/books/new', name: 'app_books_new')]
    public function index(): Response
    {
        return $this->render('book_new/index.html.twig', [
            'controller_name' => 'BookNewController',
        ]);
    }
}
