<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Form\Type\RegisterType;


class RegistrationController extends AbstractController {
#[Route('/register', name: 'register')]
public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher):Response
{
    $user = new User();
    $form = $this->createForm(RegisterType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $newPassword = $form->get('password')->getData();
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
    }

    return $this->render('security/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}}
