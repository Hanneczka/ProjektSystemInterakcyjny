<?php

namespace App\Controller;

use App\Form\Type\PasswordType;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher)
    {

    }

    #[Route('/profile/edit-password', name: 'user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request):Response{
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(PasswordType::class, $user, [
        'method'=>'POST',
        'action'=> $this->generateUrl('user_edit_password')]
    );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $newPassword = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.password_updated'));

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);

        }
        return $this->render('profile/edit_password.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/profile/edit-user', name: 'user_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request):Response{
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(UserType::class, $user, [
        'method'=>'POST',
        'action'=> $this->generateUrl('user_edit_user')]
    );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.user_updated'));

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);

        }
        return $this->render('profile/edit_user.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/profile/{id}', name: 'user_profile', methods: ['GET'])]
    public function view(UserRepository $userRepository, string $id): Response{
        $user = $userRepository->findOneById($id);

        if (null === $user) {
            throw $this->createNotFoundException();
        }


        return $this->render(
            'profile/profile.html.twig',
            ['user' => $user]
        );
    }

}
