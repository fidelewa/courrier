<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Form\Type\MailMailsentFilterType;
use Mails\MailBundle\Form\Type\MailSentFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
  * @Security("has_role('ROLE_ADMIN')")
  */
class MailsentExtraController extends Controller
{
    /**
     * Filter mails sent.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterMailsentAction(Request $request)
    {
        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        //On crée notre formulaire
        $form = $this->createForm(MailMailsentFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsentData($form->getData(), $this->getUser(), 'filtreMailsent');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailsent_filter_result'));
        }

        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/mailsent_filter.html.twig', array(
        'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterMailsentResultAction()
    {
        return $this->render('MailsMailBundle:Mail:mailsent_filter_result.html.twig');
    }

    /**
     * filter mails sent according to the specified user
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterMailsentByUserAction($id, Request $request)
    {
        // On récupère l'user par son id
        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);

        if (null === $user) {
            throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        //On crée notre formulaire
        $form = $this->createForm(MailMailsentFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsentData($form->getData(), $user, 'filtreMailsentByUser');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailsent_filter_user_result'));
        }

        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/mailsent_user_filter.html.twig', array(
        'user' => $user,
        'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterMailsentByUserResultAction()
    {
        return $this->render('MailsMailBundle:Mail:user_mailsent_filter_result.html.twig');
    }

    /**
     * filter mails sent according to the specified interlocutor
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterMailsentByInterlocutorAction($id, Request $request)
    {
        // On récupère le contact par son id
        $actor = $this->getDoctrine()->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
            throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');

        //On crée notre formulaire
        $form = $this->createForm(MailMailsentFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsentData($form->getData(), $actor, 'filtreMailsentByActor');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailsent_filter_actor_result'));
        }

        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/mailsent_actor_filter.html.twig', array(
        'actor' => $actor,
        'form' => $form->createView()
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterMailsentByInterlocutorResultAction()
    {
        return $this->render('MailsMailBundle:Mail:actor_mailsent_filter_result.html.twig');
    }

    /**
     * filter all mails sent.
     *
     * @param Request $request
     * @param $page
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterAllMailsentAction(Request $request, $page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // On récupère notre mail factory
        $mailFactory = $this->get('mails_mail.mail_factory');
        
        //On crée notre formulaire
        $form = $this->createForm(MailSentFilterType::class, $mailFactory::create(), array(
            'adminCompany' => $this->getUser()->getCompany()
        ));
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if ($form->handleRequest($request)->isSubmitted() && $request->isMethod('POST')) {

            // On récupère notre service filter
            $filter = $this->get('mails_mail.mail_filter');

            // On récupère la liste des données du courrier envoyé
            $data_mailsent_retrieved = array(
                'mail' => $form->getData(), 'days' => $form->getData()->getNbDaysBefore(), 'reception' => $form->getData()->getReceived(), 
                'destinataire' => $form->getData()->getMailsent()->getActor()->getName(), 
                'expediteur' => $this->getUser()->getUsername(), 'num_items' => $filter::NUM_ITEMS
            );

            // liste des labels des données des courriers envoyés
            $label_data_mailsent = array('days','reception','expediteur','destinataire','num_items','mail');

            // On défini les attributs de session des données du courrier envoyé
            foreach ($label_data_mailsent as $label_data){

                $request->getSession()->set($label_data, $data_mailsent_retrieved[$label_data]);
            }

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_all_mailsent_filter_result', array('page' => $page)));
        }

        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/all_mailsent_filter.html.twig', array(
        'form' => $form->createView()
        ));
    }

    /**
     * filter all mails received.
     *
     * @param $page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function filterAllMailsentResultAction($page, Request $request)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // liste des labels des données des courriers envoyés
        $label_data_mailsent = array('days','reception','expediteur','destinataire','num_items','mail');

        // liste des données des courriers envoyés
        $data_mailsent = [];

        // On récupère les données du courrier envoyés depuis la session
        foreach ($label_data_mailsent as $label_data){

            $data_mailsent[$label_data] = $request->getSession()->get($label_data);
        }

        // On récupère notre service filter
        $filter = $this->get('mails_mail.mail_filter');

        //On récupère tous les courriers envoyés, filtrés par date et par reception
        $allMailsentFilter = $filter
        ->filtreAllMailsent($data_mailsent['days'], $data_mailsent['reception'], $data_mailsent['expediteur'],
        $data_mailsent['destinataire'], $page, $data_mailsent['num_items']);

        // On récupère notre service calculator
        $nbCalculator = $this->get('mails_mail.nbpage_calculator');

        // On calcule le nombre total de pages pour la recherche
        $nombreTotalPagesByFilter = $nbCalculator
        ->calculateTotalNumberPageByFilter($allMailsentFilter, $data_mailsent['num_items']);

        // Si aucune donnée ne correspond à la recherche, on fait une redirection vers la page d'accueil
        if ($page > $nombreTotalPagesByFilter) {
            $request->getSession()->getFlashBag()->add('danger', 'Aucune donnée ne correspond a cette recherche !');
            return $this->redirect($this->generateUrl('mails_core_home'));
        }
        
        // On affiche la page correspondante
        return $this->render('@mailsent_filter_result_views/all_mailsent_filter_result.html.twig', array(
        'page' => $page,
        'allMailsentFilter' => $allMailsentFilter,
        'nbPages' => $nombreTotalPagesByFilter,
        'mail' => $data_mailsent['mail']
        ));
    }

    /**
     * validate a mail sent.
     *
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validateMailsentAction($id, Request $request)
    {
        //On récupère notre Entity Manager
        $em = $this->getDoctrine()->getManager();

        // On récupère l'$id du mail sent
        $mailsent = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id, $this->getUser()->getCompany());

        if (null === $mailsent) {
            throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
        }
        
        //On valide le mail sent
        $mailsent->setValidated(true);
        
        // Inutile de persister ici, Doctrine connait déja notre courrier envoyé
        $em->flush();
        
        // On affiche un message de confirmation
        $flashbag = $request->getSession()->getFlashBag();
        $flashbag->add('success', 'Le courrier envoyé de référence "'.$mailsent->getReference().'" a été validé');

        // On redirige vers l'espace de travail de l'utilisateur
        return $this->redirect($this->generateUrl('mails_core_workspace_admin'));
    }
}
