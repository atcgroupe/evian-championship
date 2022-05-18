<?php

namespace App\Controller\Job;

use App\Form\JobDeliveryType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/job/{id}/delivery', name: 'job_delivery')]
class JobDeliveryController extends AbstractJobController
{
    /**
     * Update Job delivery
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route('/update', name: '_update')]
    public function setDelivery(int $id, Request $request): Response
    {
        $job = $this->jobRepository->find($id);

        if (!$this->isGranted('UPDATE_DELIVERY', $job)) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour modifier la livraison d'un job");

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        $form = $this->createForm(JobDeliveryType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logManager->addDeliveryLog($job);
            $this->manager->flush();
            $this->addFlash(
                'success',
                sprintf(
                    'La livraison du job <span class="font-bold">%s</span> a été modifiée.',
                    $job->getCustomerReference()
                )
            );

            return $this->redirectToRoute('job_view', ['id' => $id]);
        }

        return $this->render(
            'job/edit_delivery.html.twig',
            [
                'form' => $form->createView(),
                'job' => $job,
            ]
        );
    }
}
