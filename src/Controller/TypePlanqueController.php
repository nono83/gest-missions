<?php

namespace App\Controller;

use App\Entity\TypePlanque;
use App\Form\TypePlanqueType;
use App\Repository\TypePlanqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/type-planque")
 */
class TypePlanqueController extends AbstractController
{
    /**
     * @Route("/", name="app_type_planque_index", methods={"GET"})
     */
    public function index(TypePlanqueRepository $typePlanqueRepository): Response
    {
        return $this->render('type_planque/index.html.twig', [
            'type_planques' => $typePlanqueRepository->findAll(),
        ]);
    }

    /**
     * @Route("/list_datatables", name="app_type_planque_list_datatables")
     */
    public function listDatatablesAction(Request $request,TypePlanqueRepository $typePlanqueRepository): Response
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
        $results = $typePlanqueRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $typePlanqueRepository->countStatuts();
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

        foreach ($objects as $key => $statut)
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
                        $responseTemp = $statut->getId();
                        break;

                    case 'nom':
                    {
                        $name = $statut->getNom();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $name = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $name));

                        // View permission ?
                        if ($this->get('security.authorization_checker')->isGranted('view_statut', $statut))
                        {
                            // Get the ID
                            $id = $statut->getId();
                            // Construct the route
                            $url = $this->generateUrl('playground_statut_view', array('id' => $id));
                            // Construct the html code to send back to datatables
                            $responseTemp = "<a href='".$url."' target='_self'>".$ref."</a>";
                        }
                        else
                        {
                            $responseTemp = $name;
                        }
                        break;
                    }

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
     * @Route("/new", name="app_type_planque_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TypePlanqueRepository $typePlanqueRepository): Response
    {
        $typePlanque = new TypePlanque();
        $form = $this->createForm(TypePlanqueType::class, $typePlanque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typePlanqueRepository->add($typePlanque, true);

            return $this->redirectToRoute('app_type_planque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_planque/new.html.twig', [
            'type_planque' => $typePlanque,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_type_planque_show", methods={"GET"})
     */
    public function show(TypePlanque $typePlanque): Response
    {
        return $this->render('type_planque/show.html.twig', [
            'type_planque' => $typePlanque,
        ]);
    }

    /**
     * @Route("/edit/{id}",requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_type_planque_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id, TypePlanqueRepository $typePlanqueRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $typePlanque = $typePlanqueRepository->findOneBy(['id' => $id]);
        if (!$typePlanque) {
            throw $this->createNotFoundException(
                'Aucun type de planque pour l\'id: ' . $id
            );
        }

        $form = $this->createForm(TypePlanqueType::class, $typePlanque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $typePlanqueRepository->add($typePlanque, true);

            return $this->redirectToRoute('app_type_planque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('type_planque/edit.html.twig', [
            'type_planque' => $typePlanque,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}",requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_type_planque_delete")
     */
    public function delete(Request $request, int $id, TypePlanqueRepository $typePlanqueRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $typePlanque = $typePlanqueRepository->findOneBy(['id' => $id]);
        if (!$typePlanque) {
            throw $this->createNotFoundException(
                'Aucun type de planque pour l\'id: ' . $id
            );
        }

        //if ($this->isCsrfTokenValid('delete'.$typePlanque->getId(), $request->request->get('_token'))) {
            $typePlanqueRepository->remove($typePlanque, true);
        //}

        return $this->redirectToRoute('app_type_planque_index', [], Response::HTTP_SEE_OTHER);
    }
}
