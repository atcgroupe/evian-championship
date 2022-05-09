<?php

namespace App\Controller\Product;

use App\Entity\Product;
use App\Form\ProductEditType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits', name: 'product')]
class ProductController extends AbstractController
{
    private ObjectManager $manager;

    public function __construct(
        private ProductRepository $productRepository,
        private ManagerRegistry $registry
    ) {
        $this->manager = $this->registry->getManager();
    }

    #[Route('', name: '_index')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('product/index.html.twig', [ 'products' => $products ]);
    }

    #[Route('/nouveau', name: '_create')]
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($product);
            $this->manager->flush();
            $this->addFlash('success', 'Le produit ' . $product->getName() . ' a été ajouté avec succès.');

            return $this->redirectToRoute('product_index');
        }

        return $this->render(
            'product/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/modifier/{id}', name: '_update')]
    public function update(int $id, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        $form = $this->createForm(ProductEditType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('info', 'Le produit ' . $product->getName() . ' a été modifié avec succès.');

            return $this->redirectToRoute('product_index');
        }

        return $this->render(
            'product/edit.html.twig',
            [
                'form' => $form->createView(),
                'product' => $product,
            ]
        );
    }
}
