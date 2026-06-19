<?php

/**
 * Element controller.
 */

namespace App\Controller;

use App\Dto\ElementListInputFiltersDto;
use App\Entity\Comment;
use App\Entity\Element;
use App\Form\Type\CommentType;
use App\Form\Type\ElementType;
use App\Form\Type\RatingType;
use App\Security\Voter\ElementVoter;
use App\Service\CommentServiceInterface;
use App\Service\ElementServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Resolver\ElementListInputFiltersDtoResolver;

/**
 * Class ElementController.
 */
#[Route('/element')]
class ElementController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ElementServiceInterface $elementService Element service
     * @param TranslatorInterface     $translator     Translator
     * @param CommentServiceInterface $commentService Comment service
     */
    public function __construct(private readonly ElementServiceInterface $elementService, private readonly TranslatorInterface $translator, private readonly CommentServiceInterface $commentService)
    {
    }

    /**
     * Index action.
     *
     * @param ElementListInputFiltersDto $filters Filters DTO
     * @param int                        $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'element_index',
        methods: ['GET'],
    )]
    public function index(#[MapQueryString(resolver: ElementListInputFiltersDtoResolver::class)] ElementListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->elementService->getPaginatedList($page, $filters);

        return $this->render('element/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
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

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Element $element Element entity
     *
     * @return Response HTTP response
     */
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

    /**
     * View action.
     *
     * @param Element  $element Element entity
     * @param int|null $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'element_view', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    #[IsGranted(ElementVoter::VIEW, subject: 'element')]
    public function view(Element $element, #[MapQueryParameter] ?int $page = null): Response
    {
        $page ??= 1;

        $cover = $this->elementService->findCoverForElement($element);

        $deleteForm = $this->createForm(
            FormType::class,
            null,
            [
                'action' => $this->generateUrl('element_rate_delete', ['id' => $element->getId()]),
                'method' => 'DELETE',
            ]
        );

        $user = $this->getUser();

        $existingRating = $user ? $this->elementService->findUserRating($element, $user) : null;
        $averageRating = $this->elementService->getAverageRating($element);

        $ratingFormView = ($user && !$existingRating) ? $this->createForm(RatingType::class)->createView() : null;
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('element_comment_add', ['id' => $element->getId()]),
            'method' => 'POST',
        ]);

        $comments = $this->commentService->getPaginatedList($page, $element);

        return $this->render(
            'element/view.html.twig',
            [
                'element' => $element,
                'cover' => $cover,
                'comment_pagination' => $comments,
                'comment_form' => $commentForm->createView(),
                'user_rating' => $existingRating,
                'form' => $ratingFormView,
                'average_rating' => $averageRating,
                'delete_rate_form' => $deleteForm->createView(),
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Element $element Element entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'element_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(ElementVoter::DELETE, subject: 'element')]
    public function delete(Request $request, Element $element): Response
    {
        $form = $this->createForm(FormType::class, $element, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('element_delete', ['id' => $element->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * Toggle favorite status action.
     *
     * @param Element $element Element entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/favorite', name: 'element_favorite', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function favorite(Element $element): Response
    {
        $user = $this->getUser();

        $messageKey = $this->elementService->toggleFavorite($element, $user);

        $this->addFlash('success', $this->translator->trans($messageKey));

        return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
    }
}
