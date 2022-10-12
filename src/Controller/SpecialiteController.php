<?php

namespace App\Controller;

use App\Entity\Specialite;
use App\Form\SpecialiteType;
use App\Repository\SpecialiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/specialite")
 */
class SpecialiteController extends AbstractController
{
    /**
     * @Route("/", name="app_specialite_index", methods={"GET"})
     */
    public function index(SpecialiteRepository $specialiteRepository): Response
    {
        return $this->render('specialite/index.html.twig', [
            'specialites' => $specialiteRepository->findAll(),
        ]);
    }

    /**
     * @Route("/list_datatables", name="app_specialite_list_datatables")
     */
    public function listDatatablesAction(Request $request,SpecialiteRepository $specialiteRepository): Response
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
        $results = $specialiteRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $specialiteRepository->countSpecialite();
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

        foreach ($objects as $key => $specialite)
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
                        $responseTemp = $specialite->getId();
                        break;

                    case 'nom':
                    {
                        $name = $specialite->getNom();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $name = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $name));

                        // View permission ?
                        if ($this->get('security.authorization_checker')->isGranted('view_specialite', $specialite))
                        {
                            // Get the ID
                            $id = $specialite->getId();
                            // Construct the route
                            $url = $this->generateUrl('playground_specialite_view', array('id' => $id));
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
     * @Route("/new", name="app_specialite_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SpecialiteRepository $specialiteRepository): Response
    {
        $specialite = new Specialite();
        $form = $this->createForm(SpecialiteType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specialiteRepository->add($specialite, true);

            return $this->redirectToRoute('app_specialite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('specialite/new.html.twig', [
            'specialite' => $specialite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_specialite_show", methods={"GET"})
     */
    public function show(Specialite $specialite): Response
    {
        return $this->render('specialite/show.html.twig', [
            'specialite' => $specialite,
        ]);
    }

    /**
     * @Route("/edit/{id}", requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_specialite_edit")
     */
    public function edit(Request $request, int $id, SpecialiteRepository $specialiteRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $specialite = $specialiteRepository->findOneBy(['id' => $id]);
        if (!$specialite) {
            throw $this->createNotFoundException(
                'Aucune spécialité pour l\'id: ' . $id
            );
        }

        $form = $this->createForm(SpecialiteType::class, $specialite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $specialiteRepository->add($specialite, true);

            return $this->redirectToRoute('app_specialite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('specialite/edit.html.twig', [
            'specialite' => $specialite,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}", requirements={"id"="\d+"}, methods={"GET", "POST"},name="app_specialite_delete")
     */
    public function delete(Request $request, int $id, SpecialiteRepository $specialiteRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $specialite = $specialiteRepository->findOneBy(['id' => $id]);
        if (!$specialite) {
            throw $this->createNotFoundException(
                'Aucune spécialité pour l\'id: ' . $id
            );
        }

        //if ($this->isCsrfTokenValid('delete'.$specialite->getId(), $request->request->get('_token'))) {
            $specialiteRepository->remove($specialite, true);
        //}

        return $this->redirectToRoute('app_specialite_index', [], Response::HTTP_SEE_OTHER);
    }
}
