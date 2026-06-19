<?php

/**
 * Admin controller.
 */

namespace App\Controller;

use App\Form\Type\PasswordType;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\UserServiceInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\User;
use App\Security\Voter\UserVoter;

/**
 * Class AdminController.
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator,)
    {
    }

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
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

    /**
     * Edit password action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit-password',
        name: 'admin_edit_password',
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(UserVoter::PASSWORD, subject: 'user')]
    public function editPassword(Request $request, User $user): Response
    {
        $form = $this->createForm(
            PasswordType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('admin_edit_password', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            $this->userService->upgradePassword($user, $newPassword);
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.password_updated'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/edit_password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit user action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit-user',
        name: 'admin_edit_user',
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(UserVoter::CHANGE, subject: 'user')]
    public function editUser(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('admin_edit_user', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.user_updated'));

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/edit_user.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Change roles action.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/change_role',
        name: 'admin_change_roles',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'POST']
    )]
    public function changeRoles(User $user): Response
    {
        if (!$this->isGranted(UserVoter::ROLES, $user)) {
            $this->addFlash('danger', $this->translator->trans('message.cannot_change_own_roles'));

            return $this->redirectToRoute('user_index');
        }
        if ($this->userService->isLastAdmin($user)) {
            $this->addFlash('danger', $this->translator->trans('message.cannot_remove_last_admin'));

            return $this->redirectToRoute('user_index');
        }

        $this->userService->toggleAdminRole($user);
        $this->addFlash('success', $this->translator->trans('message.role_changed_successfully'));

        return $this->redirectToRoute('user_index');
    }

    /**
     * Block user action.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/block_user',
        name: 'admin_block_user',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET','POST']
    )]
    public function blockUser(User $user): Response
    {
        if (!$this->isGranted(UserVoter::BLOCK, $user)) {
            $this->addFlash('danger', $this->translator->trans('message.cannot_block_yourself'));

            return $this->redirectToRoute('user_index');
        }

        $this->userService->toggleBlockUser($user);
        $this->addFlash('success', $this->translator->trans('message.block_status_changed'));

        $this->userService->save($user);

        return $this->redirectToRoute('user_index');
    }

    /**
     * Block confirm action.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/block-confirm',
        name: 'admin_user_block_confirm',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function blockConfirm(User $user): Response
    {
        return $this->render('admin/block_confirm.html.twig', [
            'user' => $user,
        ]);
    }
}
