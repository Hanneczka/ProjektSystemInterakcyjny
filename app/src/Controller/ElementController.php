<?php

namespace App\Controller;

use App\Repository\ElementRepository;
use App\Service\ElementServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Element;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Type\ElementType;
use Symfony\Component\HttpFoundation\Request;
use App\Service\TagService;



#[Route('/element')]
class ElementController extends AbstractController
{
    public function __construct(private readonly ElementServiceInterface $elementService) {}
    #[Route(
        name: 'element_index',
        methods: ['GET'],
)]
public function index(ElementRepository $elementRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $elementRepository->queryAll(),
            $request->query->getInt('page', 1),
            ElementRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['element.id', 'element.createdAt', 'element.updatedAt', 'element.title'],
                'defaultSortFieldName' => 'element.createdAt',
                'defaultSortDirection' => 'desc',
            ]
        );
        return $this->render('element/index.html.twig', ['pagination' => $pagination]);
    }
    #[Route(
        '/create',
        name: 'element_create',
        methods: ['GET', 'POST']
    )]
    public function create(Request $request): Response
    {
        $element = new Element();
        $form = $this->createForm(ElementType::class, $element);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->elementService->save($element);

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
    #[Route(
        '/{id}',
        name: 'element_view',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET']
    )]
    public function view(ElementRepository $repository, int $id): Response
    {
        $element = $repository->findOneById($id);

        if (null === $element) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'element/view.html.twig',
            ['element' => $element]
        );
    }

    #[Route(
        '/{id}/delete',
        name: 'element_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    public function delete(Request $request, Element $element): Response
    {
        $form = $this->createForm(FormType::class, $element, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('element_delete', ['id' => $element->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->elementService->delete($element);

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
}
