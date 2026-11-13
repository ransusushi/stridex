<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Security\Voter\ProductVoter;
use App\Service\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/products')]
#[IsGranted(User::ROLE_STAFF)]
class ProductController extends AbstractController
{
    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        UserRepository $userRepository,
    ): Response {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $search = $request->query->getString('q');
        $category = $request->query->getString('category');
        $stockStatus = $request->query->getString('stock');
        $scope = $request->query->getString('scope', 'all');
        $creatorId = $request->query->getInt('creator', 0);

        $ownerFilter = null;
        if ('mine' === $scope) {
            $ownerFilter = $currentUser;
        } elseif ($this->isGranted(User::ROLE_ADMIN) && $creatorId > 0) {
            $ownerFilter = $userRepository->find($creatorId);
        }

        $products = $productRepository->findByFilters(
            search: $search,
            category: $category,
            owner: $ownerFilter,
            stockStatus: $stockStatus,
        );

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'categories' => Product::CATEGORIES,
            'creators' => $this->isGranted(User::ROLE_ADMIN) ? $userRepository->findAll() : [],
            'totalRecords' => $productRepository->countAllRecords(),
            'myRecordCount' => $productRepository->countByOwner($currentUser),
            'lowStockCount' => $productRepository->countLowStock(),
            'inventoryValue' => $productRepository->getInventoryValue(),
            'filters' => [
                'q' => $search,
                'category' => $category,
                'stock' => $stockStatus,
                'scope' => $scope,
                'creator' => $creatorId,
            ],
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductManager $productManager): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productManager->createProduct($product, $currentUser);

            $this->addFlash('success', \sprintf('Product "%s" (SKU: %s) has been created.', $product->getName(), $product->getSku()));

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[IsGranted(ProductVoter::VIEW, subject: 'product')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[IsGranted(ProductVoter::EDIT, subject: 'product')]
    public function edit(Request $request, Product $product, ProductManager $productManager): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::EDIT, $product);

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productManager->updateProduct($product);

            $this->addFlash('success', \sprintf('Product "%s" has been updated successfully.', $product->getName()));

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    #[IsGranted(ProductVoter::DELETE, subject: 'product')]
    public function delete(Request $request, Product $product, ProductManager $productManager): Response
    {
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product);

        if (!$this->isCsrfTokenValid('delete_product_'.$product->getId(), $request->request->getString('_token'))) {
            $this->addFlash('error', 'Invalid CSRF security token. Product was not deleted.');

            return $this->redirectToRoute('app_product_index');
        }

        $productName = $product->getName();
        $productManager->deleteProduct($product);

        $this->addFlash('success', \sprintf('Product "%s" has been deleted.', $productName));

        return $this->redirectToRoute('app_product_index');
    }
}
