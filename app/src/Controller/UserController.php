<?php

declare(strict_types=1);

/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\Element;
use App\Entity\User;
use App\Form\Type\PasswordType;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use App\Service\ElementServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService    User service
     * @param TranslatorInterface         $translator     Translator
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param ElementServiceInterface     $elementService Element service
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher, private readonly ElementServiceInterface $elementService)
    {
    }

    /**
     * Edit password action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/profile/edit-password', name: 'user_edit_password', methods: ['GET', 'PUT'])]
    public function editPassword(Request $request): Response
    {
        /** @var User $user User entity */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(
            PasswordType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit_password'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.password_updated'));

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit user action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/profile/edit-user', name: 'user_edit_user', methods: ['GET', 'PUT'])]
    public function editUser(Request $request): Response
    {
        /** @var User $user User entity */
        $user = $this->getUser();

        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit_user'),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->save($user);

            $this->addFlash('success', $this->translator->trans('message.user_updated'));

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * View profile action.
     *
     * @param UserRepository       $userRepository User repository
     * @param string               $id             User ID
     * @param UserServiceInterface $userService    User service
     * @param Request              $request        HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/profile/{id}', name: 'user_profile', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    public function view(UserRepository $userRepository, string $id, UserServiceInterface $userService, Request $request): Response
    {
        $user = $userRepository->findOneById($id);

        if (null === $user) {
            throw $this->createNotFoundException();
        }

        $page = $request->query->getInt('page', 1);

        $favoritesPagination = $userService->getPaginatedFavorites($user, $page);
        $favoriteElements = $favoritesPagination->getItems();

        $removeFromFavoriteForms = [];

        foreach ($favoriteElements as $element) {
            $form = $this->createForm(
                FormType::class,
                null,
                [
                    'action' => $this->generateUrl('element_favorite_remove', ['id' => $element->getId()]),
                    'method' => 'DELETE',
                ]
            );

            $removeFromFavoriteForms[$element->getId()] = $form->createView();
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'favorites' => $favoritesPagination,
            'remove_forms' => $removeFromFavoriteForms,
        ]);
    }

    /**
     * Remove favorite action.
     *
     * @param Element $element Element entity
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/favorite/{id}/remove', name: 'element_favorite_remove', requirements: ['id' => '[1-9]\d*'], methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function removeFavorite(#[MapEntity(mapping: ['id' => 'id'])] Element $element, Request $request): Response
    {
        /** @var User $user User entity */
        $user = $this->getUser();

        $form = $this->createForm(
            FormType::class,
            null,
            [
                'action' => $this->generateUrl('element_favorite_remove', ['id' => $element->getId()]),
                'method' => 'DELETE',
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageKey = $this->elementService->toggleFavorite($element, $user);
            $this->addFlash('success', $this->translator->trans($messageKey));
        } else {
            $this->addFlash('error', $this->translator->trans('message.form_error'));
        }

        return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
    }
}
