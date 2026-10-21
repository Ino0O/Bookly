<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        MailerInterface $mailer
    ): Response
{
    $form = $this->createForm(RegistrationFormType::class);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
    $user = $form->getData();

    $hashedPassword = $passwordHasher->hashPassword(
        $user,
        $user->getPassword()
    );

    $verificationToken = bin2hex(random_bytes(32));
    $user->setVerificationToken($verificationToken);

    $user->setPassword($hashedPassword);

    $verificationUrl = 'http://127.0.0.1:8000/verify/' . $verificationToken;

    $email = (new Email())
    ->from('bookly@example.com')
    ->to($user->getEmail())
    ->subject('Vérifiez votre adresse email')
    ->text(
        'Bienvenue sur Bookly ! Cliquez sur ce lien pour vérifier votre adresse email : '
        . $verificationUrl
    );

    $mailer->send($email);

    $entityManager->persist($user);
    $entityManager->flush();

    $this->addFlash('success', 'Votre compte a bien été créé !');

    return $this->redirectToRoute('app_login');

}
return $this->render('registration/index.html.twig', [
        'form' => $form,
    ]);
}
}