<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mails\MailBundle\Form\Type\MailMailreceivedFilterType;
use Mails\MailBundle\Form\Type\MailReceivedFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
  * @Security("has_role('ROLE_ADMIN')")
  */
class MailreceivedExtraController extends Controller
{
    /**
     * Filter mails received.
     * 
     * @param Request $request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterMailreceivedAction(Request $request)
    {
        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');
        
        //On crée notre formulaire
        $form = $this->createForm(MailMailreceivedFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));

        //Si la requête est en POST on affiche la liste des resultats de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailreceivedData($form->getData(), $this->getUser(), 'filtreMailreceived');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailreceived_filter_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/mailreceived_filter.html.twig', array(
        'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterMailreceivedResultAction()
    {   // On affiche le resultat de la recherche sur les courriers recus
        return $this->render('MailsMailBundle:Mail:mailreceived_filter_result.html.twig');
    }

    /**
     * filter mails received according to the specified user
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @Template("@mailreceived_form_views/mailreceived_user_filter.html.twig")
     * 
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function filterMailreceivedByUserAction($id, Request $request)
    {
        // On récupère l'user par son id
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);

        // On vérifie que l'user existe bel et bien
        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        // On récupère notre service mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        //On crée notre formulaire
        $form = $this->createForm(MailMailreceivedFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailreceivedData($form->getData(), $user, 'filtreMailreceivedByUser');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailreceived_filter_user_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return array('user' => $user, 'form' => $form->createView());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterMailreceivedByUserResultAction()
    {
        return $this->render('MailsMailBundle:Mail:user_mailreceived_filter_result.html.twig');
    }

    /**
     * filter mails received according to the specified interlocutor
     *
     * @param integer $id Interlocutor id
     * @param Request $request Incoming request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterMailreceivedByInterlocutorAction($id, Request $request)
    {
        // On récupère le contact par son id
        $actor = $this->getDoctrine()->getRepository('MailsMailBundle:Actor')->find($id);

        // On vérifie que le contact existe bel et bien
        if (null === $actor) {
            throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        //On crée notre formulaire
        $form = $this->createForm(MailMailreceivedFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailreceivedData($form->getData(), $actor, 'filtreMailreceivedByActor');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailreceived_filter_actor_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/mailreceived_actor_filter.html.twig', array(
        'actor' => $actor,
        'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterMailreceivedByInterlocutorResultAction()
    {
        return $this->render('MailsMailBundle:Mail:actor_mailreceived_filter_result.html.twig');
    }

    /**
     * filter all mails received.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
     public function filterAllMailreceivedByUserAction(Request $request, $page)
     {
         if ($page < 1) {
             throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
         }

        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        //On crée notre formulaire
         $form = $this->createForm(MailReceivedFilterType::class, $mailFactory::create(), array(
             'adminCompany' => $this->getUser()->getCompany()
         ));
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {

            // On récupère notre service filter
            $filter = $this->get('mails_mail.mail_filter');

            // On récupère la liste des données du courrier reçu
            $data_mailreceived_retrieved = array(
                'mail' => $form->getData(), 'days' => $form->getData()->getNbDaysBefore(), 'reception' => $form->getData()->getReceived(), 
                'traitement' => $form->getData()->getTreated(), 'expediteur' => $form->getData()->getMailreceived()->getActor()->getName(), 
                'destinataire' => $this->getUser()->getUsername(), 'num_items' => $filter::NUM_ITEMS
            );

            // liste des labels des données des courriers reçus
            $label_data_mailreceived = array('days','reception','expediteur','destinataire','traitement','num_items','mail');

            // On défini les attributs de session des données du courrier reçu
            foreach ($label_data_mailreceived as $label_data){

                $request->getSession()->set($label_data, $data_mailreceived_retrieved[$label_data]);
            
            }

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_all_mailreceived_filter_user_result', array('page' => $page)));
        }

        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/all_mailreceived_filter_user.html.twig', array(
        'form' => $form->createView()
        ));
     }

    /**
     * filter all mails received.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterAllMailreceivedByUserResultAction($page, Request $request)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

         // liste des labels des données des courriers reçus
        $label_data_mailreceived = array('days','reception','expediteur','destinataire','traitement','num_items','mail');

        // liste des données des courriers reçus
        $data_mailreceived = [];

        // On récupère les données du courrier reçu depuis la session
        foreach ($label_data_mailreceived as $label_data){

            $data_mailreceived[$label_data] = $request->getSession()->get($label_data);
        }

        // On récupère notre service filter
        $filter = $this->get('mails_mail.mail_filter');

        //On récupère tous les courriers reçus, filtrés par date et par reception
        $allMailreceivedFilter = $filter
        ->filtreAllMailreceivedByUser($data_mailreceived['days'], $data_mailreceived['reception'], $data_mailreceived['expediteur'], 
        $data_mailreceived['destinataire'], $data_mailreceived['traitement'], $page, $data_mailreceived['num_items']);

        // On récupère notre service calculator
        $nbCalculator = $this->get('mails_mail.nbpage_calculator');

        // On calcule le nombre total de pages pour la recherche
        $nombreTotalPagesByFilter = $nbCalculator
        ->calculateTotalNumberPageByFilter($allMailreceivedFilter, $data_mailreceived['num_items']);

        // On vérifie bel et bien qu'une donnée correspond à cette recherche
        if ($page > $nombreTotalPagesByFilter) {
            $request->getSession()->getFlashBag()->add('danger', 'Aucune donnée ne correspond a cette recherche !');
            return $this->redirect($this->generateUrl('mails_core_home'));
        }
        
        // On affiche la page correspondante
        return $this->render('@mailreceived_filter_result_views/all_mailreceived_filter_user_result.html.twig', array(
        'page' => $page,
        'allMailreceivedFilter' => $allMailreceivedFilter,
        'nombreTotalPages' => $nombreTotalPagesByFilter,
        'mail' => $data_mailreceived['mail']
        ));
    }

    /**
     * filter all mails received.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
     public function filterAllMailreceivedAction(Request $request, $page)
     {
         // On vérifie que la page existe bel et bien
         if ($page < 1) {
             throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
         }

        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        // On crée notre formulaire
         $form = $this->createForm(MailReceivedFilterType::class, $mailFactory::create(), array(
             'adminCompany' => $this->getUser()->getCompany()
        ));
         
        //Si la requête est en POST on affiche la liste des resultats de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {

            // On récupère notre service filter
            $filter = $this->get('mails_mail.mail_filter');

            // On récupère la liste des données du courrier reçu
            $data_mailreceived_retrieved = array(
                'mail' => $form->getData(), 'days' => $form->getData()->getNbDaysBefore(), 'reception' => $form->getData()->getReceived(), 
                'traitement' => $form->getData()->getTreated(), 'expediteur' => $form->getData()->getMailreceived()->getActor()->getName(), 
                'destinataire' => $this->getUser()->getUsername(), 'num_items' => $filter::NUM_ITEMS
            );

            // liste des labels des données des courriers reçus
            $label_data_mailreceived = array('days','reception','expediteur','destinataire','traitement','num_items','mail');

            // On défini les attributs de session des données du courrier reçu
            foreach ($label_data_mailreceived as $label_data){

                $request->getSession()->set($label_data, $data_mailreceived_retrieved[$label_data]);
            }

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_all_mailreceived_filter_result', array('page' => $page)));
        }

        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/all_mailreceived_filter.html.twig', array(
        'form' => $form->createView()
        ));
     }

    /**
     * filter all mails received.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterAllMailreceivedResultAction($page, Request $request)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // liste des labels des données des courriers reçus
        $label_data_mailreceived = array('days','reception','expediteur','destinataire','traitement','num_items','mail');

        // liste des données des courriers reçus
        $data_mailreceived = [];

        // On récupère les données du courrier reçu depuis la session
        foreach ($label_data_mailreceived as $label_data){

            $data_mailreceived[$label_data] = $request->getSession()->get($label_data);
        }

        // On récupère notre service filter
        $filter = $this->get('mails_mail.mail_filter');

        //On récupère tous les courriers envoyés, filtrés par date et par reception
        $allMailreceivedFilter = $filter
        ->filtreAllMailreceived($data_mailreceived['days'], $data_mailreceived['reception'], 
        $data_mailreceived['expediteur'], $data_mailreceived['traitement'], $page, $data_mailreceived['num_items']);

        // On récupère notre service calculator
        $nbCalculator = $this->get('mails_mail.nbpage_calculator');

        // On calcule le nombre total de pages pour la recherche
        $nombreTotalPagesByFilter = $nbCalculator
        ->calculateTotalNumberPageByFilter($allMailreceivedFilter, $data_mailreceived['num_items']);

        // On vérifie bel et bien qu'une donnée correspond à cette recherche
        if ($page > $nombreTotalPagesByFilter) {
            $request->getSession()->getFlashBag()->add('danger', 'Aucune donnée ne correspond a cette recherche !');
            return $this->redirect($this->generateUrl('mails_core_home'));
        }
        
        // On affiche la page correspondante
        return $this->render('@mailreceived_filter_result_views/all_mailreceived_filter_result.html.twig', array(
        'page' => $page,
        'allMailreceivedFilter' => $allMailreceivedFilter,
        'nombreTotalPages' => $nombreTotalPagesByFilter,
        'mail' => $data_mailreceived['mail']
        ));
    }

    /**
     * validate a mail received.
     *
     * @param integer $id Mail received id
     * @param Request $request Incoming request
     * 
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validateMailreceivedAction($id, Request $request)
    {
        //On récupère notre Entity Manager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'$id du mail received
        $mailreceived = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id, $this->getUser()->getCompany());

        if (null === $mailreceived) {
            throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }
        
        // On valide le mail received
        $mailreceived->setValidated(true);
        
        // Inutile de persister ici, Doctrine connait déja notre courrier envoyé
        $em->flush();
        
        // On affiche le message de confirmation
        $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$mailreceived->getReference().'" a bien été validé.');
        
        // On redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_core_workspace_admin'));
    }
}
