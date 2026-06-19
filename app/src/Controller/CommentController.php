<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Element;
use App\Form\Type\CommentType;
use App\Security\Voter\CommentVoter;
use App\Service\CommentServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly CommentServiceInterface $commentService,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route('/element/{id}/add_comment', name: 'element_comment_add', requirements: ['id' => '[1-9]\d*'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function addComment(
        Element $element,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setElement($element);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTimeImmutable());

            $this->commentService->save($comment);

            $this->addFlash('success', $this->translator->trans('comment_added'));
        } else {
            $this->addFlash('error', $this->translator->trans('message.form_error'));
        }

        return $this->redirectToRoute('element_view', ['id' => $element->getId()]);
    }

    #[Route('/comment/{id}/delete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'DELETE'])]
    #[IsGranted(CommentVoter::DELETE, subject: 'comment')]
    public function delete(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(FormType::class, $comment, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('comment_delete', ['id' => $comment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $elementId = $comment->getElement()->getId();
            $this->commentService->delete($comment);

            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('element_view', ['id' => $elementId]);
        }

        return $this->render('element/comment_delete.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }
}
