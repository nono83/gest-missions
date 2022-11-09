<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/contact")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="app_contact_index", methods={"GET"})
     */
    public function index(ContactRepository $contactRepository): Response
    {
        return $this->render('contact/index.html.twig', [
            'contacts' => $contactRepository->findAll(),
        ]);
    }

        /**
     * @Route("/list_datatables", name="app_contact_list_datatables")
     */
    public function listDatatablesAction(Request $request,ContactRepository $contactRepository): Response
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
        $results = $contactRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
      
        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $contactRepository->countContact();
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
            
        foreach ($objects as $key => $contact)
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
                        $responseTemp = $contact->getId();
                        break;

                    case 'contact':
                        $contact_nom = sprintf('%s %s', $contact->getNom(),$contact->getPrenom());

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $contact_nom = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $contact_nom));
                        $responseTemp = $contact_nom;
                        break;

                    case 'date_naissance':
                        //$date_naissance = new \DateTime('');
                        $date_naissance = $contact->getDateNaissance();
                        $responseTemp = $date_naissance->format('d-m-Y');
                        break;

                    case 'nationalite':
                        $pays = $contact->getNationalite();

                        if ($pays !== null)
                        {
                            $responseTemp = $pays->getNom();
                        }
                        break; 

                    case 'code':
                        $code = $contact->getNomCode();

                        // Do this kind of treatments if you suspect that the string is not JS compatible
                        $code = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $code));
                        $responseTemp = $code;
                        break;


                    case 'mission':
                        $mission = $contact->getMission();

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
     * @Route("/new", name="app_contact_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_contact_show", methods={"GET"})
     */
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * @Route("/edit/{id}",requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_contact_edit")
     */
    public function edit(Request $request, int $id, ContactRepository $contactRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $contact = $contactRepository->findOneBy(['id' => $id]);
        if (!$contact) {
            throw $this->createNotFoundException(
                'Aucun contact pour l\'id: ' . $id
            );
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/remove/{id}",requirements={"id"="\d+"}, methods={"GET", "POST"}, name="app_contact_delete")
     */
    public function delete(Request $request, int $id, ContactRepository $contactRepository): Response
    {
        //A mettre en commentaire si appel au paramconverter config/packages/sensio_framework_extra.yaml
        $contact = $contactRepository->findOneBy(['id' => $id]);
        if (!$contact) {
            throw $this->createNotFoundException(
                'Aucun contact pour l\'id: ' . $id
            );
        }

        //if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $contactRepository->remove($contact, true);
        //}

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
