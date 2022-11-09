<?php

namespace App\Controller;

use App\Entity\Cible;
use App\Form\CibleType;
use App\Repository\CibleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/cible")
 */
class CibleController extends AbstractController
{
    /**
     * @Route("/", name="app_cible_index", methods={"GET"})
     */
    public function index(CibleRepository $cibleRepository): Response
    {
        return $this->render('cible/index.html.twig', [
            'cibles' => $cibleRepository->findAll(),
        ]);
    }

        /**
     * @Route("/list_datatables", name="app_cible_list_datatables")
     */
    public function listDatatablesAction(Request $request,CibleRepository $cibleRepository): Response
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
        $results = $cibleRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $cibleRepository->countCible();
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
            
        foreach ($objects as $key => $cible)
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
                        $responseTemp = $cible->getId();
                        break;

                    case 'cible':
                        $cible_nom = sprintf('%s %s', $cible->getNom(),$cible->getPrenom());

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $cible_nom = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $cible_nom));
                        $responseTemp = $cible_nom;
                        break;

                    case 'date_naissance':
                        //$date_naissance = new \DateTime('');
                        $date_naissance = $cible->getDateNaissance();
                        $responseTemp = $date_naissance->format('d-m-Y');
                        break;

                    case 'nationalite':
                        $pays = $cible->getNationalite();

                        if ($pays !== null)
                        {
                            $responseTemp = $pays->getNom();
                        }
                        break; 

                    case 'code':
                        $code = $cible->getNomCode();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $code = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $code));
                        $responseTemp = $code;
                        break;


                    case 'mission':
                        $mission = $cible->getMission();

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
     * @Route("/new", name="app_cible_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CibleRepository $cibleRepository): Response
    {
        $cible = new Cible();
        $form = $this->createForm(CibleType::class, $cible);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cibleRepository->add($cible, true);

            return $this->redirectToRoute('app_cible_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cible/new.html.twig', [
            'cible' => $cible,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_cible_show", methods={"GET"})
     */
    public function show(Cible $cible): Response
    {
        return $this->render('cible/show.html.twig', [
            'cible' => $cible,
        ]);
    }

    /**
     * @Route("/edit/{id}",requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_cible_edit")
     */
    public function edit(Request $request, int $id, CibleRepository $cibleRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $cible = $cibleRepository->findOneBy(['id' => $id]);
        if (!$cible) {
            throw $this->createNotFoundException(
                'Aucun cible pour l\'id: ' . $id
            );
        }

        $form = $this->createForm(CibleType::class, $cible);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cibleRepository->add($cible, true);

            return $this->redirectToRoute('app_cible_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('cible/edit.html.twig', [
            'cible' => $cible,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}",requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_cible_delete")
     */
    public function delete(Request $request, int $id, CibleRepository $cibleRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $cible = $cibleRepository->findOneBy(['id' => $id]);
        if (!$cible) {
            throw $this->createNotFoundException(
                'Aucun cible pour l\'id: ' . $id
            );
        }

        //if ($this->isCsrfTokenValid('delete'.$cible->getId(), $request->request->get('_token'))) {
            $cibleRepository->remove($cible, true);
        //}

        return $this->redirectToRoute('app_cible_index', [], Response::HTTP_SEE_OTHER);
    }
}
