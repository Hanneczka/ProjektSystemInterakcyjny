<?php

namespace App\Controller;

use App\Security\Voter\ElementVoter;
use App\Service\ElementServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Element;
use App\Form\Type\ElementType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\TagService;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\CommentServiceInterface;
use App\Form\Type\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Security\Voter\CommentVoter;
use App\Dto\ElementListInputFiltersDto;
use App\Form\Type\RatingType;
use App\Repository\RatingRepository;
use App\Repository\CommentRepository;

#[Route('/element')]
class ElementController extends AbstractController
{
    public function __construct(private readonly ElementServiceInterface $elementService, private readonly TranslatorInterface $translator, private readonly CommentServiceInterface $commentService, private readonly TagService $tagService)
    {
    }

    #[Route(
        name: 'element_index',
        methods: ['GET'],
    )]
    public function index(#[MapQueryString(resolver: ElementListInputFiltersDtoResolver::class)] ElementListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->elementService->getPaginatedList($page, $filters);

        return $this->render('element/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route(
        '/create',
        name: 'element_create',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $element = new Element();
        $form = $this->createForm(ElementType::class, $element);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->elementService->save($element);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('element_index');
        }

        return $this->render(
            'element/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route(
        '/{id}/edit',
        name: 'element_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(ElementVoter::EDIT, subject: 'element')]
    public function edit(Request $request, Element $element): Response
    {
        $form = $this->createForm(
            ElementType::class,
            $element,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('element_edit', ['id' => $element->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->elementService->save($element);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('element_index');
        }


        return $this->render(
            'element/edit.html.twig',
            [
                'form' => $form->createView(),
                'element' => $element,

            ]
        );
    }

    #[Route('/{id}', name: 'element_view', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    #[IsGranted(ElementVoter::VIEW, subject: 'element')]
    public function view(
        Element $element,
        #[MapQueryParameter] ?int $page = null,
        RatingRepository $ratingRepository,
    ): Response {
        $page = $page ?? 1;
        $user = $this->getUser();

        $existingRating = $user ? $ratingRepository->findOneBy(['element' => $element, 'user' => $user]) : null;
        $averageRating = $ratingRepository->getAverageRatingForElement($element);

        $ratingFormView = ($user && !$existingRating) ? $this->createForm(RatingType::class)->createView() : null;
        $commentFormView = $user ? $this->createForm(CommentType::class)->createView() : null;

        $comments = $this->commentService->getPaginatedList($page, $element);

        return $this->render(
            'element/view.html.twig',
            [
                'element' => $element,
                'comment_pagination' => $comments,
                'comment_form' => $commentFormView,
                'user_rating' => $existingRating,
                'form' => $ratingFormView,
                'average_rating' => $averageRating,
            ]
        );
    }




    #[Route(
        '/{id}/delete',
        name: 'element_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(ElementVoter::DELETE, subject: 'element')]
    public function delete(
        Request $request,
        Element $element,
        CommentRepository $commentRepository,
        RatingRepository $ratingRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(FormType::class, $element, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('element_delete', ['id' => $element->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comments = $commentRepository->findBy(['element' => $element]);
            foreach ($comments as $comment) {
                $entityManager->remove($comment);
            }

            $ratings = $ratingRepository->findBy(['element' => $element]);
            foreach ($ratings as $rating) {
                $entityManager->remove($rating);
            }
            $entityManager->flush();
            $this->elementService->delete($element);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('element_index');
        }

        return $this->render(
            'element/delete.html.twig',
            [
                'form' => $form->createView(),
                'element' => $element,
            ]
        );
    }

    #[Route('/{id}/favorite', name: 'element_favorite', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function favorite(Element $element, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        if ($user->getFavorites()->contains($element)) {
            $user->removeFavorite($element);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_from_favorites')
            );
        } else {
            $user->addFavorite($element);
            $this->addFlash(
                'success',
                $this->translator->trans('message.added_to_favorites')
            );
        }

        $entityManager->flush();

        return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
    }
}
