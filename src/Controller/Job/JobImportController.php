<?php

namespace App\Controller\Job;

use App\Form\JobDataImportType;
use App\Service\JobImport\JobDataImportManager;
use App\Service\JobImport\JobImportTemplateManager;
use App\Service\JobImport\TemplateDataChecker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/jobs/import', name: 'job_import')]
class JobImportController extends AbstractController
{
   #[Route('/template/download', name: '_template_download')]
    public function templateDownload(JobImportTemplateManager $jobImportManager): Response
    {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour importer des jobs");

            return $this->redirectToRoute('job_index');
        }

        $file = $jobImportManager->generateXlsxJobImportTemplate();

        $response = new BinaryFileResponse(
            $file,
            headers: ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ATC Groupe Gabarit job import.xlsx'
        );

        return $response;
    }

    #[Route('/step-one/upload', name: '_template_upload')]
    public function jobsImportStepOne(Request $request): Response
    {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour importer des jobs");

            return $this->redirectToRoute('job_index');
        }

        $form = $this->createForm(JobDataImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            try {
                $file->move($this->getParameter('app.template_file_dir'), 'jobs.xlsx');

            } catch (FileException $exception) {
                $this->addFlash(
                    'danger',
                    "Un problème est survenu lors de l'upload, veuillez réessayer.<br>"
                    . "Si le problème persiste veuillez contacter l'administrateur.");

                return $this->redirectToRoute('job_index');
            }

            return $this->redirectToRoute('job_import_data_validation');
        }

        return $this->render(
            'job/template/import_step_one.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('/step-two/validation', name: '_data_validation')]
    public function jobImportStepTwo(TemplateDataChecker $dataChecker): Response
    {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour importer des jobs");

            return $this->redirectToRoute('job_index');
        }

        $report = $dataChecker->checkData();

        return $this->render(
            'job/template/import_step_two.html.twig',
            [
                'report' => $report,
            ]
        );
    }

    #[Route('/step-three/import', name: '_import_jobs')]
    public function jobImportStepThree(
        TemplateDataChecker $dataChecker,
        JobDataImportManager $jobImportManager
    ): Response {
        if (!$this->isGranted('ROLE_CUSTOMER')) {
            $this->addFlash('danger', "Vous n'avez pas les droits pour importer des jobs");

            return $this->redirectToRoute('job_index');
        }

        $report = $dataChecker->checkData();

        if ($report->isNotValid()) {
            $this->addFlash('danger', 'Les données du tableau ne sont pas valides. Merci de recommencer l\'importation.');

            return $this->redirectToRoute('job_index');
        }

        $jobImportManager->importData();

        return $this->render(
            'job/template/import_step_three.html.twig',
            [
                'report' => $report,
            ]
        );
    }
}
