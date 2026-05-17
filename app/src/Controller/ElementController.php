<?php

namespace App\Controller;

use App\Repository\ElementRepository;
use App\Service\ElementServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Element;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
public function index(ElementRepository $elementRepository): Response
    {
        $elements = $elementRepository->findAll();
        return $this->render('element/index.html.twig', ['elements' => $elements]);
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
