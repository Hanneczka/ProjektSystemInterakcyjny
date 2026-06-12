<?php

namespace App\Controller;

use App\Form\Type\PasswordType;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;

#[Route('/admin')]
class AdminController extends AbstractController{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly TranslatorInterface $translator
    ) {}

    #[Route(
        '/',
        name: 'user_index',
        methods: ['GET']
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);
        return $this->render(
            'admin/index.html.twig',
            ['pagination' => $pagination]
        );
    }
    #[Route('/{id}/edit-password', name: 'admin_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user):Response{

        $form = $this->createForm(PasswordType::class, $user, [
                'method'=>'POST',
                'action' => $this->generateUrl('admin_edit_password', ['id' => $user->getId()])]
        );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $newPassword = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.password_updated'));

            return $this->redirectToRoute('user_index');

        }
        return $this->render('admin/edit_password.html.twig', ['form' => $form->createView()]);
    }
    #[Route('/{id}/edit-user', name: 'admin_edit_user', methods: ['GET', 'POST'])]
    public function editUser(Request $request, User $user):Response{


        $form = $this->createForm(UserType::class, $user, [
                'method'=>'POST',
                'action' => $this->generateUrl('admin_edit_user', ['id' => $user->getId()])]
        );
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.user_updated'));

            return $this->redirectToRoute('user_index');

        }
        return $this->render('admin/edit_user.html.twig', ['form' => $form->createView()]);
    }


}
