<?php

namespace App\Controller;

use App\Repository\ElementRepository;
use App\Service\ElementServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Element;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Type\ElementType;
use Symfony\Component\HttpFoundation\Request;



#[Route('/element')]
class ElementController extends AbstractController
{
    public function __construct(private readonly ElementServiceInterface $elementService) {}
    #[Route(
        name: 'element_index',
        methods: ['GET'],
)]
public function index(ElementRepository $elementRepository, PaginatorInterface $paginator, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $paginator->paginate(
            $elementRepository->queryAll(),
            $page,
            ElementRepository::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['element.id', 'element.createdAt', 'element.updatedAt', 'element.title'],
                'defaultSortFieldName' => 'element.updatedAt',
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
}
