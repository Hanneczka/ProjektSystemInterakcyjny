<?php

namespace App\Controller;

use App\Repository\ElementRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/element')]
class ElementController extends AbstractController
{
    #[Route(
        name: 'element_index',
        methods: ['GET'],
)]
public function index(ElementRepository $elementRepository): Response
    {
        $elements = $elementRepository->findAll();
        return $this->render('element/index.html.twig', ['elements' => $elements]);
    }
}
