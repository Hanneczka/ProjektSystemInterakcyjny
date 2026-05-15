<?php
namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\CategoryType;
use App\Service\CategoryServiceInterface;

#[Route('/category')]
class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryServiceInterface $categoryService) {

    }
    #[Route(
        '/',
        name: 'category_index',
        methods: ['GET']
    )]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render(
            'category/index.html.twig',
            ['categories' => $categoryRepository->findAll()]);
    }

    #[Route(
        '/create',
        name: 'category_create',
        methods: ['GET', 'POST']
    )]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

}
