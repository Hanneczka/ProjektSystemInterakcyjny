<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Tag;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TagServiceInterface;
use App\Form\Type\TagType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\FormType;

#[Route('/tag')]
class TagController extends AbstractController{
    public function __construct(private readonly TagServiceInterface $tagService) {

    }

    #[Route(
        '/',
    name: 'tag_index',
    methods: ['GET']
)]
public function index(TagRepository $tagRepository){
        $tags = $tagRepository->findAll();
        return $this->render('tag/index.html.twig', ['tags' => $tags]);
    }

    #[Route(
        '/{id}',
        name: 'tag_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(TagRepository $repository, int $id): Response
    {
        $tag = $repository->findOneById($id);

        if (null === $tag) {
            throw $this->createNotFoundException();
        }


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
public function create(Request $request): Response{
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->tagService->save($tag);
            return $this->redirectToRoute('tag_index');
        }
        return $this->render('tag/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(
        '/{id}/edit',
    name: 'tag_edit',
    requirements: ['id' => '[1-9]\d*'],
    methods: ['GET', 'PUT']
)]
public function edit(Request $request, Tag $tag): Response{
        $form = $this->createForm(TagType::class, $tag,
        [
            'method' => 'PUT',
            'action' => $this->generateUrl('tag_edit', ['id' => $tag->getId()])
        ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->tagService->save($tag);
            return $this->redirectToRoute('tag_index');
        }
        return $this->render('tag/edit.html.twig', ['form' => $form->createView(), 'tag' => $tag]);
    }

    #[Route(
        '/{id}/delete',
        name: 'tag_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    public function delete(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(FormType::class, $tag, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('tag_delete', ['id' => $tag->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->delete($tag);

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
