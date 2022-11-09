<?php

namespace App\Controller;

use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/mission/{id}", name="app_mission_index_show", methods={"GET"})
     */
    public function show(int $id, MissionRepository $missionRepository): Response
    {
         //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
         $mission = $missionRepository->findOneBy(['id' => $id]);
         if (!$mission) {
             throw $this->createNotFoundException(
                 'Aucune mission pour l\'id: ' . $id
             );
         }
        return $this->render('mission/show.html.twig', [
            'mission' => $mission,
        ]);
    }

     /**
     * @Route("/list_datatables", name="app_mission_list_datatables_index")
     */
    public function listDatatablesAction(Request $request,MissionRepository $missionRepository): Response
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
        $results = $missionRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $missionRepository->countMission();
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

        foreach ($objects as $key => $mission)
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
                        $responseTemp = $mission->getId();
                        break;

                    case 'titre':
                        $titre = $mission->getTitre();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $titre = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $titre));
                        $responseTemp = $titre;
                        break;


                    case 'statut':
                        $statut = $mission->getStatut();

                        if ($statut !== null)
                        {
                            $responseTemp = $statut->getNom();
                        }
                        break;

                    case 'pays':
                            $pays = $mission->getPays();
    
                            if ($pays !== null)
                            {
                                $responseTemp = $pays->getNom();
                            }
                            break;

                    case 'nom_code':
                        $nom_code = $mission->getNomCode();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $nom_code = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $nom_code));
                        $responseTemp = $nom_code;
                        break;


                    case 'date_debut':
                        //$date_debut = new \DateTime($mission->getDateDebut());
                        $date_debut = $mission->getDateDebut();
                        $responseTemp = $date_debut->format('d-m-Y');
                        break;
                    
                    case 'date_fin':
                        //$date_fin = new \DateTime($mission->getDateFin());
                        $date_fin = $mission->getDateFin();
                        $responseTemp = $date_fin->format('d-m-Y');
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
}
