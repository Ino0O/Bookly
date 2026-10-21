<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VerificationController extends AbstractController
{
    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verify(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->findOneBy([
            'verificationToken' => $token,
        ]);

        if (!$user) {
            throw $this->createNotFoundException('Token de vérification invalide.');
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);

        $entityManager->flush();

        return new Response('Votre adresse email a bien été vérifiée !');
    }
}