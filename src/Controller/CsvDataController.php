<?php

namespace App\Controller;

use App\Form\CsvDataFormType;
use App\Repository\CsvDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CsvDataController extends AbstractController
{
    public function convertCsvToJson(Request $request, CsvDataRepository $csvDataRepository): Response
    {
        $csvPath = $this->getParameter('kernel.project_dir') . '/public/gaz.csv';
        $data = $csvDataRepository->convertCsvToJson($csvPath);

        return new Response(json_encode($data), 200, ['Content-Type' => 'application/json']);
    }

    public function modifyCsv(Request $request, CsvDataRepository $csvDataRepository): Response
    {
        $form = $this->createForm(CsvDataFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $identifier = $formData['identifier'];
            $newData = $formData['newData'];

            $csvDataRepository->addDataToCsv([$newData]);
            $csvDataRepository->deleteDataFromCsv($identifier);
            $csvDataRepository->updateDataInCsv($identifier, ['colonne_modifiable' => $newData]);

            return $this->redirectToRoute('app_csv_data');
        }

        return $this->render('csv_data/modify.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
