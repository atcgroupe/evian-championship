<?php

namespace App\Controller\Delivery;

use App\Entity\Delivery;
use App\Form\DeliveryEditType;
use App\Repository\DeliveryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livraisons', name: 'delivery')]
class DeliveryController extends AbstractController
{
    private ObjectManager $manager;

    public function __construct(
        private DeliveryRepository $deliveryRepository,
        private ManagerRegistry $registry
    ) {
        $this->manager = $this->registry->getManager();
    }

    #[Route('', name: '_index')]
    public function index(): Response
    {
        $deliveries = $this->deliveryRepository->findAll();

        return $this->render(
            'delivery/index.html.twig',
            [
                'deliveries' => $deliveries,
            ]
        );
    }

    #[Route('/ajouter', name: '_create')]
    public function create(Request $request): Response
    {
        $delivery = new Delivery();
        $form = $this->createForm(DeliveryEditType::class, $delivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($delivery);
            $this->manager->flush();
            $this->addFlash('success', 'La date de livraison ' . $delivery->getTitle() . ' a été ajoutée avec succès.');

            return $this->redirectToRoute('delivery_index');
        }

        return $this->render(
            'delivery/edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/modifier/{id}', name: '_update')]
    public function update(int $id, Request $request): Response
    {
        $delivery = $this->deliveryRepository->find($id);
        $form = $this->createForm(DeliveryEditType::class, $delivery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();
            $this->addFlash('success', 'La livraison ' . $delivery->getTitle() . ' a été modifiée avec succès.');

            return $this->redirectToRoute('delivery_index');
        }

        return $this->render(
            'delivery/edit.html.twig',
            [
                'form' => $form->createView(),
                'delivery' => $delivery,
            ]
        );
    }

    #[Route('/supprimer/{id}', name: '_delete')]
    public function delete(int $id, Request $request): Response
    {
        $delivery = $this->deliveryRepository->find($id);

        if ($request->isMethod('POST')) {
            // ToDo: Verify if a delivery is linked to a Job
            // In this case, the delivery can't be deleted.
            $this->manager->remove($delivery);
            $this->manager->flush();
            $this->addFlash('success', 'La livraison a été supprimée avec succès.');

            return $this->redirectToRoute('delivery_index');
        }

        return $this->render('delivery/delete.html.twig', ['delivery' => $delivery]);
    }
}
