<?php

namespace App\Controller;

use App\Security\Voter\TagVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Tag;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TagServiceInterface;
use App\Form\Type\TagType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/tag')]
class TagController extends AbstractController
{
    public function __construct(private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {

    }

    #[Route(
        '/',
        name: 'tag_index',
        methods: ['GET']
    )]
    public function index(#[MapQueryParameter] int $page = 1)
    {
        $pagination = $this->tagService->getPaginatedList($page);

        return $this->render(
            'tag/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    #[Route(
        '/{slug}',
        name: 'tag_view',
        methods: ['GET']
    )]
    #[IsGranted(TagVoter::VIEW, subject: 'tag')]
    public function view(Tag $tag): Response
    {

        return $this->render(
            'tag/view.html.twig',
            ['tag' => $tag]
        );
    }

    #[Route(
        '/create',
        name: 'tag_create',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(
        '/{slug}/edit',
        name: 'tag_edit',
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(TagVoter::EDIT, subject: 'tag')]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(
            TagType::class,
            $tag,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('tag_edit', ['slug' => $tag->getSlug()]),
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/edit.html.twig', ['form' => $form->createView(), 'tag' => $tag]);
    }

    #[Route(
        '/{slug}/delete',
        name: 'tag_delete',
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(TagVoter::DELETE, subject: 'tag')]
    public function delete(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(FormType::class, $tag, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('tag_delete', ['slug' => $tag->getSlug()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render(
            'tag/delete.html.twig',
            [
                'form' => $form->createView(),
                'tag' => $tag,
            ]
        );
    }
}
