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
    #[Route('/{id}/change_role', name: 'admin_change_roles', requirements: ['id' => '[1-9]\d*'], methods: ['POST', 'GET'])]
    public function changeRoles(User $user, UserRepository $userRepository):Response{
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            $allAdmins = $userRepository->createQueryBuilder('u')
                ->where('u.roles LIKE :role')
                ->setParameter('role', '%"ROLE_ADMIN"%')
                ->getQuery()
                ->getResult();

            if (count($allAdmins) <= 1) {
                $this->addFlash('danger', $this->translator->trans('message.cannot_remove_last_admin'));
                return $this->redirectToRoute('user_index');
            }
            else{
            $roles = array_diff($roles, ['ROLE_ADMIN']);
            $this->addFlash('success', $this->translator->trans('message.admin_role_removed'));}
        } else {
            $roles[] = 'ROLE_ADMIN';
            $this->addFlash('success', $this->translator->trans('message.admin_role_granted'));
        }

        $user->setRoles(array_values(array_unique($roles)));
        $this->entityManager->flush();

        return $this->redirectToRoute('user_index');
    }

    #[Route('/{id}/block_user', name: 'admin_block_user', requirements: ['id' => '[1-9]\d*'], methods: ['POST', 'GET'])]
    public function blockUser(User $user, EntityManagerInterface $entityManager):Response{
        $roles = $user->getRoles();

        if (in_array('ROLE_BLOCKED', $roles)) {
            $user->setRoles(array_diff($roles, ['ROLE_BLOCKED']));
            $this->addFlash('success', $this->translator->trans('message.account_unblocked'));
        } else {
            $roles[] = 'ROLE_BLOCKED';
            $user->setRoles(array_unique($roles));
            $this->addFlash('warning', $this->translator->trans('message.account_blocked'));
        }

        $entityManager->flush();

        return $this->redirectToRoute('user_index');
    }

    #[Route('/{id}/block-confirm', name: 'admin_user_block_confirm', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    public function blockConfirm(User $user): Response
    {
        return $this->render('admin/block_confirm.html.twig', [
            'user' => $user,
        ]);
    }


}
