<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookShowController extends AbstractController
{
    #[Route('/books/{id}', name: 'app_books_show')]
    public function index(): Response
    {
        return $this->render('book_show/index.html.twig', [
            'controller_name' => 'BookShowController',
        ]);
    }
}
