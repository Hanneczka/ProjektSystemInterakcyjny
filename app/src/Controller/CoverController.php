<?php

/**
 * Cover controller.
 */

namespace App\Controller;

use App\Entity\Cover;
use App\Entity\Element;
use App\Form\Type\CoverType;
use App\Security\Voter\CoverVoter;
use App\Service\CoverServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class CoverController.
 */
#[Route('/cover')]
class CoverController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CoverServiceInterface $coverService Cover service
     * @param TranslatorInterface   $translator   Translator
     */
    public function __construct(
        private readonly CoverServiceInterface $coverService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     * @param Element $element Element entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/create',
        name: 'cover_create',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, Element $element): Response
    {

        $cover = new Cover();
        $form = $this->createForm(
            CoverType::class,
            $cover,
            ['action' => $this->generateUrl('cover_create', ['id' => $element->getId()])]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $this->coverService->create(
                $file,
                $cover,
                $element
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
        }

        return $this->render(
            'element/cover_create.html.twig',
            [
                'form' => $form->createView(),
                'element' => $element,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Cover   $cover   Cover entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'cover_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(CoverVoter::EDIT, subject: 'cover')]
    public function edit(Request $request, Cover $cover): Response
    {
        $element = $cover->getElement();

        $form = $this->createForm(
            CoverType::class,
            $cover,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('cover_edit', ['id' => $cover->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $this->coverService->update(
                $file,
                $cover
            );

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
        }

        return $this->render(
            'element/cover_edit.html.twig',
            [
                'form' => $form->createView(),
                'cover' => $cover,
                'element' => $element,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Cover   $cover   Cover entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'cover_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(CoverVoter::DELETE, subject: 'cover')]
    public function delete(Request $request, Cover $cover): Response
    {
        $element = $cover->getElement();

        $form = $this->createForm(
            FormType::class,
            null,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('cover_delete', ['id' => $cover->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->coverService->delete($cover);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
        }

        return $this->render(
            'element/cover_delete.html.twig',
            [
                'form' => $form->createView(),
                'cover' => $cover,
            ]
        );
    }
}
