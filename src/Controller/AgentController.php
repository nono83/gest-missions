<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Form\AgentType;
use App\Repository\AgentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/agent")
 */
class AgentController extends AbstractController
{
    /**
     * @Route("/", name="app_agent_index", methods={"GET"})
     */
    public function index(AgentRepository $agentRepository): Response
    {
        return $this->render('agent/index.html.twig', [
            'agents' => $agentRepository->findAll(),
        ]);
    }

        /**
     * @Route("/list_datatables", name="app_agent_list_datatables")
     */
    public function listDatatablesAction(Request $request,AgentRepository $agentRepository): Response
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
        $results = $agentRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $agentRepository->countAgent();
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

        foreach ($objects as $key => $agent)
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
                        $responseTemp = $agent->getId();
                        break;

                    case 'agent':
                        $agent_nom = sprintf('%s %s', $agent->getNom(),$agent->getPrenom());
                        $responseTemp = $agent_nom;
                        break;


                    case 'code':
                        $code = $agent->getCodeIdentification('f');
                        $responseTemp = $code;
                        break;

                    case 'nationalite':
                            $pays = $agent->getNationalite();
    
                            if ($pays !== null)
                            {
                                $responseTemp = $pays->getNom();
                            }
                            break;

                    case 'date_naissance':
                        //$date_debut = new \DateTime($mission->getDateDebut());
                        $date_naissance = $agent->getDateNaissance();
                        $responseTemp = $date_naissance->format('d-m-Y');
                        break;


                    case 'specialites':
                        $specialites = $agent->getSpecialites()->toArray();
                        foreach($specialites as $specialite)
                        {
                            $responseTemp .= sprintf('%s,', $specialite->getNom());
                        } 
                        //Suppression de la dernières virvule
                        $responseTemp=($responseTemp!='-')?substr($responseTemp,0,strlen($responseTemp)-1):"-";
                        break;
                    
                    case 'missions':
                        $missions = $agent->getMissions()->toArray();
                        foreach($missions as $mission)
                        {
                            $responseTemp .= sprintf('%s,', $mission->getTitre());
                        } 
                         //Suppression de la dernières virvule
                         $responseTemp=($responseTemp!='-')?substr($responseTemp,0,strlen($responseTemp)-1):"-";
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
     * @Route("/new", name="app_agent_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AgentRepository $agentRepository): Response
    {
        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agentRepository->add($agent, true);

            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/new.html.twig', [
            'agent' => $agent,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_agent_show", methods={"GET"})
     */
    public function show(Agent $agent): Response
    {
        return $this->render('agent/show.html.twig', [
            'agent' => $agent,
        ]);
    }

    /**
     * @Route("/edit/{id}", requirements={"id"="\d+"}, methods={"GET", "POST"},  name="app_agent_edit")
     */
    public function edit(Request $request, int $id, AgentRepository $agentRepository): Response
    {
         //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
         $agent = $agentRepository->findOneBy(['id' => $id]);
         if (!$agent) {
             throw $this->createNotFoundException(
                 'Aucun agent pour l\'id: ' . $id
             );
         }

        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agentRepository->add($agent, true);

            return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('agent/edit.html.twig', [
            'agent' => $agent,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}", requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_agent_delete")
     */
    public function delete(Request $request, int $id, AgentRepository $agentRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $agent = $agentRepository->findOneBy(['id' => $id]);
        if (!$agent) {
            throw $this->createNotFoundException(
                'Aucun agent pour l\'id: ' . $id
            );
        }
        
        //if ($this->isCsrfTokenValid('delete'.$agent->getId(), $request->request->get('_token'))) {
            $agentRepository->remove($agent, true);
        //}

        return $this->redirectToRoute('app_agent_index', [], Response::HTTP_SEE_OTHER);
    }
}
