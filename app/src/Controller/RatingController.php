<?php

namespace App\Controller;

use App\Entity\Element;
use App\Entity\Rating;
use App\Form\Type\RatingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ElementRepository;
use App\Repository\RatingRepository;

class RatingController extends AbstractController{

    #[Route('/element/{id}/rate', name: 'element_rate', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function rate(Request $request,
                         Element $element,
                         EntityManagerInterface $entityManager,
                         TranslatorInterface $translator):Response{
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setElement($element);
            $rating->setUser($this->getUser());

            $entityManager->persist($rating);
            $entityManager->flush();
            $this->addFlash(
                'success',
                $translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
        }

        return $this->redirectToRoute('element_view', ['id' => $element->getId()]);

    }
    #[Route('/highest_rated', name: 'highest_rated', methods: ['GET'])]
    public function highestRating(ElementRepository $elementRepository):Response{
        $highestRated =$elementRepository->getHighestRated(10);

        return $this->render('rating/index.html.twig', [
            'elements' => $elementRepository->findAll(),
            'highest_rated' => $highestRated,
        ]);
    }

}
