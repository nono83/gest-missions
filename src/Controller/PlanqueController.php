<?php

namespace App\Controller;

use App\Entity\Planque;
use App\Form\PlanqueType;
use App\Repository\PlanqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/planque")
 */
class PlanqueController extends AbstractController
{
    /**
     * @Route("/", name="app_planque_index", methods={"GET"})
     */
    public function index(PlanqueRepository $planqueRepository): Response
    {
        return $this->render('planque/index.html.twig', [
            'planques' => $planqueRepository->findAll(),
        ]);
    }

     /**
     * @Route("/list_datatables", name="app_planque_list_datatables")
     */
    public function listDatatablesAction(Request $request,PlanqueRepository $planqueRepository): Response
    {
        // Get the parameters from DataTable Ajax Call
        if ($request->getMethod() == 'POST')
        {
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
        }
        else // If the request is not a POST one, die hard
            die;

        // Process Parameters

        // Orders
        foreach ($orders as $key => $order)
        {
            // Orders does not contain the name of the column, but its number,
            // so add the name so we can handle it just like the $columns array
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }


        // Further filtering can be done in the Repository by passing necessary arguments
        $otherConditions = "array or whatever is needed";

        // Get results from the Repository
        $results = $planqueRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $planqueRepository->countPlanque();
        // Get total number of results
        $selected_objects_count = count($objects);
        // Get total number of filtered data
        $filtered_objects_count = $results["countResult"];

        // Construct response
        $response = '{
            "draw": '.$draw.',
            "recordsTotal": '.$total_objects_count.',
            "recordsFiltered": '.$filtered_objects_count.',
            "data": [';

        $i = 0;

        foreach ($objects as $key => $planque)
        {
            $response .= '["';

            $j = 0; 
            $nbColumn = count($columns);
            foreach ($columns as $key => $column)
            {
                // In all cases where something does not exist or went wrong, return -
                $responseTemp = "-";

                switch($column['name'])
                {
                    case 'id':
                        $responseTemp = $planque->getId();
                        break;

                    case 'code':
                        $code = $planque->getCode();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $code = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $code));
                        $responseTemp = $code;
                        break;


                    case 'adresse':
                        $adresse = $planque->getAdresse();
                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $adresse = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $adresse));
                        $responseTemp = $adresse;
                        break;

                    case 'type_planque':
                            $type_planque = $planque->getTypePlanque();
    
                            if ($type_planque !== null)
                            {
                                $responseTemp = $type_planque->getNom();
                            }
                            break;

                    case 'mission':
                        $mission = $planque->getMission();

                        if ($mission !== null)
                        {
                            $responseTemp = $mission->getTitre();
                        }
                        break;
                }

                // Add the found data to the json
                $response .= $responseTemp;

                if(++$j !== $nbColumn)
                    $response .='","';
            }

            $response .= '"]';

            // Not on the last item
            if(++$i !== $selected_objects_count)
                $response .= ',';
        }

        $response .= ']}';

        // Send all this stuff back to DataTables
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);

        return $returnResponse;

    }

    /**
     * @Route("/new", name="app_planque_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PlanqueRepository $planqueRepository): Response
    {
        $planque = new Planque();
        $form = $this->createForm(PlanqueType::class, $planque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planqueRepository->add($planque, true);

            return $this->redirectToRoute('app_planque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('planque/new.html.twig', [
            'planque' => $planque,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_planque_show", methods={"GET"})
     */
    public function show(Planque $planque): Response
    {
        return $this->render('planque/show.html.twig', [
            'planque' => $planque,
        ]);
    }

    /**
     * @Route("/edit/{id}", requirements={"id"="\d+"}, name="app_planque_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id, PlanqueRepository $planqueRepository): Response
    {
         //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
         $planque = $planqueRepository->findOneBy(['id' => $id]);
         if (!$planque) {
             throw $this->createNotFoundException(
                 'Aucune planque pour l\'id: ' . $id
             );
         }

        $form = $this->createForm(PlanqueType::class, $planque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $planqueRepository->add($planque, true);

            return $this->redirectToRoute('app_planque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('planque/edit.html.twig', [
            'planque' => $planque,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}",requirements={"id"="\d+"}, name="app_planque_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, int $id, PlanqueRepository $planqueRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $planque = $planqueRepository->findOneBy(['id' => $id]);
        if (!$planque) {
            throw $this->createNotFoundException(
                'Aucune planque pour l\'id: ' . $id
            );
        }

        //if ($this->isCsrfTokenValid('delete'.$planque->getId(), $request->request->get('_token'))) {
            $planqueRepository->remove($planque, true);
        //}

        return $this->redirectToRoute('app_planque_index', [], Response::HTTP_SEE_OTHER);
    }
}
