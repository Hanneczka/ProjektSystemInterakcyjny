<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\CategoryType;
use App\Service\CategoryServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Security\Voter\CategoryVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\ElementServiceInterface;

#[Route('/category')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryServiceInterface $categoryService,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[Route(
        '/',
        name: 'category_index',
        methods: ['GET']
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->categoryService->getPaginatedList($page);

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    #[Route(
        '/{slug}',
        name: 'category_view',
        methods: ['GET']
    )]

    #[IsGranted(CategoryVoter::VIEW, subject: 'category')]
    public function view(Category $category, ElementServiceInterface $elementService, #[MapQueryParameter] int $page = 1): Response
    {
        $elements = $elementService->getPaginatedListByCategory($page, $category);

        return $this->render(
            'category/view.html.twig',
            ['category' => $category,
                'pagination' => $elements, ]
        );
    }

    #[Route(
        '/create',
        name: 'category_create',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    // ...
    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/edit',
        name: 'category_edit',
        methods: ['GET', 'PUT']
    )]
    #[IsGranted(CategoryVoter::EDIT, subject: 'category')]
    public function edit(Request $request, Category $category): Response
    {

        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('category_edit', ['slug' => $category->getSlug()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('category_index');
        }


        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{slug}/delete',
        name: 'category_delete',
        methods: ['GET', 'DELETE']
    )]
    #[IsGranted(CategoryVoter::DELETE, subject: 'category')]
    public function delete(Request $request, Category $category): Response
    {
        if (!$this->categoryService->canBeDeleted($category)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.category_contains_tasks')
            );

            return $this->redirectToRoute('category_index');
        }

        $form = $this->createForm(FormType::class, $category, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('category_delete', ['slug' => $category->getSlug()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);
            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
