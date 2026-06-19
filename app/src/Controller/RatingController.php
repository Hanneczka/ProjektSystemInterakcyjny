<?php

/**
 * Rating controller.
 */

namespace App\Controller;

use App\Entity\Element;
use App\Entity\Rating;
use App\Form\Type\RatingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ElementRepository;
use App\Repository\RatingRepository;
use App\Service\RatingServiceInterface;

/**
 * Class RatingController.
 */
class RatingController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param RatingServiceInterface $ratingService Rating service
     * @param TranslatorInterface   $translator    Translator
     */
    public function __construct(
        private readonly RatingServiceInterface $ratingService,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * Rate action.
     *
     * @param Request $request HTTP request
     * @param Element $element Element entity
     *
     * @return Response HTTP response
     */
    #[Route('/element/{id}/rate', name: 'element_rate', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function rate(Request $request, Element $element): Response
    {
        $rating = new Rating();
        $form = $this->createForm(RatingType::class, $rating);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rating->setElement($element);
            $rating->setUser($this->getUser());

            $this->ratingService->save($rating);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
        }

        return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
    }

    /**
     * Delete rate action.
     *
     * @param Element          $element          Element entity
     * @param RatingRepository $ratingRepository Rating repository
     *
     * @return Response HTTP response
     */
    #[Route('/element/{id}/rate-delete', name: 'element_rate_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteRate(Element $element, RatingRepository $ratingRepository): Response
    {
        $user = $this->getUser();

        $rating = $ratingRepository->findOneBy([
            'element' => $element,
            'user' => $user,
        ]);

        if ($rating) {
            $this->ratingService->delete($rating);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );
        }

        return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
    }

    /**
     * Highest rated elements action.
     *
     * @param ElementRepository $elementRepository Element repository
     *
     * @return Response HTTP response
     */
    #[Route('/highest_rated', name: 'highest_rated', methods: ['GET'])]
    public function highestRating(ElementRepository $elementRepository): Response
    {
        $highestRated = $elementRepository->getHighestRated(10);

        return $this->render('rating/index.html.twig', [
            'elements' => $elementRepository->findAll(),
            'highest_rated' => $highestRated,
        ]);
    }
}
